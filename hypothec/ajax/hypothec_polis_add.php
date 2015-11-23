<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}
// echo "<pre>";
// print_r($_POST);
// echo "</pre><br><pre>";
// print_r($_SESSION);
// echo "</pre>";
// echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
// exit();
require_once('../../config.php');
require_once('../../function.php');
//require_once('../template/header.html');
connect_to_base();
$err_text='';
//Общие проверки при редактирование и создание полиса
require_once('hypothec_check_data/hypothec_check_data_contract_'.$_SESSION['step_1']['bank'].'.php');
/////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(!empty($err_text)){
	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();
}

//Проверяем есть ли в базе данных данные страхователя
$query = mysql_query("SELECT * FROM `hypothec_contact` WHERE `first_name` = '".$first_name."' AND `second_name` = '".$second_name."' AND `third_name` = '".$third_name."' AND `date_birth` = '".$date_birthe."'"); 
if(mysql_num_rows($query) == 1){
	$contact_data = mysql_fetch_assoc($query);
	$contact_id = $contact_data["id"];	
} else {
	$query = "INSERT INTO `hypothec_contact`( `first_name`, `second_name`, `third_name`, `sex`, `date_birth`, `place_birth`, `place_work`, `phone_number`, `inn`, `index_registration`, `adress_registration`, `passport_series`, `passport_number`, `passport_organ`, `passport_date`, `passport_code`) 
	VALUES ('".$first_name."','".$second_name."','".$third_name."','".$sex."','".$date_birth."','".$place_birth."','".$place_work."','".$phone_number."','".$inn."','".$index_registration."','".$adress_registration."','".$passport_series."','".$passport_number."','".$passport_organ."','".$passport_date."','".$passport_code."')";
	
	if(mysql_query($query)){
		$contact_id = mysql_insert_id();
	} else {
		echo '<p class="text-danger text-center">Произошла ошибка при добавление данных по страхователю в базу данных</p>';
		exit();		
	}
}
//Запихиваем данные по объекту недвижимости в таблицу
$query = "INSERT INTO `hypothec_property`(`property_type_name`, `property_type_name_other`, `property_full_name`, `property_cadastral_number`, `property_gross_area`, `property_adress_registration`, `property_right_of_possession`, `property_actual_value`, `property_credit_summa`, `property_year`, `property_characteristics`) 
VALUES ('".$property_type_name."','".$property_type_name_other."','".$property_full_name."','".$property_cadastral_number."','".$property_gross_area."','".$property_adress_registration."','".$property_right_of_possession."','".$property_actual_value."','".$property_credit_summa."','".$property_year."','".$property_characteristics."')";
if(mysql_query($query)){
	$property_id = mysql_insert_id();
} else {
	echo '<p class="text-danger text-center">Произошла ошибка при добавление данных по объекту недвижимости в базу данных</p>';
	exit();		
}
$calc_data = serialize($_SESSION['step_1']);
$calc_result = serialize($_SESSION['calc']);
$step_2_data = serialize($step_2);
//Запихиваем данные в таблицу с договорами
$query = "INSERT INTO `hypothec_contract`(`user_id`, `unit_id`, `contact_id`, `property_id`, `calc_data`, `calc_result`, `step_2_data`, `start_date`, `end_date`, `project`,`md5_id`) 
VALUES ('".$_SESSION['user_id']."','".$_SESSION['unit_id']."','".$contact_id."','".$property_id."','".$calc_data."','".$calc_result."','".$step_2_data."','".$start_date."','".$end_date."','".($action == 'project' ? '1' : '0')."','".$md5_id."')";

echo $query;
























// if(mysql_query($query)){
// 	$contract_id = mysql_fetch_assoc(mysql_query("SELECT * FROM `contract` WHERE `md5_id` = '".$md5_id."'"));
// 	$contract_id = $contract_id["md5_id"];
// 	if($action == 'add'){
// 		if(mysql_query("DELETE FROM `bso` WHERE `number`= '".$bso_number."'") && (isset($a7_number) ? mysql_query("DELETE FROM `a7` WHERE `number`= '".$a7_number."'") : (1 == 1)) ){
// 			//..
// 		} else {
// 			echo '<p class="text-danger">Произошла ошибка при удаление бланка БСО или бланка а7 из базы доступных бланков</p>';
// 		}
// 		echo '<div class="alert alert-success text-center">Данные успешно добавлены!</div>';
// 		echo '<center><div class="btn-group btn-group-justified"><div class="btn-group"><a href="/print/statement.php?id='.$contract_id.'" target="_blank" class="btn btn-default" >Распечатать заявление</a></div><div class="btn-group"><a href="/print/bso.php?id='.$contract_id.'" target="_blank" class="btn btn-default">Распечатать полис</a></div><div class="btn-group"><a href="/print/a7.php?id='.$contract_id.'" target="_blank" class="btn btn-default" '.(isset($a7_number) ? '' : 'disabled="disabled"').'>Распечатать бланк А7</a></div></div></center>';
// 	}
// 	if($action == 'project'){
// 		echo '<div class="alert alert-success text-center">Проект договора сохранён!</div>';
// 		echo '<center><div class="btn-group btn-group-justified"><div class="btn-group"><a href="/print/statement.php?id='.$contract_id.'" target="_blank" class="btn btn-default" >Распечатать заявление</a></div><div class="btn-group"></div></div></center>';

// 	}
// 	unset($_SESSION["step_1"]);
// 	unset($_SESSION["calc"]);
// }else{
// 	echo "<p class=\"text-danger\">Произошла ошибка при добавление договора в базу данных!</p>";
// 	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
// }
//echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
?>



