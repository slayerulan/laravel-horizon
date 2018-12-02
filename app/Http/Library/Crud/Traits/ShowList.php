<?php
namespace App\Http\Library\Crud\Traits;
use Illuminate\Support\Facades\DB;

/**
 * This will contain all methods we need to display data into table format
 *
 *  @author Anirban Saha
 */
trait ShowList
{
	/**
	 * visible coloumn list in table, associative array where coloumn name in key and label as value
	 */
	public $columns_list;
	/**
	 * if you want to unset default action button, set this. view/edit/delete
	 * @var array
	 */
	public $unset_actions_button = array();
	/**
	 * if you want to unset default realtion field, set this.all _id fields are default relational field
	 * @var array
	 */
	public $unset_relation_coloumn 	= array();
	/**
	 * If you want to add any additional clause in fetch query like where/ group by/ order by
	 * @var string
	 */
	public $additional_where;
	/**
	 * If you want to unset any coloumn from table. by default 3 are unset
	 * @var array
	 */
	public $unset_coloumn 			= ['id','created_at','updated_at'];
	/**
	 * You can set it true if you want to export data
	 * @var [type]
	 */
	public $show_export				= false;
	/**
	 * contain custom add link
	 * @var string
	 */
	public $add_link				= false;
	/**
	 * This will contain field name and callback function name
	 * @var array
	 */
	private $callback_coloumn 		= array();
	/**
	 * contain those coloumn name and label those has callback function
	 * @var array
	 */
	private $extra_coloumn 			= array();
	/**
	 * It will contain relationship details of a table
	 * @var array
	 */
	private $relation_with 			= array();
	/**
	 * If nothing defined crud will try to create relationship for those coloumn which has _id in title
	 * @var array
	 */
	private $auto_relationship_field = array();
	/**
	 * contain action button lists
	 * @var array
	 */
	private $actions_button 		= array();
	/**
	 * Contain add button details of false
	 * @var mixed
	 */
	public $add_button 			= array();
	/**
	 * This will contain fetched data
	 * @var array
	 */
	private $selected_data 			= array();

