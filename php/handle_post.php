<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once("project_action.php");
require_once("layout.php");

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
	}

}

$handle=new Handle_post;
$handle->post_handle_post();

?>
