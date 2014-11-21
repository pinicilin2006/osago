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
	$query = "SELECT * FROM `contract` WHERE `id` = '".$id."' AND `unit_id` = '".$_SESSION["unit_id"]."'";
}else{
	$query = "SELECT * FROM `contract` WHERE `id` = '".$id."' AND `unit_id` = '".$_SESSION["unit_id"]."' AND `user_id` = '".$_SESSION["user_id"]."'";
}
if(mysql_num_rows(mysql_query($query))<1){
	require_once('../template/header.html');
	echo "<p class=\"text-danger text-center\">Договор с запрашиваемым id не найден в базе данных</p>";
	exit();
}
$contract_data = mysql_fetch_assoc(mysql_query($query));
//Получаем данные страхователя
$insurer_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `".($contract_data["insurer_type"] == 1 ? "contact_phiz" : "contact_jur")."` WHERE `id` = '".$contract_data["insurer_id"]."'"));
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


// echo "<pre>";
// print_r($city_data);
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
