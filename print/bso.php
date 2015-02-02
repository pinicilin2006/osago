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
	$query = "SELECT * FROM `contract` WHERE `md5_id` = '".$id."' AND `unit_id` = '".$_SESSION["unit_id"]."'";
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
$owner_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `".($contract_data["owner_type"] == 1 ? "contact_phiz" : "contact_jur")."` WHERE `id` = '".$contract_data["owner_id"]."'"));
$vehicle_data = unserialize($contract_data['vehicle_data']);
$step_2_data = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $contract_data['step_2_data']);//боримся с проблемой unserialize если есть кавычки
$step_2_data = unserialize($step_2_data);
$calc_data = unserialize($contract_data['calc_data']);
$calc_result = unserialize($contract_data['calc_result']);
require_once('fpdf17/fpdf.php'); 
require_once('fpdi/fpdi.php'); 
//Вносим необходимые данные в полис и отдаём в формате pdf
$pdf = new FPDI();
$pdf->AddFont('ArialMT','','arial_cyr.php');
$pdf->AddPage();

// $pdf->setSourceFile('blank/bso3.pdf'); 
// // Указываем номер импортируемой страницы
// $tplIdx = $pdf->importPage(1); 
// //указываем размер страницы
// //$pdf->useTemplate($tplIdx, 0, 0, 210, 297, true);
// $pdf->useTemplate($tplIdx, 0, 0, 210, 297, true);

