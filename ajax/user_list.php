<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
require_once('../config.php');
require_once('../function.php');
connect_to_base();
$id = mysql_real_escape_string($_POST["id"]);
$query = mysql_query("SELECT * FROM user, user_unit WHERE user_unit.unit_id = '".$id."' AND user_unit.user_id = user.user_id");
$return = "<div class=\"panel panel-danger\"><div class=\"panel-heading\">Пользователи подразделения</div><div class=\"panel-body\">";
$return .= "<ol class=\"list-group\">";
$i = 0;
while($row = mysql_fetch_assoc($query)){
	$i++;
	$return .="<li class=\"list-group-item\">";
	$return .= $i.". ".$row["second_name"].' '.$row["first_name"].' '.$row["third_name"];
	$return .="</li>";
}
$return .="</ol></div>";
echo $return;
?>
