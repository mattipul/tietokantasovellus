<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once("layout.php");
require_once("table.php");
require_once("row.php");
require_once("column.php");
require_once("search_results.php");

class Database{
	
	private $name;
	private $config;
	private $database;
	
	function db_set_name($name){
		$this->name=$name;
	}

	function db_create_connection($user, $password){
		if( strlen($user)>0 && strlen($user)>0 && strlen($password)>0  ){		
			$this->config = array(
	  		'dburl' => 'mysql:unix_socket=/home/mattipul/mysql/socket;dbname='.$this->name,
	  		'dbusername' => $user,
	  		'dbpassword' => $password,
			);

			$this->database = new PDO($this->config['dburl'], $this->config['dbusername'], $this->config['dbpassword']);	
			$this->database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->database->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		}
	}

	function db_escape($sqlstatement){
		return $this->database->quote($sqlstatement);
	}
	
	function db_exec_esc($sqlstatement, $hide_array){
		$mysqlquery = $this->database->prepare($sqlstatement);
		$ret=$mysqlquery->execute($hide_array);
  		if($ret == FALSE){
			die("Virhe tietokannassa!");
		}
	}

	function db_exec($sqlstatement){
		$sqlstatement_escaped=$this->db_escape($sqlstatement);
		$mysqlquery = $this->database->prepare($sqlstatement_escaped);
  		if($mysqlquery->execute() == FALSE){
			die("Virhe tietokannassa!");
		}
	}
	
	function db_select_esc($sqlstatement, $hide_array){
		$mysqlquery = $this->database->prepare($sqlstatement);
  		$ret=$mysqlquery->execute($hide_array);
		if($ret==TRUE){		
			return $mysqlquery->fetchAll();
		}else{
			die("Virhe tietokannassa!");
		}
	}

	function db_select($sqlstatement){
		$sqlstatement_escaped=$this->db_escape($sqlstatement);
		$mysqlquery = $this->database->prepare($sqlstatement_escaped);
  		$ret=$mysqlquery->execute();
		if($ret==TRUE){		
			return $mysqlquery->fetchAll();
		}else{
			die("Virhe tietokannassa!");
		}
	}

	function db_close_connection(){
		$this->config=NULL;
		$this->database=NULL;
	}

	function db_create_table($table){
		$table_name = $table->table_name;
		$table_columns = $table->table_columns;
		$this->db_exec_esc( "INSERT INTO Taulu (taulun_id, taulun_nimi) VALUES ( NULL, :table_name )", array(":table_name" => $table_name) );
		
		$ret_object = $this->db_select("SELECT taulun_id FROM Taulu ORDER BY taulun_id DESC LIMIT 1");
		$new_table_id=$ret_object[0]['taulun_id'];

		$columns_sql = "";

		$table_columns_sql_string="";
		for($i=0; $i<count($table_columns); $i++){
			$column=explode(":", $table_columns[$i]);
			$column_name=$column[0];
			$column_type=$column[1];
			$columns_sql=$columns_sql.$column[0]." ".$column[1];
			if( $i<count($table_columns)-1 ){
				$columns_sql=$columns_sql.",";
			}
			$hide_array=array(":new_table_id"=>$new_table_id, ":column_name"=>$column_name, ":column_type"=>$column_type);
			$this->db_exec_esc( "INSERT INTO Sarake (sarakkeen_id, taulun_id, sarakkeen_nimi, sarakkeen_tyyppi) VALUES ( NULL, :new_table_id, :column_name, :column_type ) ", $hide_array );
			
		}

		$columns_sql=$columns_sql.",`id` int PRIMARY KEY AUTO_INCREMENT";
		$hide_array=array(":table_name"=>$table_name, ":columns_sql"=>$columns_sql);
		return $this->db_exec_esc( "CREATE TABLE :table_name (:columns_sql);", $hide_array );
	}

	function db_create_layout($layout){
		$layout_name = $layout->name;
		$layout_sql = $layout->sqlstatement;
		$layout_xml_insert="<?xml version=\"1.0\"?>\n<managelayout>\n</managelayout>";
		$layout_xml_browse="<?xml version=\"1.0\"?>\n<layout>\n<visuals>\n</visuals>\n</layout>";
		$hide_array=array(":layout_name"=>$layout_name, ":layout_sql"=>$layout_sql, ":layout_xml_insert"=>$layout_xml_insert, ":layout_xml_browse"=>$layout_xml_browse);
		return $this->db_exec_esc("INSERT INTO Asetelma (asetelman_id, asetelman_nimi, sqllauseke, xml_yllapito, xml_arkisto) VALUES(NULL, ':layout_name', ':layout_sql', ':layout_xml_insert', ':layout_xml_browse')", $hide_array);

		
	}
	
