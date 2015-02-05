<?php
session_start();
if(!isset($_SESSION['user_id'])|| !isset($_GET["id"]) || empty($_GET["id"])){
	header("Location: /index.php");
	exit;
}
require_once('../config.php');
require_once('../function.php');
connect_to_base();
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
//массив с параметрами для замены в документе
$params = array();
$id = mysql_escape_string($_GET["id"]);
if(isset($_SESSION["access"][6])){
	$query = "SELECT * FROM `contract` WHERE `md5_id` = '".$id."'";
}else{
	$query = "SELECT * FROM `contract` WHERE `md5_id` = '".$id."' AND `unit_id` = '".$_SESSION["unit_id"]."' AND `user_id` = '".$_SESSION["user_id"]."'";
}
if(isset($_SESSION["access"][10])){
	$query = "SELECT * FROM `contract` WHERE `md5_id` = '".$id."'";
}
if(mysql_num_rows(mysql_query($query))<1){
	require_once('../template/header.html');
	echo "<p class=\"text-danger text-center\">Договор с запрашиваемым id не найден в базе данных</p>";
	exit();
}
$contract_data = mysql_fetch_assoc(mysql_query($query));
//Получаем данные страхователя
$insurer_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `".($contract_data["insurer_type"] == 1 ? "contact_phiz" : "contact_jur")."` WHERE `id` = '".$contract_data["insurer_id"]."'"));
//Данные по второму шагу оформления полиса
$step_2_data = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $contract_data['step_2_data']);//боримся с проблемой unserialize если есть кавычки
$step_2_data = unserialize($step_2_data);
//если физ лицо
if($contract_data["insurer_type"] == 1){
	$params['[NAME]'] = $insurer_data['second_name']." ".$insurer_data['first_name']." ".$insurer_data['third_name'];
	$params['[DATE_BIRTH]'] = $insurer_data['date_birth'];
	$params['[INN]'] = '------';
	//получаем название документа
	$name_document = mysql_fetch_assoc(mysql_query("SELECT * FROM `document` WHERE `id` = '".$insurer_data["doc_name"]."'"));
	$params['[NAME_DOCUMENT]'] = $name_document['name'];
	/////////////////////////////////////////////////////
	$params['[SERIES]'] = $insurer_data['doc_series'];	
	$params['[NUMBER]'] = $insurer_data['doc_number'];
	
	//получаем индекс и улицу
	$street_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_7` WHERE `aoguid` = '".$insurer_data["street"]."'"));
	$params['[INDEX]'] = $street_data['postalcode'];;
	$params['[STREET]'] = $street_data['shortname'].". ".$street_data['formalname'];
	//////////////////////////////////
	
	//Получаем населённый пункт и район
	if(!empty($insurer_data['city'])){
		$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_6` WHERE `aoid` = '".$insurer_data["city"]."'"));
	} else {
		$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoid` = '".$insurer_data["aoid"]."'"));
	}
	$params['[CITY]'] = $city_data["shortname"].'. '.$city_data['formalname'];
	///////////////////////////////////////////////////////
	//Получаем район
	if(!empty($insurer_data['city'])){
		$destrict_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoguid` = '".$city_data['parentguid']."'"));
		$params['[DISTRICT]'] = $destrict_data['shortname']." ".$destrict_data['formalname'];
	}else {
		$params['[DISTRICT]'] = '------';
	}
	//Получаем область
	$subject_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `kt_subject` WHERE `id_fias` = '".$insurer_data["subject"]."'"));
	$params['[SUBJECT]'] = $subject_data['name'];
	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$params['[HOUSE]'] = $insurer_data['house'];
	$params['[HOUSING]'] = (empty($insurer_data['housing']) ? '------' : $insurer_data['housing']);
	$params['[APARTMENT]'] = (empty($insurer_data['apartment']) ? '------' : $insurer_data['apartment']);
	$params['[PHONE]'] = $insurer_data['phone'];

}