	/**
	 * This will add more button in action coloumn
	 * @param string $label visible name of button
	 * @param string $class class name of button
	 * @param string $icon  icon of button
	 * @param string $link  link of button, we will add id of respective row at the end
	 */
	public function setActionButton($label,$class,$icon,$link)
	{
		$each_action = [];
		$each_action['label'] 	= $label;
		$each_action['class'] 	= $class;
		$each_action['icon'] 	= $icon;
		$each_action['link'] = $link;
		$this->actions_button[$label] = $each_action;
	}
	/**
	 * If you want to unset Add button
	 */
	public function unsetAdd()
	{
		$this->add_button = false;
	}
	/**
	 * this will generate all data to create table view of a table
	 */
	private function setShowTableData()
	{
		$final_coloumn_list 			= $this->getTableColumnList();
		if(count($final_coloumn_list)){
			$all_data 					= $this->generateTableData($final_coloumn_list);
			$this->show_table_data 		= $all_data;
			$extra_fields 				= array_diff_key($this->extra_coloumn,$final_coloumn_list);
			$view_final_coloumn_list 		= array_merge($extra_fields,$final_coloumn_list);
			$this->show_table_coloumns 	= $this->action_type == "list" ? $final_coloumn_list :$view_final_coloumn_list;
		}else{
			abort(403, 'No coloumn found');
		}
	}
	/**
	 * This will generate table data
	 * @param  array $final_coloumn_list coloumn list of a table
	 * @return array                     all data to be displayed in table
	 */
	private function generateTableData($final_coloumn_list)
	{
		$all_data 					= $this->getShowData($final_coloumn_list);
		$file_field 				= $this->getFileTypeField();
		$table_data 				= [];
		if($all_data){
			$normal_field 			= array_diff_key($final_coloumn_list,$this->callback_coloumn);
			foreach($all_data as $each_row){
				$row_data 			= [];
				foreach ($this->callback_coloumn as $field_name => $details) {
					if(property_exists($each_row,$field_name)){
						$row_data[$field_name] =  $this->{$details['callback_function']}($each_row,$each_row->{$field_name},$this->action_type);
					}
				}
				foreach ($normal_field as $key => $value) {
					if($key != 'action'){
						//set field value name checking with relation field
						$should_to_show_field_value = isset($this->relation_with[$key]) ? $this->relation_with[$key]['show_constraint'] : $key;
						$row_data[$key] 			= $this->action_type == 'list' ? str_limit($each_row->{$should_to_show_field_value}, 50, '...') : $each_row->{$should_to_show_field_value};
						if(in_array($key, $file_field)){
							$row_data[$key] 		= '<a href="'.asset('storage/'.$row_data[$key]).'" target="_blank">'.$row_data[$key].'</a>';
						}
					}else{
						//set action field
						$action 					= '';
						if(count($this->actions_button) && is_array($this->actions_button)){
							foreach ($this->actions_button as $label => $button) {
							$action 				.= '<a href="'.$button['link'].'/'.$each_row->id.'" class="waves-effect '.$button['class'].'"><i class="material-icons">'.$button['icon'].'</i>'.$button['label'].'</a>';
							}
						}
						$row_data[$key] 			= $action;
					}
				}
				$table_data[] 						= $row_data;
			}
		}
		return $table_data;
	}
	/**
	 * this retun raw table data
	 * @param  array $final_coloumn_list coloumn list of table
	 * @return array                     table data
	 */
	private function getShowData($final_coloumn_list)
	{
		$sql 					= $this->setQuery($final_coloumn_list);
		$all_data 				= DB::select($sql);
		$this->selected_data 	= $all_data;
		return $all_data;
	}
	/**
	 * This will generate Sql for fetching data
	 * @param array $final_coloumn_list coloumn list that need to fetch
	 */
	private function setQuery($final_coloumn_list)
	{
		$final_selected_field = array_keys($final_coloumn_list);
		$selected_field 	  = [];
		$alphabet = range('A', 'Z');
		foreach($final_selected_field as $each_field){
			if($each_field != 'action'){
				$selected_field[] = $this->table_name.'.'.$each_field;
			}
		}
		$join_statement = [];
		if(count($this->relation_with)){
			$i = 0;
			foreach($this->relation_with as $each_relation){
				$selected_field[] 	= $alphabet[$i].'.'.$each_relation['show_field'].' AS '.$each_relation['show_constraint'];
				$join_statement[] 	= 'LEFT JOIN '.$each_relation['with_table'].' AS '.$alphabet[$i].' ON '.$this->table_name.'.'.$each_relation['set_relation'].'='.$alphabet[$i].'.'.$each_relation['with_relation'] ;
				$i++;
			}
		}
		$sql 						= "SELECT ".$this->table_name.'.id,'.implode(',',$selected_field).' FROM '.$this->table_name.' '.implode(" ", $join_statement);
		if($this->additional_where){
			$sql 					.= ' '.$this->additional_where;
		}
		return $sql;
	}
	/**
	 * set default action like view edit delete
	 */
	private function setDefaultActions()
	{
		$default = [
			'view' => [
						'label' => 'View',
						'class' => 'btn btn-info',
						'icon' 	=> 'info',
						'link' 	=> route($this->route_slug.'view')
					],
			'edit' => [
						'label' => 'Edit',
						'class' => 'btn btn-warning',
						'icon' 	=> 'create',
						'link' 	=> route($this->route_slug.'edit')
					],
			'delete' => [
						'label' => 'Delete',
						'class' => 'delete_button btn btn-danger',
						'icon' 	=> 'delete_sweep',
						'link' 	=> route($this->route_slug.'delete')
					],
		];
		//set default action button
		foreach ($default as $key => $each) {
			if(!in_array($key,$this->unset_actions_button)){
				$this->setActionButton($each['label'],$each['class'],$each['icon'],$each['link']);
			}
		}
	}
	/**
	 * this will set add button
	 */
	public function setAdd()
	{
		$each_action = [];
		$each_action['label'] 	= 'Add';
		$each_action['class'] 	= 'btn btn-success add_button';
		$each_action['icon'] 	= 'add_circle_outline';
		$each_action['link'] 	= $this->add_link == false ?   route($this->route_slug.'add') :  $this->add_link;
		$this->add_button = $each_action;
	}
	/**
	 * This will set custom  add  link
	 * @param string $link custom add link
	 */
	public function setAddLink($link)
	{
		$this->add_link = $link;
	}
}

 ?>
