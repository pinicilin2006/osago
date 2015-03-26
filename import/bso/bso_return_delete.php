<?php
//Удаляем возвращённые полисы из ОСАГО
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
$query_bso = mysql_query("SELECT `bso`.*, `user`.`id_in_ibs` FROM `user`, `bso` WHERE `unit_id` = 0 AND `bso`.`user_id` > 0 AND `user`.`user_id` = `bso`.`user_id` AND `user`.`id_in_ibs` > 0");
if(mysql_num_rows($query_bso) == 0){
  echo 'Не обнаруженно бланков БСО';
  exit();
}
//Сопастовляем серию написанную русскими буквами в системе IBS с серией написанной английскими буквами в  сервисе осаго
// Серия на русском => серия на английском
$series_name = array(
  'ААА' => 'AAA',
  'ВВВ' => 'BBB',
  'ССС' => 'CCC',
  'ЕЕЕ' => 'EEE',
  'ННН' => 'HHH',
  'ККК' => 'KKK', 
);
//Обратный массив
//серия на ангийском => серия на русском
$series_name_reverse = array_flip($series_name);

while($row = mysql_fetch_assoc($query_bso)){
  $series = $series_name_reverse["$row[series]"];
  $series = iconv('utf-8', 'windows-1251', $series);
//В этой част проверяем есть ли в базе IBS полис с таким номером у данного пользователя и с такой же серией в статусе выдан 
  $oracle_sql_count = oci_parse($conn, "
  select count(*) AS NUM_ROWS
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
      and b.num = '".$row['number']."'
      and bs.series_name = '".$series."'
      and bh.hist_type_id = 1 -- статус полиса выдан
      and b.bso_hist_id = bh.bso_hist_id
      and bh.num = (select max(bh1.num)
                      from ins.bso_hist bh1
                     where bh1.bso_id = b.bso_id)
          ");
  oci_define_by_name($oracle_sql_count, 'NUM_ROWS', $num_rows);
  oci_execute($oracle_sql_count);
  oci_fetch($oracle_sql_count);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if($num_rows == 0){
    mysql_query("DELETE FROM `bso` WHERE `number` = '".$row['number']."' AND `series` = '".$row['series']."'");
    echo 'Удалён БСО №'.$row['number'];
  }
  //oci_free_statement($oracle_sql_count);
  //oci_close($oracle_sql_count);
}
?>
