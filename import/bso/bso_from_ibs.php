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
//Сопастовляем серию написанную русскими буквами в системе IBS с серией написанной английскими буквами в  сервисе осаго
$series_name = array(
  'ААА' => 'AAA',
  'ВВВ' => 'BBB',
  'ССС' => 'CCC',
  'ЕЕЕ' => 'EEE',
  'ННН' => 'HHH',
  'ККК' => 'KKK', 
);
while($row = mysql_fetch_assoc($query_ibs_id_agent)){
  $oracle_sql = oci_parse($conn, "
  select bht.name, bh.hist_date, bs.series_name, b.num bso_num
    from bso_document bd, 
         bso_doc_cont bdc,
         ins.bso      b,
         ins.bso_hist bh,
         bso_series   bs,
         bso_hist_type bht
    where b.bso_id = bh.bso_id
      and b.num >= bdc.num_start
      and (b.num <= bdc.num_end or bdc.num_end is null) 
      and bh.hist_type_id=bht.bso_hist_type_id
      and bh.bso_doc_cont_id = bdc.bso_doc_cont_id
      and bd.bso_document_id = bdc.bso_document_id
      --and bd.bso_document_id =d.document_id
      and b.bso_series_id = bs.bso_series_id
      and bdc.bso_series_id = b.bso_series_id 
      and bs.bso_type_id = 1 --Осаго
      and bd.contact_to_id = '".$row['id_in_ibs']."' --AGENT_ID
      and bh.hist_type_id = 1 -- статус полиса выдан
      and b.bso_hist_id = bh.bso_hist_id
      and bh.num = (select max(bh1.num)
                      from ins.bso_hist bh1
                     where bh1.bso_id = b.bso_id)
          ");
  oci_execute($oracle_sql);
  while (oci_fetch($oracle_sql)){
  	$series = iconv('windows-1251', 'utf-8', oci_result($oracle_sql,"SERIES_NAME"));
    $series = $series_name["$series"];
    $number = oci_result($oracle_sql,"BSO_NUM");
  	// echo '<br>';
    if(mysql_num_rows(mysql_query("SELECT * FROM `bso` WHERE `series` = '".$series."' AND `number` = '".$number."'")) == 0 && mysql_num_rows(mysql_query("SELECT * FROM `contract` WHERE `bso_series` = '".$series."' AND `bso_number` = '".$number."'")) == 0 ){
      if(mysql_query("INSERT INTO `bso` (`series`, `number`, `user_id`) VALUES ('".$series."', '".$number."', '".$row['user_id']."')")){
        //echo 'Успешно добавлен';
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
