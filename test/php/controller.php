<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);
require_once("login.php");
require_once('db.php');
require_once('xml.php');
require_once("table.php");
require_once('layout.php');
require_once("row.php");
require_once("column.php");
require_once("search_results.php");

class Controller{

	public $db;
	public $xml;

	function init(){
		$this->db=new Database;
		$this->db->db_set_name("tietokanta");
		$this->db->db_create_connection("sovellus", "ietokantawi0Bieyo");

		$this->xml=new Xml;
	}
	
	function controller_check_layout_name($layout_name){
		$layoutObj=new Layout;
		$layoutObj->name=$layout_name;
		$c=$this->db->db_check_same_name_layout($layoutObj);
		if($c!=0){
			die();
		}
	}
	
	function controller_check_table_name($table_name){
		$tableObj=new Table;
		$tableObj->table_name=$table_name;
		$c=$this->db->db_check_same_name_table($tableObj);
		if($c!=0){
			die();
		}
	}
	
	function controller_check_column_name($table_name, $column_name){
		$columnObj=new Column;
		$columnObj->table_name=$table_name;
		$columnObj->column_name=$column_name;
		$c=$this->db->db_check_same_name_column($columnObj);
		if($c!=0){
			die();
		}
	}
	
	function print_layout_html_arr( $layout_id, $row){
		$layout_ret=$this->db->db_get_layout($layout_id);	
		$layout=(object)$layout_ret;
	
		$this->xml->xml_parse_browse($layout->xml_browse);
		$this->xml->init_db();
		$this->xml->xml_parse_insert($layout->xml_insert);
		$html = array('browse_html' => $this->xml->print_divs_browse(), 'insert_html' => $this->xml->print_divs_insert($row));
		return $html;
	}

	function print_xml_arr($layout_id){
		$layout=$this->db->db_get_layout($layout_id);
		return $layout;
	}
	
	function print_row_arr($row){
		$keys=$row->row_keys;
		$data=$row->row_data;
		$rowRet=array();
		for($i=0; $i<count($keys); $i++){
			$rowRet[$keys[$i]]=$data[$i];
		}
		return $rowRet;
	}
	
	function controller_next($row, $layout_id){
	
		if( $row != NULL && $layout_id != NULL ){
			if( $row >= 1 && $layout_id >= 1 ){
				$layout=$this->db->db_get_layout($layout_id);
				if( $layout != NULL && count($layout) == 1 ){
					$layoutObj=new Layout;
					$layoutObj->id=$layout_id;
					$layout=$this->db->db_get_layout($layout_id);
					$json_return[]=$this->print_layout_html_arr($layout_id, $row);
					$json_return[]=$this->print_xml_arr($layout_id);
					$rowObj=$this->db->db_get_row($row, $layout->sqlstatement);
					$json_return[]=$this->print_row_arr($rowObj);
					echo json_encode($json_return);
				}else{
					echo '0';
				}
			}else{
				echo '0';
			}
		}else{
			echo '0';
		}
		
	}
	
	function controller_previous($row, $layout_id){
	
		if( $row != NULL && $layout_id != NULL ){
			if( $row >= 1 && $layout_id >= 1 ){
				$layout=$this->db->db_get_layout($layout_id);
				if( $layout != NULL && count($layout) == 1 ){
					$layoutObj=new Layout;
					$layoutObj->id=$layout_id;
					$layout=$this->db->db_get_layout($layout_id);
					$json_return[]=$this->print_layout_html_arr($layout_id, $row);
					$json_return[]=$this->print_xml_arr($layout_id);
					$rowObj=$this->db->db_get_row($row, $layout->sqlstatement);
					$json_return[]=$this->print_row_arr($rowObj);
					echo json_encode($json_return);
				}else{
					echo '0';
				}
			}else{
				echo '0';
			}
		}else{
			echo '0';
		}
	
	}
	
