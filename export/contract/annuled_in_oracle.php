<?php
require_once('../../config.php');
require_once('../../function.php');
include("../../ibs_connector_2.php");
connect_to_base();
$query_all_contract = mysql_query("SELECT * FROM `contract` WHERE `project` = '0' AND `annuled` = '1'");
	while($row = mysql_fetch_assoc($query_all_contract)){
		$query_in_oracle = "UPDATE export_table SET annuled = 1 WHERE md5_id = '".$row['md5_id']."'";
		$query_in_oracle = oci_parse($conn, $query_in_oracle);
		if(oci_execute($query_in_oracle)){
			echo 'Изменён статус договора '.$row['md5_id'];
		}	
	}
?>
