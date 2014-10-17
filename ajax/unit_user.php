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
$return = '';
$query = mysql_query("SELECT `user`.user_id,first_name,second_name,third_name FROM `user`,`user_unit` WHERE `user_unit`.`unit_id` ='".$id."' AND user.user_id = user_unit.user_id");
if(mysql_num_rows($query)>0){
	$return = "<select class=\"form-control\"  name=\"user_id\" id=\"user_id\"><option value=\"\">Выберите пользователя</option>";
	while($row=mysql_fetch_assoc($query)){
		$return .= "<option value=\"".$row["user_id"]."\">".$row["second_name"]." ".$row["first_name"]." ".$row["third_name"]."</option>";
	}
	$return .= "</select><p class=\"help-block\"><small class=\"text-danger\">Если не выбирать пользователя, то отображаются бланки <b>подразделения</b>.</small></p>";
}
echo $return;
?>