//Если юридическое лицо
if($contract_data["insurer_type"] == 2){
	$params['[NAME]'] = $insurer_data['jur_name'];
	$params['[DATE_BIRTH]'] = '------';
	$params['[INN]'] = $insurer_data['jur_inn'];
	//получаем название документа
	$params['[NAME_DOCUMENT]'] = 'Cвидетельство о регистрации юридического лица';
	/////////////////////////////////////////////////////
	$params['[SERIES]'] = $insurer_data['jur_series'];	
	$params['[NUMBER]'] = $insurer_data['jur_number'];
	
	//получаем индекс и улицу
	$street_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_7` WHERE `aoguid` = '".$insurer_data["street"]."'"));
	$params['[INDEX]'] = $street_data['postalcode'];;
	$params['[STREET]'] = $street_data['shortname'].". ".$street_data['formalname'];
	//////////////////////////////////
	
	//Получаем населённый пункт и район
	if(!empty($insurer_data['city'])){
		$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_6` WHERE `aoid` = '".$insurer_data["city"]."'"));
	} else {
		$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoid` = '".$insurer_data["aoid"]."'"));
	}
	$params['[CITY]'] = $city_data["shortname"].'. '.$city_data['formalname'];
	///////////////////////////////////////////////////////
	//Получаем район
	if(!empty($insurer_data['city'])){
		$destrict_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoguid` = '".$city_data['parentguid']."'"));
		$params['[DISTRICT]'] = $destrict_data['shortname']." ".$destrict_data['formalname'];
	}else {
		$params['[DISTRICT]'] = '------';
	}
	//Получаем область
	$subject_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `kt_subject` WHERE `id_fias` = '".$insurer_data["subject"]."'"));
	$params['[SUBJECT]'] = $subject_data['name'];
	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$params['[HOUSE]'] = $insurer_data['house'];
	$params['[HOUSING]'] = (empty($insurer_data['housing']) ? '------' : $insurer_data['housing']);
	$params['[APARTMENT]'] = (empty($insurer_data['apartment']) ? '------' : $insurer_data['apartment']);
	$params['[PHONE]'] = $insurer_data['phone'];

}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$params['[START_DATE]'] = $contract_data['start_date']." г.";
$params['[END_DATE]'] = $contract_data['end_date']." г.";


//Данные по собственнику
$owner_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `".($contract_data["owner_type"] == 1 ? "contact_phiz" : "contact_jur")."` WHERE `id` = '".$contract_data["owner_id"]."'"));
//если физ лицо
if($contract_data["owner_type"] == 1){
	$params['[JUR_NAME]'] = '------';
	$params['[OWNER_NAME]'] = $owner_data['second_name']." ".$owner_data['first_name']." ".$owner_data['third_name'];
	$params['[OWNER_DATE_BIRTH]'] = $owner_data['date_birth'];
	$params['[OWNER_INN]'] = '------';
	//получаем название документа
	$name_document = mysql_fetch_assoc(mysql_query("SELECT * FROM `document` WHERE `id` = '".$owner_data["doc_name"]."'"));
	$params['[OWNER_NAME_DOCUMENT]'] = $name_document['name'];
	/////////////////////////////////////////////////////
	$params['[OWNER_SERIES]'] = $owner_data['doc_series'];	
	$params['[OWNER_NUMBER]'] = $owner_data['doc_number'];
	
	//получаем индекс и улицу
	$street_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_7` WHERE `aoguid` = '".$owner_data["street"]."'"));
	$params['[OWNER_INDEX]'] = $street_data['postalcode'];;
	$params['[OWNER_STREET]'] = $street_data['shortname'].". ".$street_data['formalname'];
	//////////////////////////////////
	
	//Получаем населённый пункт и район
	if(!empty($owner_data['city'])){
		$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_6` WHERE `aoid` = '".$owner_data["city"]."'"));
	} else {
		$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoid` = '".$owner_data["aoid"]."'"));
	}
	$params['[OWNER_CITY]'] = $city_data["shortname"].'. '.$city_data['formalname'];
	///////////////////////////////////////////////////////
	//Получаем район
	if(!empty($owner_data['city'])){
		$destrict_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoguid` = '".$city_data['parentguid']."'"));
		$params['[OWNER_DISTRICT]'] = $destrict_data['shortname']." ".$destrict_data['formalname'];
	}else {
		$params['[OWNER_DISTRICT]'] = '------';
	}
	//Получаем область
	$subject_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `kt_subject` WHERE `id_fias` = '".$owner_data["subject"]."'"));
	$params['[OWNER_SUBJECT]'] = $subject_data['name'];
	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$params['[OWNER_HOUSE]'] = $insurer_data['house'];
	$params['[OWNER_HOUSING]'] = (empty($owner_data['housing']) ? '------' : $owner_data['housing']);
	$params['[OWNER_APARTMENT]'] = (empty($owner_data['apartment']) ? '------' : $owner_data['apartment']);
	$params['[OWNER_PHONE]'] = $owner_data['phone'];

}


