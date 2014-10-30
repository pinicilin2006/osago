<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}
if(!isset($_POST['mark'])){
	echo '';
	exit;
}
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// exit();
require_once('../config.php');
require_once('../function.php');
connect_to_base();

$mark = mysql_real_escape_string($_POST["mark"]);
$return = '';
$query = mysql_query("SELECT * FROM `model` WHERE `rsa_mark_id` = '".$mark."' ORDER BY `name`");
if(mysql_num_rows($query)>0){
	$return .='<div><select class="form-control input-sm" name="model" id="model" required><option value="" disabled selected>Выберите модель</option>';
	while ($row = mysql_fetch_assoc($query)) {
		$return .= '<option value='.$row["rsa_model_id"].'>'.$row["name"].'</option>';
	}
	$return .='</select></div>';
}
echo $return;
?>

