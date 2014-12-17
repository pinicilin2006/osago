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
if(mysql_num_rows($query)>0){
	$return .='<div><select class="form-control" name="unit" id="unit" required><option value="'.$id.'" selected>Сотрудник филиала</option>';
	while ($row = mysql_fetch_assoc($query)) {
		$return .= '<option value='.$row["unit_id"].'>'.$row["unit_full_name"].'</option>';
	}
	$return .='</select></div>';
}
echo $return;
?>
