<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
require_once("controller.php");

class Handle_post{

	private $controller;

	function post_handle_init(){
		$this->controller=new Controller;
		$this->controller->init();
	}

	function post_handle_post(){
			$type = $_POST['type'];

			if( $type == 0 ){
				$row = $_POST['row'];
				$layout_id = $_POST['layout_id'];
				$this->controller->controller_next($row, $layout_id);
			}

			if( $type == 1 ){
				$row = $_POST['row'];
				$layout_id = $_POST['layout_id'];
				$this->controller->controller_previous($row, $layout_id);
			}
	
			if( $type == 2 ){
				$xml_browse = $_POST['xml_browse'];
				$xml_insert = $_POST['xml_insert'];
				$layout_id = $_POST['layout_id'];
				$layout_name = $_POST['layout_name'];
				$layout_sqlstatement =$_POST['layout_sqlstatement'];
				$row = $_POST['row'];
				$this->controller->controller_refresh($layout_id, $layout_name, $layout_sqlstatement, $xml_browse, $xml_insert, $row);
			}	

			if( $type == 3 ){
				$row = $_POST['row'];
				$layout_id = $_POST['layout_id'];
				$this->controller->controller_change_layout($row, $layout_id);
			}

			if( $type == 4 ){
				$layout_name = $_POST['layout_name'];
				$layout_sql = $_POST['layout_sqlstatement'];
				$this->controller->controller_create_layout($layout_name, $layout_sql);
			}

			if( $type == 5 ){
				$table_name = $_POST['table_name'];
				$table_columns = $_POST['table_columns'];
				$columns = explode(",", $table_columns);
				$this->controller->controller_create_table($table_name, $columns);
			}
			
			if( $type == 6 ){
				$data_keys=$_POST['data_keys'];
				$data_data=$_POST['data_data'];
				$data_lengths=$_POST['data_lengths'];
				$table=$_POST['table'];
				$row=$_POST['row'];
				$this->controller->controller_insert_data($data_keys, $data_data, $data_lengths, $table, $row);
			}

			if( $type == 7 ){
				$table=$_POST['table'];
				$row=$_POST['row'];
				$this->controller->controller_delete_data($table, $row);
			}
	
			if( $type == 8 ){
				$user=$_POST['user'];
				$pass=$_POST['pass'];
				$this->controller->controller_authorize($user, $pass);
			}
			
			if( $type == 9 ){
				$this->controller->controller_logout();
			}
			
			if( $type == 10 ){
				$this->controller->controller_get_table_list();
			}

			if( $type == 11 ){
				$table_name=$_POST['table'];
				$this->controller->controller_get_column_list($table_name);
			}

			if( $type == 12 ){
				$table_name=$_POST['table'];
				$column_name=$_POST['column_name'];
				$column_type=$_POST['column_type'];
				$this->controller->controller_add_column($table_name, $column_name, $column_type);
			}

			if( $type == 13 ){
				$table_name=$_POST['table'];
				$new_table_name=$_POST['new_table_name'];
				$this->controller->controller_change_table_name($table_name, $new_table_name);
			}

			if( $type == 14 ){
				$table_name=$_POST['table'];
				$column_name=$_POST['column_name'];
				$new_column_name=$_POST['new_column_name'];
				$new_column_type=$_POST['new_column_type'];
				$this->controller->controller_change_column_name($table_name, $column_name, $new_column_name, $new_column_type);
			}

			if( $type == 15 ){
				$table_name=$_POST['table'];
				$column_name=$_POST['column_name'];
				$this->controller->controller_destroy_column($table_name, $column_name);
			}

			if( $type == 16 ){
				$table_name=$_POST['table'];
				$this->controller->controller_destroy_table($table_name);
			}

			if( $type == 17 ){
				$this->controller->controller_get_layout_list();
			}

			if( $type == 18 ){
				$layout_name=$_POST['layout'];
				$new_layout_name=$_POST['new_layout_name'];
				$this->controller->controller_change_layout_name($layout_name, $new_layout_name);
			}

			if( $type == 19 ){
				$layout_name=$_POST['layout'];
				$sql=$_POST['sql'];
				$this->controller->controller_change_layout_sqlstatement($layout_name, $sql);
			}

			if( $type == 20 ){
				$layout_name=$_POST['layout'];
				$this->controller->controller_destroy_layout($layout_name);
			}
			
			if( $type == 21 ){
				$data_keys=$_POST['data_keys'];
				$data_data=$_POST['data_data'];
				$data_lengths=$_POST['data_lengths'];
				$sql=$_POST['sql'];
				$this->controller->controller_search($data_keys, $data_data, $data_lengths, $sql);
			}
	}

}

$handle=new Handle_post;
$handle->post_handle_init();
$handle->post_handle_post();


?>
