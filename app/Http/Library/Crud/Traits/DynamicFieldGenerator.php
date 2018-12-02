<?php
namespace App\Http\Library\Crud\Traits;
use Illuminate\Support\Facades\DB;

/**
 * This will fetch field from table and assign respective input type
 *
 *  @author Anirban Saha
 */
trait DynamicFieldGenerator
{
	/**
	 * basic input type respect of database field type
	 */
	public $input_type = array(
		'int' 		=> 'text',
		'varchar' 	=> 'text',
		'bigint' 	=> 'text',
		'text' 		=> 'editor',
		'longtext' 	=> 'editor',
		'date' 		=> 'date',
		'time' 		=> 'time',
		'timestamp' => 'date-time',
		'datetime'  => 'date-time',
		'enum'  	=> 'select',
		'tinyint'	=> 'radio',
	);
	public $input_type_by_name = array(
		'image'		=> 'file',
		'site_logo'		=> 'file',
		'file'		=> 'file',
		'pdf'		=> 'file',
		'password'	=> 'password',
		'eamil'		=> 'email',
	);
	/**
	 * This will return all coloumn name of a table
	 */
	public function fetchFields($table=null)
	{
		$table = $table == null ? $this->table_name : $table;
		$sql = "SHOW columns FROM ".$table;
		//fetch all coloumn name
		$all_fields = DB::select($sql);
		if($all_fields){
			return $all_fields;
		}else{
			return [];
		}
	}
	/**
	 * This will set basic input type to a coloumn
	 */
	public function setInputType()
	{
		$all_fields = $this->fetchFields();
		$input_fields = [];
		foreach($all_fields as $each_field){
			$each_field_info 	= [];
			$field_type_array 	= explode('(',$each_field->Type);
			$field_type 		= $field_type_array[0];
			//assign primary input type
			$field_type 		= isset($this->input_type[$field_type]) ? $this->input_type[$field_type] : 'text';
			$field_type 		= isset($this->input_type_by_name[$each_field->Field]) ? $this->input_type_by_name[$each_field->Field] : $field_type;
			$option_value		= '';
			if($field_type == "select"){
				$option_value		= [];
				$raw_option_value 	= isset($field_type_array[1]) ? $field_type_array[1] : '' ;
				$raw_option_value 	= str_ireplace([")","'","'"], '', $raw_option_value);
				$option_value_array		= explode(",",$raw_option_value);
				foreach($option_value_array as $value){
					$option_value[$value] = $value;
				}
			}
			if($field_type == "radio"){
				$option_value		= [];
				$option_value[1] 	= 'active';
				$option_value[0] 	= 'inactive';
			}
			$each_field_info['field_name'] 		= $each_field->Field;
			$each_field_info['field_type'] 		= $field_type;
			$each_field_info['field_label'] 	= labelCase($each_field->Field);
			$each_field_info['default_value'] 	= $each_field->Default != "CURRENT_TIMESTAMP" ? $each_field->Default : date('Y-m-d H:i:s');
			$each_field_info['option_values'] 	= $option_value;
			$each_field_info['class_name'] 		= 'form-control';
			$each_field_info['extra_attribute'] = '';
			$each_field_info['raw_html'] 		= '';
			$input_fields[$each_field->Field] 		= $each_field_info;
		}
		return $input_fields;
	}
	/**
	 * this will return label field if found or 2nd field as default
	 * @param  string $table table name
	 * @return string        field name of given table
	 */
	public function getLabelFieldName($table)
	{
		$all_fields = $this->fetchOnlyFieldName($table);
		$label_field = [];
		foreach ($all_fields as  $value) {
			if(str_contains($value,['name','title'])){
				$label_field[] = $value;
			}
		}
		return isset($label_field[0]) ? $label_field[0] : $all_fields[1];
	}
	/**
	 * This will return an array of a all field of a table
	 * @param  string $table table name
	 * @return array        list of field name
	 */
	public function fetchOnlyFieldName($table)
	{
		$all_fields = $this->fetchFields($table);
		$field_name = [];
		foreach($all_fields as $each_field){
			$field_name[] = $each_field->Field;
		}
		return $field_name;
	}
	/**
	 * If you need to add an extra coloumn or need to modify details before showing in table
	 * @param string $field             field name
	 * @param string $label             will display in table header
	 * @param string $callback_function function name, must be public and accept a param for current row object
	 */
	public function addCallBackColoumn($field,$label,$callback_function)
	{
		$each_coloumn = [];
		$each_coloumn['field'] 				= $field;
		$each_coloumn['label'] 				= $label;
		$each_coloumn['callback_function'] 	= $callback_function;
		$this->callback_coloumn[$field] 	= $each_coloumn;
		$this->extra_coloumn[$field] 		= $label;
	}
	/**
	 * This will set table coloumn headers and name
	 */
	public function getTableColumnList(){
		$all_fields = $this->fetchFields();
		$field_name = [];
		$custom_coulumn_list = false;
		if(is_array($this->columns_list)){
			//customized column list
			$custom_coulumn_list = $this->action_type == 'list' ? true : false;
			$all_fields_name = $this->fetchOnlyFieldName($this->table_name);
			foreach ($this->columns_list as $key => $value) {
				if(in_array($key, $all_fields_name)){
					$field_name[$key] = $value;
					$this->setAutoCallBackField($key);
				}
			}
		}
		foreach($all_fields as $each_field){
			if($custom_coulumn_list == false || ($custom_coulumn_list == true && in_array($each_field->Field,$field_name))){
				$field_name[$each_field->Field] = labelCase($each_field->Field);
				if(ends_with($each_field->Field,'_id') && !in_array($each_field->Field,$this->unset_relation_coloumn)){
					$this->auto_relationship_field[] = $each_field->Field;
				}
				$this->setAutoCallBackField($each_field->Field);
			}
		}
		$auto_relationship_field = array_diff($this->auto_relationship_field,$this->unset_relation_coloumn);
		if(count($auto_relationship_field)){
			foreach($this->auto_relationship_field as $each_field){
				if($custom_coulumn_list == false || ($custom_coulumn_list == true && in_array($each_field,$field_name))){
					$table_name = str_plural(str_ireplace('_id','',$each_field));
					$this->setRelation($each_field,$table_name,$this->getLabelFieldName($table_name));
				}
			}
		}
		$field_name			= $this->unsetUnwantedColumn($field_name);
		$final_coloumn_list = $field_name;
		if($this->action_type == 'list'){
			$this->setDefaultActions();
			if(count($this->actions_button)){
				$final_coloumn_list['action'] = 'Action';
			}
		}
		return $final_coloumn_list;
	}
	/**
	 * This will unset id, created_at, updated_at coloumn from table
	 * @param array $field_name all coloumn name in array
	 * @return array  visible coloumn name
	 */
	public function unsetUnwantedColumn($field_name)
	{
		if(is_array($this->unset_coloumn)){
			foreach ($this->unset_coloumn as $field) {
				if(isset($field_name[$field])){
					unset($field_name[$field]);
				}
			}
		}
		return $field_name;
	}
	/**
	 * this will set relationship with coloumn
	 * @param string $coloumn_name this table coloumn name
	 * @param string $table_name   set relation with this table
	 * @param string $show_field   show this field value
	 * @param string $key          default id column, else set this param
	 */
	public function setRelation($coloumn_name,$table_name,$show_field,$key='id')
	{
		$each_relation = [];
		$each_relation['set_relation'] 	= $coloumn_name;
		$each_relation['with_table'] 	= $table_name;
		$each_relation['show_field'] 	= $show_field;
		$each_relation['with_relation'] = $key;
		$each_relation['show_constraint'] = $table_name.'_'.$show_field;
		$this->relation_with[$coloumn_name] = $each_relation;
	}
	/**
	 * This will return file type field name
	 * @return array contain field name
	 */
	private function getFileTypeField(){
		$this->setInsertForm();
		$final_fields = $this->form_input_list;
		$file_field = [];
		foreach ($final_fields as $key => $value) {
			if($value['field_type'] == 'file'){
				$file_field[] = $key;
			}
		}
		return $file_field;
	}
	private function setAutoCallBackField($key)
	{
		//we will automatically callback for these fields
		if(strpos($key,'image') !== false){
			$this->addCallBackColoumn($key, ucfirst($key), 'setImage');
		}
		if(strpos($key,'status') !== false){
			$this->addCallBackColoumn($key, ucfirst($key), 'setStatus');
		}
	}
}

 ?>
