<?php

class Table{

	public $table_id;
	public $table_name;
	public $table_columns;

	function table_set_id($id){
		$this->table_id=$id;
	}

	function table_get_id(){
		return $this->table_id;
	}

	function table_set_name($name){
		$this->table_name=$name;
	}

	function table_get_name(){
		return $this->table_name;
	}

	function table_get_columns(){
		return explode(";", $this->table_columns);
	}

	function table_get_columns_arr(){
		return $this->table_columns;
	}

	function table_set_columns($columns){
		$this->table_columns=$columns;
	}

	function table_add_column($column, $type){
		$this->table_columns=$this->table_columns.",".$column.":".$type;
	}

	function table_create_table($table_id, $table_name, $table_columns){
		$this->table_set_id($table_id);
		$this->table_set_name($table_name);
		$this->table_set_columns($table_columns);
	}

}

?>
