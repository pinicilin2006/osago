<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// exit();
require_once('../config.php');
require_once('../function.php');
//require_once('../template/header.html');
connect_to_base();
$err_text='';
foreach($_POST as $key => $val){
	$$key = mysql_escape_string($val);
	//echo $key."<br>";
}
$err_text = '';
if(!$unit_full_name){
	$err_text .= "<li class=\"text-danger\">Не указано название подразделения</li>";
}
if(!$unit_city){
	$err_text .= "<li class=\"text-danger\">Не указан город подразделения </li>";
}

if(!$unit_address){
	$err_text .= "<li class=\"text-danger\">Не указан адрес подразделения</li>";
}

if(!$unit_parent_id){
	$err_text .= "<li class=\"text-danger\">Не указан родительский филиал</li>";
}

if(!empty($err_text)){
	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();
}
if(mysql_num_rows(mysql_query("SELECT * FROM `unit` WHERE `unit_full_name` = '".$unit_full_name."' AND `unit_city` = '".$unit_city."'"))>0){
	echo "<br><p class=\"text-danger text-center\">Ошибка!<br>Подразделение с названием \"<b>$unit_full_name</b>\" в городе <b>$unit_city</b> имеется в базе данных!</p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();	
}

if(!$active){
	$active = 0;
}

if(!$unit_parent_id){
	$unit_parent_id = 0;
}

if(!$id_in_ibs){
	$id_in_ibs = 0;
}
if(mysql_query("INSERT INTO `unit` (unit_full_name,unit_city,unit_address,unit_parent_id,id_in_ibs,active) VALUES('".$unit_full_name."','".$unit_city."','".$unit_address."','".$unit_parent_id."','".$id_in_ibs."','".$active."')")){
	//if($unit_parent_id == '1'){
		$id = mysql_insert_id();
		if(!mysql_query("INSERT INTO `unit` (unit_full_name,unit_city,unit_parent_id,active) VALUES('Физические лица','".$unit_city."','".$id."','1')")){
			echo "<p class=\"text-danger\">Произошла ошибка при добавление подразделения 'Физические лица'!</p>";
		}
	//}
	echo "<br><p class=\"text-success text-center\">Подразделение <b>$unit_full_name</b> успешно добавлено.";
} else {
	echo "<p class=\"text-danger\">Произошла ошибка при добавление подразделения в базу данных!</p>";
}
?>

