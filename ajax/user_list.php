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
$query = mysql_query("SELECT * FROM user, user_unit WHERE user_unit.unit_id = '".$id."' AND user_unit.user_id = user.user_id ORDER BY `second_name`");
$return = "<div class=\"panel panel-danger\"><div class=\"panel-heading\">Пользователи подразделения</div><div class=\"panel-body\">";
$return .= "<ol class=\"list-group\">";
$i = 0;
while($row = mysql_fetch_assoc($query)){
	$i++;
	$return .="<li class=\"list-group-item\">";
	$return .='<span class="rights" data-toggle="popover" data-trigger="hover focus" data-placement="top" data-html="true" data-title="<center>Права пользователя:</center>" data-content="<ol>';
	$query_rights = mysql_query("SELECT * FROM `rights`,`user_rights` WHERE user_rights.rights = rights.id AND user_id = '".$row['user_id']."'");
	while ($rights = mysql_fetch_assoc($query_rights)) {
		$return .="<li>".$rights['name']."</li>";
	}
	$return .= '</ol>">';
	$return .= $i.". ".$row["second_name"].' '.$row["first_name"].' '.$row["third_name"].'</span> <form style="display:inline" method="post" action="/user_edit.php"><input type="hidden" name="user" value='.$row['user_id'].'><button type="submit" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-pencil"></span></button></form>';
	$return .="</li>";
}
$return .="</ol></div>";
echo $return;
?>