//Ставим поля по нулям
$pdf->SetMargins(0,0,0,0);
$pdf->SetAutoPageBreak(false);
//указываем шрифт и размер
$pdf->SetFont('ArialMT', '', '13');
//указываем цвет текста 
$pdf->SetTextColor(0,0,0);
////////////////////////////////////////////////////////Добавляем данные в полис//////////////////////////////
//Время начала действия договора
$pdf->SetXY(120.8, 42.5);
$pdf->Write(0, $contract_data['start_time'][0]);
$pdf->SetXY(125.8, 42.5);
$pdf->Write(0, $contract_data['start_time'][1]);
$pdf->SetXY(137.3, 42.5);
$pdf->Write(0, $contract_data['start_time'][3]);
$pdf->SetXY(142.3, 42.5);
$pdf->Write(0, $contract_data['start_time'][4]);
//Дата начала
$pdf->SetXY(156, 42.5);
$pdf->Write(0, $contract_data['start_date'][0]);
$pdf->SetXY(161, 42.5);
$pdf->Write(0, $contract_data['start_date'][1]);
$pdf->SetXY(168.7, 42.5);
$pdf->Write(0, $contract_data['start_date'][3]);
$pdf->SetXY(173.9, 42.5);
$pdf->Write(0, $contract_data['start_date'][4]);
$pdf->SetXY(187, 42.5);
$pdf->Write(0, $contract_data['start_date'][8]);
$pdf->SetXY(192.3, 42.5);
$pdf->Write(0, $contract_data['start_date'][9]);
//Дата окончания
$pdf->SetXY(156, 50.1);
$pdf->Write(0, $contract_data['end_date'][0]);
$pdf->SetXY(161, 50.1);
$pdf->Write(0, $contract_data['end_date'][1]);
$pdf->SetXY(168.7, 50.1);
$pdf->Write(0, $contract_data['end_date'][3]);
$pdf->SetXY(173.9, 50.1);
$pdf->Write(0, $contract_data['end_date'][4]);
$pdf->SetXY(187, 50.1);
$pdf->Write(0, $contract_data['end_date'][8]);
$pdf->SetXY(192.3, 50.1);
$pdf->Write(0, $contract_data['end_date'][9]);
//Период использования
//Первый период
//старт
$pdf->SetXY(8.7, 69);
$pdf->Write(0, $step_2_data['auto_used_start_1'][0]);
$pdf->SetXY(13.7, 69);
$pdf->Write(0, $step_2_data['auto_used_start_1'][1]);
$pdf->SetXY(21.2, 69);
$pdf->Write(0, $step_2_data['auto_used_start_1'][3]);
$pdf->SetXY(26.4, 69);
$pdf->Write(0, $step_2_data['auto_used_start_1'][4]);
$pdf->SetXY(38, 69);
$pdf->Write(0, $step_2_data['auto_used_start_1'][8]);
$pdf->SetXY(43, 69);
$pdf->Write(0, $step_2_data['auto_used_start_1'][9]);
//конец
$pdf->SetXY(59.7, 69);
$pdf->Write(0, $step_2_data['auto_used_end_1'][0]);
$pdf->SetXY(64.7, 69);
$pdf->Write(0, $step_2_data['auto_used_end_1'][1]);
$pdf->SetXY(72, 69);
$pdf->Write(0, $step_2_data['auto_used_end_1'][3]);
$pdf->SetXY(77, 69);
$pdf->Write(0, $step_2_data['auto_used_end_1'][4]);
$pdf->SetXY(88, 69);
$pdf->Write(0, $step_2_data['auto_used_end_1'][8]);
$pdf->SetXY(93, 69);
$pdf->Write(0, $step_2_data['auto_used_end_1'][9]);
//Второй период
if(isset($step_2_data['auto_used_start_2']) && isset($step_2_data['auto_used_end_2'])){
	//старт
	$pdf->SetXY(109.2, 69);
	$pdf->Write(0, $step_2_data['auto_used_start_2'][0]);
	$pdf->SetXY(114.2, 69);
	$pdf->Write(0, $step_2_data['auto_used_start_2'][1]);
	$pdf->SetXY(120.7, 69);
	$pdf->Write(0, $step_2_data['auto_used_start_2'][3]);
	$pdf->SetXY(126.3, 69);
	$pdf->Write(0, $step_2_data['auto_used_start_2'][4]);
	$pdf->SetXY(137.5, 69);
	$pdf->Write(0, $step_2_data['auto_used_start_2'][8]);
	$pdf->SetXY(142.5, 69);
	$pdf->Write(0, $step_2_data['auto_used_start_2'][9]);
	//конец
	$pdf->SetXY(159.2, 69);
	$pdf->Write(0, $step_2_data['auto_used_end_2'][0]);
	$pdf->SetXY(164.2, 69);
	$pdf->Write(0, $step_2_data['auto_used_end_2'][1]);
	$pdf->SetXY(171.7, 69);
	$pdf->Write(0, $step_2_data['auto_used_end_2'][3]);
	$pdf->SetXY(175.9, 69);
	$pdf->Write(0, $step_2_data['auto_used_end_2'][4]);
	$pdf->SetXY(187, 69);
	$pdf->Write(0, $step_2_data['auto_used_end_2'][8]);
	$pdf->SetXY(192, 69);
	$pdf->Write(0, $step_2_data['auto_used_end_2'][9]);	
} else {
	//старт
	$pdf->SetXY(109.2, 68.4);
	$pdf->Write(0, '-');
	$pdf->SetXY(114.2, 68.4);
	$pdf->Write(0, '-');
	$pdf->SetXY(120.7, 68.4);
	$pdf->Write(0, '-');
	$pdf->SetXY(126.3, 68.4);
	$pdf->Write(0, '-');
	$pdf->SetXY(137.5, 68.4);
	$pdf->Write(0, '-');
	$pdf->SetXY(142.5, 68.4);
	$pdf->Write(0, '-');
	//конец
	$pdf->SetXY(159.2, 68.4);
	$pdf->Write(0, '-');
	$pdf->SetXY(164.2, 68.4);
	$pdf->Write(0, '-');
	$pdf->SetXY(171.7, 68.4);
	$pdf->Write(0, '-');
	$pdf->SetXY(175.9, 68.4);
	$pdf->Write(0, '-');
	$pdf->SetXY(187, 68.4);
	$pdf->Write(0, '-');
	$pdf->SetXY(192, 68.4);
	$pdf->Write(0, '-');		
}
//Третий период
if(isset($step_2_data['auto_used_start_3']) && isset($step_2_data['auto_used_end_3'])){
	//старт
	$pdf->SetXY(8.7, 74.2);
	$pdf->Write(0, $step_2_data['auto_used_start_3'][0]);
	$pdf->SetXY(13.7, 74.2);
	$pdf->Write(0, $step_2_data['auto_used_start_3'][1]);
	$pdf->SetXY(21.2, 74.2);
	$pdf->Write(0, $step_2_data['auto_used_start_3'][3]);
	$pdf->SetXY(26.4, 74.2);
	$pdf->Write(0, $step_2_data['auto_used_start_3'][4]);
	$pdf->SetXY(38, 74.2);
	$pdf->Write(0, $step_2_data['auto_used_start_3'][8]);
	$pdf->SetXY(43, 74.2);
	$pdf->Write(0, $step_2_data['auto_used_start_3'][9]);
	//конец
	$pdf->SetXY(60.7, 74.2);
	$pdf->Write(0, $step_2_data['auto_used_end_3'][0]);
	$pdf->SetXY(65.7, 74.2);
	$pdf->Write(0, $step_2_data['auto_used_end_3'][1]);
	$pdf->SetXY(73.2, 74.2);
	$pdf->Write(0, $step_2_data['auto_used_end_3'][3]);
	$pdf->SetXY(78.4, 74.2);
	$pdf->Write(0, $step_2_data['auto_used_end_3'][4]);
	$pdf->SetXY(90, 74.2);
	$pdf->Write(0, $step_2_data['auto_used_end_3'][8]);
	$pdf->SetXY(95, 74.2);
	$pdf->Write(0, $step_2_data['auto_used_end_3'][9]);	
} else {
	//старт
	$pdf->SetXY(8.7, 74.8);
	$pdf->Write(0, '-');
	$pdf->SetXY(13.7, 74.8);
	$pdf->Write(0, '-');
	$pdf->SetXY(21.2, 74.8);
	$pdf->Write(0, '-');
	$pdf->SetXY(26.4, 74.8);
	$pdf->Write(0, '-');
	$pdf->SetXY(38, 74.8);
	$pdf->Write(0, '-');
	$pdf->SetXY(43, 74.8);
	$pdf->Write(0, '-');
	//конец
	$pdf->SetXY(60.7, 74.8);
	$pdf->Write(0, '-');
	$pdf->SetXY(65.7, 74.8);
	$pdf->Write(0, '-');
	$pdf->SetXY(73.2, 74.8);
	$pdf->Write(0, '-');
	$pdf->SetXY(78.4, 74.8);
	$pdf->Write(0, '-');
	$pdf->SetXY(90, 74.8);
	$pdf->Write(0, '-');
	$pdf->SetXY(95, 74.8);
	$pdf->Write(0, '-');		
}

