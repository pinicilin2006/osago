<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}
if(!isset($_POST)){
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
$return = '';
if($_POST["subject"] && !$_POST["aoid"]){
	$subject = mysql_real_escape_string($_POST["subject"]);
	$query = mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `regioncode` = '".$subject."' ORDER BY `formalname`");
	if(mysql_num_rows($query)>0){
		$return .='<div><select class="form-control input-sm" name="aoid" id="aoid" required><option value="" disabled selected>Район / город</option>';
		while ($row = mysql_fetch_assoc($query)) {
			$return .= '<option value='.$row["aoid"].'>'.$row["shortname"].'. '.$row["formalname"].'</option>';
		}
		$return .='</select></div><div id="message_1"></div>';
	}
}

if($_POST["aoid"]){
	$aoid = mysql_real_escape_string($_POST["aoid"]);
	$subject = mysql_real_escape_string($_POST["subject"]);
	$query = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoid` = '".$aoid."'"));
	if($query["aolevel"] == '3'){
		$query_city = mysql_query("SELECT * FROM `d_fias_addrobj_6` WHERE `regioncode` = '".$query["regioncode"]."' AND `areacode` = '".$query["areacode"]."' ORDER BY `formalname`");
		$return .='<div><select class="form-control input-sm" name="city" id="city" required><option value="" disabled selected>Населённый пункт</option>';
		while ($row = mysql_fetch_assoc($query_city)) {
			$return .= '<option value='.$row["aoid"].'>'.$row["shortname"].'. '.$row["formalname"].'</option>';
		}
		$return .='</select></div><div id="message_2"></div>';
	}
	if($query["aolevel"] == '4'){
		$query_city = mysql_query("SELECT * FROM `d_fias_addrobj_7` WHERE `regioncode` = '".$query["regioncode"]."' AND `areacode` = '".$query["areacode"]."' AND `citycode` = '".$query["citycode"]."' ORDER BY `formalname`");
		$return .='<div><select class="form-control input-sm" name="street" id="street" required><option value="" disabled selected>Улица</option>';
		while ($row = mysql_fetch_assoc($query_city)) {
			$return .= '<option value='.$row["aoguid"].'>'.$row["shortname"].'. '.$row["formalname"].'</option>';
		}
		$return .='</select></div><div id="message_3"></div>';
	}	
}
if($_POST["city"]){
	$city = mysql_real_escape_string($_POST["city"]);
	$query = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_6` WHERE `aoid` = '".$city."'"));
		$query_city = mysql_query("SELECT * FROM `d_fias_addrobj_7` WHERE `regioncode` = '".$query["regioncode"]."' AND `areacode` = '".$query["areacode"]."' AND `citycode` = '".$query["citycode"]."' AND `placecode` = '".$query["placecode"]."' ORDER BY `formalname`");
		$return .='<div><select class="form-control input-sm" name="street" id="street" required><option value="" disabled selected>Улица</option>';
		while ($row = mysql_fetch_assoc($query_city)) {
			$return .= '<option value='.$row["aoguid"].'>'.$row["shortname"].'. '.$row["formalname"].'</option>';
		}
		$return .='</select></div><div id="message_3"></div>';

}

echo $return;
?>

