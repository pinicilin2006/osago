<?php
//Импортируем людей, страхование которых запрещенно из IBS
require_once('../../config.php');
require_once('../../function.php');
include("../../ibs_connector_2.php");
connect_to_base();
  	$oracle_sql = oci_parse($conn, "
  		select * from export_table_1c_badpeople
    ");
oci_execute($oracle_sql);
while (oci_fetch($oracle_sql)){
	$first_name = oci_result($oracle_sql,"FIRST_NAME");
	$second_name = oci_result($oracle_sql,"SECOND_NAME");
	$third_name = oci_result($oracle_sql,"THIRD_NAME");
	$date_of_birth = oci_result($oracle_sql,"B_DATE");
	if(mysql_num_rows(mysql_query("SELECT * FROM `bad_people` WHERE `first_name` = '".$first_name."' AND `second_name` = '".$second_name."' AND `third_name` = '".$third_name."' AND `date_of_birth` = '".$date_of_birth."'")) == 0){
		mysql_query("INSERT INTO `bad_people` (first_name,second_name,third_name,date_of_birth) VALUES ('".$first_name."','".$second_name."','".$third_name."','".$date_of_birth."')");
	}
}
?>
