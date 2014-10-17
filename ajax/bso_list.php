<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// exit();
require_once('../config.php');
require_once('../function.php');
connect_to_base();

foreach ($_POST as $key => $value) {
	$$key = mysql_real_escape_string($value);
}
// $query = "SELECT * FROM `bso` WHERE ".(isset($unit) ? "`unit_id` = $unit" : "`user_id` = $user_id");
// echo $query;
// exit();
$return = '';
$query = mysql_query("SELECT * FROM `bso` WHERE ".(isset($unit) ? "`unit_id` = $unit" : "`user_id` = $user_id"));
if(mysql_num_rows($query)>0){
	$return = '<hr><p class="lead">'.(isset($unit) ? 'БСО подразделения' : 'БСО пользователя').'</p><table class="table table-hover table-condensed table-bordered"><thead><tr><td class="text-center">#</td><td class="text-center">Номер бланка</td><td class="text-right">Отметить все <input type="checkbox" id="select_all"></td></tr></thead><tbody>';
	$n = 0;
	while($row=mysql_fetch_assoc($query)){
		$n++;
		$return .= "<tr><td class=\"text-center\">".$n."</td><td class=\"text-center\">".$row["number"]."</td><td class=\"text-right\"><input type=\"checkbox\" name=\"bso_number[]\" class=\"bso\" value=\"".$row["number"]."\"></td></tr>";
	}
	$return .= "</tbody></table>";
}
echo $return;
?>
