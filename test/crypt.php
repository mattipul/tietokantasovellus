<?php

require_once("php/hash.php");

$hasher=new Hash;
$pass=$hasher->crypt_password("admin");

echo $pass[0]." ".$pass[1];

?>