<?php
//Файл с общими проверками для всех банков
require_once('hypothec_check_data_contract_all.php');
//Проверки для данного банка
//Данные недвижимости
if(!$property_type_name){
	$err_text .= "<li class=\"text-danger\">Не указан вид недвижимого имущества</li>";
}
if($_SESSION['step_1']['ins_property_type_1'] == '1'){
	if(!$property_type_name){
		$err_text .= "<li class=\"text-danger\">Не указан вид недвижимого имущества</li>";
	}
	if($property_type_name && $property_type_name == '5' && !$property_type_name_other){
		$err_text .= "<li class=\"text-danger\">Не указан вид недвижимого имущества (код 2)</li>";
	}
	if(!$property_year){
		$err_text .= "<li class=\"text-danger\">Не указан год постройки дома</li>";
	}
	if(!$property_characteristics){
		$err_text .= "<li class=\"text-danger\">Не указана характеристика дома</li>";
	}		
}
if(!$property_full_name){
	$err_text .= "<li class=\"text-danger\">Не указано полное наименование объекта, согласно свидетельства</li>";
}
if(!$property_cadastral_number){
	$err_text .= "<li class=\"text-danger\">Не указан кадастровый номер недвижимого имущества</li>";
}
if(!$property_gross_area){
	$err_text .= "<li class=\"text-danger\">Не указана общая площадь недвижимого имущества</li>";
}
if(!$property_adress_registration){
	$err_text .= "<li class=\"text-danger\">Не указан адрес регистрации недвижимого имущества</li>";
}
if(!$property_right_of_possession){
	$err_text .= "<li class=\"text-danger\">Не указано право владения недвижимым имуществом</li>";
}
if(!$property_actual_value){
	$err_text .= "<li class=\"text-danger\">Не указана действительная стоимость недвижимого имущества</li>";
}
if(!$property_credit_summa){
	$err_text .= "<li class=\"text-danger\">Не указана сумма кредита</li>";
}
?>
