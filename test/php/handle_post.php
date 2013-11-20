<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once("project_action.php");
require_once("layout.php");
require_once("row.php");
require_once("login.php");

class Handle_post{
	
	function post_handle_refresh($id, $name, $sqlstatement, $xml_browse, $xml_insert, $row){
		$layout=new Layout;
		$layout->layout_set_id($id);
		$layout->layout_set_name($name);
		$layout->layout_set_sqlstatement($sqlstatement);
		$layout->layout_set_xml_browse($xml_browse);
		$layout->layout_set_xml_insert($xml_insert);

		$action = new Project_action;
		$action->init();
		$action->update_layout($layout);
		$json_return[]=$action->print_layout_html_arr($id, $row);
		$json_return[]=$action->print_xml_arr($id);
		$json_return[]=$action->get_row($layout, $row);

		echo json_encode($json_return);
	}

	function post_handle_next($row, $layout_id){
		$action = new Project_action;
		$action->init();
		$layout=$action->get_layout($layout_id);
		$json_return[]=$action->print_layout_html_arr($layout_id, $row);
		$json_return[]=$action->print_xml_arr($layout_id);
		$json_return[]=$action->get_row($layout, $row);
		echo json_encode($json_return);
	}

	function post_handle_layout_change($row, $layout_id){
		$action = new Project_action;
		$action->init();
		$layout=$action->get_layout($layout_id);
		$json_return[]=$action->print_layout_html_arr($layout_id, $row);
		$json_return[]=$action->print_xml_arr($layout_id);
		$json_return[]=$action->get_row($layout, $row);
		echo json_encode($json_return);
	}

	function post_handle_previous($row, $layout_id){
		$action = new Project_action;
		$action->init();
		$layout=$action->get_layout($layout_id);
		$json_return[]=$action->print_layout_html_arr($layout_id, $row);
		$json_return[]=$action->print_xml_arr($layout_id);
		$json_return[]=$action->get_row($layout, $row);
		echo json_encode($json_return);
	}

	function post_handle_create_layout($layout_name, $layout_sql){
		$action = new Project_action;
		$action->init();
		$layout=new Layout;
		$layout->name=$layout_name;
		$layout->sqlstatement=$layout_sql;
		if($action->create_layout($layout))
			echo 'Uusi asetelma on luotu.';
	}

	function post_handle_create_table($table_name, $columns){
		$action = new Project_action;
		$action->init();
		$table = new Table;
		$table->table_name=$table_name;
		$table->table_columns=$columns;
		$action->create_table($table);		
	}

	function post_handle_insert($data_keys, $data_data, $data_lengths, $table, $row_c){
		$action = new Project_action;
		$action->init();
		$data_keys_arr=explode(",", $data_keys);
		$data_lengths_arr=explode(",", $data_lengths);
		$data_data_arr;
	
		$c=0;
		for($i=0; $i<count($data_lengths_arr); $i++){
			$data_data_arr[]=substr($data_data, $c, $data_lengths_arr[$i]);
			$c=$data_lengths_arr[$i]+1;
		}

		$row=new Row;
		$row->set_keys($data_keys_arr);
		$row->set_data($data_data_arr);	
		$row->count=$row_c;

		$action->set_row($row,$table);

	}

	function post_handle_delete($table, $row){
		$action = new Project_action;
		$action->init();

		$rowObj=new Row;
		$rowObj->count=$row;
		$action->delete_row($rowObj, $table);
	}
	
	function post_handle_authorize($user, $pass){
		$login=new Login;
		$login->login_init();
		$auth=$login->login_authorize($user, $pass);
		
		if( $auth['auth'] == 1 ){
			echo '1';
			$login->login_session_start( $auth['id'] );
		}else{
			echo '0';
		}
	}
	
	function post_handle_logout(){
		$login=new Login;
		$login->login_session_destroy();
	}
	