	function db_get_table_columns($id){
		$hide_array=array(":id"=>$id);
		$ret_object = $this->db_select_esc("SELECT * FROM Sarake WHERE taulun_id=:id", $hide_array);
		$column_list=array();		

		foreach ($ret_object as $column)
    		{

			$column_name=$column['sarakkeen_nimi'];
			$column_type=$column['sarakkeen_tyyppi'];
			$column_id=$column['sarakkeen_id'];
			$column_table_id=$column['taulun_id'];

			$columnObj=new Column;
			$columnObj->column_name=$column_name;
			$columnObj->column_type=$column_type;
			$columnObj->column_id=$column_id;
			$columnObj->table_id=$column_table_id;

			array_push( $column_list, (object)$columnObj );
         	}


		return $column_list;
	}
	
	function db_get_table_columns_by_name($name){
		$hide_array=array(":name"=>$name);
		$ret_object = $this->db_select("SELECT * FROM Sarake WHERE taulun_nimi=':name'", $hide_array);
		$column_list=array();		

		foreach ($ret_object as $column)
    		{

			$column_name=$column['sarakkeen_nimi'];
			$column_type=$column['sarakkeen_tyyppi'];
			$column_id=$column['sarakkeen_id'];
			$column_table_id=$column['taulun_id'];

			$columnObj=new Column;
			$columnObj->column_name=$column_name;
			$columnObj->column_type=$column_type;
			$columnObj->column_id=$column_id;
			$columnObj->table_id=$column_table_id;

			array_push( $column_list, (object)$columnObj );
         	}


		return $column_list;
	}

	function db_get_tables(){
		$ret_object = $this->db_select("SELECT * FROM Taulu");

		$table_list[]=NULL;

		foreach ($ret_object as $tables_row)
    	{
          	$table_id = $tables_row['taulun_id'];
			$table_name = $tables_row['taulun_nimi'];
			$table_columns = $this->db_get_table_columns($table_id);

			$tableObj = new Table;
			$tableObj->table_name=$table_name;
			$tableObj->table_columns=$table_columns;
			$tableObj->table_id=$table_id;
			array_push( $table_list, $tableObj );
         	}

		return $table_list;
	}

	function db_get_layouts(){
		$ret_object = $this->db_select("SELECT * FROM Asetelma");

		$layout_list[]=NULL;

		foreach ($ret_object as $layouts_row)
    	{
          	$layout_id = $layouts_row['asetelman_id'];
			$layout_name = $layouts_row['asetelman_nimi'];
			$layout_sqlstatement=$layouts_row['sqllauseke'];
			$layout_browse_xml = $layouts_row['xml_arkisto'];
			$layout_insert_xml = $layouts_row['xml_yllapito'];
			
			$layoutObj = new Layout;
			$layoutObj->id=$layout_id;
			$layoutObj->name=$layout_name;
			$layoutObj->sqlstatement=$layout_sqlstatement;
			$layoutObj->xml_browse=$layout_browse_xml;
			$layoutObj->xml_insert=$layout_insert_xml;

			array_push( $layout_list, (object)$layoutObj );
         }

		return $layout_list;
	}

	function db_get_layout($id){
		$hide_array=array(":id"=>$id);
		$ret_object = $this->db_select_esc("SELECT * FROM Asetelma WHERE asetelman_id=:id", $hide_array);

		$layout=NULL;
		foreach ($ret_object as $layouts_row)
    		{
          		$layout_id = $layouts_row['asetelman_id'];
			$layout_name = $layouts_row['asetelman_nimi'];
			$layout_sqlstatement=$layouts_row['sqllauseke'];
			$layout_browse_xml = $layouts_row['xml_arkisto'];
			$layout_insert_xml = $layouts_row['xml_yllapito'];
			
			$layoutObj = new Layout;
			$layoutObj->id=$layout_id;
			$layoutObj->name=$layout_name;
			$layoutObj->sqlstatement=$layout_sqlstatement;
			$layoutObj->xml_browse=$layout_browse_xml;
			$layoutObj->xml_insert=$layout_insert_xml;

			$layout=(object)$layoutObj;
         	}

		return $layout;
	}

