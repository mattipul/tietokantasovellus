<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

class Xml{

	public $xml_parsed_browse;

	function xml_parse_browse($xmlstr){
		$this->xml_parsed_browse=new SimpleXMLElement($xmlstr);
	}

	function xml_validate(){

	}

	function xml_text_string($text){
		$ret="";
		$string=$text->string[0];
		$data=$text->data[0];		
		$laskuri=1;

		while($string!=NULL || $data!=NULL){
			if($string!=NULL){
				$ret=$ret."<span>".$string."</span>";
			}
			if($data!=NULL){
				$ret=$ret."<span class='".$data."'></span>";
			}
			$string=$text->string[$laskuri];
			$data=$text->data[$laskuri];
			$laskuri++;
		}

		return $ret;
	}

	function xml_create_text_element_browse($div){
		$html="";		

		foreach ($div->text as $text) {
			$text_style = $text['style'];
			$html=$html."".$this->xml_text_string($text)."";			
		}

		return $html;
	}

	function xml_create_image_element_browse($div){
		$html="";		

		foreach ($div->image as $image) {
			$image_style = $image['style'];
			if( strlen( $image['column']) == 0 ){
				$html=$html."<img style='".$image_style."' src='".$image."'/>";		
			}else{
				$html=$html."<img class='".$image['column']."' style='".$image_style."' src=''/>";	
			}	
		}

		return $html;
	}

	function xml_element($element){
		$browse_html="";
		$browse_html="<div>";
   		$browse_html = $browse_html."".$this->xml_create_text_element_browse($element);
		$browse_html = $browse_html."".$this->xml_create_image_element_browse($element);
		$browse_html = $browse_html."".$this->xml_create_table_element_browse($element);
		$browse_html = $browse_html."</div>";
		return $browse_html;	
	}

	function xml_table_elements($table, $col, $row){
		$html="";		
		$laskuri=0;
		for($i=0; $i<$row; $i++){
			$html=$html."<tr>";
			for($j=0; $j<$col; $j++){
				$html=$html."<td><span>".$this->xml_element($table->element[$laskuri])."</span></td>";
				$laskuri++;
			}
			$html=$html."</tr>";
		}	

		return $html;
	}

	function xml_create_table_element_browse($div){
		$html="";		

		foreach ($div->table as $table) {
			$table_style = $table['style'];
			$html=$html."<div style='display:table '><table border='1' style='".$table_style."'>";
			$html=$html.$this->xml_table_elements($table, $table['col'], $table['row']);		
			$html=$html."</table></div>";			
		}

		return $html;
	}


	function xml_create_div_element_browse(){
		$browse_html="";
		foreach ($this->xml_parsed_browse->visuals->section as $div) {
			$div_style = $div['style'];

			$browse_html="<div style='".$div_style."'";
   			$browse_html = $browse_html."".$this->xml_create_text_element_browse($div);
			$browse_html = $browse_html."".$this->xml_create_image_element_browse($div);
			$browse_html = $browse_html."".$this->xml_create_table_element_browse($div);
			$browse_html = $browse_html."</div>";
		}	
		return $browse_html;	
	}

	function print_divs_browse(){
		$browse_html = $this->xml_create_div_element_browse();
		return $browse_html;
	}

}

?>
