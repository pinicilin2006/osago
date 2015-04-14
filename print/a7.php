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
$unit_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `unit` WHERE `unit_id` = '".$contract_data['unit_id']."'"));
$agent_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `user_id` = '".$contract_data['user_id']."'"));
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
////////////////////////////////////////////////////
// $pdf->setSourceFile('blank/a7.pdf'); 
// // Указываем номер импортируемой страницы
// $tplIdx = $pdf->importPage(1); 
// //указываем размер страницы
// //$pdf->useTemplate($tplIdx, 0, 0, 210, 297, true);
// $pdf->useTemplate($tplIdx, 0, 0, 210, 297, true);
// //Ставим поля по нулям
// $pdf->SetMargins(0,0,0,0);
// $pdf->SetAutoPageBreak(false);
////////////////////////////////////////////////////
//указываем шрифт и размер
$pdf->SetFont('ArialMT', '', '10');
//указываем цвет текста 
$pdf->SetTextColor(0,0,0);
////////////////////////////////////////////////////////Добавляем данные в полис//////////////////////////////
if($contract_data["insurer_type"] == 2){
	require_once('../template/header.html');
	echo "<p class=\"text-danger text-center\">Бланк А7 не выдаётся юридическим лицам.</p>";
	exit();
}
//Страхователь
$insurer = $insurer_data['second_name']." ".$insurer_data['first_name']." ".$insurer_data['third_name'];
$insurer = iconv('utf-8', 'windows-1251', "$insurer");
$pdf->SetXY(40, 43);
$pdf->Write(0, $insurer);
//Номер и серия страхового полиса
$pdf->SetXY(70, 50);
$pdf->Write(0, 'EEE '.$contract_data['bso_number']);
//Вид страхования
$name_osago_text_1 = iconv('utf-8', 'windows-1251', "Правила обязательного страхования гражданской ответственности");
$name_osago_text_2 = iconv('utf-8', 'windows-1251', "владельцев транспортных средств");
$pdf->SetXY(50, 57);
$pdf->Write(0, $name_osago_text_1);
$pdf->SetXY(50, 64);
$pdf->Write(0, $name_osago_text_2);
//Представитель страховщика
if($unit_data['unit_full_name'] == 'Физические лица'){
	$agent = $agent_data['second_name'].' '.$agent_data['first_name'].' '.$agent_data['third_name'];
} else {
	$agent = $unit_data['unit_full_name'];
}
$agent = iconv('utf-8', 'windows-1251', "$agent");
$pdf->SetXY(100, 71);
$pdf->Write(0, $agent);
//Страховая премия
$tarif = num2str($calc_result['t'],2);
$tarif = iconv('utf-8', 'windows-1251', $tarif);
$pdf->SetXY(65, 78);
$pdf->Write(0, $calc_result['t'].' ('.$tarif.')');
$pdf->SetXY(65, 92);
$pdf->Write(0, $calc_result['t'].' ('.$tarif.')');
//Дата заключения договора
$date_create = date('d.m.Y', strtotime($contract_data["time_create"]));
$pdf->SetXY(13, 127);
$pdf->Write(0, $date_create[0].$date_create[1]);
$month = get_month($date_create[3].$date_create[4]);
$month = iconv('utf-8', 'windows-1251', $month);
$pdf->SetXY(35, 127);
$pdf->Write(0, $month.'       20'.$date_create[8].$date_create[9]);
// $pdf->SetXY(98, 127);
// $pdf->Write(0, $date_create[8].$date_create[9]);
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
// exit();
//Отдаём готовый pdf. D - выдаст запрос на скачивание. I - отобразит в браузере
$pdf->Output('policy.pdf', 'I'); 

?>
