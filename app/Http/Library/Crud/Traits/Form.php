<?php
namespace App\Http\Library\Crud\Traits;
use Illuminate\Support\Facades\DB;

/**
 * This will contain all methods we need to display insert form
 *
 *  @author Anirban Saha
 */
trait Form
{
	/**
	* If you want to load extra js. put here. it will placed in script tag
	* @var string
	*/
	public $load_js 			= false;
	/**
	 * Changed field details wil be stored here
	 * @var array
	 */
	private $changed_field_type = [];

	/**
	 * You can change a field type
	 * @param  string $field_name      field name that you want to change
	 * @param  string $field_type      changed field type
	 * @param  string $field_label     field name in form
	 * @param  string $default_value   if you want to pass any default value for this fields
	 * @param  array $option_values   pass array for select box/radio/check box.value as key, label as value
	 * @param  string $class_name      class name of fields
	 * @param  string $extra_attribute if you want to add any extra attribute like onclick/onchange/ .. this string will be placed in input group
	 * @param  html $raw_html        if you want to pass raw html for this field. if you set this all other options will be ignored
	 * @return void                  change field type
	 */
	public function changeFieldType($field_name,$field_type,$field_label=null,$default_value=null,$option_values=null,$class_name='form-control',$extra_attribute=null,$raw_html=null)
	{
		$each_field_info						= [];
		$all_params 							= func_get_args();
		$field_option	= ['field_name','field_type','field_label','default_value','option_values','class_name','extra_attribute','raw_html'];
		foreach ($all_params as $key => $value) {
			if(!is_null($value)){
				$each_field_info[$field_option[$key]] = $value;
			}
		}
		$this->changed_field_type[$field_name] 	= $each_field_info;
	}
	/**
	 * This will set all form input list
	 */
	private function setInsertForm()
	{
		$final_coloumn_list = $this->getTableColumnList();
		if(count($final_coloumn_list)){
			$default_input_type = $this->setInputType();
			$final_fields = array_intersect_key($final_coloumn_list, $default_input_type);
			$form_input_list = [];
			foreach ($final_fields as $key => $details) {
				if(in_array($key,array_keys($this->relation_with))){
					$option_values = DB::select('SELECT id,'.$this->relation_with[$key]['show_field'].' FROM '.$this->relation_with[$key]['with_table']);
					$options = [];
					if($option_values && is_array($option_values)){
						foreach ($option_values as  $each_option) {
							$options[$each_option->id] = $each_option->{ $this->relation_with[$key]['show_field'] };
						}
					}
					$form_input_list[$key] = $default_input_type[$key];
					$form_input_list[$key]['field_type'] 	= 'select';
					$form_input_list[$key]['option_values'] = $options;
				}else{
					$form_input_list[$key] = $default_input_type[$key];
				}
				if(in_array($key,array_keys($this->changed_field_type))){
					$form_input_list[$key] = array_merge($form_input_list[$key],$this->changed_field_type[$key]);
				}
			}
			$this->form_input_list =  $form_input_list;
		}
	}
	/**
	 * this will set all details for edit form
	 */
	private function setEditForm()
	{
		$id_key = array_search('id',$this->unset_coloumn);
		if($id_key !== false){
			unset($this->unset_coloumn[$id_key]);
		}
		$this->setShowTableData();
		if(!isset($this->selected_data[0])){
			$this->form_input_list = false;
			return false;
		}
		$old_data = $this->selected_data[0];
		$this->changeFieldType('id', 'hidden', null, $old_data->id);
		$this->setInsertForm();
		$edit_form_data = [];
		foreach ($this->form_input_list as $key => $details) {
			if($key != 'password'){
				$details['default_value'] = $old_data->$key;
			}
			if(in_array($key,array_keys($this->callback_coloumn))){
				$call_back = $this->callback_coloumn[$key];
				$details['default_value'] = $this->{$call_back['callback_function']}($old_data,$old_data->$key,$this->action_type);
			}
			$edit_form_data[$key] = $details;
		}
		if(isset($edit_form_data['id'])){
			$this->form_input_list =  $edit_form_data;
		}else {
			$this->form_input_list = false;
		}
	}
}
