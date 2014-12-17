<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
require_once('../config.php');
require_once('../function.php');
//require_once('../template/header.html');
connect_to_base();
$err_text='';
foreach($_POST as $key => $val){
	if($key == 'rights'){
		continue;
	}
	$$key = mysql_escape_string($val);
	//echo $key."<br>";
}
$err_text = '';
if(!$first_name){
	$err_text .= "<li class=\"text-danger\">Не указано имя</li>";
}
if(!$second_name){
	$err_text .= "<li class=\"text-danger\">Не указана фамилия</li>";
}
if(!$third_name){
	$err_text .= "<li class=\"text-danger\">Не указано отчество</li>";
}
if(!$date_birth){
	$err_text .= "<li class=\"text-danger\">Не указана дата рождения</li>";
}
if(!$sex){
	$err_text .= "<li class=\"text-danger\">Не указан пол</li>";
}
if(!$phone){
	$err_text .= "<li class=\"text-danger\">Не указан номер телефона</li>";
}
if(!$login){
	$err_text .= "<li class=\"text-danger\">Не указан логин</li>";
}
if(!$password){
	$err_text .= "<li class=\"text-danger\">Не указан пароль</li>";
}
if(isset($password) && (strlen($password) < 6 || !preg_match('#^[A-Za-z0-9]+$#', $password))){
	$err_text .= "<li class=\"text-danger\">Пароль должен содержать минимум 6 символов, включающих в себя большую маленькую букву на английском языке и одну цифру<br>";
}
if(!$filial){
	$err_text .= "<li class=\"text-danger\">Не указан филиал</li>";
}
if(!$unit){
	$err_text .= "<li class=\"text-danger\">Не указано подразделение</li>";
}
if(!$_POST["rights"]){
	$err_text .= "<li class=\"text-danger\">Не указаны права пользователя</li>";	
}
if(!empty($err_text)){
	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();
}
if(mysql_num_rows(mysql_query("SELECT * FROM `user` WHERE `login` = '".$login."'"))>0){
	echo "<br><p class=\"text-danger text-center\">Логин занят!</p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();	
}
if(mysql_num_rows(mysql_query("SELECT * FROM `user` WHERE `first_name` = '".$first_name."' AND `second_name` = '".$second_name."' AND `third_name` = '".$third_name."' AND `date_birth` = '".$date_birth."' AND `sex` = '".$sex."'"))>0){
	echo "<br><p class=\"text-danger text-center\">Пользователь с такими данными уже имеется в базе данных пользователей.</p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();	
}
if(!$active){
	$active = 0;
}
if(!$email){
	$email = '';
}
$password_hash = password_hash($password, PASSWORD_DEFAULT);
if(mysql_query("INSERT INTO `user` (login,password,first_name,second_name,third_name,email,date_birth,sex,phone,active,who_added) VALUES('".$login."','".$password_hash."','".$first_name."','".$second_name."','".$third_name."','".$email."','".$date_birth."','".$sex."','".$phone."','".$active."','".$_SESSION["user_id"]."')")){
	$user_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `login` = '".$login."'"));
	$user_id = $user_data["user_id"];
	mysql_query("INSERT INTO `user_unit` (user_id,unit_id) VALUES('".$user_id."','".mysql_real_escape_string($unit)."')");
	foreach ($_POST["rights"] as $key => $value) {
		mysql_query("INSERT INTO `user_rights` (user_id,rights) VALUES('".$user_id."','".mysql_real_escape_string($value)."')");
	}
	echo "<br><p class=\"text-success text-center\">Пользователь <strong>$second_name $first_name $third_name</strong> успешно добавлен. <br> Логин <strong>$login</strong><br>Пароль <strong>$password</strong></p>";
} else {
	echo "<p class=\"text-danger\">Произошла ошибка при добавление пользователя в базу данных!</p>";
}
?>
