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
				$this->controller->check_user_session();
				$row = $_POST['row'];
				$layout_id = $_POST['layout_id'];
				$this->controller->has_read_rights($layout_id);
				$this->controller->controller_next($row, $layout_id);
			}

			if( $type == 1 ){
				$this->controller->check_user_session();			
				$row = $_POST['row'];
				$layout_id = $_POST['layout_id'];
				$this->controller->has_read_rights($layout_id);
				$this->controller->controller_previous($row, $layout_id);
			}
	
			if( $type == 2 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$xml_browse = $_POST['xml_browse'];
				$xml_insert = $_POST['xml_insert'];
				$layout_id = $_POST['layout_id'];
				$row = $_POST['row'];
				$this->controller->controller_refresh($layout_id, $xml_browse, $xml_insert, $row);
			}
	

			if( $type == 3 ){
				$this->controller->check_user_session();
				$row = $_POST['row'];
				$layout_id = $_POST['layout_id'];
				$this->controller->has_read_rights($layout_id);
				$this->controller->controller_change_layout($row, $layout_id);
			}

			if( $type == 4 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$layout_name = $_POST['layout_name'];
				$layout_sql = $_POST['layout_sqlstatement'];
				$this->controller->controller_create_layout($layout_name, $layout_sql);
			}

			if( $type == 5 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$table_name = $_POST['table_name'];
				$table_columns = $_POST['table_columns'];
				$columns = explode(",", $table_columns);
				$this->controller->controller_create_table($table_name, $columns);
			}
			
			if( $type == 6 ){
				$this->controller->check_user_session();
				$data_keys=$_POST['data_keys'];
				$data_data=$_POST['data_data'];
				$data_lengths=$_POST['data_lengths'];
				$table=$_POST['table'];
				$row=$_POST['row'];
				$layout_id=$_POST['layout_id'];
				$this->controller->has_write_rights($layout_id);
				$this->controller->controller_insert_data($data_keys, $data_data, $data_lengths, $table, $row, $layout_id);
			}

			if( $type == 7 ){
				$this->controller->check_user_session();
				$table=$_POST['table'];
				$row=$_POST['row'];
				$layout_id=$_POST['layout_id'];
				$this->controller->has_write_rights($layout_id);
				$this->controller->controller_delete_data($table, $row, $layout_id);
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
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$this->controller->controller_get_table_list();
			}

			if( $type == 11 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$table_name=$_POST['table'];
				$this->controller->controller_get_column_list($table_name);
			}

			if( $type == 12 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$table_name=$_POST['table'];
				$column_name=$_POST['column_name'];
				$column_type=$_POST['column_type'];
				$this->controller->controller_add_column($table_name, $column_name, $column_type);
			}

			if( $type == 13 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$table_name=$_POST['table'];
				$new_table_name=$_POST['new_table_name'];
				$this->controller->controller_change_table_name($table_name, $new_table_name);
			}

			if( $type == 14 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$table_name=$_POST['table'];
				$column_name=$_POST['column_name'];
				$new_column_name=$_POST['new_column_name'];
				$new_column_type=$_POST['new_column_type'];
				$this->controller->controller_change_column_name($table_name, $column_name, $new_column_name, $new_column_type);
			}

			if( $type == 15 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$table_name=$_POST['table'];
				$column_name=$_POST['column_name'];
				$this->controller->controller_destroy_column($table_name, $column_name);
			}

			if( $type == 16 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$table_name=$_POST['table'];
				$this->controller->controller_destroy_table($table_name);
			}

			if( $type == 17 ){
				$this->controller->check_user_session();
				$this->controller->controller_get_layout_list();
			}

			if( $type == 18 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$layout_name=$_POST['layout'];
				$new_layout_name=$_POST['new_layout_name'];
				$this->controller->controller_change_layout_name($layout_name, $new_layout_name);
			}

			if( $type == 19 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$layout_name=$_POST['layout'];
				$sql=$_POST['sql'];
				$this->controller->controller_change_layout_sqlstatement($layout_name, $sql);
			}

			if( $type == 20 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$layout_name=$_POST['layout'];
				$this->controller->controller_destroy_layout($layout_name);
			}
			
			if( $type == 21 ){
				$this->controller->check_user_session();
				$data_keys=$_POST['data_keys'];
				$data_data=$_POST['data_data'];
				$data_lengths=$_POST['data_lengths'];
				$identifier=$_POST['identifier'];
				$layout_id=$_POST['layout_id'];
				$this->controller->controller_search($data_keys, $data_data, $data_lengths, $identifier, $layout_id);
			}
		
			if( $type == 22 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$username=$_POST['username'];
				$password1=$_POST['password1'];
				$password2=$_POST['password2'];
				$this->controller->controller_add_user($username, $password1, $password2);
			}

			if( $type == 23 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$this->controller->controller_get_user_list();
			}

			if( $type == 24 ){
				$this->controller->check_user_session();
				$user_id=$_POST['user_id'];
				$this->controller->controller_get_layout_list_permission($user_id);
			}
			
			if( $type == 25 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$layout_id=$_POST['layout_id'];
				$user_id=$_POST['user_id'];
				$this->controller->controller_read_rights($layout_id, $user_id);
			}

			if( $type == 26 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$layout_id=$_POST['layout_id'];
				$user_id=$_POST['user_id'];
				$this->controller->controller_write_rights($layout_id, $user_id);
			}

			if( $type == 27 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$layout_id=$_POST['layout_id'];
				$user_id=$_POST['user_id'];
				$this->controller->controller_notvisible_rights($layout_id, $user_id);
			}

			if( $type == 28 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$user_id=$_POST['user_id'];
				$this->controller->controller_make_admin($user_id);
			}

			if( $type == 29 ){
				$this->controller->check_user_session();
				$this->controller->is_admin();
				$user_id=$_POST['user_id'];
				$this->controller->controller_destroy_user($user_id);
			}




	}

}

$handle=new Handle_post;
$handle->post_handle_init();
$handle->post_handle_post();


?>
