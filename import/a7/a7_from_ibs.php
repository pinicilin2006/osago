<?php
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
$query_ibs_id_agent = mysql_query("SELECT * FROM `user` WHERE `id_in_ibs` > 0");
if(mysql_num_rows($query_ibs_id_agent) == 0){
  echo 'Не обнаруженно агентов с заполненым полем "id в системе ibs"';
  exit();
}

while($row = mysql_fetch_assoc($query_ibs_id_agent)){
  $oracle_sql = oci_parse($conn, "
   select b.num bso_num
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
       and bs.bso_type_id = 3 --А7
       and bd.contact_to_id = ".$row['id_in_ibs']." --AGENT_ID
       and bh.hist_type_id = 1 -- статус полиса выдан
       and bh.num = (select max(bh1.num)
                       from ins.bso_hist bh1
                      where bh1.bso_id = b.bso_id)
          ");
  oci_execute($oracle_sql);
  while (oci_fetch($oracle_sql)){
    $number = oci_result($oracle_sql,"BSO_NUM");
  	// echo '<br>';
    if(mysql_num_rows(mysql_query("SELECT * FROM `a7` WHERE `number` = '".$number."'")) == 0 && mysql_num_rows(mysql_query("SELECT * FROM `contract` WHERE  `a7_number` = '".$number."'")) == 0 ){
      if(mysql_query("INSERT INTO `a7` (`number`, `user_id`) VALUES ('".$number."', '".$row['user_id']."')")){
        //echo 'Успешно добавлен '.$number;
      } else {
        //echo 'Произошла ошибка';
      }
      // echo 'Полис серии '.$series.' №'.$number.' не обнаружен';
      // echo '<br>';
    } else {
      // echo 'Полис серии '.$series.' №'.$number.' обнаружен----------------------------------';
      // echo '<br>';     
    }
  } 
}
?>
