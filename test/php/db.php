<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once("layout.php");
require_once("table.php");
require_once("row.php");

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
    			$this->database->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);	
		}
	}

	function db_escape($sqlstatement){
		return $sqlstatement;
	}

	function db_exec($sqlstatement){
		$sqlstatement_escaped=$this->db_escape($sqlstatement);
		$mysqlquery = $this->database->prepare($sqlstatement_escaped);
  		return $mysqlquery->execute();
	}

	function db_select($sqlstatement){
		$sqlstatement_escaped=$this->db_escape($sqlstatement);
		$mysqlquery = $this->database->prepare($sqlstatement_escaped);
  		$mysqlquery->execute();
		return $mysqlquery->fetchAll();
	}

	function db_close_connection(){
		$this->config=NULL;
		$this->database=NULL;
	}

	function db_create_table($table){
		$table_name = $table->table_get_name();
		$table_columns = $table->table_get_columns_arr();
		$this->db_exec( $this->db_escape( "INSERT INTO Taulu (taulun_id, taulun_nimi) VALUES ( NULL, '".$table_name."' )" ) );
		
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
			$this->db_exec( $this->db_escape( "INSERT INTO Sarake (sarakkeen_id, taulun_id, sarakkeen_nimi, sarakkeen_tyyppi) VALUES ( NULL, ".$new_table_id.", '".$column_name."', '".$column_type."' ) " ) );
			
		}

		$columns_sql=$columns_sql.",id int PRIMARY KEY AUTO_INCREMENT";
		echo $columns_sql;

		$this->db_exec( $this->db_escape("CREATE TABLE ".$table_name." (".$columns_sql.");") );
	}

	function db_create_layout($layout){
		$layout_name = $layout->layout_get_name();
		$layout_sql = $layout->layout_get_sqlstatement();
		$layout_xml_insert="<?xml version=\'1.0\'?>\n<managelayout>\n</managelayout>";
		$layout_xml_browse="<?xml version=\'1.0\'?>\n<layout>\n<visuals>\n</visuals>\n</layout>";
		return $this->db_exec($this->db_escape("INSERT INTO Asetelma (asetelman_id, asetelman_nimi, sqllauseke, xml_yllapito, xml_arkisto) VALUES(NULL, '".$layout_name."', '".$layout_sql."', '".$layout_xml_insert."', '".$layout_xml_browse."')"));

		
	}
	
	function db_get_table_columns($id){
		$ret_object = $this->db_select("SELECT * FROM Sarake WHERE taulun_id=".$id);
		return $ret_object;
	}
	
	function db_get_table_columns_by_name($name){
		$ret_object = $this->db_select("SELECT * FROM Sarake WHERE taulun_nimi=".$name);
		return $ret_object;
	}

	function db_get_tables(){
		$ret_object = $this->db_select("SELECT * FROM Taulu");

		$table_list[]=NULL;

		foreach ($ret_object as $tables_row)
    		{
          		$table_id = $tables_row['taulun_id'];
			$table_name = $tables_row['taulun_nimi'];
			$table_columns = $this->db_get_table_columns($table_id);

			$table_new = new Table;
			$table_new->table_create_table($table_id, $table_name, $table_columns);
			array_push( $table_list, $table_new );
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
			
			$layout_new = new Layout;
			$layout_new->layout_create_layout($layout_id, $layout_name, $layout_sqlstatement, $layout_browse_xml, $layout_insert_xml);

			array_push( $layout_list, (object)$layout_new );
         	}

		return $layout_list;
	}

	function db_get_layout($id){
		$ret_object = $this->db_select("SELECT * FROM Asetelma WHERE asetelman_id=".$id);

		$layout=NULL;
		foreach ($ret_object as $layouts_row)
    		{
          		$layout_id = $layouts_row['asetelman_id'];
			$layout_name = $layouts_row['asetelman_nimi'];
			$layout_sqlstatement=$layouts_row['sqllauseke'];
			$layout_browse_xml = $layouts_row['xml_arkisto'];
			$layout_insert_xml = $layouts_row['xml_yllapito'];
			
			$layout_new = new Layout;
			$layout_new->layout_create_layout($layout_id, $layout_name, $layout_sqlstatement, $layout_browse_xml, $layout_insert_xml);

			$layout=(object)$layout_new;
         	}

		return $layout;
	}

	function db_set_layout($layout){
		$layout_id=$layout->id;
		$layout_name=$layout->name;
		$layout_xml_browse=$layout->xml_browse;
		$layout_xml_insert=$layout->xml_insert;
		$sqlstatement=$layout->sqlstatement;
		$this->db_exec( $this->db_escape("UPDATE Asetelma SET asetelman_nimi='".$layout_name."', sqllauseke='".$sqlstatement."', xml_arkisto='".$layout_xml_browse."', xml_yllapito='".$layout_xml_insert."' WHERE asetelman_id='".$layout_id."'") );

	}

	function db_get_row($row, $sql){
		$sqlstatement=$sql." ORDER BY id ASC LIMIT ".($row-1).",1";
		$sqlstatement_escaped=$this->db_escape($sqlstatement);
		$ret_object = $this->db_select($sqlstatement_escaped);
		
		return $ret_object;	
	}

	function db_count_rows($table){
		$table_name = $table->table_name;
		$ret_object=$this->db_select( $this->db_escape("SELECT COUNT(*) FROM ".$table_name."") );
		return $ret_object[0][0];
	}


	function db_insert_to_database($table, $row){
		$table_name = $table->table_name;
		$this->db_exec( $this->db_escape("INSERT INTO ".$table_name." (id) VALUES (NULL)") );

		$ret_object = $this->db_select($this->db_escape("SELECT id FROM ".$table_name." ORDER BY id DESC LIMIT 1"));
		$new_row_id=$ret_object[0]['id'];

		$update_str="UPDATE ".$table_name." SET ";
		for($i=0; $i<count($row->row_keys); $i++){
			$update_str=$update_str.$row->row_keys[$i]."='".$row->row_data[$i]."' ";
			if( $i < count( $row->row_keys )-1 ){
				$update_str=$update_str.",";
			}
		}
		$update_str=$update_str." WHERE id=".$new_row_id;
		//echo $update_str;
		$this->db_exec( $this->db_escape( $update_str ) );
	}

	
	function db_update_row($table, $row){
		$table_name = $table->table_name;
		$ret_object = $this->db_select($this->db_escape("SELECT id FROM ".$table_name." ORDER BY id ASC LIMIT ".($row->count-1).",1"));
		$update_row_id=$ret_object[0]['id'];

		$update_str="UPDATE ".$table_name." SET ";
		for($i=0; $i<count($row->row_keys); $i++){
			$update_str=$update_str.$row->row_keys[$i]."='".$row->row_data[$i]."' ";
			if( $i < count( $row->row_keys )-1 ){
				$update_str=$update_str.",";
			}
		}
		$update_str=$update_str." WHERE id=".$update_row_id;
		//echo $update_str;
		$this->db_exec( $this->db_escape( $update_str ) );
	}

	function db_delete_row($table, $row){
		$table_name = $table->table_name;
		$ret_object = $this->db_select($this->db_escape("SELECT id FROM ".$table_name." ORDER BY id LIMIT ".($row->count-1).",1"));
		$delete_row_id=$ret_object[0]['id'];
		echo $delete_row_id;
		$this->db_exec( $this->db_escape("DELETE FROM ".$table_name." WHERE id=".$delete_row_id) );	
	}
	
	function db_get_user($user){
		$usr=$user->username;
		$ret_object=$this->db_select( "SELECT * FROM Kayttaja WHERE kayttajanimi='".$usr."'" );
		return $ret_object;
	}
	
	function db_get_user_priviledges($user){
		$usr=$user->username;
		$ret_object=$this->db_select( "SELECT * FROM Kayttaja JOIN Oikeudet ON Kayttaja.kayttaja_id=Oikeudet.kayttaja_id WHERE kayttajanimi='".$usr."'" );
		return $ret_object;
	}

	function db_add_column($table_name, $column_name, $column_type){

		$ret_object = $this->db_select("SELECT taulun_id FROM Taulu WHERE taulun_nimi='".$table_name."'");
		$table_id=$ret_object[0]['taulun_id'];

		//echo "INSERT INTO Sarake (sarakkeen_id, taulun_id, sarakkeen_nimi, sarakkeen_tyyppi) VALUES (NULL, '".$table_id."', '".$column_name."', '".$column_type."')";
		$this->db_exec("INSERT INTO Sarake (sarakkeen_id, taulun_id, sarakkeen_nimi, sarakkeen_tyyppi) VALUES (NULL, '".$table_id."', '".$column_name."', '".$column_type."')");

		//echo "ALTER TABLE ".$table_name." ADD ".$column_name." ".$column_type." ";
		$this->db_exec("ALTER TABLE ".$table_name." ADD ".$column_name." ".$column_type." ");
	}

	function db_change_table_name($table_name, $new_table_name){
		$ret_object = $this->db_select("SELECT taulun_id FROM Taulu WHERE taulun_nimi='".$table_name."'");
		$table_id=$ret_object[0]['taulun_id'];

		//echo "UPDATE Taulu SET taulun_nimi='".$new_table_name."' WHERE taulun_id=".$table_id;
		$this->db_exec("UPDATE Taulu SET taulun_nimi='".$new_table_name."' WHERE taulun_id=".$table_id);

		//echo "ALTER TABLE ".$table_name." RENAME ".$new_table_name.";";
		$this->db_exec("ALTER TABLE ".$table_name." RENAME ".$new_table_name.";");
	}

	function db_change_column_name($table_name, $column_name, $new_column_name, $new_column_type){
		$ret_object = $this->db_select("SELECT taulun_id FROM Taulu WHERE taulun_nimi='".$table_name."'");
		$table_id=$ret_object[0]['taulun_id'];

		$ret_object = $this->db_select("SELECT sarakkeen_id FROM Sarake WHERE taulun_id='".$table_id."' AND sarakkeen_nimi='".$column_name."'");
		$column_id=$ret_object[0]['sarakkeen_id'];

		//echo "UPDATE Sarake SET sarakkeen_nimi='".$new_column_name."', sarakkeen_tyypi='".$new_column_type."' WHERE sarakkeen_id=".$column_id;
		$this->db_exec("UPDATE Sarake SET sarakkeen_nimi='".$new_column_name."', sarakkeen_tyyppi='".$new_column_type."' WHERE sarakkeen_id=".$column_id);

		//echo "ALTER TABLE ".$table_name." CHANGE ".$column_name." ".$new_column_name." ".$new_column_type.";";
		$this->db_exec("ALTER TABLE ".$table_name." CHANGE ".$column_name." ".$new_column_name." ".$new_column_type.";");
	}

	function db_destroy_column($table_name, $column_name){
		$ret_object = $this->db_select("SELECT taulun_id FROM Taulu WHERE taulun_nimi='".$table_name."'");
		$table_id=$ret_object[0]['taulun_id'];

		$ret_object = $this->db_select("SELECT sarakkeen_id FROM Sarake WHERE taulun_id='".$table_id."' AND sarakkeen_nimi='".$column_name."'");
		$column_id=$ret_object[0]['sarakkeen_id'];

		//echo "DELETE FROM Sarake WHERE sarakkeen_id=".$column_id;
		$this->db_exec("DELETE FROM Sarake WHERE sarakkeen_id=".$column_id);

		//echo "ALTER TABLE ".$table_name." DROP COLUMN ".$column_name;
		$this->db_exec("ALTER TABLE ".$table_name." DROP COLUMN ".$column_name);
	}

	function db_destroy_table($table_name){
		$ret_object = $this->db_select("SELECT taulun_id FROM Taulu WHERE taulun_nimi='".$table_name."'");
		$table_id=$ret_object[0]['taulun_id'];

		//echo "DELETE FROM Taulu WHERE taulun_id=".$table_id;
		$this->db_exec("DELETE FROM Taulu WHERE taulun_id=".$table_id);

		//echo "DROP TABLE ".$table_name;
		$this->db_exec("DROP TABLE ".$table_name);
	}

	function db_change_layout_name($layout_name, $new_layout_name){
		//echo 'UPDATE Asetelma SET asetelman_nimi="'.$new_layout_name.'" WHERE asetelman_nimi="'.$layout_name.'"';
		$this->db_exec('UPDATE Asetelma SET asetelman_nimi="'.$new_layout_name.'" WHERE asetelman_nimi="'.$layout_name.'"');
	}

	function db_change_layout_sql($layout_name, $sql){
		//echo 'UPDATE Asetelma SET asetelman_nimi="'.$new_layout_name.'" WHERE asetelman_nimi="'.$layout_name.'"';
		$this->db_exec('UPDATE Asetelma SET sqllauseke="'.$sql.'" WHERE asetelman_nimi="'.$layout_name.'"');
	}

	function db_destroy_layout($layout_name){
		//echo "DELETE FROM Asetelma WHERE asetelman_nimi='".$layout_name."'";
		$this->db_exec("DELETE FROM Asetelma WHERE asetelman_nimi='".$layout_name."'");
	}

}


?>
