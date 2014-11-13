<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}
echo "<pre>";
print_r($_POST);
echo "</pre><br><pre>";
print_r($_SESSION);
echo "</pre>";
echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
exit();
require_once('../config.php');
require_once('../function.php');
//require_once('../template/header.html');
connect_to_base();
$err_text='';
if(!$_SESSION["step_1"]){
	$err_text .= "<li class=\"text-danger\">Отсутствуют данные для расчёта страховой премии</li>";
}
if(!$_SESSION["calc"]){
	$err_text .= "<li class=\"text-danger\">Отсутствует результат расчёта страховой премии</li>";
}
foreach($_POST as $key => $val){
	if(empty($val)){
		continue;
	}
	$$key = mysql_escape_string($val);
}
if(!$type_ins){
	$err_text .= "<li class=\"text-danger\">Не указан собственника ТС</li>";
}
if(!empty($err_text)){
	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();
}
?>