	function db_set_layout($layout){
		$layout_id=$layout->id;
		$layout_name=$layout->name;
		$layout_xml_browse=$layout->xml_browse;
		$layout_xml_insert=$layout->xml_insert;
		$sqlstatement=$layout->sqlstatement;
		$hide_array=array(":layout_name"=>$layout_name, ":sqlstatement"=>$sqlstatement, ":layout_xml_browse"=>$layout_xml_browse, ":layout_xml_insert"=>$layout_xml_insert, ":layout_id"=>$layout_id);
		$this->db_exec_esc( "UPDATE Asetelma SET asetelman_nimi=':layout_name', sqllauseke=':sqlstatement', xml_arkisto=':layout_xml_browse', xml_yllapito=':layout_xml_insert' WHERE asetelman_id=:layout_id", $hide_array );

	}

	function db_get_row($row, $sql){
		if(!is_numeric($row)){
			die();
		}
		$sqlstatement=$sql." ORDER BY id ASC LIMIT ".($row-1).",1";
		$sqlstatement_escaped=$sqlstatement;
		$ret_object = $this->db_select($sqlstatement_escaped);
		if(count($ret_object)==0){
			$rowObj=new Row;
			$rowObj->row_keys=array();
			$rowObj->row_data=array();
			$rowObj->count=$row;
			return $rowObj;	
		}else{
		$rowObj=new Row;
		$rowObj->row_keys=array_keys($ret_object[0]);
		$rowObj->row_data=array_values($ret_object[0]);
		$rowObj->count=$row;
		return $rowObj;	
		}
	}

	function db_count_rows($table){
		$table_name = $table->table_name;
		$hide_array=array(":table_name"=>$table_name);
		$ret_object=$this->db_select_esc( "SELECT COUNT(*) FROM :table_name", $hide_array );
		return $ret_object[0][0];
	}


	function db_insert_to_database($table, $row){
		$table_name = $table->table_name;
		$hide_array=array(":table_name"=>$table_name);
		$this->db_exec_esc( "INSERT INTO :table_name (id) VALUES (NULL)", $hide_array );
		$ret_object = $this->db_select_esc("SELECT id FROM :table_name ORDER BY id DESC LIMIT 1", $hide_array);
		$new_row_id=$ret_object[0]['id'];
		
		$update_str="UPDATE :table_name SET ";
		for($i=0; $i<count($row->row_keys); $i++){
			$hide_array[":column"+$i]=$row->row_keys[$i];
			$hide_array[":data"+$i]=$row->row_data[$i];
			$update_str=$update_str.":column".$i."=:data".$i;
			//$update_str=$update_str.$row->row_keys[$i]."='".$row->row_data[$i]."' ";
			if( $i < count( $row->row_keys )-1 ){
				$update_str=$update_str.",";
			}
		}
		$hide_array[':new_row_id']=$new_row_id;
		$update_str=$update_str." WHERE id=:new_row_id";
		echo $update_str;
		$this->db_exec_esc( $update_str, $hide_array );
	}

	
	function db_update_row($table, $row){
		if(!is_numeric($row->count)){
			die();
		}
		$table_name = $table->table_name;
		$hide_array=array(":table_name"=>$table_name);
		$ret_object = $this->db_select("SELECT id FROM :table_name ORDER BY id ASC LIMIT ".($row->count-1).",1");
		$update_row_id=$ret_object[0]['id'];

		$update_str="UPDATE :table_name SET ";
		for($i=0; $i<count($row->row_keys); $i++){
			$hide_array[":column"+$i]=$row->row_keys[$i];
			$hide_array[":data"+$i]=$row->row_data[$i];
			$update_str=$update_str.":column".$i."=':data'".$i;
			//$update_str=$update_str.$row->row_keys[$i]."='".$row->row_data[$i]."' ";
			if( $i < count( $row->row_keys )-1 ){
				$update_str=$update_str.",";
			}
		}
		$hide_array[':update_row_id']=$update_row_id;
		$update_str=$update_str." WHERE id=:update_row_id";
		//echo $update_str;
		$this->db_exec_esc(  $update_str, $hide_array  );
	}

