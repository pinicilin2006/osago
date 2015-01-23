<?php
session_start();
if(!isset($_SESSION['user_id'])|| !isset($_GET["id"]) || empty($_GET["id"])){
	header("Location: /index.php");
	exit;
}
require_once('../config.php');
require_once('../function.php');
require_once('../template/header.html');
connect_to_base();
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
//массив с параметрами для замены в документе
$id = mysql_escape_string($_GET["id"]);
if(isset($_SESSION["access"][6])){
	$query = "SELECT * FROM `contract` WHERE `md5_id` = '".$id."' AND `unit_id` = '".$_SESSION["unit_id"]."' AND `bso_number` > 0 AND `a7_number` > 0";
}else{
	$query = "SELECT * FROM `contract` WHERE `md5_id` = '".$id."' AND `unit_id` = '".$_SESSION["unit_id"]."' AND `user_id` = '".$_SESSION["user_id"]."' AND `bso_number` > 0 AND `a7_number` > 0";
}
if(mysql_num_rows(mysql_query($query))<1){
	echo "<p class=\"text-danger text-center\">Договор с запрашиваемым id не найден в базе данных <br>либо<br> у проекта договора не внесенны номер БСО и бланка А7</p><meta http-equiv=\"Refresh\" content=\"5; url='/contract.php'\">";
	exit();
} else {
	$query = "UPDATE `contract` SET `project` = '0' WHERE `md5_id` = '".$id."'";
	if(mysql_query($query)){
		echo "<p class=\"text-success text-center\">Договор переведён в статус \"оформлен\"</p><meta http-equiv=\"Refresh\" content=\"3; url='/contract.php'\">";
		exit();		
	} else {
		echo "<p class=\"text-error text-center\">Произошла ошибка при изменение статуса договора в базе данных</p>";
		exit();		
	}
}
?>
