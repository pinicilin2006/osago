<?php
include("../../config.php");
include("../../function.php");
include("../../ibs_connector.php");
connect_to_base();
$query = mysql_query("SELECT * FROM `user` WHERE `id_in_ibs` = '0'");
if(mysql_num_rows($query) == 0){
  echo 'Не обнаруженно в базе данных агентов с пустым полем id_in_ibs';
  exit();
}
while($row = mysql_fetch_assoc($query)){
  $oracle_sql = oci_parse($conn, "
    select distinct c.contact_id, c.obj_name_orig
 	from contact c
	where c.contact_id in (select h.agent_id from ag_contract_header h)
  	and c.contact_type_id = 3
    and c.obj_name_orig = '".iconv('utf-8', 'windows-1251', trim($row['second_name']).' '.trim($row['first_name']).' '.trim($row['third_name']))."'  
  ");
  oci_execute($oracle_sql);
  while (oci_fetch($oracle_sql)){
    mysql_query("UPDATE `user` SET `id_in_ibs` = '".oci_result($oracle_sql,"CONTACT_ID")."' WHERE `user_id` = '".$row['user_id']."'");
  }   
}
?>
