<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once("db.php");
require_once("settings_db.php");


class Xml{

	private $db;
	public $xml_parsed_browse;
	public $xml_parsed_insert;


	function init_db(){
		global $settings_db_name;
		global $settings_db_user;
		global $settings_db_password;
		$this->db=new Database;
		$this->db->db_set_name($settings_db_name);
		$this->db->db_create_connection($settings_db_user, $settings_db_password);
	}

	function xml_parse_browse($xmlstr){
		$this->xml_parsed_browse=new SimpleXMLElement($xmlstr);
	}

	function xml_parse_insert($xmlstr){
		$this->xml_parsed_insert=new SimpleXMLElement($xmlstr);
	}


	function xml_validate(){

	}

///ARKISTO///

	function xml_text_string($text, $text_style){
		$ret="";
		$string=$text->string[0];
		$data=$text->data[0];		
		$laskuri=1;
		$ret=$ret."<div style='".$text_style."'>";
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
		$ret=$ret."</div>";
		return $ret;
	}

	function xml_create_text_element_browse($div){
		$html="";		

		foreach ($div->text as $text) {
			$text_style = $text['style'];
			$html=$html."".$this->xml_text_string($text, $text_style)."";			
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

			$browse_html=$browse_html."<div style='".$div_style."'>";
   			$browse_html = $browse_html."".$this->xml_create_text_element_browse($div);
			$browse_html = $browse_html."".$this->xml_create_image_element_browse($div);
			$browse_html = $browse_html."".$this->xml_create_table_element_browse($div);
			$browse_html = $browse_html."</div>";
		}	
		return $browse_html;	
	}

	//Luodaan arkiston HTML-merkkijono parsitusta XML-datasta
	function print_divs_browse(){
		$browse_html = $this->xml_create_div_element_browse();
		return $browse_html;
	}


///////////
///////////
///////////

	function xml_create_area_element($div, $i){
		$html="";		

		foreach ($div->area as $area) {
			$area_style = $area['style'];
			$html=$html."<div><span>".$area['caption']."</span><textarea data-insid='".$i."' class='insert_entry_".$area['column']."' type='text' style='".$area_style."'></textarea></div>";	
			
		}

		return $html;
	}

	function xml_create_entry_element($div, $i){
		$html="";		

		foreach ($div->entry as $entry) {
			$entry_style = $entry['style'];
			$html=$html."<div><span>".$entry['caption']."</span><input data-insid='".$i."' class='insert_entry_".$entry['column']."' type='text' style='".$entry_style."'/></div>";	
			
		}

		return $html;
	}

	function xml_create_ok_element($div, $i,$to){
		$html="";		

		foreach ($div->ok as $ok) {
			$ok_style = $ok['style'];
			$html=$html."<input data-table='".$to."' onclick='insert_data_to_database(this,".$i.")' data-insid='".$i."' class='insert_button' value='".$ok."' type='button' style='".$ok_style."'/>";	
			
		}

		return $html;
	}

	function xml_create_delete_element($div, $i,$to){
		$html="";		

		foreach ($div->delete as $delete) {
			$delete_style = $delete['style'];
			$html=$html."<input data-table='".$to."' onclick='delete_data_from_database(this,".$i.")' data-insid='".$i."' class='insert_button' value='".$delete."' type='button' style='".$delete_style."'/>";	
			
		}

		return $html;
	}


	function xml_create_div_element_insert($table, $i){
		$insert_html="";
		foreach ($table->section as $div) {
			$div_style = $div['style'];

			$insert_html=$insert_html."<div style='".$div_style."'>";
   			$insert_html = $insert_html."".$this->xml_create_text_element_browse($div);
			$insert_html = $insert_html."".$this->xml_create_image_element_browse($div);
			$insert_html = $insert_html."".$this->xml_create_table_element_browse($div);
			$insert_html = $insert_html."".$this->xml_create_entry_element($div, $i);
			$insert_html = $insert_html."".$this->xml_create_area_element($div, $i);
			$insert_html = $insert_html."".$this->xml_create_ok_element($div, $i, $table['name']);
			$insert_html = $insert_html."".$this->xml_create_delete_element($div, $i, $table['name']);
			$insert_html = $insert_html."</div>";
		}	
		return $insert_html;	
	}
	