	function controller_refresh($layout_id, $layout_name, $layout_sqlstatement, $xml_browse, $xml_insert, $row){
	
		if( $layout_id!=NULL && $layout_name!=NULL && $layout_sqlstatement!=NULL && $xml_browse!=NULL && $xml_insert!=NULL && $row!=NULL ){
			if( $layout_id >= 1 && $row >= 1 ){
				$layout=new Layout;
				$layout->id=$layout_id;
				$layout->name=$layout_name;
				$layout->sqlstatement=$layout_sqlstatement;
				$layout->xml_browse=$xml_browse;
				$layout->xml_insert=$xml_insert;
				
				$this->db->db_set_layout($layout);
				$json_return[]=$this->print_layout_html_arr($layout_id, $row);
				$json_return[]=$this->print_xml_arr($layout_id);
				$rowObj=$this->db->db_get_row($row, $layout->sqlstatement);
				$json_return[]=$this->print_row_arr($rowObj);
				echo json_encode($json_return);
			}
		}else{
			echo $layout_id.",". $layout_name.",". $layout_sqlstatement.",". $xml_browse.",". $xml_insert.",". $row;
		}
	}
	
	function controller_change_layout($row, $layout_id){
		if( $row!=NULL && $layout_id!=NULL ){
			$layoutObj=new Layout;
			$layoutObj->id=$layout_id;
			$layout=$this->db->db_get_layout($layout_id);
			$json_return[]=$this->print_layout_html_arr($layout_id, $row);
			$json_return[]=$this->print_xml_arr($layout_id);
			$rowObj=$this->db->db_get_row($row, $layout->sqlstatement);
			$json_return[]=$this->print_row_arr($rowObj);
			echo json_encode($json_return);
		}
	}
	
	function controller_create_layout($layout_name, $layout_sql){
		if( $layout_name!=NULL && $layout_sql!=NULL ){
			$this->controller_check_layout_name($layout_name);
			$layout=new Layout;
			$layout->name=$layout_name;
			$layout->sqlstatement=$layout_sql;
			$this->db->db_create_layout($layout);
		}
	}
	
	function controller_create_table($table_name, $columns){
		if( $table_name!=NULL && $columns!=NULL ){
			$this->controller_check_table_name($table_name);
			$table = new Table;
			$table->table_name=$table_name;
			$table->table_columns=$columns;
			$this->db->db_create_table($table);	
		}
	}
	
	function set_row($row,$table_str){
		$table=new Table;
		$table->table_name=$table_str;
		$row_count = $this->db->db_count_rows($table);
		if($row_count<$row->count)
		{
			$this->db->db_insert_to_database($table, $row);
		}
		else
		{
			$this->db->db_update_row($table, $row);
		}
	}
	
	function controller_insert_data($data_keys, $data_data, $data_lengths, $table, $row_c){
		if( $data_keys!=NULL && $data_data!=NULL && $data_lengths!=NULL && $table!=NULL && $row_c!=NULL ){
			$data_keys_arr=explode(",", $data_keys);
			$data_lengths_arr=explode(",", $data_lengths);
			$data_data_arr;
		
			$c=0;
			for($i=0; $i<count($data_lengths_arr); $i++){
				$data_data_arr[]=substr($data_data, $c, $data_lengths_arr[$i]);
				echo substr($data_data, $c, $data_lengths_arr[$i]) ." ".$c."|";
				$c+=$data_lengths_arr[$i]+1;
			}

			$row=new Row;
			$row->row_keys=$data_keys_arr;
			$row->row_data=$data_data_arr;	
			$row->count=$row_c;

			$this->set_row($row,$table);
		}
	}
	
	function delete_row($row,$table_str){
		$table=new Table;
		$table->table_name=$table_str;
		$row_count = $this->db->db_count_rows($table);
		if($row_count>=$row->count)
		{
			$this->db->db_delete_row($table, $row);
		}
		
	}
	
	function controller_delete_data($table, $row){
		if($table!=NULL && $row!=NULL){
			$rowObj=new Row;
			$rowObj->count=$row;
			$this->delete_row($rowObj, $table);
		}
	}
	