//Страхователь
if($contract_data["insurer_type"] == 1){
	$insurer = $insurer_data['second_name']." ".$insurer_data['first_name']." ".$insurer_data['third_name'];
	$insurer = iconv('utf-8', 'windows-1251', "$insurer");
}
if($contract_data["insurer_type"] == 2){
	$insurer = iconv('utf-8', 'windows-1251', $insurer_data['jur_name']);
}
$pdf->SetXY(7.7, 90);
$pdf->Write(0, $insurer);

//Собственник
if($contract_data["owner_type"] == 1){
	$owner = $owner_data['second_name']." ".$owner_data['first_name']." ".$owner_data['third_name'];
	$owner = iconv('utf-8', 'windows-1251', "$owner");
}
if($contract_data["owner_type"] == 2){
	$owner = iconv('utf-8', 'windows-1251', $owner_data['jur_name']);
}
$pdf->SetXY(7.7, 107);
$pdf->Write(0, $owner);
//Есть ли прицеп
if($calc_data["category"] != '2' && $calc_data["category"] != '3'){
	if($calc_data['trailer'] == 2){//если ДА
		$pdf->SetXY(94.5, 114.7);
		$pdf->Write(0, 'V');	
	}
	if($calc_data['trailer'] == 1){//если ДА
		$pdf->SetXY(106, 114.7);
		$pdf->Write(0, 'V');	
	}
}
//Марка и модель транспортного средства
if(isset($vehicle_data['mark_pts'])){
	$mark = iconv('utf-8', 'windows-1251', $vehicle_data['mark_pts']);
	$model = iconv('utf-8', 'windows-1251', $vehicle_data['model_pts']);
}else{
	$mark = mysql_fetch_assoc(mysql_query("SELECT * FROM `mark` WHERE `rsa_mark_id`='".$vehicle_data['mark']."'"));
	$mark = iconv('utf-8', 'windows-1251', $mark['name']);
	$model = mysql_fetch_assoc(mysql_query("SELECT * FROM `model` WHERE `rsa_model_id`='".$vehicle_data['model']."'"));
	$model = iconv('utf-8', 'windows-1251', $model['name']);
}
$pdf->SetXY(7.7, 126);
$pdf->Write(0, $mark.',');
$pdf->SetXY(7.7, 131);
$pdf->Write(0, $model);
//VIN
$vin = iconv('utf-8', 'windows-1251', $step_2_data['vin']);
$pdf->SetXY(70.5, 129);
$pdf->Write(0, $vin[0]);
$pdf->SetXY(75.5, 129);
$pdf->Write(0, $vin[1]);
$pdf->SetXY(80, 129);
$pdf->Write(0, $vin[2]);
$pdf->SetXY(85.2, 129);
$pdf->Write(0, $vin[3]);
$pdf->SetXY(89.8, 129);
$pdf->Write(0, $vin[4]);
$pdf->SetXY(95.2, 129);
$pdf->Write(0, $vin[5]);
$pdf->SetXY(100.5, 129);
$pdf->Write(0, $vin[6]);
$pdf->SetXY(105.7, 129);
$pdf->Write(0, $vin[7]);
$pdf->SetXY(110, 129);
$pdf->Write(0, $vin[8]);
$pdf->SetXY(115.2, 129);
$pdf->Write(0, $vin[9]);
$pdf->SetXY(120.5, 129);
$pdf->Write(0, $vin[10]);
$pdf->SetXY(125.5, 129);
$pdf->Write(0, $vin[11]);
$pdf->SetXY(130.3, 129);
$pdf->Write(0, $vin[12]);
$pdf->SetXY(135.3, 129);
$pdf->Write(0, $vin[13]);
$pdf->SetXY(140.5, 129);
$pdf->Write(0, $vin[14]);
$pdf->SetXY(145.5, 129);
$pdf->Write(0, $vin[15]);
$pdf->SetXY(150.7, 129);
$pdf->Write(0, $vin[16]);
//Государственный регистрационный номер
$auto_reg_number = iconv('utf-8', 'windows-1251', $vehicle_data['auto_reg_number']);
$pdf->SetXY(166, 129);
$pdf->Write(0, $auto_reg_number);
//Вид документа
$auto_doc_type = mysql_fetch_assoc(mysql_query("SELECT * FROM `document_auto` WHERE `id` = '".$vehicle_data['auto_doc_type']."'"));
$auto_doc_type = iconv('utf-8', 'windows-1251', $auto_doc_type['name']);
$pdf->SetFont('ArialMT', '', '10');
$pdf->SetXY(25, 141);
$pdf->Write(0, $auto_doc_type);
//Серия
$pdf->SetXY(135, 141);
$vehicle_data['auto_doc_series'] = iconv('utf-8', 'windows-1251', $vehicle_data['auto_doc_series']);
$pdf->Write(0, $vehicle_data['auto_doc_series']);
//Номер
$pdf->SetXY(175, 141);
$vehicle_data['auto_doc_number'] = iconv('utf-8', 'windows-1251', $vehicle_data['auto_doc_number']);
$pdf->Write(0, $vehicle_data['auto_doc_number']);
//Цель использования
//Личная
if($step_2_data['purpose_use'] == 1){
	$pdf->SetXY(85.2, 148.1);
	$pdf->Write(0, 'V');	
}
//Учебная
if($step_2_data['purpose_use'] == 2){
	$pdf->SetXY(99.4, 148.1);
	$pdf->Write(0, 'V');	
}
//Такси
if($step_2_data['purpose_use'] == 3){
	$pdf->SetXY(121.4, 148.1);
	$pdf->Write(0, 'V');	
}
//Перевозка опасных и легковоспламенящихся грузов
if($step_2_data['purpose_use'] == 4){
	$pdf->SetXY(134, 148.1);
	$pdf->Write(0, 'V');	
}
//Прокат/красткосрочная аренда
if($step_2_data['purpose_use'] == 5){
	$pdf->SetXY(4, 152.3);
	$pdf->Write(0, 'V');	
}
//Регулярные поссажирские перевозки
if($step_2_data['purpose_use'] == 6){
	$pdf->SetXY(46.8, 152.3);
	$pdf->Write(0, 'V');	
}
//Дорожные и специальные транспортные средства
if($step_2_data['purpose_use'] == 7){
	$pdf->SetXY(140, 152.3);
	$pdf->Write(0, 'V');	
}
//экстренные и комунальные службы
if($step_2_data['purpose_use'] == 8){
	$pdf->SetXY(4, 156.1);
	$pdf->Write(0, 'V');	
}
//прочее
if($step_2_data['purpose_use'] == 9){
	$pdf->SetXY(57.3, 156.1);
	$pdf->Write(0, 'V');	
}
//ограниченное или не ограниченное число водителей
$pdf->SetFont('ArialMT', '', '13');
if($calc_data['drivers'] == 1){
	$pdf->SetXY(158, 164.5);
	$pdf->Write(0, 'V');	
}else{
	$pdf->SetXY(158, 169.5);
	$pdf->Write(0, 'V');	
}
//Список водителей допущенных к управлению
$pdf->SetFont('ArialMT', '', '10');
if($calc_data['drivers'] == 2){
	$drivers_data = unserialize($contract_data['drivers_data']);
	$y = 0;
	for($x=1;$x<5;$x++){
		if($x<=$drivers_data['number_of_drivers']){
			$first_name = iconv('utf-8', 'windows-1251', $drivers_data["driver_".$x."_first_name"]);
			$second_name = iconv('utf-8', 'windows-1251', $drivers_data["driver_".$x."_second_name"]);
			$third_name = iconv('utf-8', 'windows-1251', $drivers_data["driver_".$x."_third_name"]);
			$series = iconv('utf-8', 'windows-1251', $drivers_data["driver_".$x."_series"]);
			$number= iconv('utf-8', 'windows-1251', $drivers_data["driver_".$x."_number"]);
			$pdf->SetXY(6, 182+$y);
			$pdf->Write(0, $x);
			$pdf->SetXY(12, 182+$y);
			$pdf->Write(0, $second_name.' '.$first_name.' '.$third_name);
			$pdf->SetXY(150, 182+$y);
			$pdf->Write(0, $series.' '.$number);
		}else{
			$pdf->SetXY(4, 182+$y);
			$pdf->Write(0, '------');
			$pdf->SetXY(52, 182+$y);
			$pdf->Write(0, '-----------------------------------------');
			$pdf->SetXY(150, 182+$y);
			$pdf->Write(0, '---------------------------------------');			
		}
	$y=$y+5.8;	
	}
}
//Прочерки в списке допущенных лиц при выборе неограниченного числа 
if($calc_data['drivers'] == 1){
	$y = 0;
	for($x=1;$x<5;$x++){
		$pdf->SetXY(5, 182+$y);
		$pdf->Write(0, '-----');
		$pdf->SetXY(52, 182+$y);
		$pdf->Write(0, '-----------------------------------------');
		$pdf->SetXY(150, 182+$y);
		$pdf->Write(0, '---------------------------------------');					
		$y=$y+5.8;	
	}
}
//Страховая премия
	$tarif = num2str($calc_result['t']);
	$tarif = iconv('utf-8', 'windows-1251', $tarif);
	$pdf->SetXY(40, 236);
	$pdf->Write(0, $calc_result['t'].' ('.$tarif.')');
