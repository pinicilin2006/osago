<?php
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
//$sql  = OCIParse($conn, "begin DBMS_APPLICATION_INFO.SET_ACTION(action_name => 'RP".substr(getcwd(),33).'|'.$_POST['user_name']."'); end;");
//OCIExecute($sql, OCI_DEFAULT);
//$sql  = OCIParse($conn, "begin ins.safety.set_rls_status(0); end;");
//OCIExecute($sql, OCI_DEFAULT);
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
?>