	function db_delete_row($table, $row){
		if(!is_numeric($row->count)){
			die();
		}
		$table_name = $table->table_name;
		$hide_array=array(":table_name"=>$table_name);
		$ret_object = $this->db_select_esc("SELECT id FROM :table_name ORDER BY id LIMIT ".($row->count-1).",1", $hide_array);
		$delete_row_id=$ret_object[0]['id'];
		echo $delete_row_id;
		$hide_array=array(":table_name"=>$table_name, ":delete_row_id"=>$delete_row_id);
		$this->db_exec_esc( "DELETE FROM :table_name WHERE id=:delete_row_id", $hide_array );	
	}
	
	function db_get_user($user){
		$usr=$user->username;
		$hide_array=array(":usr"=>$usr);
		$ret_object=$this->db_select_esc( "SELECT * FROM Kayttaja WHERE kayttajanimi=':usr'", $hide_array );
		return $ret_object;
	}
	
	function db_get_user_priviledges($user){
		$usr=$user->username;
		$hide_array=array(":usr"=>$usr);
		$ret_object=$this->db_select_esc( "SELECT * FROM Kayttaja JOIN Oikeudet ON Kayttaja.kayttaja_id=Oikeudet.kayttaja_id WHERE kayttajanimi=':usr'", $hide_array );
		return $ret_object;
	}

	function db_add_column($column){	
		$hide_array=array(":table_name"=>$column->table_name);
		$ret_object = $this->db_select_esc("SELECT taulun_id FROM Taulu WHERE taulun_nimi=':table_name'", $hide_array);
		$table_id=$ret_object[0]['taulun_id'];
		$hide_array=array(":table_id"=>$table_id, ":column_name"=>$column->column_name, ":column_type"=>$column->column_type);
		$this->db_exec_esc("INSERT INTO Sarake (sarakkeen_id, taulun_id, sarakkeen_nimi, sarakkeen_tyyppi) VALUES (NULL, ':table_id', ':column_name', ':column_type')", $hide_array);
		$this->db_exec_esc("ALTER TABLE :table_name ADD :column_name :column_type", $hide_array);
	}

	function db_change_table_name($table, $new_table_name){
		$hide_array=array(":table_name"=>$table->table_name);
		$ret_object = $this->db_select_esc("SELECT taulun_id FROM Taulu WHERE taulun_nimi=':table_name'", $hide_array);
		$table_id=$ret_object[0]['taulun_id'];
		$hide_array=array(":new_table_name"=>$new_table_name, ":table_id"=>$table_id);
		$this->db_exec_esc("UPDATE Taulu SET taulun_nimi=':new_table_name' WHERE taulun_id=:table_id", $hide_array);
		$hide_array=array(":table_name"=>$table->table_name, ":new_table_name"=>$new_table_name);
		$this->db_exec_esc("ALTER TABLE :table_name RENAME :new_table_name", $hide_array);
	}

	function db_change_column_name($column, $new_column){
		$hide_array=array(":table_name"=>$column->table_name);
		$ret_object = $this->db_select_esc("SELECT taulun_id FROM Taulu WHERE taulun_nimi=:table_name", $hide_array);
		$table_id=$ret_object[0]['taulun_id'];
		$hide_array=array(":table_id"=>$table_id, ":column_name"=>$column->column_name);
		$ret_object = $this->db_select_esc("SELECT sarakkeen_id FROM Sarake WHERE taulun_id=:table_id AND sarakkeen_nimi=':column_name'", $hide_array);
		$column_id=$ret_object[0]['sarakkeen_id'];
		$hide_array=array(":column_name"=>$new_column->column_name, ":column_type"=>$new_column->column_type, ":column_id"=>$column_id);
		$this->db_exec_esc("UPDATE Sarake SET sarakkeen_nimi=':column_name', sarakkeen_tyyppi=':column_type' WHERE sarakkeen_id=:column_id", $hide_array);
		$hide_array=array(":table_name"=>$column->table_name, ":column_name"=>$new_column->column_name, ":column_name_new"=>$new_column->column_name, ":column_type"=>$new_column->column_type);
		$this->db_exec_esc("ALTER TABLE :table_name CHANGE :column_name :column_name_new :column_type;", $hide_array);
	}

