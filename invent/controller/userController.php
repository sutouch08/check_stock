<?php
require "../../library/config.php";
require "../../library/functions.php";
require '../function/tools.php';

if( isset( $_GET['addUser']) )
{
	$sc = 'fail';
	$userName = $_POST['userName'];
	$pass 	= md5($_POST['password']);
	$firstName = $_POST['firstName'];
	$lastName = $_POST['lastName'];
	$isAdmin	= $_POST['isAdmin'];
	$active 	= $_POST['active'];

	$qs = dbQuery("INSERT INTO tbl_user (user_name, password, first_name, last_name, is_admin,active) VALUES ('".$userName."', '".$pass."', '".$firstName."','".$lastName."',".$isAdmin.", ".$active.")");

	if( $qs)
	{
		$sc = 'success';
	}
	echo $sc;
}


?>
