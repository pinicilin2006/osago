<?php
session_start();
if(!isset($_GET["user_id"]) || !isset($_GET["owner"]) || !isset($_SESSION['user_id'])){
	exit;
}
require_once('../config.php');
require_once('../function.php');
connect_to_base();
$user_id = htmlspecialchars($_GET["user_id"]);
$owner = htmlspecialchars($_GET["owner"]);
$owner = $owner == 'yes' ? 'owner_' : '';
$query = mysql_query("SELECT * FROM `contact_phiz` WHERE id = '".$user_id."' AND `user_id` = '".$_SESSION["user_id"]."'");
if(mysql_num_rows($query) == 0){
	exit;   
}
$user_data = mysql_fetch_assoc($query);
$result = array();
$result[$owner."first_name"] = $user_data["first_name"];
$result[$owner."second_name"] = $user_data["second_name"];
$result[$owner."third_name"] = $user_data["third_name"];
$result[$owner."date_birth"] = $user_data["date_birth"];
$result[$owner."doc_name"] = $user_data["doc_name"];
$result[$owner."doc_series"] = $user_data["doc_series"];
$result[$owner."doc_number"] = $user_data["doc_number"];
$result[$owner."phone"] = $user_data["phone"];
$result[$owner."subject"] = $user_data["subject"];
$result[$owner."aoid"] = $user_data["aoid"];
$result[$owner."city"] = $user_data["city"];
$result[$owner."street"] = $user_data["street"];
$result[$owner."house"] = $user_data["house"];
$result[$owner."housing"] = $user_data["housing"];
$result[$owner."apartment"] = $user_data["apartment"];
echo json_encode($result);
?>

