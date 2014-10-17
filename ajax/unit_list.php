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
$query = mysql_query("SELECT * FROM `unit` WHERE `unit_parent_id` ='".$id."'");
$return = "<ul>";
while($row = mysql_fetch_assoc($query)){
	$return .="<li>";

	$return .= $row["unit_full_name"];
	if(mysql_num_rows(mysql_query("SELECT * FROM `user_unit` WHERE `unit_id` ='".$row["unit_id"]."'")) > 0){
		$return .=" <span list_id=\"".$row["unit_id"]."\" class=\"list_user glyphicon glyphicon-user\"></span> ";
	}	
	if(mysql_num_rows(mysql_query("SELECT * FROM `unit` WHERE `unit_parent_id` ='".$row["unit_id"]."'")) > 0){
		$return .=" <span id=\"".$row["unit_id"]."\" style=\"font-size:8px;top:0px\" class=\"plus glyphicon glyphicon-plus\"></span><div id=\"message_".$row["unit_id"]."\"></div>";
	}
	$return .="</li>";
}
$return .="</ul>";
echo $return;
?>
