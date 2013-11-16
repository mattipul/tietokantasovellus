<?php

class Row{

	public $row_keys;
	public $row_data;
	public $count;

	function set_keys($keys){
		$this->row_keys=$keys;
	}

	function set_data($data){
		$this->row_data=$data;
	}

}

?>

