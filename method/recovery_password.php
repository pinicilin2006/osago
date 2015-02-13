<?php
$status = '';
session_start();
if(isset($_SESSION['user_id'])){
	header("Location: ../index.php");
	exit;
}
if(isset($_SESSION['attempt'])){
		$_SESSION['attempt']++;
	} else {
		$_SESSION['attempt'] = 1;
	}
require_once('../config.php');
require_once('../function.php');
require_once('../template/header_login.html');
connect_to_base();	
if($_SESSION['attempt'] > 10){
	mysql_query("INSERT INTO `log_recovery` (ip, browser, status) VALUES ('".$_SERVER['REMOTE_ADDR']."', '".$_SERVER['HTTP_USER_AGENT']."', 'Превышенно количество попыток')");
	echo '<center><p class="text-danger">Количество попыток закончилось. Попробуйте позже.</p></center>';
	exit();
}

$err = '';
if(!$_POST['type_recovery']){
	$err .= '<p class="text-danger text-center">Не указанн тип восстановления!</p>';
}
if(!$_POST['phone'] && !$_POST['email']){
	$err .= '<p class="text-danger text-center">Необходимо указать либо номер телефона либо адрес электронной почты!</p>';
}
if($_POST['phone'] && $_POST['email']){
	$err .= '<p class="text-danger text-center">Необходимо указать либо номер телефона либо адрес электронной почты!</p>';
}
if($_POST['phone'] && !$_POST['email'] && mysql_num_rows(mysql_query("SELECT * FROM `user` WHERE `phone` = '".mysql_real_escape_string($_POST['phone'])."'"))<1){
	$err .= '<p class="text-danger text-center">Пользователь с данным номером телефона отсутствует!</p>';
	$status = 'Отсутствует телефон '.$_POST['phone'].' в базе данных';
}
if(!$_POST['phone'] && $_POST['email'] && mysql_num_rows(mysql_query("SELECT * FROM `user` WHERE `email` = '".mysql_real_escape_string($_POST['email'])."'"))<1){
	$err .= '<p class="text-danger text-center">Пользователь с данным электронным адресом отсутствует!</p>';
	$status = 'Отсутствует email '.$_POST['email'].' в базе данных';
}
if($err){
	mysql_query("INSERT INTO `log_recovery` (ip, browser, status) VALUES ('".$_SERVER['REMOTE_ADDR']."', '".$_SERVER['HTTP_USER_AGENT']."', '".$status."')");
	echo $err;
	echo '<p class="text-center"><a href="/recovery_password.php">Вернутся обратно</></p>';
	exit();
} else {
	unset($_SESSION['attempt']);
}
if($_POST['phone']){
	$phone = mysql_real_escape_string($_POST['phone']);
	$user_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `phone` = '".$phone."'"));
	$phone = str_replace(array(")","(","-"),'',$phone);
	$password = generate_password(6);
	$password_hash = password_hash($password, PASSWORD_DEFAULT);
	if(mysql_query("UPDATE `user` SET `password` = '".$password_hash."' WHERE `user_id` = '".$user_data['user_id']."'")){
		//Отправка смс с данными для доступа
		$header="Content-type:text/plain;charset=windows-1251\r\n";
		$header.="From: it@sngi.ru\r\n";
		$message = 'UserLogin=SURGUTNEFTEGAS2
		Password=1q2w3e
		SourceAddress=SNGI
		PhoneNumber=+7'.$phone.'
		Доступ в сервис ОСАГО https://osago.sngi.ru Логин: '.$user_data['login'].' Пароль: '.$password.'';
		$message = iconv('utf-8', 'windows-1251', $message);
		if(mail('smsgate@sngi.ru','Sms',$message,$header)){
			echo '<p class="text-success text-center">Данные отправленны на указанный номер телефона</p>';
			$status = 'Пароль отправлен на телефон';
		} else {
			echo '<p class="text-danger text-center">Произошла ошибка при отправке sms сообщения!</p>';
			$status = 'Пароль не отправлен на телефон';
		}
	} else {
		echo '<p class="text-danger text-center">Произошла ошибка при обновление данных</p>';
		$status = 'Произошла ошибка при обновление данных';
	}
}
if($_POST['email']){
	$email = mysql_real_escape_string($_POST['email']);
	$user_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `email` = '".$email."'"));
	$password = generate_password(6);
	$password_hash = password_hash($password, PASSWORD_DEFAULT);
	if(mysql_query("UPDATE `user` SET `password` = '".$password_hash."' WHERE `user_id` = '".$user_data['user_id']."'")){
		//Отправка смс с данными для доступа
		$header="Content-type:text/plain;charset=windows-1251\r\n";
		$header.="From: noreply@sngi.ru\r\n";
		$message = 'Доступ в сервис ОСАГО https://osago.sngi.ru Логин: '.$user_data['login'].' Пароль: '.$password.'';
		$message = iconv('utf-8', 'windows-1251', $message);
		if(mail($email,'ОСАГО',$message,$header)){
			echo '<p class="text-success text-center">Данные отправленны на указанный email</p>';
			$status = 'Пароль отправлен на электронный адрес';
		} else {
			echo '<p class="text-danger text-center">Произошла ошибка при отправке сообщения!</p>';
			$status = 'Пароль не отправлен на электронный адрес';
		}
	} else {
		echo '<p class="text-danger text-center">Произошла ошибка при обновление данных</p>';
		$status = 'Произошла ошибка при обновление данных';
	}
}
mysql_query("INSERT INTO `log_recovery` (ip, browser, login, status) VALUES ('".$_SERVER['REMOTE_ADDR']."', '".$_SERVER['HTTP_USER_AGENT']."', '".$user_data["user_id"]."', '".$status."')");
//echo 'OK';
exit();
?>

