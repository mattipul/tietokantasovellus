<?php

class Hash{

	function generate_salt(){
		$chars='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$retstr = '';
		for ($i = 0; $i < 16; $i++) {
			$retstr .= $chars[rand(0, strlen($chars) - 1)];
		}
		return $retstr;
	}
	
	function crypt_password($str){
		
		$salt=$this->generate_salt();
		$hash=crypt($str, $salt);
		
		$password_ret[0]=$hash;
		$password_ret[1]=$salt;
		
		return $password_ret;
	
	}

}

?>