	function post_handle_get_tablelist(){
		$action = new Project_action;
		$action->init();
		$table_list=$action->get_table_list();
		$ret_str;
		$c=0;
		for($i=0; $i<count($table_list); $i++){
			if($table_list[$i]!=NULL){
				$ret_str[$c]['table_name']=$table_list[$i]->table_name;
				$ret_str[$c]['columns']=$table_list[$i]->table_columns;
				$c++;
			}
		}
		
		echo json_encode($ret_str);
	}

	function post_handle_get_columnlist($table_name){
		$action = new Project_action;
		$action->init();
		$column_list=$action->get_column_list();

		$ret_str;
		$c=0;
		for($i=0; $i<count($table_list); $i++){
			if($table_list[$i]!=NULL){
				$ret_str[$c]['column_name']=$column_list[$i]['sarakkeen_nimi'];
				$ret_str[$c]['column_type']=$column_list[$i]['sarakkeen_tyyppi'];
				$c++;
			}
		}
		
		echo json_encode($ret_str);
	}

	function post_handle_add_column($table_name, $column_name, $column_type){
		$action = new Project_action;
		$action->init();

		$tableObj=new Table;
		$tableObj->table_name=$table_name;
		
		$action->add_column($tableObj, $column_name, $column_type);
	}

	function post_handle_change_table_name($table, $new_table_name){
		$action = new Project_action;
		$action->init();

		$tableObj=new Table;
		$tableObj->table_name=$table;
		
		$action->change_table_name($tableObj, $new_table_name);
	}

	function post_handle_change_column_name($table_name, $column_name, $new_column_name, $new_column_type){
		$action = new Project_action;
		$action->init();

		$tableObj=new Table;
		$tableObj->table_name=$table_name;
		
		$action->change_column_name($tableObj, $column_name, $new_column_name, $new_column_type);	
	}

	function post_handle_destroy_column($table_name, $column_name){
		$action = new Project_action;
		$action->init();

		$tableObj=new Table;
		$tableObj->table_name=$table_name;
		
		$action->destroy_column($tableObj, $column_name);	
	}

	function post_handle_destroy_table($table_name){
		$action = new Project_action;
		$action->init();

		$tableObj=new Table;
		$tableObj->table_name=$table_name;
		
		$action->destroy_table($tableObj);	
	}

	function post_handle_get_layoutlist(){
		$action = new Project_action;
		$action->init();

		$layout_list=$action->get_layout_list();
		$ret_str;
		$c=0;
		for($i=0; $i<count($layout_list); $i++){
			if($layout_list[$i]!=NULL){
				$ret_str[$c]['layout_name']=$layout_list[$i]->name;
				$ret_str[$c]['sql']=$layout_list[$i]->sqlstatement;
				$c++;
			}
		}
		
		echo json_encode($ret_str);
	}

	function post_handle_change_layout_name($layout_name, $new_layout_name){
		$action = new Project_action;
		$action->init();	
		$layoutObj=new Layout;
		$layoutObj->name=$layout_name;

		$action->change_layout_name($layoutObj, $new_layout_name);
	}

	function post_handle_change_layout_sql($layout_name, $sql){
		$action = new Project_action;
		$action->init();	
		$layoutObj=new Layout;
		$layoutObj->name=$layout_name;

		$action->change_layout_sql($layoutObj, $sql);
	}

	function post_handle_destroy_layout($layout_name){
		$action = new Project_action;
		$action->init();	
		$layoutObj=new Layout;
		$layoutObj->name=$layout_name;

		$action->destroy_layout($layoutObj);
	}