//Если юридическое лицо
if($contract_data["owner_type"] == 2){
	$params['[JUR_NAME]'] = $owner_data['jur_name'];
	$params['[OWNER_NAME]'] = '------';
	$params['[OWNER_DATE_BIRTH]'] = '------';
	$params['[OWNER_INN]'] = $owner_data['jur_inn'];
	//получаем название документа
	$params['[OWNER_NAME_DOCUMENT]'] = 'Cвидетельство о регистрации юридического лица';
	/////////////////////////////////////////////////////
	$params['[OWNER_SERIES]'] = $owner_data['jur_series'];	
	$params['[OWNER_NUMBER]'] = $owner_data['jur_number'];
	
	//получаем индекс и улицу
	$street_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_7` WHERE `aoguid` = '".$owner_data["street"]."'"));
	$params['[OWNER_INDEX]'] = $street_data['postalcode'];;
	$params['[OWNER_STREET]'] = $street_data['shortname'].". ".$street_data['formalname'];
	//////////////////////////////////
	
	//Получаем населённый пункт и район
	if(!empty($owner_data['city'])){
		$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_6` WHERE `aoid` = '".$owner_data["city"]."'"));
	} else {
		$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoid` = '".$owner_data["aoid"]."'"));
	}
	$params['[OWNER_CITY]'] = $city_data["shortname"].'. '.$city_data['formalname'];
	///////////////////////////////////////////////////////
	//Получаем район
	if(!empty($owner_data['city'])){
		$destrict_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoguid` = '".$city_data['parentguid']."'"));
		$params['[OWNER_DISTRICT]'] = $destrict_data['shortname']." ".$destrict_data['formalname'];
	}else {
		$params['[OWNER_DISTRICT]'] = '------';
	}
	//Получаем область
	$subject_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `kt_subject` WHERE `id_fias` = '".$insurer_data["subject"]."'"));
	$params['[OWNER_SUBJECT]'] = $subject_data['name'];
	////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$params['[OWNER_HOUSE]'] = $owner_data['house'];
	$params['[OWNER_HOUSING]'] = (empty($owner_data['housing']) ? '------' : $owner_data['housing']);
	$params['[OWNER_APARTMENT]'] = (empty($owner_data['apartment']) ? '------' : $owner_data['apartment']);
	$params['[OWNER_PHONE]'] = $owner_data['phone'];

}

