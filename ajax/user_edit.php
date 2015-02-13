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
	if($key == 'rights' || empty($val)){
		continue;
	}
	$$key = mysql_escape_string($val);
	//echo $key."<br>";
}
$err_text = '';
if(!$user){
	$err_text .= "<li class=\"text-danger\">Не указан id пользователя</li>";
}
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
if(!$phone && !$email){
	$err_text .= "<li class=\"text-danger\">Не указан номер телефона и электронный адрес</li>";
}
if(!$login){
	$err_text .= "<li class=\"text-danger\">Не указан логин</li>";
}
if(isset($password) && (strlen($password) < 6 || !preg_match("/([0-9]+)/", $password) || !preg_match("/([a-zA-Z]+)/", $password))){
	$err_text .= "<li class=\"text-danger\">Пароль должен содержать минимум 6 символов, включающих в себя букву на английском языке и одну цифру<br>";
}
if(!$unit){
	$err_text .= "<li class=\"text-danger\">Не указано подразделение</li>";
}
if(!$_POST["rights"]){
	$err_text .= "<li class=\"text-danger\">Не указаны права пользователя</li>";	
}
if($phone && mysql_num_rows(mysql_query("SELECT * FROM `user` WHERE `phone` = '".$phone."' AND `user_id` <> '".$user."'"))>0){
	echo "<br><p class=\"text-danger text-center\">Пользователь с таким телефоном уже имеется в базе данных!</p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();	
}
if($email && mysql_num_rows(mysql_query("SELECT * FROM `user` WHERE `email` = '".$email."'AND `user_id` <> '".$user."'"))>0){
	echo "<br><p class=\"text-danger text-center\">Пользователь с таким email уже имеется в базе данных!</p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();	
}
if(!empty($err_text)){
	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();
}
if(mysql_num_rows(mysql_query("SELECT * FROM `user` WHERE `login` = '".$login."' AND `user_id` <> '".$user."'"))>0){
	echo "<br><p class=\"text-danger text-center\">Логин занят!</p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();	
}
if(mysql_num_rows(mysql_query("SELECT * FROM `user` WHERE `first_name` = '".$first_name."' AND `second_name` = '".$second_name."' AND `third_name` = '".$third_name."' AND `date_birth` = '".$date_birth."' AND `sex` = '".$sex."' AND `user_id` <> '".$user."'"))>0){
	echo "<br><p class=\"text-danger text-center\">Пользователь с такими данными уже имеется в базе данных пользователей.</p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();	
}
if(!$active){
	$active = 0;
}
if(!$email){
	$email = '';
}
if($password){
	$password_hash = password_hash($password, PASSWORD_DEFAULT);
}
if(mysql_query("UPDATE `user` SET `login` = '".$login."',`first_name` = '".$first_name."',`second_name` = '".$second_name."',`third_name` = '".$third_name."',`email` = '".$email."',`date_birth` = '".$date_birth."',`sex` = '".$sex."',`active` = '".$active."',`phone` = '".$phone."'".(isset($password) ? ",`password` = '".$password_hash."'" : '')." WHERE `user_id` = '".$user."'")){
//if(mysql_query("INSERT INTO `user` (login,password,first_name,second_name,third_name,email,date_birth,sex,phone,active,who_added) VALUES('".$login."','".$password_hash."','".$first_name."','".$second_name."','".$third_name."','".$email."','".$date_birth."','".$sex."','".$phone."','".$active."','".$_SESSION["user_id"]."')")){
	//Првоерка на изменение логина
	$user_old_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `user_id` = '".$user."'"));
	//Отправка смс с данными для доступа
	$message_sms = '';
	if($user_old_data["login"] != $login){
		$message_sms .= ' Логин:'.$login;
	}
	if(isset($password)){
		$message_sms .= ' Пароль:'.$password;
	}
	if(!empty($message_sms)){
	$message_sms = 'Изменились данные для доступа в сервис ОСАГО.'.$message_sms;	
	$header="Content-type:text/plain;charset=windows-1251\r\n";
	$header.="From: it@sngi.ru\r\n";
	$phone = str_replace(array(")","(","-"),'',$user_old_data['phone']);
	$message = 'UserLogin=SURGUTNEFTEGAS2
	Password=1q2w3e
	SourceAddress=SNGI
	PhoneNumber=+7'.$phone.'
	'.$message_sms;
	$message = iconv('utf-8', 'windows-1251', $message);
	mail('smsgate@sngi.ru','Sms',$message,$header);
	//mail('husainov_aa@sngi.ru','Sms',$message,$header);		
	}	
	$user_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `login` = '".$login."'"));
	$user_id = $user_data["user_id"];
	mysql_query("DELETE FROM `user_rights` WHERE `user_id` = '".$user_id."'");
	mysql_query("DELETE FROM `user_unit` WHERE `user_id` = '".$user_id."'");
	mysql_query("INSERT INTO `user_unit` (user_id,unit_id) VALUES('".$user_id."','".mysql_real_escape_string($unit)."')");
	foreach ($_POST["rights"] as $key => $value) {
		mysql_query("INSERT INTO `user_rights` (user_id,rights) VALUES('".$user_id."','".mysql_real_escape_string($value)."')");
	}
	echo "<br><p class=\"text-success text-center\">Пользователь успешно изменён. <br> Логин <strong>$login</strong>";
	echo (isset($password) ? "<br>Пароль <strong>$password</strong></p>" : '');
} else {
	echo "<p class=\"text-danger\">Произошла ошибка при изменение пользователя в базе данных!</p>";
}
?>

