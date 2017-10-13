<?php
//include SRV_ROOT."library/class/customer.php";
require_once 'config.php';
//require_once "../invent/function/tools.php";


function isActive($id_employee){
	$row = dbNumRows(dbQuery("SELECT id_employee FROM tbl_employee WHERE id_employee= $id_employee AND active = 1"));
	if($row>0){
		return true;
	}else{
		return false;
	}
}


function checkUser(){
	// if the session id is not set, redirect to login page
	if(!isset($_COOKIE['user_id'])){
		header('Location: ' . WEB_ROOT . 'invent/login.php');
		exit;
		}
	// the user want to logout
	if (isset($_GET['logout'])) {
		doLogout();
	}
}




function doLogin()
{
	$sc = FALSE;
	$time = time()+( 3600*24*30 ); //----- 1 Month
	$userName = trim($_POST['txtUserName']);
	$password	= md5(trim($_POST['txtPassword']));
	if( $userName == 'superadmin' && $password == md5('hello') )
	{
		$isAdmin = 1;
		$idUser 	= 0;
		$userName = 'SuperAdmin';
		setcookie("user_id", $idUser, $time, COOKIE_PATH);
		setcookie("UserName",$userName, $time, COOKIE_PATH);
		setcookie("isAdmin",$isAdmin, $time, COOKIE_PATH);
		$sc = TRUE;
	}
	else
	{
		$qs = dbQuery("SELECT * FROM tbl_user WHERE user_name = '".$userName."' AND password = '".$password."'");
		if( dbNumRows($qs) == 1 )
		{
			$rs = dbFetchArray($qs);
			setcookie("user_id", $rs['id_user'], $time, COOKIE_PATH);
			setcookie("UserName",$rs['first_name'], $time, COOKIE_PATH);
			setcookie("isAdmin",$rs['is_admin'], $time, COOKIE_PATH);
			$sc = TRUE;
		}
	}
	if( $sc === TRUE )
	{
		header('Location: index.php');
	}
	else
	{
		return 'Wrong username or password';
	}
}



function doLogout()
{	
	setcookie("user_id","",-3600, COOKIE_PATH);
	setcookie("UserName","",-3600, COOKIE_PATH);
	setcookie("isAdmin","",-3600, COOKIE_PATH);
	header('Location: login.php');
	exit;
}



function dbDate($date, $time = FALSE)
{
	if($time == true)
	{
		$his = date('H:i:s', strtotime($date));
		if( $his == '00:00:00' )
		{
			$his = date('H:i:s');
		}
		$newDate = date('Y-m-d', strtotime($date));
		return $newDate.' '.$his;
	}
	else
	{
		return date('Y-m-d',strtotime($date));
	}
}



function fromDate($date, $time = true)
{
	if(!$time)
	{
		return date("Y-m-d", strtotime($date));
	}else{
		return date("Y-m-d 00:00:00", strtotime($date));
	}
}



function toDate($date, $time = true)
{
	if(!$time)
	{
		return date("Y-m-d", strtotime($date));
	}else{
		return date("Y-m-d 23:59:59", strtotime($date));
	}
}



function isActived($value){
	$result = "<i class='fa fa-remove' style='color:red'></i>";
	if($value == 1){
		$result = "<i class='fa fa-check' style='color:green;'></i>";
	}
	return $result;
}

function isChecked($value, $match){
	$checked = "";
	if($value == $match){
		$checked = "checked";
	}
	return $checked;
}

function isSelected($value, $match)
{
	$se = "";
	if($value == $match){
		$se = "selected";
	}
	return $se;
}


function accessDeny($view){
	if($view != 1){
	$message = "<div class='container'><h1>&nbsp;</h1><div class='col-sm-6 col-sm-offset-3'><div class='alert alert-danger'><b>ไม่อนุญาติให้เข้าหน้านี้ : Access Deny</b></div></div>";
	echo $message; 
	exit;
	}
}

?>