//Данные по автомобилю
$vehicle_data = unserialize($contract_data['vehicle_data']);
$calc_data = unserialize($contract_data['calc_data']);
//Получаем марку и модель
if(isset($vehicle_data['mark_pts'])){
	$mark = $vehicle_data['mark_pts'];
	$model = $vehicle_data['model_pts'];
}else{
	$mark = mysql_fetch_assoc(mysql_query("SELECT * FROM `mark` WHERE `rsa_mark_id`='".$vehicle_data['mark']."'"));
	$mark = $mark['name'];
	$model = mysql_fetch_assoc(mysql_query("SELECT * FROM `model` WHERE `rsa_model_id`='".$vehicle_data['model']."'"));
	$model = $model['name'];
}
$category = mysql_fetch_assoc(mysql_query("SELECT * FROM `category_code` WHERE `id`='".$vehicle_data['category']."'"));
$category = $category['name'];
$params['[MARK]'] = $mark;
$params['[MODEL]'] = $model;
$params['[CATEGORY]'] = $category;
$params['[VIN]'] = $vehicle_data['vin'];
$params['[YEAR_MANUFACTURED]'] = $calc_data['year_manufacture'];
$params['[POWER]'] = $vehicle_data['power'];
$params['[POWER_K]'] = round($vehicle_data['power']/1.36, 2);
$params['[MAX_WEIGHT]'] = (isset($vehicle_data['max_weight']) ? $vehicle_data['max_weight'] : '------');
$params['[MAX_PASSENGER]'] = (isset($vehicle_data['number_seats']) ? $vehicle_data['number_seats'] : '------');
$params['[CHASSIS]'] = $vehicle_data['chassis'];
$params['[TRAILER]'] = $vehicle_data['trailer'];
$params['[AUTO_DOC_SERIES]'] = $vehicle_data['auto_doc_series'];
$params['[AUTO_DOC_NUMBER]'] = $vehicle_data['auto_doc_number'];
$params['[AUTO_DOC_DATE]'] = $vehicle_data['auto_doc_date'];
$params['[AUTO_REG_NUMBER]'] = $vehicle_data['auto_reg_number'];
$params['[AUTO_DIAG_CARD_NUMBER]'] = (isset($step_2_data['auto_diag_card_number']) ? $step_2_data['auto_diag_card_number'] : '------');
$params['[AUTO_DIAG_CARD_NEXT_DATE]'] = (isset($step_2_data['auto_diag_card_next_date']) ? $step_2_data['auto_diag_card_next_date'] : '------');
if($calc_data["category"] != '2' && $calc_data["category"] != '3'){
	$params['[TRAILER_YES]'] = ($calc_data['trailer'] == 2 ? '<w:sym w:font="Wingdings" w:char="F0FE"/>' : '<w:sym w:font="Wingdings" w:char="F0A8"/>');
	$params['[TRAILER_NO]'] = ($calc_data['trailer'] == 1 ? '<w:sym w:font="Wingdings" w:char="F0FE"/>' : '<w:sym w:font="Wingdings" w:char="F0A8"/>');
}else{
	$params['[TRAILER_YES]'] = '<w:sym w:font="Wingdings" w:char="F0A8"/>';
	$params['[TRAILER_NO]'] = '<w:sym w:font="Wingdings" w:char="F0A8"/>';
}
for($x=1;$x<10;$x++){
	if($vehicle_data["purpose_use"] == $x){
		$params['['.$x.']'] = '<w:sym w:font="Wingdings" w:char="F0FE"/>';
	} else {
		$params['['.$x.']'] = '<w:sym w:font="Wingdings" w:char="F0A8"/>';
	}
}
$params['[NO_LIMIT]'] = ($calc_data['drivers'] == 1 ? '<w:sym w:font="Wingdings" w:char="F0FE"/>' : '<w:sym w:font="Wingdings" w:char="F0A8"/>');
$params['[LIMIT]'] = ($calc_data['drivers'] == 2 ? '<w:sym w:font="Wingdings" w:char="F0FE"/>' : '<w:sym w:font="Wingdings" w:char="F0A8"/>');
if($calc_data['drivers'] == 2){
	$drivers_data = unserialize($contract_data['drivers_data']);
}
for($x=1;$x<6;$x++){
	if($calc_data['drivers'] == 2 && $x<=$drivers_data['number_of_drivers']){
		$params['[DRIVER_'.$x.'_NAME]'] = $drivers_data['driver_'.$x.'_second_name'].' '.$drivers_data['driver_'.$x.'_first_name'].' '.$drivers_data['driver_'.$x.'_third_name'];
		$params['[DRIVER_'.$x.'_DATE]'] = $drivers_data['driver_'.$x.'_date_birth'];
		$params['[DRIVER_'.$x.'_DOC]'] =  $drivers_data['driver_'.$x.'_series'].' '.$drivers_data['driver_'.$x.'_number'];
		$params['[DRIVER_'.$x.'_EXP]'] = $drivers_data['driver_'.$x.'_experience'];
	} else {
		$params['[DRIVER_'.$x.'_NAME]'] = '------------';
		$params['[DRIVER_'.$x.'_DATE]'] = '------------';
		$params['[DRIVER_'.$x.'_DOC]'] =  '------------';
		$params['[DRIVER_'.$x.'_EXP]'] = '------------';
	}
}
$params['[START_PERIOD_USE_1]'] = $step_2_data['auto_used_start_1'].' г.';
$params['[END_PERIOD_USE_1]'] = $step_2_data['auto_used_end_1'].' г.';
$params['[START_PERIOD_USE_2]'] = (isset($step_2_data['auto_used_start_2']) && isset($step_2_data['auto_used_end_2']) ? $step_2_data['auto_used_start_2'].' г.' : '------------');
$params['[END_PERIOD_USE_2]'] = (isset($step_2_data['auto_used_start_2']) && isset($step_2_data['auto_used_end_2']) ? $step_2_data['auto_used_end_2'].' г.' : '------------');
$params['[START_PERIOD_USE_3]'] = (isset($step_2_data['auto_used_start_3']) && isset($step_2_data['auto_used_end_3']) ? $step_2_data['auto_used_start_3'].' г.' : '------------');
$params['[END_PERIOD_USE_3]'] = (isset($step_2_data['auto_used_start_3']) && isset($step_2_data['auto_used_end_3']) ? $step_2_data['auto_used_end_3'].' г.' : '------------');
$params['[OSAGO_OLD_SERIES]'] = (isset($step_2_data['osago_old_series']) ? $step_2_data['osago_old_series'] : '------------');
$params['[OSAGO_OLD_NUMBER]'] = (isset($step_2_data['osago_old_number']) ? $step_2_data['osago_old_number'] : '------------');
$params['[OSAGO_OLD_NAME]'] = (isset($step_2_data['osago_old_name']) ? $step_2_data['osago_old_name'] : '------------');
$params['[BSO_NUMBER]'] = (isset($step_2_data['bso_number']) ? $step_2_data['bso_number'] : '------------');
$params['[BSO_SERIES]'] = (isset($step_2_data['bso_number']) ? $contract_data['bso_series'] : '---');
$params['[DATE_CREATE]'] = date('d.m.Y', strtotime($contract_data["time_create"]));
$calc_result = unserialize($contract_data['calc_result']);
$params['[TB]'] = $calc_result['tb'];
$params['[KT]'] = $calc_result['kt'];
$params['[KBM]'] = $calc_result['kbm'];
$params['[KVS]'] = $calc_result['kvs'];
$params['[KS]'] = $calc_result['ks'];
$params['[KP]'] = $calc_result['kp'];
$params['[KM]'] = $calc_result['km'];
$params['[KPR]'] = $calc_result['kpr'];
$params['[KN]'] = $calc_result['kn'];
$params['[TARIF]'] = $calc_result['t'];
$params['[AIS_REQUEST]'] = $contract_data['rsa_number'];
$params['[SPECIAL_NOTES]'] = $contract_data['special_notes'];
//определяем вид страхователя
$unit_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `unit` WHERE `unit_id` = '".$contract_data['unit_id']."'"));
if($unit_data['unit_full_name'] == 'Физические лица'){
	$agent_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `user_id` = '".$contract_data['user_id']."'"));
	$params['[AGENT_NAME]'] = $agent_data['second_name'].' '.$agent_data['first_name'].' '.$agent_data['third_name'];
} else {
	$params['[AGENT_NAME]'] = $unit_data['unit_full_name'];
}

// echo "<pre>";
// print_r($vehicle_data);
// echo "</pre>";
// exit();
/////////////////////////////////////////////////////////////////////////////////////////////////////
$blank_orig = "blank/statement.docx";
$name = md5(date("F j, Y, g:i:s "));
copy($blank_orig, "blank/tmp/$name.docx");
$blank = "blank/tmp/$name.docx";

$zip = new ZipArchive();
if (!$zip->open($blank)) {
    exit('Не удалось открыть бланк заявления');
}
$data_xml = $zip->getFromName("word/document.xml");
//Заменяем все найденные переменные в файле на значения
$data_xml = str_replace(array_keys($params), array_values($params), $data_xml);
$zip->deleteName('word/document.xml');
$zip->addFromString('word/document.xml', $data_xml);
$zip->close();
//ОТдаём файл браузеру
// заставляем браузер показать окно сохранения файла
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=Заявление.docx');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($blank));
// читаем файл и отправляем его пользователю
readfile($blank);
unlink($blank);
?>
