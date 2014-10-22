<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}
if(!isset($_POST['subject'])){
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

$subject = mysql_real_escape_string($_POST["subject"]);
$return = '';
$query = mysql_query("SELECT * FROM `kt_city` WHERE `id_subject` = ".$subject."");
if(mysql_num_rows($query)>0){
	$return .='<div><select class="form-control input-sm" name="city" id="city" required><option value="" disabled selected>Город</option>';
	while ($row = mysql_fetch_assoc($query)) {
		$return .= '<option value='.$row["id"].'>'.$row["name"].'</option>';
	}
	$return .='</select></div>';
}
echo $return;
?>

