<?php

class Layout{

	public $id;
	public $name;
	public $sqlstatement;
	public $xml_browse;
	public $xml_insert;
	public $row;

	function layout_set_id($id){
		$this->id=$id;
	}

	function layout_get_id(){
		return $this->id;
	}
	function layout_set_name($name){
		$this->name=$name;
	}

	function layout_set_sqlstatement($sqlstatement){
		$this->sqlstatement=$sqlstatement;
	}

	function layout_set_xml_browse($xml_browse){
		$this->xml_browse=$xml_browse;
	}

	function layout_set_xml_insert($xml_insert){
		$this->xml_insert=$xml_insert;
	}

	function layout_get_name(){
		return $this->name;
	}

	function layout_get_sqlstatement(){
		return $this->sqlstatement;
	}

	function layout_get_xml_browse(){
		return $this->xml_browse;
	}

	function layout_get_xml_insert(){	
		return $this->xml_insert;
	}

	function layout_next_row(){
		$this->row++;
	}

	function layout_previous_row(){
		if($this->row>0){
			$this->row--;
		}
	}

	function layout_create_layout($layout_id, $layout_name, $sqlstatement, $layout_browse_xml, $layout_insert_xml){
		$this->layout_set_id($layout_id);
		$this->layout_set_name($layout_name);
		$this->layout_set_sqlstatement($sqlstatement);	
		$this->layout_set_xml_browse($layout_browse_xml);
		$this->layout_set_xml_insert($layout_insert_xml);
		//HAE SQLSTATEMENT XML:STÄ
	}

}

?>
