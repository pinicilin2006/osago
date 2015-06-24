<?php
//Импортируем людей, со статусом VIP из IBS
require_once('../../config.php');
require_once('../../function.php');
include("../../ibs_connector_2.php");
connect_to_base();
  	$oracle_sql = oci_parse($conn, "
	select c.name,
	       c.first_name,
	       c.middle_name,
	       cp.date_of_birth,
	       c.obj_name_orig,
	       st.name as status_name,
	       st.t_contact_status_id as status_id
	  from ins.contact c,
	       ins.cn_person cp,
	       ins.t_contact_status st
	 where c.contact_id = cp.contact_id
	   and c.t_contact_status_id = st.t_contact_status_id
	   and c.t_contact_status_id = 2
	   and cp.date_of_birth is not null
    ");
oci_execute($oracle_sql);
while (oci_fetch($oracle_sql)){
	$first_name = oci_result($oracle_sql,"FIRST_NAME");
	$second_name = oci_result($oracle_sql,"NAME");
	$third_name = oci_result($oracle_sql,"MIDDLE_NAME");
	$date_of_birth = oci_result($oracle_sql,"DATE_OF_BIRTH");
	if(mysql_num_rows(mysql_query("SELECT * FROM `vip_people` WHERE `first_name` = '".$first_name."' AND `second_name` = '".$second_name."' AND `third_name` = '".$third_name."' AND `date_of_birth` = '".$date_of_birth."'")) == 0){
		mysql_query("INSERT INTO `vip_people` (first_name,second_name,third_name,date_of_birth) VALUES ('".$first_name."','".$second_name."','".$third_name."','".$date_of_birth."')");
	}
}
?>
