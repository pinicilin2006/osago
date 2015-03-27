<?php
//Удаляем возвращённые полисы из ОСАГО для Юр лиц
//Статусы полисов в IBS
// 246              Списан 
// 226              Устарел 
// 10               Зарезервирован  
// 1                Выдан 
// 2                Использован
// 3                Не выдан 
// 4               Утерян
// 5             Испорчен
// 7                Уничтожен
// 6                Похищен
// 8                Передан
// 266              Утратил силу 
include("../../config.php");
include("../../function.php");
include("../../ibs_connector.php");
connect_to_base();
$query_a7 = mysql_query("SELECT `a7`.*, `unit`.`id_in_ibs` FROM `unit`, `a7` WHERE `a7`.`user_id` = 0 AND `a7`.`unit_id` > 0 AND `unit`.`unit_id` = `a7`.`unit_id` AND `unit`.`id_in_ibs` > 0");
if(mysql_num_rows($query_a7) == 0){
  echo 'Не обнаруженно бланков А7';
  exit();
}

while($row = mysql_fetch_assoc($query_a7)){
//В этой част проверяем есть ли в базе IBS полис с таким номером у данного пользователя и с такой же серией в статусе выдан 
  $oracle_sql_count = oci_parse($conn, "
  select count(*) AS NUM_ROWS
     from bso_document bd,
          bso_doc_cont bdc,
          ins.bso      b,
          bso_series   bs,
          ins.bso_hist bh
     where b.bso_id = bh.bso_id
       and b.bso_hist_id = bh.bso_hist_id
       and b.num >= bdc.num_start
       and (b.num <= bdc.num_end or bdc.num_end is null)
       and bh.bso_doc_cont_id = bdc.bso_doc_cont_id
       and bd.bso_document_id = bdc.bso_document_id
       and b.bso_series_id = bs.bso_series_id
       and bdc.bso_series_id = bs.bso_series_id
       and b.num = '".$row['number']."'
       and bs.bso_type_id = 3 --А7
       and bd.contact_to_id = ".$row['id_in_ibs']." --AGENT_ID
       and bh.hist_type_id = 1 -- статус полиса выдан
       and bh.num = (select max(bh1.num)
                       from ins.bso_hist bh1
                      where bh1.bso_id = b.bso_id)
          ");
  oci_define_by_name($oracle_sql_count, 'NUM_ROWS', $num_rows);
  oci_execute($oracle_sql_count);
  oci_fetch($oracle_sql_count);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($num_rows == 0){
    mysql_query("DELETE FROM `a7` WHERE `number` = '".$row['number']."'");
    echo 'Удалён А7 №'.$row['number'];
  }
  //oci_free_statement($oracle_sql_count);
  //oci_close($oracle_sql_count);
}
?>