//Особые отметки
	$special_notes = iconv('utf-8', 'windows-1251', $contract_data['special_notes']);
	$pdf->SetXY(5, 250);
	$pdf->Write(0, $special_notes);
//Дата заключения договора
	$date_create = date('d.m.Y', strtotime($contract_data["time_create"]));
	$pdf->SetXY(52, 263);
	$pdf->Write(0, $date_create[0].$date_create[1]);
	$month = get_month($date_create[3].$date_create[4]);
	$month = iconv('utf-8', 'windows-1251', $month);
	$pdf->SetXY(68, 263);
	$pdf->Write(0, $month);
	$pdf->SetXY(98, 263);
	$pdf->Write(0, $date_create[8].$date_create[9]);
//Наименование страхователя
	$unit_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `unit` WHERE `unit_id` = '".$contract_data['unit_id']."'"));
	if($unit_data['unit_full_name'] == 'Физические лица'){
		$agent_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `user_id` = '".$contract_data['user_id']."'"));
		$agent = $agent_data['second_name'].' '.$agent_data['first_name'].' '.$agent_data['third_name'];
		$agent = iconv('utf-8', 'windows-1251', $agent);
	} else {
		$agent = iconv('utf-8', 'windows-1251', $unit_data['unit_full_name']);
	}
	$pdf->SetXY(135, 283);
	$pdf->Write(0, $agent);	
//Дата выдачи полиса
	$pdf->SetXY(52+27, 290);
	$pdf->Write(0, $date_create[0].$date_create[1]);
	$month = get_month($date_create[3].$date_create[4]);
	$month = iconv('utf-8', 'windows-1251', $month);
	$pdf->SetXY(68+27, 290);
	$pdf->Write(0, $month);
	$pdf->SetXY(98+26, 290);
	$pdf->Write(0, $date_create[8].$date_create[9]);
// echo "<pre>";
// print_r($date_create);
// echo "</pre>";
// exit();
//Отдаём готовый pdf. D - выдаст запрос на скачивание. I - отобразит в браузере
$pdf->Output('policy.pdf', 'I'); 

?>
