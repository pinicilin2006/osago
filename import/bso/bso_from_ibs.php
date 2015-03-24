<?php
include("../../config.php");
include("../../ibs_connector.php");
//Статусы полисов в IBS
// 246              Списан 
// 226              Устарел 
// 10               Зарезервирован  
// 1                Выдан 
// 2                Использован
// 3                Не выдан 
// 4        	     Утерян
// 5           	 Испорчен
// 7                Уничтожен
// 6                Похищен
// 8                Передан
// 266              Утратил силу 

$SQL = oci_parse($conn, "
select  d.num, bht.name, bh.hist_date,bs.series_name, b.num bso_num, c.obj_name_orig
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
   and bh.hist_type_id = 2 -- передан
   and bt.bso_type_id = 1 --Осаго
   and b.bso_series_id = bs.bso_series_id
   and bs.bso_type_id = bt.bso_type_id
   and bh.num = (select max(bh1.num)
                   from ins.bso_hist bh1
                  where bh1.bso_id = b.bso_id)
   --and bs.series_name='ЕЕЕ' --Серия
   and c.obj_name_orig='".iconv('utf-8', 'windows-1251', 'Антипина Галина Михайловна')."' --ФИО Агента
   --and b.num='0181990878' --НОмер бланка
   --and c.contact_id = 119717 --AGENT_ID    
        ");
//echo $SQL;
//var_dump($SQL);
//echo 'asdasdasd';
oci_execute($SQL);
while (oci_fetch($SQL)){
	echo iconv('windows-1251', 'utf-8', oci_result($SQL,"SERIES_NAME")).' '.oci_result($SQL,"BSO_NUM");
	echo '<br>';
} 

?>
