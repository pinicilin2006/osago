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
require_once('fpdf17/fpdf.php'); 
require_once('fpdi/fpdi.php'); 
//Вносим необходимые данные в полис и отдаём в формате pdf
$pdf = new FPDI();
$pdf->AddFont('ArialMT','','arial_cyr.php');
$pdf->AddPage(); 
$pdf->setSourceFile('blank/bso.pdf'); 
// Указываем номер импортируемой страницы
$tplIdx = $pdf->importPage(1); 
//указываем размер страницы
$pdf->useTemplate($tplIdx, 0, 0, 210, 297, true);
//указываем шрифт и размер
$pdf->SetFont('ArialMT', '', '12');
//указываем цвет текста 
$pdf->SetTextColor(0,0,0);
////////////////////////////////////////////////////////Добавляем данные в полис//////////////////////////////
//Время
$start_time = explode(':', $contract_data['start_time']);
$pdf->SetXY(120, 35);
$pdf->Write(0, $start_time[0]);
//Отдаём готовый pdf. D - выдаст запрос на скачивание. I - отобразит в браузере
$pdf->Output('policy.pdf', 'I'); 

?>
