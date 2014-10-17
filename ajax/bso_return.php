<?php
session_start();
if(!isset($_SESSION['user_id'])){
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

if(!isset($_POST["bso_number"])){
	$err_text .= "<li class=\"text-danger\">Не указан номер БСО</li>";
}
if(isset($_POST["bso_number"])){
	foreach ($_POST["bso_number"] as $key => $value) {
		if(mysql_num_rows(mysql_query("SELECT * FROM `bso` WHERE `number` = '".$value."'"))<1){
		$err_text .= "<li class=\"text-danger\">Ошибка! БСО №".$x." отсутствует в базе данных.</li>";
		}	
	}
}
if(!empty($err_text)){
	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();
}
//Добавляем в базу данных
$n = 0;//количество успешно добавленных бсо
//Если не диапазон
	foreach ($_POST["bso_number"] as $key => $value) {
		if(mysql_query("DELETE FROM `bso` WHERE `number` = '".$value."'")){
			echo "<p class=\"text-success text-center\">Бланк №".$value." успешно удалён из списка доступных бланков</p>";
			$n++;
		}else{
			echo "<p class=\"text-danger text-center\">Бланк №".$value." не удалён из списка доступных БСО</p>";
		}	
	}
	echo "<p class=\"text-success text-center\">Всего удаленно бланков - ".$n."</p>";
	exit();

?>