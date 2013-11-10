<?php

require_once("db.php");

class Project{

	private $tables;
	private $layouts;
	private $db;

	function project_create_layout_workbench($i){
		$layout=(object)$this->layouts[$i];
		$layout_name=str_replace(" ", "", $layout->name);
		$layout_browse_xml=$layout->xml_browse;
		$layout_insert_xml=$layout->xml_insert;
		echo '<div class="tab-pane" id="'.$layout_name.'">
				<div class="tyokalut">
				<div class="btn-group" style="">
				  	<button type="button" class="btn btn-default" onclick="previous();"><span class="glyphicon glyphicon-circle-arrow-left"></span></button>
					<button type="button" class="btn btn-default" onclick="next();"><span class="glyphicon glyphicon-circle-arrow-right"></span></button>
					<button type="button" class="btn btn-default" onclick="avaa_valilehti(0,'.$i.')"><span class="glyphicon glyphicon-edit"></span> Arkisto XML</button>
					<button type="button" class="btn btn-default" onclick="avaa_valilehti(1,'.$i.')"><span class="glyphicon glyphicon-edit"></span> Ylläpito XML</button>
					<button type="button" class="btn btn-default" onclick="avaa_valilehti(2,'.$i.')"><span class="glyphicon glyphicon-book"></span> Ylläpito</button>
					<button type="button" class="btn btn-default" onclick="avaa_valilehti(3,'.$i.')"><span class="glyphicon glyphicon-book"></span> Arkisto</button>
					<button type="button" class="btn btn-default" onclick="refresh();"><span class="glyphicon glyphicon-refresh"></span></button>
				</div>
				</div>

				<div id="arkisto-xml'.$i.'" class="arkisto-xml">
					<textarea class="xml-area" id="xml-area-arkisto'.$i.'">'.$layout_browse_xml.'</textarea>
				</div>

				<div id="yllapito-xml'.$i.'" class="yllapito-xml">
					<textarea class="xml-area" id="xml-area-yllapito'.$i.'">'.$layout_insert_xml.'</textarea>
				</div>

				<div id="yllapito'.$i.'" class="yllapito">
				</div>

				<div id="arkisto'.$i.'" class="arkisto">
				</div>
			</div>';
	}

	function project_create_layout($i){
		$layout=(object)$this->layouts[$i];
		$layout_name=str_replace(" ", "", $layout->name);
		echo '<li><a onclick="set_current_layout('.$i.')" href="#'.$layout_name.'" data-toggle="tab">'.$layout_name.'</a></li>';
	}

	function project_create_layouts(){
		for($i=1; $i<count($this->layouts); $i++){
			$this->project_create_layout($i);
		}
	}

	function project_create_layout_workbenchs(){
		for($i=1; $i<count($this->layouts); $i++){
			$this->project_create_layout_workbench($i);
		}
		echo '<script>layout_count='.(count($this->layouts)-1).';</script>';
	}

	function project_create_project(){
		$db=new Database;
		$db->db_set_name("tietokanta");
		$db->db_create_connection("sovellus", "ietokantawi0Bieyo");
		$this->layouts = $db->db_get_layouts();
	}

}


?>