	function post_handle_post(){
			$type = $_POST['type'];

			if( $type == 0 ){
				$row = $_POST['row'];
				$layout_id = $_POST['layout_id'];
				$this->post_handle_next($row, $layout_id);
			}

			if( $type == 1 ){
				$row = $_POST['row'];
				$layout_id = $_POST['layout_id'];
				$this->post_handle_previous($row, $layout_id);
			}
	
			if( $type == 2 ){
				$xml_browse = $_POST['xml_browse'];
				$xml_insert = $_POST['xml_insert'];
				$layout_id = $_POST['layout_id'];
				$layout_name = $_POST['layout_name'];
				$layout_sqlstatement =$_POST['layout_sqlstatement'];
				$row = $_POST['row'];
				$this->post_handle_refresh($layout_id, $layout_name, $layout_sqlstatement, $xml_browse, $xml_insert, $row);
			}	

			if( $type == 3 ){
				$row = $_POST['row'];
				$layout_id = $_POST['layout_id'];
				$this->post_handle_layout_change($row, $layout_id);
			}

			if( $type == 4 ){
				$layout_name = $_POST['layout_name'];
				$layout_sql = $_POST['layout_sqlstatement'];
				$this->post_handle_create_layout($layout_name, $layout_sql);
			}

			if( $type == 5 ){
				$table_name = $_POST['table_name'];
				$table_columns = $_POST['table_columns'];
				$columns = explode(",", $table_columns);
				$this->post_handle_create_table($table_name, $columns);
			}
			
			if( $type == 6 ){
				$data_keys=$_POST['data_keys'];
				$data_data=$_POST['data_data'];
				$data_lengths=$_POST['data_lengths'];
				$table=$_POST['table'];
				$row=$_POST['row'];
				$this->post_handle_insert($data_keys, $data_data, $data_lengths, $table, $row);
			}

			if( $type == 7 ){
				$table=$_POST['table'];
				$row=$_POST['row'];
				$this->post_handle_delete($table, $row);
			}
	
			if( $type == 8 ){
				$user=$_POST['user'];
				$pass=$_POST['pass'];
				$this->post_handle_authorize($user, $pass);
			}
			
			if( $type == 9 ){
				$this->post_handle_logout();
			}
			
			if( $type == 10 ){
				$this->post_handle_get_tablelist();
			}

			if( $type == 11 ){
				$table_name=$_POST['table'];
				$this->post_handle_get_columnlist($table_name);
			}

			if( $type == 12 ){
				$table_name=$_POST['table'];
				$column_name=$_POST['column_name'];
				$column_type=$_POST['column_type'];
				$this->post_handle_add_column($table_name, $column_name, $column_type);
			}

			if( $type == 13 ){
				$table_name=$_POST['table'];
				$new_table_name=$_POST['new_table_name'];
				$this->post_handle_change_table_name($table_name, $new_table_name);
			}

			if( $type == 14 ){
				$table_name=$_POST['table'];
				$column_name=$_POST['column_name'];
				$new_column_name=$_POST['new_column_name'];
				$new_column_type=$_POST['new_column_type'];
				$this->post_handle_change_column_name($table_name, $column_name, $new_column_name, $new_column_type);
			}

			if( $type == 15 ){
				$table_name=$_POST['table'];
				$column_name=$_POST['column_name'];
				$this->post_handle_destroy_column($table_name, $column_name);
			}

			if( $type == 16 ){
				$table_name=$_POST['table'];
				$this->post_handle_destroy_table($table_name);
			}

			if( $type == 17 ){
				$this->post_handle_get_layoutlist();
			}

			if( $type == 18 ){
				$layout_name=$_POST['layout'];
				$new_layout_name=$_POST['new_layout_name'];
				$this->post_handle_change_layout_name($layout_name, $new_layout_name);
			}

			if( $type == 19 ){
				$layout_name=$_POST['layout'];
				$sql=$_POST['sql'];
				$this->post_handle_change_layout_sql($layout_name, $sql);
			}

			if( $type == 20 ){
				$layout_name=$_POST['layout'];
				$this->post_handle_destroy_layout($layout_name);
			}
	}

}

$handle=new Handle_post;
$handle->post_handle_post();

?>
