<?php

require_once("db.php");
require_once("user.php");
require_once("controller.php");

class Project{

	private $tables;
	private $layouts;
	private $db;
	private $controller;

	function project_create_layout_workbench($i){
		$layout=$this->layouts[$i];
		$layout_name=str_replace(" ", "", $layout['layout_name']);
		$layout_browse_xml=$layout['xml_browse'];
		$layout_insert_xml=$layout['xml_insert'];
		$layout_id=$layout['layout_id'];
		echo '<div class="tab-pane" id="'.$layout_name.'">
				<div class="tyokalut">
				<div class="btn-group" style="">
				  	<button type="button" class="btn btn-default" onclick="previous();"><span class="glyphicon glyphicon-circle-arrow-left"></span></button>
					<button type="button" class="btn btn-default" onclick="next();"><span class="glyphicon glyphicon-circle-arrow-right"></span></button>
					<button type="button" class="btn btn-default" onclick="avaa_valilehti(0,'.$layout_id.')"><span class="glyphicon glyphicon-edit"></span> Arkisto XML</button>
					<button type="button" class="btn btn-default" onclick="avaa_valilehti(1,'.$layout_id.')"><span class="glyphicon glyphicon-edit"></span> Ylläpito XML</button>
					<button type="button" class="btn btn-default" onclick="avaa_valilehti(2,'.$layout_id.')"><span class="glyphicon glyphicon-book"></span> Ylläpito</button>
					<button type="button" class="btn btn-default" onclick="avaa_valilehti(3,'.$layout_id.')"><span class="glyphicon glyphicon-book"></span> Arkisto</button>
					<button type="button" class="btn btn-default" onclick="refresh();"><span class="glyphicon glyphicon-refresh"></span></button>
				</div>
				</div>

				<div id="arkisto-xml'.$layout_id.'" class="arkisto-xml">
					<textarea class="xml-area" id="xml-area-arkisto'.$layout_id.'">'.$layout_browse_xml.'</textarea>
				</div>

				<div id="yllapito-xml'.$layout_id.'" class="yllapito-xml">
					<textarea class="xml-area" id="xml-area-yllapito'.$layout_id.'">'.$layout_insert_xml.'</textarea>
				</div>

				<div id="yllapito'.$layout_id.'" class="yllapito">
				</div>
				<div id="arkisto'.$layout_id.'" class="arkisto">
				</div>
			</div>';
	}


	function project_create_layout_workbench_read($i){
		$layout=$this->layouts[$i];
		$layout_name=str_replace(" ", "", $layout['layout_name']);

		$layout_id=$layout['layout_id'];
		echo '<div class="tab-pane" id="'.$layout_name.'">
				<div class="tyokalut">
				<div class="btn-group" style="">
				  	<button type="button" class="btn btn-default" onclick="previous();"><span class="glyphicon glyphicon-circle-arrow-left"></span></button>
					<button type="button" class="btn btn-default" onclick="next();"><span class="glyphicon glyphicon-circle-arrow-right"></span></button>

					<button type="button" class="btn btn-default" onclick="avaa_valilehti(3,'.$layout_id.')"><span class="glyphicon glyphicon-book"></span> Arkisto</button>
					<button type="button" class="btn btn-default" onclick="refresh();"><span class="glyphicon glyphicon-refresh"></span></button>
				</div>
				</div>
				<div id="yllapito'.$layout_id.'" class="yllapito">
				</div>
				<div id="arkisto'.$layout_id.'" class="arkisto">
				</div>
			</div>';
	}

	function project_create_layout_workbench_write($i){
		$layout=$this->layouts[$i];
		$layout_name=str_replace(" ", "", $layout['layout_name']);

		$layout_id=$layout['layout_id'];
		echo '<div class="tab-pane" id="'.$layout_name.'">
				<div class="tyokalut">
				<div class="btn-group" style="">
				  	<button type="button" class="btn btn-default" onclick="previous();"><span class="glyphicon glyphicon-circle-arrow-left"></span></button>
					<button type="button" class="btn btn-default" onclick="next();"><span class="glyphicon glyphicon-circle-arrow-right"></span></button>
					<button type="button" class="btn btn-default" onclick="avaa_valilehti(2,'.$layout_id.')"><span class="glyphicon glyphicon-book"></span> Ylläpito</button>
					<button type="button" class="btn btn-default" onclick="avaa_valilehti(3,'.$layout_id.')"><span class="glyphicon glyphicon-book"></span> Arkisto</button>
					<button type="button" class="btn btn-default" onclick="refresh();"><span class="glyphicon glyphicon-refresh"></span></button>
				</div>
				</div>

				<div id="yllapito'.$layout_id.'" class="yllapito">
				</div>
				<div id="arkisto'.$layout_id.'" class="arkisto">
				</div>
			</div>';
	}

	function project_create_layout($i){
		$layout=$this->layouts[$i];
		$layout_name=str_replace(" ", "", $layout["layout_name"]);
		echo '<li><a onclick="set_current_layout('.$layout["layout_id"].')" href="#'.$layout_name.'" data-toggle="tab">'.$layout_name.'</a></li>';
	}

	function project_create_layouts(){
		for($i=0; $i<count($this->layouts); $i++){
			if( $this->layouts[$i]['admin']==1 || ($this->layouts[$i]['admin']==0 && $this->layouts[$i]['permission']!=-1 )){
				$this->project_create_layout($i);
			}
		}
	}

	function project_create_layout_workbenchs(){
		for($i=0; $i<count($this->layouts); $i++){
			if($this->layouts[$i]['admin']==0){
				if( $this->layouts[$i]['admin'] == NULL && $this->layouts[$i]['permission']==1 ){
					$this->project_create_layout_workbench_write($i);
				}
				if( $this->layouts[$i]['admin'] == NULL && $this->layouts[$i]['permission']==2 ){
					$this->project_create_layout_workbench_read($i);
				}

			}else{
				if($this->layouts[$i]['admin'] == 1 ){
					$this->project_create_layout_workbench($i);
				}
			}
		}
		echo '<script>layout_count='.(count($this->layouts)-1).';</script>';
	}

	function project_create_project($user_id){
		$this->controller=new Controller;
		$this->controller->init();
		$this->layouts = $this->controller->controller_get_layout_list_permission_nojson($user_id);
	}

	function project_is_admin($user_id){
		$this->controller=new Controller;
		$this->controller->init();
		return $this->controller->admin($user_id);
	}

}


?>