	function controller_authorize($user, $pass){
		if($user!=NULL && $pass!=NULL){
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
	}
	
	function controller_logout(){
		$login=new Login;
		$login->login_session_destroy();
	}
	
	function controller_get_table_list(){
		$table_list=$this->db->db_get_tables();
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
	
	function controller_get_column_list($table_name){
		$tableObj=new Table;
		$tableObj->table_name=$table_name;
		$column_list=$this->db->db_get_table_columns_by_name($tableObj->table_name);

		$ret_str;
		$c=0;
		for($i=0; $i<count($column_list); $i++){
			if($table_list[$i]!=NULL){
				$ret_str[$c]['column_name']=$column_list[$i]->column_name;
				$ret_str[$c]['column_type']=$column_list[$i]->column_type;
				$c++;
			}
		}
		echo json_encode($ret_str);
	}
	
	function controller_add_column($table_name, $column_name, $column_type){
		if( $table_name!=NULL && $column_name!=NULL && $column_type!=NULL ){
			$this->controller_check_column_name($table_name, $column_name);
			$columnObj=new Column;
			$columnObj->table_name=$table_name;
			$columnObj->column_name=$column_name;
			$columnObj->column_type=$column_type;
			$this->db->db_add_column($columnObj);
		}
	}
	
	function controller_change_table_name($table_name, $new_table_name){
		if( $table_name!=NULL && $new_table_name!=NULL ){
			$this->controller_check_table_name($new_table_name);
			$tableObj=new Table;
			$tableObj->table_name=$table_name;
			$this->db->db_change_table_name($tableObj, $new_table_name);
		}
	}
	
	function controller_change_column_name($table_name, $column_name, $new_column_name, $new_column_type){
		if( $table_name!=NULL && $column_name!=NULL && $new_column_name!=NULL && $new_column_type!=NULL ){
			$this->controller_check_column_name($table_name, $new_column_name);
			$column_old=new Column;
			$column_new=new Column;

			$column_old->column_name=$column_name;
			$column_old->table_name=$table_name;
		
			$column_new->column_name=$new_column_name;
			$column_new->table_name=$table_name;
			$column_new->column_type=$new_column_type;				

			$this->db->db_change_column_name($column_old, $column_new);
		}	
	}
	
	function controller_destroy_column($table_name, $column_name){
		if( $table_name!=NULL && $column_name!=NULL ){
			$columnObj=new Column;
			$columnObj->table_name=$table_name;
			$columnObj->column_name=$column_name;
			$this->db->db_destroy_column($columnObj);
		}
	}
	
	function controller_destroy_table($table_name){
		if( $table_name!=NULL ){
			$tableObj=new Table;
			$tableObj->table_name=$table_name;
			$this->db->db_destroy_table($tableObj);
		}
	}

	
	function controller_get_layout_list(){
		$layout_list=$this->db->db_get_layouts();
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
	
	function controller_change_layout_name($layout_name, $new_layout_name){
		if( $layout_name!=NULL && $new_layout_name!=NULL ){
			$this->controller_check_layout_name($new_layout_name);
			$layout_old=new Layout;
			$layout_new=new Layout;

			$layout_old->name=$layout_name;
			$layout_new->name=$new_layout_name;

			$this->db->db_change_layout_name($layout_old, $layout_new);
		}
	}
	
	function controller_change_layout_sqlstatement($layout_name, $sql){
		if( $layout_name!=NULL && $sql!=NULL ){
			$layout_old=new Layout;
			$layout_new=new Layout;

			$layout_old->name=$layout_name;
			$layout_new->sqlstatement=$sql;

			$this->db->db_change_layout_sql($layout_old, $layout_new);	
		}
	}
	
	function controller_destroy_layout($layout_name){
		if( $layout_name!=NULL ){
			$layoutObj=new Layout;
			$layoutObj->name=$layout_name;
			$this->db->db_destroy_layout($layoutObj);
		}
	
	}
	
	function controller_search($data_keys, $data_data, $data_lengths, $sql){
		if( $data_keys!=NULL && $data_data!=NULL && $data_lengths!=NULL && $sql!=NULL ){
			$data_keys_arr=explode(",", $data_keys);
			$data_lengths_arr=explode(",", $data_lengths);
			$data_data_arr;
	
			$c=0;
			for($i=0; $i<count($data_lengths_arr); $i++){
				$data_data_arr[]=substr($data_data, $c, $data_lengths_arr[$i]);
				$c=$data_lengths_arr[$i]+1;
			}
		
			$ret_data[0]=$data_keys_arr;
			$ret_data[1]=$data_data_arr;
			$search_results=$this->db->db_search($ret_data, $sql);
			echo json_encode($search_results->resultsArr);
		}	
	}

}

?>
