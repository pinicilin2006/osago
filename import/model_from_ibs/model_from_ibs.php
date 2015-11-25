<?php
//Импортируем людей, со статусом VIP из IBS
require_once('../../config.php');
require_once('../../function.php');
include("../../ibs_connector_2.php");
connect_to_base();
  	$oracle_sql = oci_parse($conn, "
	select * from INS.T_MAIN_MODEL TMM
    ");
oci_execute($oracle_sql);
while (oci_fetch($oracle_sql)){
	$name = oci_result($oracle_sql,"NAME");
	$t_vehicle_mark_id = oci_result($oracle_sql,"T_VEHICLE_MARK_ID");
	$t_main_model_id = oci_result($oracle_sql,"T_MAIN_MODEL_ID");
	if(mysql_num_rows(mysql_query("SELECT * FROM `model` WHERE `name` = '".$name."' AND `t_main_model_id` = '".$t_main_model_id."'")) == 0){
		mysql_query("INSERT INTO `model` (name,t_vehicle_mark_id,t_main_model_id ) VALUES ('".$name."','".$t_vehicle_mark_id."','".$t_main_model_id."')");
	}
}
?>
