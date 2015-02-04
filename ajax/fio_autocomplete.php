<?php
session_start();
if(!isset($_GET["term"]) || !isset($_SESSION['user_id'])){
	exit;
}
require_once('../config.php');
require_once('../function.php');
connect_to_base();
$name = htmlspecialchars($_GET["term"]); 
$query = mysql_query("SELECT * FROM `contact_phiz` WHERE second_name like '%".$name."%' AND `user_id` = '".$_SESSION['user_id']."' ORDER BY `second_name` ");
if(mysql_num_rows($query) == 0){
	exit;   
}
$result=array();
while ($row = mysql_fetch_array($query, MYSQL_ASSOC)) {
		$names["label"] = $row["second_name"].' '.$row["first_name"].' '.$row["third_name"];
		$names["value"] = $row["id"];
		//$names["value"] = $row["id"];
		array_push($result, $names);
    }
echo json_encode($result);
?>
