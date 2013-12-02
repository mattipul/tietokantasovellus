<?php

//HUOMAA DOKUMENTAATION 11. osio
//YLLÄPITOMETODIT EIVÄT TARKOITUKSENMUKAISESTI SISÄLLÄ INJEKTIOSUOJAA

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once("layout.php");
require_once("table.php");
require_once("row.php");
require_once("column.php");
require_once("search_results.php");
require_once("user.php");

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
		}
	}

	function db_escape($sqlstatement){
		return $sqlstatement;
	}

	function db_exec($sqlstatement){
		//try {
			$sqlstatement_escaped=$this->db_escape($sqlstatement);
			$mysqlquery = $this->database->prepare($sqlstatement_escaped);
	  		if($mysqlquery->execute() == FALSE){
				die("Virhef!");
			}
		//}catch(Exception $e){
		//	die("Virhe!");
		//}
	}

	function db_select($sqlstatement){
		//try{
			$sqlstatement_escaped=$this->db_escape($sqlstatement);
			$mysqlquery = $this->database->prepare($sqlstatement_escaped);
	  		$ret=$mysqlquery->execute();
			if($ret==TRUE){		
				return $mysqlquery->fetchAll();
			}else{
				die("Virhe!");
			}
		//}catch(Exception $e){
		//	die("Virhe!");
		//}
	}

	function db_exec_esc($sqlstatement, $hide_array){
		//try{
			$mysqlquery = $this->database->prepare($sqlstatement);
			$ret=$mysqlquery->execute($hide_array);
	  		if($ret == FALSE){
				die("Virhe!");
			}
		//}catch(Exception $e){
		//	die("Virhe!");
		//}
	}
	
	function db_select_esc($sqlstatement, $hide_array){
		//try{
			$mysqlquery = $this->database->prepare($sqlstatement);
	  		$ret=$mysqlquery->execute($hide_array);
			if($ret==TRUE){		
				return $mysqlquery->fetchAll();
			}else{
				die("Virhe!");
			}
		//}catch(Exception $e){
		//	die("Virhe!");
		//}
	}

	function db_close_connection(){
		$this->config=NULL;
		$this->database=NULL;
	}

	function db_create_table($table){
		$table_name = $table->table_name;
		$table_columns = $table->table_columns;

		$columns_sql = "";
		for($i=0; $i<count($table_columns); $i++){
			$column=explode(":", $table_columns[$i]);
			$column[0] = trim(preg_replace('/ +/', '', preg_replace('/[^A-Za-z0-9 ]/', '', urldecode(html_entity_decode(strip_tags($column[0]))))));
			$column[1] = trim(preg_replace('/ +/', '', preg_replace('/[^A-Za-z0-9 ]/', '', urldecode(html_entity_decode(strip_tags($column[1]))))));
			$columns_sql=$columns_sql.$column[0]." ".$column[1];
			if( $i<count($table_columns)-1 ){
				$columns_sql=$columns_sql.",";
			}
		}

		$columns_sql=$columns_sql.",id int PRIMARY KEY AUTO_INCREMENT";
		$this->db_exec( "CREATE TABLE ".$table_name." (".$columns_sql.");" );

		$this->db_exec( "INSERT INTO Taulu (taulun_id, taulun_nimi) VALUES ( NULL, '".$table_name."' )" );
		
		$ret_object = $this->db_select("SELECT taulun_id FROM Taulu ORDER BY taulun_id DESC LIMIT 1");
		$new_table_id=$ret_object[0]['taulun_id'];

		$columns_sql = "";

		$table_columns_sql_string="";
		for($i=0; $i<count($table_columns); $i++){
			$column=explode(":", $table_columns[$i]);
			$column[0] = trim(preg_replace('/ +/', '', preg_replace('/[^A-Za-z0-9 ]/', '', urldecode(html_entity_decode(strip_tags($column[0]))))));
			$column[1] = trim(preg_replace('/ +/', '', preg_replace('/[^A-Za-z0-9 ]/', '', urldecode(html_entity_decode(strip_tags($column[1]))))));
			$column_name=$column[0];
			$column_type=$column[1];
			$columns_sql=$columns_sql.$column[0]." ".$column[1];
			if( $i<count($table_columns)-1 ){
				$columns_sql=$columns_sql.",";
			}
			$this->db_exec( "INSERT INTO Sarake (sarakkeen_id, taulun_id, sarakkeen_nimi, sarakkeen_tyyppi) VALUES ( NULL, ".$new_table_id.", '".$column_name."', '".$column_type."' ) "  );
			
		}

		

	}

	function db_create_layout($layout){
		$layout_name = $layout->name;
		$layout_sql = $layout->sqlstatement;
		$layout_xml_insert="<?xml version=\"1.0\"?>\n<managelayout>\n</managelayout>";
		$layout_xml_browse="<?xml version=\"1.0\"?>\n<layout>\n<visuals>\n</visuals>\n</layout>";
		$hide_array=array(":layout_name"=>$layout_name, ":layout_sql"=>$layout_sql, ":layout_xml_insert"=>$layout_xml_insert, ":layout_xml_browse"=>$layout_xml_browse);
		$this->db_exec_esc("INSERT INTO Asetelma (asetelman_id, asetelman_nimi, sqllauseke, xml_yllapito, xml_arkisto) VALUES(NULL, :layout_name, :layout_sql, :layout_xml_insert, :layout_xml_browse)", $hide_array);

		
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
		$ret_object = $this->db_select_esc("SELECT * FROM Sarake WHERE taulun_nimi=:name", $hide_array);
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

		$layout_list=array();

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

//INJEKTIOSUOJA(VALMIS)

	function db_get_layout($id){
		if(!is_numeric($id)){
			die();
		}
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
		$layout_xml_browse=$layout->xml_browse;
		$layout_xml_insert=$layout->xml_insert;
		$hide_array=array(":layout_xml_browse"=>$layout_xml_browse, ":layout_xml_insert"=>$layout_xml_insert, ":layout_id"=>$layout_id);
		$this->db_exec_esc( "UPDATE Asetelma SET xml_arkisto=:layout_xml_browse, xml_yllapito=:layout_xml_insert WHERE asetelman_id=:layout_id", $hide_array );

	}

//INJEKTIOSUOJA(VALMIS)

	function db_get_row($row, $sql){
		if(!is_numeric($row)){
			die();
		}
		$sqlstatement=$sql." LIMIT ".($row-1).",1";
		$sqlstatement_escaped=$sqlstatement;
		//echo $sqlstatement;
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
		$ret_object=$this->db_select( "SELECT COUNT(*) FROM ".$table_name);
		return $ret_object[0][0];
	}

	function db_check_column($table, $column_name){
		$hide_array=array(":column_name"=>$column_name);
		$ret_object=$this->db_select_esc("SHOW COLUMNS FROM ".$table->table_name." LIKE :column_name", $hide_array);		
		if(count($ret_object)!=1){
			die("Sarake!");
		}
	}

	function db_check_column_sql($sql, $column_name){
		$ret_object=$this->db_select($sql);		
		if(isset($ret_object[0][$column_name])==FALSE){
			die($sql);
		}
	}

	function db_check_column_if_int($table, $column_name){
		$hide_array=array(":column_name"=>$column_name);
		$ret_object=$this->db_select_esc("SHOW COLUMNS FROM ".$table->table_name." LIKE :column_name", $hide_array);		
		if( strstr($ret_objet[0]['type'], "int")!=FALSE ){
			return 1;
		}else{
			return 0;
		}
	
	}

	function db_check_column_if_float($table, $column_name){
		$hide_array=array(":column_name"=>$column_name);
		$ret_object=$this->db_select_esc("SHOW COLUMNS FROM ".$table->table_name." LIKE :column_name", $hide_array);		
		if( strstr($ret_objet[0]['type'], "double")!=FALSE ){
			return 1;
		}else{
			return 0;
		}
	
	}

//INJEKTIOSUOJA(TARKISTA SARAKKEIDEN OLEMASSAOLO)VALMIS


	function db_insert_to_database($table, $row){
		$table_name = $table->table_name;
		$this->db_exec( "INSERT INTO ".$table_name." (id) VALUES (NULL)" );
		$hide_array=array();
		$ret_object = $this->db_select("SELECT id FROM ".$table_name." ORDER BY id DESC LIMIT 1");
		$new_row_id=$ret_object[0]['id'];

		$update_str="UPDATE ".$table_name." SET ";
		for($i=0; $i<count($row->row_keys); $i++){
			$this->db_check_column($table, $row->row_keys[$i]);
			
			if($this->db_check_column_if_int($table, $row->row_keys[$i]) == 1){
				$row->row_keys[$i]=intval($row->row_keys[$i]);
			}
			if($this->db_check_column_if_float($table, $row->row_keys[$i]) == 1){
				$row->row_keys[$i]=floatval($row->row_keys[$i]);
			}
			$row->row_keys[$i]=htmlentities($row->row_keys[$i], ENT_QUOTES, "UTF-8");
			$hide_array[':data'.$i]=$row->row_data[$i];
			$update_str=$update_str.$row->row_keys[$i]."=:data".$i." ";
			if( $i < count( $row->row_keys )-1 ){
				$update_str=$update_str.",";
			}
		}
		$update_str=$update_str." WHERE id=".$new_row_id;
		echo $update_str;
		$this->db_exec_esc( $update_str, $hide_array );
	}

//INJEKTIOSUOJA(TARKISTA SARAKKEIDEN OLEMASSAOLO)VALMIS
	
	function db_update_row($table, $row){
		if(!is_numeric($row->count)){
			die();
		}
		$table_name = $table->table_name;
		$hide_array=array();
		$ret_object = $this->db_select("SELECT id FROM ".$table_name." ORDER BY id ASC LIMIT ".($row->count-1).",1");
		$update_row_id=$ret_object[0]['id'];

		$update_str="UPDATE ".$table_name." SET ";
		for($i=0; $i<count($row->row_keys); $i++){
			//$update_str=$update_str.$row->row_keys[$i]."='".$row->row_data[$i]."' ";
			$this->db_check_column($table, $row->row_keys[$i]);
			$hide_array[':data'.$i]=$row->row_data[$i];
			$update_str=$update_str.$row->row_keys[$i]."=:data".$i." ";
			if( $i < count( $row->row_keys )-1 ){
				$update_str=$update_str.",";
			}
		}
		$update_str=$update_str." WHERE id=".$update_row_id;
		//echo $update_str;
		$this->db_exec_esc(  $update_str, $hide_array  );
	}

//INJEKTIOSUOJA(VALMIS)

	function db_delete_row($table, $row){
		if(!is_numeric($row->count)){
			die();
		}
		$table_name = $table->table_name;
		$ret_object = $this->db_select($this->db_escape("SELECT id FROM ".$table_name." ORDER BY id LIMIT ".($row->count-1).",1"));
		$delete_row_id=$ret_object[0]['id'];
		echo $delete_row_id;
		$this->db_exec( "DELETE FROM ".$table_name." WHERE id=".$delete_row_id );	
	}

//INJEKTIOSUOJA(VALMIS)
	
	function db_get_user($user){
		$usr=$user->username;
		$hide_array=array(":usr"=>$usr);
		$ret_object=$this->db_select_esc( "SELECT * FROM Kayttaja WHERE kayttajanimi=:usr", $hide_array );
		return $ret_object;
	}

//INJEKTIOSUOJA(VALMIS)
	
	function db_get_user_priviledges($user){
		$usr=$user->username;
		$hide_array=array(":usr"=>$usr);
		$ret_object=$this->db_select_esc( "SELECT * FROM Kayttaja JOIN Oikeudet ON Kayttaja.kayttaja_id=Oikeudet.kayttaja_id WHERE kayttajanimi=:usr", $hide_array );
		return $ret_object;
	}

	function db_add_column($column){	
		$ret_object = $this->db_select("SELECT taulun_id FROM Taulu WHERE taulun_nimi='".$column->table_name."'");
		$table_id=$ret_object[0]['taulun_id'];
		$this->db_exec("ALTER TABLE ".$column->table_name." ADD ".$column->column_name." ".$column->column_type." ");
		$this->db_exec("INSERT INTO Sarake (sarakkeen_id, taulun_id, sarakkeen_nimi, sarakkeen_tyyppi) VALUES (NULL, '".$table_id."', '".$column->column_name."', '".$column->column_type."')");
	}

	function db_change_table_name($table, $new_table_name){
		$ret_object = $this->db_select("SELECT taulun_id FROM Taulu WHERE taulun_nimi='".$table->table_name."'");
		$table_id=$ret_object[0]['taulun_id'];
		$this->db_exec("ALTER TABLE ".$table->table_name." RENAME ".$new_table_name.";");
		$this->db_exec("UPDATE Taulu SET taulun_nimi='".$new_table_name."' WHERE taulun_id=".$table_id);
	}

	function db_change_column_name($column, $new_column){
		$ret_object = $this->db_select("SELECT taulun_id FROM Taulu WHERE taulun_nimi='".$column->table_name."'");
		$table_id=$ret_object[0]['taulun_id'];
		$ret_object = $this->db_select("SELECT sarakkeen_id FROM Sarake WHERE taulun_id='".$table_id."' AND sarakkeen_nimi='".$column->column_name."'");
		$column_id=$ret_object[0]['sarakkeen_id'];
		$this->db_exec("ALTER TABLE ".$column->table_name." CHANGE ".$column->column_name." ".$new_column->column_name." ".$new_column->column_type.";");
		$this->db_exec("UPDATE Sarake SET sarakkeen_nimi='".$new_column->column_name."', sarakkeen_tyyppi='".$new_column->column_type."' WHERE sarakkeen_id=".$column_id);
	}

	function db_destroy_column($column){
		$ret_object = $this->db_select("SELECT taulun_id FROM Taulu WHERE taulun_nimi='".$column->table_name."'");
		$table_id=$ret_object[0]['taulun_id'];
		$ret_object = $this->db_select("SELECT sarakkeen_id FROM Sarake WHERE taulun_id='".$table_id."' AND sarakkeen_nimi='".$column->column_name."'");
		$column_id=$ret_object[0]['sarakkeen_id'];
		$this->db_exec("ALTER TABLE ".$column->table_name." DROP COLUMN ".$column->column_name);
		$this->db_exec("DELETE FROM Sarake WHERE sarakkeen_id=".$column_id);
	}

	function db_destroy_table($table){
		$ret_object = $this->db_select("SELECT taulun_id FROM Taulu WHERE taulun_nimi='".$table->table_name."'");
		$table_id=$ret_object[0]['taulun_id'];
		$this->db_exec("DROP TABLE ".$table->table_name);
		$this->db_exec("DELETE FROM Taulu WHERE taulun_id=".$table_id);
	}

	function db_change_layout_name($layout, $new_layout){
		$hide_array=array(":name"=>$new_layout->name, ":name_old"=>$layout->name);
		$this->db_exec_esc('UPDATE Asetelma SET asetelman_nimi=:name WHERE asetelman_nimi=:name_old', $hide_array);
	}

	function db_change_layout_sql($layout, $new_layout){
		$hide_array=array(":sqlstatement"=>$new_layout->sqlstatement, ":name"=>$layout->name);
		$this->db_exec_esc('UPDATE Asetelma SET sqllauseke=:sqlstatement WHERE asetelman_nimi=:name', $hide_array);
	}

	function db_destroy_layout($layout){
		$hide_array=array(":name"=>$layout->name);
		$this->db_exec_esc("DELETE FROM Asetelma WHERE asetelman_nimi=:name", $hide_array);
	}

//INJEKTIOSUOJA(TARKISTA SARAKKEIDEN OLEMASSAOLO)(SQL POIS JS:N PUOLELTA)
	
	function db_search($ret_data, $sql){
		$search_str=$sql . " WHERE ";
		$hide_array=array();
		for($i=0; $i<count($ret_data[0]); $i++){
			//$this->db_check_column($table, $ret_data[0][$i]);
			$this->db_check_column_sql($sql, $ret_data[0][$i]);
			$hide_array[':data'.$i]=$ret_data[1][$i];
			$search_str=$search_str.$ret_data[0][$i]." LIKE :data".$i." ";
			if( $i<count($ret_data[0])-1 ){
				$search_str=$search_str." AND ";
			}
		}
		$ret_object=$this->db_select_esc( $search_str,$hide_array );
		$search_results=new SearchResults;
		$search_results->resultsArr=$ret_object;
		$search_results->sqlstatement=$search_str;
		return $search_results;
	}
	
	function db_check_same_name_layout($layout){
		$hide_array=array(":name"=>$layout->name);
		$ret_object=$this->db_select_esc("SELECT * FROM Asetelma WHERE asetelman_nimi=:name", $hide_array);
		return count($ret_object);
	}
	
	function db_check_same_name_table($table){
		$hide_array=array(":table_name"=>$table->table_name);
		$ret_object=$this->db_select_esc("SELECT * FROM Taulu WHERE taulun_nimi=:table_name", $hide_array);
		return count($ret_object);
	}
	
	function db_check_same_name_column($column){
		$hide_array=array(":table_name"=>$column->table_name);
		$ret_object = $this->db_select_esc("SELECT taulun_id FROM Taulu WHERE taulun_nimi=:table_name", $hide_array);
		$table_id=$ret_object[0]['taulun_id'];
		$hide_array=array(":table_id"=>$table_id, ":column_name"=>$column->column_name);
		$ret_object=$this->db_select_esc("SELECT * FROM Sarake WHERE taulun_id=:table_id AND sarakkeen_nimi=:column_name", $hide_array);
		return count($ret_object);
	}

//INJEKTIOSUOJA

	function db_check_layout_permission($layout, $user){
		//$hide_array=array(":user_id"=>$user->user_id, "layout_id"=>$layout->id);
		//$ret_object=$this->db_select("SELECT * FROM Oikeudet WHERE kayttaja_id=:user_id AND kohde=:layout_id ORDER BY id DESC LIMIT 1", $hide_array);
		$hide_array=array(":user_id"=>$user->user_id, ":layout_id"=>$layout->id);
		$ret_object=$this->db_select_esc("SELECT * FROM Oikeudet WHERE kayttaja_id=:user_id AND kohde=:layout_id ORDER BY oikeus_id DESC LIMIT 1", $hide_array);
		return $ret_object;
	}

//INJEKTIOSUOJA

	function db_check_admin($user){
		//$hide_array=array(":user_id"=>$user->user_id, "layout_id"=>$layout->id);
		//$ret_object=$this->db_select("SELECT * FROM Oikeudet WHERE kayttaja_id=:user_id AND kohde=:layout_id ORDER BY id DESC LIMIT 1", $hide_array);
		$hide_array=array(":user_id"=>$user->user_id);
		$ret_object=$this->db_select_esc("SELECT * FROM Oikeudet WHERE kayttaja_id=:user_id ORDER BY oikeus_id DESC LIMIT 1", $hide_array);
		return $ret_object;
	}

	function db_set_layout_permission($layout, $user, $permission){
		$hide_array=array(":user_id"=>$user->user_id, ":layout_id"=>$layout_id, ":permission"=>$permission);
		$this->db_exec_esc("INSERT INTO Oikeudet (oikeus_id, kayttaja_id, tyyppi, kohde, oikeus) VALUES (NULL, :user_id, '1', :layout_id, :permission)", $hide_array);
	}

	function db_set_admin($user){
		$hide_array=array(":user_id"=>$user->user_id);
		$this->db_exec_esc("INSERT INTO Oikeudet (oikeus_id, kayttaja_id, tyyppi, kohde, oikeus) VALUES (NULL, :user_id, '-1', '-1', '0')", $hide_array);
	}
	
	function db_create_user($user){
		//echo "INSERT INTO Kayttaja (kayttaja_id, kayttajanimi, tiiviste, suola) VALUES ( NULL, '".$user->username."', '".$user->hash."', '".$user->salt."' )";
		$hide_array=array(":username"=>$user->username, ":hash"=>$user->hash, ":salt"=>$user->salt);
		$this->db_exec_esc("INSERT INTO Kayttaja (kayttaja_id, kayttajanimi, tiiviste, suola) VALUES ( NULL, :username, :hash, :salt )", $hide_array);
	}

	function db_get_users(){
		$ret_object = $this->db_select("SELECT * FROM Kayttaja");
		$user_list=array();		

		foreach ($ret_object as $user)
    		{
			$username=$user['kayttajanimi'];
			$user_id=$user['kayttaja_id'];

			$userObj=new User;
			$userObj->user_id=$user_id;
			$userObj->username=$username;

			$user_list[]=$userObj;
         	}


		return $user_list;
	}

	function db_read_rights($layout, $user){
		$hide_array=array(":user_id"=>$user->user_id, ":layout_id"=>$layout->id);
		$this->db_exec_esc("INSERT INTO Oikeudet (oikeus_id, kayttaja_id, tyyppi, kohde, oikeus) VALUES ( NULL, :user_id, '1', :layout_id, '2' )", $hide_array);
	}

	function db_write_rights($layout, $user){
		$hide_array=array(":user_id"=>$user->user_id, ":layout_id"=>$layout->id);
		$this->db_exec_esc("INSERT INTO Oikeudet (oikeus_id, kayttaja_id, tyyppi, kohde, oikeus) VALUES ( NULL, :user_id, '1', :layout_id, '1' )", $hide_array);
	}

	function db_notvisible_rights($layout, $user){
		$hide_array=array(":user_id"=>$user->user_id, ":layout_id"=>$layout->id);
		$this->db_exec_esc("INSERT INTO Oikeudet (oikeus_id, kayttaja_id, tyyppi, kohde, oikeus) VALUES ( NULL, :user_id, '1', :layout_id, '-1' )", $hide_array);
	}

	function db_make_admin($user){
		$hide_array=array(":user_id"=>$user->user_id);
		$this->db_exec_esc("INSERT INTO Oikeudet (oikeus_id, kayttaja_id, tyyppi, kohde, oikeus) VALUES ( NULL, :user_id, '-1', '-1', '0' )", $hide_array);
	}

	function db_destroy_user($user){
		$hide_array=array(":user_id"=>$user->user_id);
		$this->db_exec_esc("DELETE FROM Kayttaja WHERE kayttaja_id=:user_id", $hide_array);
	}

}


?>
