<?php
session_start();
if(isset($_SESSION['user_id'])){
	header("Location: ../index.php");
	exit;
}
require_once('../config.php');
require_once('../function.php');
connect_to_base();
$login = mysql_real_escape_string($_POST["login"]);
$password = mysql_real_escape_string($_POST["password"]);
$data_user = mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `login` = '".$login."'"));
if((!$data_user) || (!password_verify($password, $data_user["password"]))){
	$_SESSION["login_error_message"] = 'Неверный логин или пароль!';
	header("Location: ../login.php");
	exit;	
} elseif ($data_user["active"] == 0) {
	$_SESSION["login_error_message"] = 'Учётная запись заблокирована!';
	header("Location: ../login.php");
	exit;	
} else {
$_SESSION = array();	
	foreach($data_user  as $key => $val){
		if($key == 'password' || $key == 'active' || $key == 'date_register' || $key == 'id_in_ibs' || $key == 'who_added'){
			continue;
		}
		$_SESSION[$key] = $val;
	}
$unit_data = mysql_fetch_assoc(mysql_query("SELECT * FROM user_unit, unit WHERE user_unit.unit_id = unit.unit_id AND user_unit.user_id = $data_user[user_id]"));
$query = mysql_query("SELECT * FROM `user_rights` WHERE `user_id` = '".$data_user["user_id"]."'");
while($row = mysql_fetch_assoc($query)){
	$_SESSION["access"][$row["rights"]] = 1;
}
$_SESSION["unit_name"] = $unit_data["unit_full_name"];
$_SESSION["unit_id"] = $unit_data["unit_id"];		
header("Location: ../index.php");
exit;		
}
?>

