<?php

require_once("db.php");
require_once("user.php");

class Login{

	private $db;
	
	function login_init(){
		$this->db=new Database;
		$this->db->db_set_name("tietokanta");
		$this->db->db_create_connection("sovellus", "ietokantawi0Bieyo");
	}

	function login_authorize($user, $pass){
		$userObj=new User;
		$userObj->username=$user;
		$userRet=$this->db->db_get_user($userObj);
		
		if( strcmp( crypt($pass, $userRet['suola']), $userRet['tiiviste'] ) == 0  ){
			return 1;
		}else{
			return 0;
		}
	}
	
	function login_check_priviledges($user){
		$userObj=new User;
		$userObj->username=$user;
		$userRet=$this->db->db_get_user_priviledges($userObj);
		
		$user_priviledges;
		
		if( count($userRet)==1 && $userRet['tyyppi']==-1 && $userRet['kohde']==-1 && $userRet['oikeus']==0 ){
			$user_priviledges['admin']=1;
		}
		else{
			$priviledges;
			for($i=0; $i<count($userRet); $i++){
				$priviledges['type']=$userRet[$i]['tyyppi'];
				$priviledges['id']=$userRet[$i]['kohde'];
			}
			$user_priviledges['admin']=0;
			$user_priviledges[]=$priviledges;
		}
		
		return $user_priviledges;

	}

}

?>