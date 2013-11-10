<?php

class Row{

	public $row;

	function row_set_row($row){
		$this->row=$row;
	}

	function row_get_row($row){
		return $this->$row;
	}

}

?>

