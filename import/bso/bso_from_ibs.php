<?php
include("../../config.php");
//include("../../function.php");
//connect_ibs();
//global $oracle_host, $oracle_sid, $oracle_port, $oracle_db_user, $oracle_db_psw;
	putenv("NLS_LANG=RUSSIAN_CIS.CL8MSWIN1251");
	$conn    = oci_connect($oracle_db_user, $oracle_db_psw, "(DESCRIPTION =
	                      (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)
	                      (HOST = $oracle_host)(PORT = $oracle_port)))
	                      (CONNECT_DATA = (SID = $oracle_sid)))","CL8MSWIN1251");
	if (!$conn)
	{
	        $e = oci_error();
	        trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	}
	$sql  = OCIParse($conn, "ALTER SESSION SET NLS_DATE_FORMAT='DD.MM.YYYY'");
	OCIExecute($sql, OCI_DEFAULT);
	$sql  = OCIParse($conn, "begin DBMS_APPLICATION_INFO.SET_ACTION(action_name => 'RP".substr(getcwd(),33).'|'.$_POST['user_name']."'); end;");
	OCIExecute($sql, OCI_DEFAULT);
	$sql  = OCIParse($conn, "begin ins.safety.set_rls_status(0); end;");
	OCIExecute($sql, OCI_DEFAULT);
	if (!function_exists("db_query"))
	{
	        function db_query($ASql, $AQueryType = 0)
	        {
	                global $conn;
	                $ASql = "/*".$_SERVER['REMOTE_ADDR'].", ".$_SERVER['SCRIPT_FILENAME'].", ".date('d.m.Y H:i', time())."*/".$ASql;
	                $result = oci_parse($conn, $ASql);
	                $return = array();
	                ociexecute($result, OCI_DEFAULT);
	                switch ($AQueryType)
	                {
	                        case 0: $return = oci_fetch_array($result, OCI_ASSOC); break;
	                        case 1: while ($row = oci_fetch_array($result, OCI_ASSOC)) { $return[] = $row;} break;
	                }
	                return $return;
	        }
	}
#Форма 
$SQL = oci_parse($conn, "                        
--Запрос выбирает последний статус передан, выводит ФИО агента
select  d.num, bht.name, bh.hist_date,bs.series_name, b.num, c.obj_name_orig
  from ins.bso      b,
       bso_series   bs,
       bso_type     bt,
       ins.bso_hist bh,
       bso_doc_cont bdc,
       bso_document bd,
       bso_hist_type bht,
       document d,
       ins.contact  c
 where b.bso_id = bh.bso_id
   and bh.hist_type_id=bht.bso_hist_type_id
   and bh.bso_doc_cont_id = bdc.bso_doc_cont_id
   and bd.bso_document_id = bdc.bso_document_id
   and bd.bso_document_id=d.document_id
   and c.contact_id = bd.contact_to_id
   and bh.hist_type_id = 8 -- передан
   and bt.bso_type_id = 1 --Осаго
   and b.bso_series_id = bs.bso_series_id
   and bs.bso_type_id = bt.bso_type_id
   and bh.num = (select max(bh1.num)
                   from ins.bso_hist bh1
                  where bh1.bso_id = b.bso_id)
   --and bs.series_name='EEE' --Серия
   --and c.obj_name_orig='Яганцева Людмила Николаевна' --ФИО Агента
   --and b.num='0181990878' --НОмер бланка
   and c.contact_id = 93063 --AGENT_ID 
   ");
echo $SQL;
oci_execute($SQL);
//$CLAIM_ARRAY = oci_fetch_array($SQL, OCI_ASSOC+OCI_RETURN_NULLS);
while($row = oci_fetch_array($SQL, OCI_BOTH)){
echo '<pre>';
print_r($row);
echo '</pre>';
}      
?>
