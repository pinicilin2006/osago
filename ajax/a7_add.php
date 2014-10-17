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
foreach($_POST as $key => $val){
	if($key == 'a7_number' || empty($val)){
		continue;
	}
	$$key = mysql_escape_string($val);
}
$err_text = '';
if(!$unit){
	$err_text .= "<li class=\"text-danger\">Не выбрано подразделение</li>";
}
if(!isset($a7_range_start) && !isset($a7_range_end) && !isset($_POST["a7_number"])){
	$err_text .= "<li class=\"text-danger\">Не указан номер бланка A7</li>";
}
if(isset($a7_range_start) && isset($a7_range_end)){
	if(empty($a7_range_start)){
		$err_text .= "<li class=\"text-danger\">Не указан номер начала диапазона БСО</li>";
	}
	if(empty($a7_range_end)){
		$err_text .= "<li class=\"text-danger\">Не указан номер окончания диапазона БСО</li>";
	}
	if($a7_range_end < $a7_range_start){
		$err_text .= "<li class=\"text-danger\">Диапазон указан неверно</li>";	
	}
	for($x = $a7_range_start;$x<=$a7_range_end;$x++){
		if(mysql_num_rows(mysql_query("SELECT * FROM `a7` WHERE `number` = '".$x."'"))>0){
		$err_text .= "<li class=\"text-danger\">Ошибка! Бланк А7 №".$x." был присвоен ранее.</li>";
		}		
	}
}
if(isset($_POST["a7_number"]) && empty($_POST["a7_number"])){
	$err_text .= "<li class=\"text-danger\">Не указан номер бланка А7</li>";
}
if(isset($_POST["a7_number"])){
	foreach ($_POST["a7_number"] as $key => $value) {
		if(mysql_num_rows(mysql_query("SELECT * FROM `a7` WHERE `number` = '".$value."'"))>0){
		$err_text .= "<li class=\"text-danger\">Ошибка! Бланк А7 №".$x." был присвоен ранее.</li>";
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
if(isset($_POST["a7_number"])){
	foreach ($_POST["a7_number"] as $key => $value) {
		if(mysql_query("INSERT INTO `a7` (number,unit_id,user_id,who_add) VALUES('".$value."','".(isset($user_id) ? '' : $unit)."','".(isset($user_id) ? $user_id : '')."','".$_SESSION["user_id"]."')")){
			echo "<p class=\"text-success text-center\">Бланк A7 №".$value." успешно добавлен</p>";
			$n++;
		}else{
			echo "<p class=\"text-danger text-center\">Бланк A7 №".$value." не добавлен</p>";
		}	
	}
	echo "<p class=\"text-success text-center\">Всего добавлено бланков - ".$n."</p>";
	exit();
}
//для диапазона
if(isset($a7_range_start) && isset($a7_range_end)){
	for($x = $a7_range_start;$x<=$a7_range_end;$x++){
		if(mysql_query("INSERT INTO `a7` (number,unit_id,user_id,who_add) VALUES('".$x."','".(isset($user_id) ? '' : $unit)."','".(isset($user_id) ? $user_id : '')."','".$_SESSION["user_id"]."')")){
			echo "<p class=\"text-success text-center\">Бланк №".$x." успешно добавлен</p>";
			$n++;
		}else{
			echo "<p class=\"text-danger text-center\">Бланк №".$x." не добавлен</p>";
		}			
	}
	echo "<p class=\"text-success text-center\">Всего добавлено бланков - ".$n."</p>";
	exit();	
}
?>