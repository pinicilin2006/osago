<?php
session_start();
if(!isset($_SESSION['user_id']) || !isset($_SESSION["access"][11])){
	header("Location: login.php");
	exit;
}
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// echo "<br><p class=\"text-danger text-center\">Логин занят!</p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
// exit();
require_once('../config.php');
require_once('../function.php');
//require_once('../template/header.html');
connect_to_base();
$err_text='';
foreach($_POST as $key => $val){
	$$key = mysql_escape_string($val);
}
$err_text = '';
if(!$news){
	$err_text .= "<li class=\"text-danger\">Отсутствует текст новости</li>";
}
if(!empty($err_text)){
	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();
}
//Добавляем в базу данных
if(mysql_query("INSERT INTO `news` (text,who_add) VALUES('".$news."','".$_SESSION["user_id"]."')")){
	echo "<p class=\"text-success text-center\">Новость успешно добавленна</p>";
}else{
	echo "<p class=\"text-danger text-center\">Произошла ошибка при добавление новости</p>";
}
//echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";		
exit();

?>