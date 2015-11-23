<?php
if(!isset($_SESSION['user_id'])){
	$err_text .= "<li class=\"text-danger\">Отсутствуют данные сессии. Вам необходимо выйти из программы и снова в неё войти.</li>";
}
if(!$_SESSION["step_1"]){
	$err_text .= "<li class=\"text-danger\">Отсутствуют данные расчёта страховой премии</li>";
}
if(!$_SESSION["calc"]){
	$err_text .= "<li class=\"text-danger\">Отсутствует результат расчёта страховой премии</li>";
}
if(!$_POST){
	$err_text .= "<li class=\"text-danger\">Отсутствуют данные полиса</li>";
}
if(!empty($err_text)){
	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();
}
$step_2 = '';
$step_2 = array();
foreach($_POST as $key => $val){
	if(empty($val)){
		continue;
	}
	// if(($key == 'owner_doc_name' && $_POST["insisown"] == 1) || ($key == 'a7_number' && $_POST["a7_number"] == 'no')){
	// 	continue;
	// }
	
	$$key = mysql_real_escape_string($val);	
}
//Блок проверки личных данных
if(!$second_name){
	$err_text .= "<li class=\"text-danger\">Не указана фамилия страхователя</li>";
}
if(!$first_name){
	$err_text .= "<li class=\"text-danger\">Не указано имя страхователя</li>";
}
if(!$third_name){
	$err_text .= "<li class=\"text-danger\">Не указано отчество страхователя</li>";
}
if(!$date_birth){
	$err_text .= "<li class=\"text-danger\">Не указана дата рождения страхователя</li>";
}
if(!valid_date($date_birth) || age($date_birth)> 100){
	$err_text .= "<li class=\"text-danger\">Дата рождения собственника указанна не верно</li>";
}		
if($first_name && $second_name && $third_name && $date_birth && mysql_num_rows(mysql_query("SELECT * FROM `bad_people` WHERE first_name = '".trim($first_name)."' AND second_name = '".trim($second_name)."' AND third_name = '".trim($third_name)."' AND date_of_birth = '".$date_birth."'"))>0){
	$err_text .= "<li class=\"text-danger\">Страхователь находится в списке людей, страхование которых запрещено!</li>";
}
if(!$place_birth){
	$err_text .= "<li class=\"text-danger\">Не указано место рождения страхователя</li>";
}
if(!$place_work){
	$err_text .= "<li class=\"text-danger\">Не указано место работы страхователя</li>";
}
if(!$phone_number){
	$err_text .= "<li class=\"text-danger\">Не указан номер телефона страхователя</li>";
}
if(!$inn){
	$err_text .= "<li class=\"text-danger\">Не указан ИНН страхователя</li>";
}
if(!$index_registration){
	$err_text .= "<li class=\"text-danger\">Не указан индекс страхователя</li>";
}
if(!$adress_registration){
	$err_text .= "<li class=\"text-danger\">Не указан адрес регистрации страхователя</li>";
}
if(!$passport_series){
	$err_text .= "<li class=\"text-danger\">Не указана серия паспорта страхователя</li>";
}
if(!$passport_number){
	$err_text .= "<li class=\"text-danger\">Не указан номер паспорта страхователя</li>";
}
if(!$passport_organ){
	$err_text .= "<li class=\"text-danger\">Не указано кем выдан паспорт страхователя</li>";
}
if(!$passport_date){
	$err_text .= "<li class=\"text-danger\">Не указана дата выдачи паспорта страхователя</li>";
}
if(!$passport_code){
	$err_text .= "<li class=\"text-danger\">Не указан код подразделения выдавшего паспорт страхователя</li>";
}
//Прочие данные
if(!$date_start){
	$err_text .= "<li class=\"text-danger\">Не указана дата начала действия договора</li>";
}
if(!$date_end){
	$err_text .= "<li class=\"text-danger\">Не указана дата окончания действия договора</li>";
}
if($md5_id){
	if(mysql_num_rows(mysql_query("SELECT * FROM `hypothec_contract` WHERE `md5_id` = '".$md5_id."'"))>0){
		$err_text .= "<li class=\"text-danger\">Данные уже внесенны в базу данных</li>";
	}
}
?>
