<?php

ini_set('display_errors',1); 
error_reporting(E_ALL);

require_once("layout.php");
require_once("table.php");

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

	function db_get_tables(){
		$ret_object = $this->db_select("SELECT * FROM Taulu");

		$table_list[]=NULL;

		foreach ($ret_object as $tables_row)
    		{
          		$table_id = $tables_row['taulun_id'];
			$table_name = $tabless_row['taulun_nimi'];
			$table_columns = "";

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
		$sqlstatement=$sql." LIMIT ".($row-1).",1";
		$sqlstatement_escaped=$this->db_escape($sqlstatement);
		
		$ret_object = $this->db_select($sqlstatement_escaped);
		
		return $ret_object;	
	}

}


?>