	function xml_create_searchentry($div, $id, $to){
		$html="";		

		foreach ($div->searchentry as $search_entry) {
			$search_entry_style = $search_entry['style'];
			$html=$html."<input data-sqlstatement='".$to."' data-searchidentifier='".$id."' class='search_entry_".$search_entry['column']."' type='text' style='".$search_entry_style."'/>";	
			
		}

		return $html;
	}

	function xml_create_searchok($div, $id, $to){
		$html="";		

		foreach ($div->searchok as $search_ok) {
			$search_ok_style = $search_ok['style'];
			$html=$html."<input onclick='search(this);' data-searchidentifier='".$id."' class='insert_button' value='".$search_ok."' type='button' style='".$search_ok_style."'/>";	
			
		}

		return $html;
	}
	
	function xml_create_search_section($table, $i){
		$insert_html="";
		foreach ($table->searchsection as $search_section) {
			$search_section_style = $search_section['style'];
			$search_section_id = $search_section['identifier'];
			
			$insert_html="<div data-searchidentifier='".$search_section_id."' class='search_section_div' style='".$search_section_style."'>";
   			$insert_html = $insert_html."".$this->xml_create_searchentry($search_section,$search_section_id, $search_section['sqlstatement']);
			$insert_html = $insert_html."".$this->xml_create_searchok($search_section,$search_section_id, $search_section['sqlstatement']);
			$insert_html = $insert_html."".$this->xml_create_text_element_browse($search_section);
			$insert_html = $insert_html."</div>";
		}	
		return $insert_html;		
	}
	
	function xml_create_search_results_result($search_results, $id){
		return "<div style='display:table' class='search_results_div' data-searchresults='on' data-searchidentifier='".$id."' style='".$search_results['style']."'></div>";
	}
	
	function xml_create_search_results($table, $i){
		$insert_html="";
		foreach ($table->searchresult as $search_results) {
			$search_results_style = $search_results['style'];
			$search_results_id = $search_results['identifier'];
			
			$insert_html="<div style='".$search_results_style."'>";
   			$insert_html = $insert_html."".$this->xml_create_search_results_result($search_results, $search_results_id);
			$insert_html = $insert_html."</div>";
		}	
		return $insert_html;		
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

	function xml_create_tables($row){
		$arr=array();
		$i=0;
		foreach ($this->xml_parsed_insert->table as $table) {
			$insert_html="<div class='insert_layout' id='insert_layout_".$i."'>";
			$insert_html=$insert_html.$this->xml_create_div_element_insert($table, $i);
			$insert_html=$insert_html.$this->xml_create_search_section($table, $i);
			$insert_html=$insert_html.$this->xml_create_search_results($table, $i);
			$insert_html=$insert_html."</div>";
			$arr[]=$insert_html;
			$arr[]=$table['name'];
			if( strlen($table['name']) >0 ){
				$to="SELECT * FROM ".$table['to'];
				$arr[]=$this->print_row_arr($this->db->db_get_row($row, $to));
			}
			else{
				$arr[]="";
			}
			$i++;
		}
		return $arr;
	}

	function xml_get_sqlstatement_from_identifier($identifier){
		$sqlstatement="";
		foreach ($this->xml_parsed_insert->table as $table) {
			foreach ($table->searchsection as $section) {
				if( strcmp($section['identifier'], $identifier) == 0 ){
					$sqlstatement=$section['sqlstatement'];				
				}	
			}
		}
		return $sqlstatement;
	}

	function xml_get_changes_to_column_names($identifier){
		$changes=array();
		foreach ($this->xml_parsed_insert->table as $table) {
			foreach ($table->searchresult as $section) {
				if( strcmp($section['identifier'], $identifier) == 0 ){
					foreach ($section->change as $change) {
						$changes[ "".$change['from'] ] = $change['to'];
					}				
				}	
			}
		}
		return $changes;
	}


	function xml_get_table_from_name($name){
		$name_str="";
		foreach ($this->xml_parsed_insert->table as $table) {
			if( strcmp($table['name'], $name) == 0 ){
				$name_str=$table['to'];				
			}	
		
		}
		return $name_str;
	}

	//Luodaan ylläpidon HTML-merkkijono parsitusta XML-datasta
	function print_divs_insert($row){
		$arr = $this->xml_create_tables($row);
		return $arr;
	}

}

?>
