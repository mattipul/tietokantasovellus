<?php
ini_set('display_errors',1); 
error_reporting(E_ALL);
require_once('db.php');
require_once('xml.php');
require_once("table.php");
require_once('layout.php');
require_once("row.php");

class Project_action{

	public $db;
	public $xml;

	function init(){
		$this->db=new Database;
		$this->db->db_set_name("tietokanta");
		$this->db->db_create_connection("sovellus", "ietokantawi0Bieyo");

		$this->xml=new Xml;
	}
	
	function print_layouts_json_string($id){

	}

	function print_tables_json_string(){

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

	function update_layout($layout){
		$this->db->db_set_layout($layout);
	}

	function get_row($layout, $row){
		return $this->db->db_get_row($row, $layout->sqlstatement);
	}

	function get_layout($layout_id){
		return $this->db->db_get_layout($layout_id);
	}

	function create_layout($layout){
		return $this->db->db_create_layout($layout);
	}

	function create_table($table){
		return $this->db->db_create_table($table);
	}
	
	function set_row($row,$table_str){
		$table=new Table;
		$table->table_name=$table_str;
		$row_count = $this->db->db_count_rows($table);
		//echo $row_count." ".$row->count;
		if($row_count<$row->count)
		{
			$this->db->db_insert_to_database($table, $row);
		}
		else
		{
			$this->db->db_update_row($table, $row);
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
}


?>