	function db_destroy_column($column){
		$hide_array=array(":table_name"=>$column->table_name);
		$ret_object = $this->db_select_esc("SELECT taulun_id FROM Taulu WHERE taulun_nimi=:table_name", $hide_array);
		$table_id=$ret_object[0]['taulun_id'];
		$hide_array=array(":table_id"=>$table_id, ":column_name"=>$column->column_name);
		$ret_object = $this->db_select_esc("SELECT sarakkeen_id FROM Sarake WHERE taulun_id=:table_id AND sarakkeen_nimi=':column_name'", $hide_array);
		$column_id=$ret_object[0]['sarakkeen_id'];
		$hide_array=array(":column_id"=>$column_id);
		$this->db_exec_esc("DELETE FROM Sarake WHERE sarakkeen_id=:column_id", $hide_array);
		$hide_array=array(":table_name"=>$column->table_name, ":column_name"=>$column->column_name);
		$this->db_exec_esc("ALTER TABLE :table_name DROP COLUMN :column_name");
	}

	function db_destroy_table($table){
		$hide_array=array(":table_name"=>$table->table_name);
		$ret_object = $this->db_select_esc("SELECT taulun_id FROM Taulu WHERE taulun_nimi=':table_name'", $hide_array);
		$table_id=$ret_object[0]['taulun_id'];
		$hide_array=array(":table_id"=>$table_id);
		$this->db_exec_esc("DELETE FROM Taulu WHERE taulun_id=:table_id", $hide_array);
		$hide_array=array(":table_name"=>$table->table_name);
		$this->db_exec_esc("DROP TABLE :table_name", $hide_array);
	}

	function db_change_layout_name($layout, $new_layout){
		$hide_array=array(":layout_name_new"=>$new_layout->name, ":layout_name"=>$layout->name);
		$this->db_exec_esc('UPDATE Asetelma SET asetelman_nimi=":layout_name_new" WHERE asetelman_nimi=":layout_name"', $hide_array);
	}

	function db_change_layout_sql($layout, $new_layout){
		$hide_array=array(":sqlstatement"=>$new_layout->sqlstatement, ":layout_name"=>$layout->name);
		$this->db_exec_esc('UPDATE Asetelma SET sqllauseke=":sqlstatement" WHERE asetelman_nimi=":layout_name"', $hide_array);
	}

	function db_destroy_layout($layout){
		$hide_array=array(":layout_name"=>$layout->name);
		$this->db_exec_esc("DELETE FROM Asetelma WHERE asetelman_nimi=':layout_name'", $hide_array);
	}
	
	function db_search($ret_data, $sql){
		$search_str=$sql . " WHERE ";
		$hide_array=array();
		for($i=0; $i<count($ret_data[0]); $i++){
			$hide_array[':key'+$i]=$ret_data[0][$i];
			$hide_array[':data'+$i]=$ret_data[1][$i];
			$search_str=$search_str.":key".$i." LIKE ':data'".$i."";
			//$search_str=$search_str.$ret_data[0][$i]." LIKE '".$ret_data[1][$i]."'";
			if( $i<count($ret_data[0])-1 ){
				$search_str=$search_str."AND";
			}
		}
		$ret_object=$this->db_select_esc( $search_str, $hide_array );
		$search_results=new SearchResults;
		$search_results->resultsArr=$ret_object;
		$search_results->sqlstatement=$search_str;
		return $search_results;
	}
	
	function db_check_same_name_layout($layout){
		$hide_array=array(":layout_name"=>$layout->name);
		$ret_object=$this->db_select_esc("SELECT * FROM Asetelma WHERE asetelman_nimi=':layout_name'", $hide_array);
		return count($ret_object);
	}
	
	function db_check_same_name_table($table){
		$hide_array=array(":table_name"=>$table->table_name);
		$ret_object=$this->db_select_esc("SELECT * FROM Taulu WHERE taulun_nimi=:table_name", $hide_array);
		return count($ret_object);
	}
	
	function db_check_same_name_column($column){
		$hide_array=array(":table_name"=>$column->table_name);
		$ret_object = $this->db_select_esc("SELECT taulun_id FROM Taulu WHERE taulun_nimi=':table_name'", $hide_array);
		$table_id=$ret_object[0]['taulun_id'];
		$hide_array=array("table_id"=>$table_id, "column_name"=>$column->column_name);
		$ret_object=$this->db_select_esc("SELECT * FROM Sarake WHERE taulun_id=:table_id AND sarakkeen_nimi=':column_name'", $hide_array);
		return count($ret_object);
	}

}


?>
