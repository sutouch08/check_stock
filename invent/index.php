<?php
require_once '../library/config.php';
require_once '../library/functions.php';
require_once "function/tools.php";

checkUser();
$user_id = $_COOKIE['user_id'];
$content = 'main.php';
$page = (isset($_GET['content'])&& $_GET['content'] !='')?$_GET['content']:'';
switch($page){

//**********  ระบบคลังสินค้า  **********//
		case 'user' :
			$content = 'user.php';
			$pageTitle = 'เพิ่ม/แก้ไข พนักงาน';
			break;
		case 'check' :
			$content = 'check.php';
			$pageTitle = 'เพิ่ม/แก้ไข การตรวจนับ';
			break;
		case 'checkstock' :
			$content = 'check_stock.php';
			$pageTitle = 'ตรวจนับสินค้า';
			break;

		default:
			$content = 'main.php';
			$pageTitle = 'Check stock';
			break;
}

require_once 'template.php';

?>
