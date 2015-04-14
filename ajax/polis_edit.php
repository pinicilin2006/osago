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
//exit();
require_once('../config.php');
require_once('../function.php');
//require_once('../template/header.html');
connect_to_base();
$err_text='';
if(!$_SESSION["step_1"]){
	$err_text .= "<li class=\"text-danger\">Отсутствуют данные для расчёта страховой премии</li>";
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
	if(($key == 'owner_doc_name' && $_POST["insisown"] == 1) || ($key == 'a7_number' && $_POST["a7_number"] == 'no')){
		continue;
	}
	$$key = mysql_escape_string($val);
	$step_2[$key] = $$key;
}

if(!$insurer){
	$err_text .= "<li class=\"text-danger\">Не указан тип страхователя</li>";
}
//Если страхователь физическое лицо или ИП
if($insurer == 1){
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
	if(!$doc_name){
		$err_text .= "<li class=\"text-danger\">Не указано наименование документа удостоверяющего личность страхователя</li>";
	}
	if(!$doc_series){
		$err_text .= "<li class=\"text-danger\">Не указана серия документа удостоверяющего личность страхователя</li>";
	}
	if(!$doc_number){
		$err_text .= "<li class=\"text-danger\">Не указан номер документа удостоверяющего личность страхователя</li>";
	}			
}
//если страхователь Юридическое лицо
if($insurer == 2){
	if(!$jur_name){
		$err_text .= "<li class=\"text-danger\">Не указано наименования юр. лица (полностью)</li>";
	}
	if(!$jur_series){
		$err_text .= "<li class=\"text-danger\">Не указана серия свидетельства о регистрации юр. лица </li>";
	}
	if(!$jur_number){
		$err_text .= "<li class=\"text-danger\">Не указан номер свидетельства о регистрации юр. лица </li>";
	}
	if(!$jur_inn){
		$err_text .= "<li class=\"text-danger\">Не указан ИНН юр. лица </li>";
	}		
}
//проверкра на адрес
if(!$subject){
	$err_text .= "<li class=\"text-danger\">Не указан субъект РФ</li>";
}
if(!$street){
	$err_text .= "<li class=\"text-danger\">Не указана улица</li>";
}
if(!$house){
	$err_text .= "<li class=\"text-danger\">Не указан номер дома</li>";
}
if(!$phone){
	$err_text .= "<li class=\"text-danger\">Не указан номер телефона</li>";
}
//остальное
if(!$mark){
	$err_text .= "<li class=\"text-danger\">Не указана марка ТС</li>";
}
if(!$model){
	$err_text .= "<li class=\"text-danger\">Не указана модель ТС</li>";
}
if($vin && $vin != 'Отсутствует' && strlen($vin) <> 17){
	$err_text .= "<li class=\"text-danger\">Номер VIN должен содержать 17 символов</li>";
}
if($auto_reg_number && $auto_reg_number != 'Отсутствует' && (strlen($auto_reg_number) < 8 || strlen($auto_reg_number) > 9)){
	$err_text .= "<li class=\"text-danger\">Государственный регистрационный знак должен содержать 8 или 9 символов</li>";
}
if(!$auto_doc_type){
	$err_text .= "<li class=\"text-danger\">Не указано название документа о регистрации ТС</li>";
}
if(!$auto_doc_series){
	$err_text .= "<li class=\"text-danger\">Не указана серия документа о регистрации ТС</li>";
}
if(!$auto_doc_number){
	$err_text .= "<li class=\"text-danger\">Не указан номер документа о регистрации ТС</li>";
}
if(!$auto_doc_date){
	$err_text .= "<li class=\"text-danger\">Не указана дата выдачи документа о регистрации ТС</li>";
}
if(!$start_date){
	$err_text .= "<li class=\"text-danger\">Не указана дата начала действия договора страхования</li>";
}
if($auto_diag_card_next_date && !strtotime($auto_diag_card_next_date)){
	$err_text .= "<li class=\"text-danger\">Не верно указана дата срока действия диагностической карты</li>";
}
if($_SESSION['step_1']['category'] == (2 || 3)){
	if($_SESSION['step_1']['capacity'] == 1 && $power > 50){
		$err_text .= "<li class=\"text-danger\">Неверно указана мощность транспортного средства</li>";
	}
	if($_SESSION['step_1']['capacity'] == 2 && ($power < 51 || $power > 70)){
		$err_text .= "<li class=\"text-danger\">Неверно указана мощность транспортного средства</li>";
	}	
	if($_SESSION['step_1']['capacity'] == 3 && ($power < 71 || $power > 100)){
		$err_text .= "<li class=\"text-danger\">Неверно указана мощность транспортного средства</li>";
	}
	if($_SESSION['step_1']['capacity'] == 4 && ($power < 101 || $power > 120)){
		$err_text .= "<li class=\"text-danger\">Неверно указана мощность транспортного средства</li>";
	}
	if($_SESSION['step_1']['capacity'] == 5 && ($power < 121 || $power > 150)){
		$err_text .= "<li class=\"text-danger\">Неверно указана мощность транспортного средства</li>";
	}
	if($_SESSION['step_1']['capacity'] == 6 && $power >151){
		$err_text .= "<li class=\"text-danger\">Неверно указана мощность транспортного средства</li>";
	}			
}
// if(!$start_time){
// 	$err_text .= "<li class=\"text-danger\">Не указано время начала действия договора страхования</li>";
// }
if(!$md5_id){
	$err_text .= "<li class=\"text-danger\">Не указан уникальный идентификатор полиса</li>";
}
if($md5_id){
	if(mysql_num_rows(mysql_query("SELECT * FROM `contract` WHERE `md5_id` = '".$md5_id."'"))<1){
		$err_text .= "<li class=\"text-danger\">Редактируемый договор не обнаружен в базе данных</li>";
	}
}
if($action == 'add'){
	if(!$bso_number){
		$err_text .= "<li class=\"text-danger\">Не указан номер БСО</li>";
	}
	if(!$ais_request_identifier){
		$err_text .= "<li class=\"text-danger\">Не указан номер запроса в АИС РСА</li>";
	}		
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if(!empty($err_text)){
	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();
}
//проверяем есть ли данные по страхователю
if($insurer == 1){
	$query_insurer_data = "SELECT * FROM `contact_phiz` WHERE `first_name` = '".$first_name."' AND `second_name` = '".$second_name."' AND `third_name` = '".$third_name."' AND `date_birth` = '".$date_birth."' AND `doc_name` = '".$doc_name."' AND `doc_series` = '".$doc_series."' AND `doc_number` = '".$doc_number."' AND `aoid` = '".$aoid."' AND `city` = '".$city."' AND `street` = '".$street."' AND `house` = '".$house."' AND `housing` = '".$housing."' AND `apartment` = '".$apartment."' AND `phone` = '".$phone."'";
	if(mysql_num_rows(mysql_query($query_insurer_data))>0){
		//.....//
	} else {
		$query = "INSERT INTO `contact_phiz` (first_name,second_name,third_name,date_birth,doc_name,doc_series,doc_number,subject,aoid,city,street,house,housing,apartment,phone,user_id,unit_id) VALUES ('".$first_name."','".$second_name."','".$third_name."','".$date_birth."','".$doc_name."','".$doc_series."','".$doc_number."','".$subject."','".$aoid."','".(isset($city) ? $city : '')."','".$street."','".$house."','".(isset($housing) ? $housing : '')."','".(isset($apartment) ? $apartment : '')."','".$phone."','".$_SESSION["user_id"]."','".$_SESSION["unit_id"]."')";
		if(mysql_query($query)){
			//...//
		}else{
			echo "<p class=\"text-danger\">Произошла ошибка при добавление данных по страхователю в базу данных</p>";
			exit();
		}
	}
	//echo $query_insurer_data;
	$insurer_data = mysql_fetch_assoc(mysql_query($query_insurer_data));
	$insurer_id = $insurer_data["id"];
}
if($insurer == 2){
	$query_insurer_data = "SELECT * FROM `contact_jur` WHERE `jur_name` = '".$jur_name."' AND `jur_series` = '".$jur_series."' AND `jur_number` = '".$jur_number."'";
	if(mysql_num_rows(mysql_query($query_insurer_data))>0){
		//.....//
	} else {
		$query = "INSERT INTO `contact_jur` (jur_name,jur_series,jur_number,jur_inn,subject,aoid,city,street,house,housing,apartment,phone,user_id,unit_id) VALUES ('".$jur_name."','".$jur_series."','".$jur_number."','".$jur_inn."','".$subject."','".$aoid."','".(isset($city) ? $city : '')."','".$street."','".$house."','".(isset($housing) ? $housing : '')."','".(isset($apartment) ? $apartment : '')."','".$phone."','".$_SESSION["user_id"]."','".$_SESSION["unit_id"]."')";
		if(mysql_query($query)){
			//...//
		}else{
			echo "<p class=\"text-danger\">Произошла ошибка при добавление данных по страхователю в базу данных</p>";
			exit();
		}
	}
	$insurer_data = mysql_fetch_assoc(mysql_query($query_insurer_data));
	$insurer_id = $insurer_data["id"];
}
$insurer_type = $insurer;	
if($insisown == 1){
	$owner_id = $insurer_id;
	$owner_type = $insurer;
}else{
	if($_SESSION["step_1"]["type_ins"] != 'jur'){
	$query_owner_data = "SELECT * FROM `contact_phiz` WHERE `first_name` = '".$owner_first_name."' AND `second_name` = '".$owner_second_name."' AND `third_name` = '".$owner_third_name."' AND `date_birth` = '".$owner_date_birth."' AND `doc_name` = '".$owner_doc_name."' AND `doc_series` = '".$owner_doc_series."' AND `doc_number` = '".$owner_doc_number."' AND `aoid` = '".$owner_aoid."' AND `city` = '".$owner_city."' AND `street` = '".$owner_street."' AND `house` = '".$owner_house."' AND `housing` = '".$owner_housing."' AND `apartment` = '".$owner_apartment."' AND `phone` = '".$owner_phone."'";
		if(mysql_num_rows(mysql_query($query_owner_data))>0){
			//.....//
		} else {
			$query = "INSERT INTO `contact_phiz` (first_name,second_name,third_name,date_birth,doc_name,doc_series,doc_number,subject,aoid,city,street,house,housing,apartment,phone,user_id,unit_id) VALUES ('".$owner_first_name."','".$owner_second_name."','".$owner_third_name."','".$owner_date_birth."','".$owner_doc_name."','".$owner_doc_series."','".$owner_doc_number."','".$owner_subject."','".$owner_aoid."','".(isset($city) ? $city : '')."','".$owner_street."','".$owner_house."','".(isset($owner_housing) ? $owner_housing : '')."','".(isset($owner_apartment) ? $owner_apartment : '')."','".$owner_phone."','".$_SESSION["user_id"]."','".$_SESSION["unit_id"]."')";
			if(mysql_query($query)){
				//...//
			}else{
				echo "<p class=\"text-danger\">Произошла ошибка при добавление данных по собственнику в базу данных</p>";
				exit();
			}
		}
		$owner_data = mysql_fetch_assoc(mysql_query($query_owner_data));
		$owner_id = $owner_data["id"];
		$owner_type = 1;		
	}else{
		$query_owner_data = "SELECT * FROM `contact_jur` WHERE `jur_name` = '".$owner_jur_name."' AND `jur_series` = '".$owner_jur_series."' AND `jur_number` = '".$owner_jur_number."'";
		if(mysql_num_rows(mysql_query($query_owner_data))>0){
			//.....//
		} else {
			$query = "INSERT INTO `contact_jur` (jur_name,jur_series,jur_number,jur_inn,subject,aoid,city,street,house,housing,apartment,phone,user_id,unit_id) VALUES ('".$owner_jur_name."','".$owner_jur_series."','".$owner_jur_number."','".$owner_jur_inn."','".$owner_subject."','".$owner_aoid."','".(isset($owner_city) ? $owner_city : '')."','".$owner_street."','".$owner_house."','".(isset($owner_housing) ? $owner_housing : '')."','".(isset($owner_apartment) ? $owner_apartment : '')."','".$owner_phone."','".$_SESSION["user_id"]."','".$_SESSION["unit_id"]."')";
			if(mysql_query($query)){
				//...//
			}else{
				echo "<p class=\"text-danger\">Произошла ошибка при добавление данных по собственнику в базу данных</p>";
				exit();
			}
		}
		$owner_data = mysql_fetch_assoc(mysql_query($query_owner_data));
		$owner_id = $owner_data["id"];
		$owner_type = 2;
	}
}
//анные по ТС
$vehicle_data = array(
	'mark' => $mark,
	'model' => $model,
	'vin' => $vin,
	'power' => $power,
	'chassis' => $chassis,
	'trailer' => $trailer,
	'auto_doc_type' => $auto_doc_type,
	'auto_doc_series' => $auto_doc_series,
	'auto_doc_number' => $auto_doc_number,
	'auto_doc_date' => $auto_doc_date,
	'auto_reg_number' => $auto_reg_number,
	'purpose_use' => $purpose_use,
	// 'max_weight' => (isset($max_weight) ? $max_weight : ''),
	// 'number_seats' => (isset($number_seats) ? $number_seats : ''),
	'category' => $category,
	);
if($max_weight){
	$vehicle_data['max_weight'] = $max_weight;
}
if(isset($number_seats)){
	$vehicle_data['number_seats'] = $number_seats;
}
if(isset($mark_pts)){
	$vehicle_data['mark_pts'] = $mark_pts;
}
if(isset($model_pts)){
	$vehicle_data['model_pts'] = $model_pts;
}
$vehicle_data = serialize($vehicle_data);

//Данные по водителям
if($_SESSION["step_1"]["driver"] == 1){
	$drivers_data = '';
} else {
	$number_of_drivers = 0;
	$drivers_data = array();
	for($x=1;$x<6;$x++){
		if(isset($_SESSION["step_1"]["driver_$x"])){
			$number_of_drivers++;
			$drivers_data["driver_".$x."_first_name"] = ${"driver_".$x."_first_name"};
			$drivers_data["driver_".$x."_second_name"] = ${"driver_".$x."_second_name"};
			$drivers_data["driver_".$x."_third_name"] = ${"driver_".$x."_third_name"};
			$drivers_data["driver_".$x."_date_birth"] = ${"driver_".$x."_date_birth"};
			$drivers_data["driver_".$x."_series"] = ${"driver_".$x."_series"};
			$drivers_data["driver_".$x."_number"] = ${"driver_".$x."_number"};
			$drivers_data["driver_".$x."_experience"] = ${"driver_".$x."_experience"};
		}
	}
	$drivers_data['number_of_drivers'] = $number_of_drivers;
	$drivers_data = serialize($drivers_data);
}
$calc_data = serialize($_SESSION['step_1']);
$calc_result = serialize($_SESSION['calc']);
$step_2_data = serialize($step_2);
// echo $insurer_id.' '.$insurer_type."<br>".$owner_id." ".$owner_type;
// echo "<br>";
// echo "<pre>";
// print_r($step_2);
// echo "</pre>";
if(mysql_num_rows(mysql_query("SELECT * FROM `contract` WHERE `md5_id` = '".$md5_id."'"))<1){
	echo "<br><p class=\"text-danger text-center\">Ошибка!<br>Не найден редактируемый договор в базе данных.</p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();	
}
//$query = "INSERT INTO `contract` (user_id,unit_id,insurer_id,insurer_type,owner_id,owner_type,vehicle_data,drivers_data,calc_data,calc_result,start_date,start_time,end_date,step_2_data,bso_number,a7_number,rsa_number,project,md5_id) VALUES ('".$_SESSION['user_id']."','".$_SESSION['unit_id']."','".$insurer_id."','".$insurer_type."','".$owner_id."','".$owner_type."','".$vehicle_data."','".$drivers_data."','".$calc_data."','".$calc_result."','".$start_date."','".(isset($start_time) ? $start_time : '00:00')."','".$end_date."','".$step_2_data."','".($action=='project' ? '' : $bso_number)."','".($action=='project' ? '' : $a7_number)."','".$ais_request_identifier."','".($action == 'project' ? '1' : '0')."','".$md5_id."')";
if($action == 'add'){
	$bso_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `bso` WHERE `number` = '".$bso_number."'"));
	$bso_series = $bso_data['series'];
} else {
	$bso_series = '';
}
$query = "UPDATE `contract` SET `insurer_id` = '".$insurer_id."',`insurer_type`='".$insurer_type."',`owner_id` = '".$owner_id."',`owner_type` = '".$owner_type."',`vehicle_data` = '".$vehicle_data."',`drivers_data` = '".$drivers_data."',`calc_data` = '".$calc_data."',`calc_result` = '".$calc_result."',`start_date` = '".$start_date."',`start_time` = '".(isset($start_time) ? $start_time : '00:00')."',`end_date` = '".$end_date."',`step_2_data` = '".$step_2_data."',`bso_number` = '".($action=='project' ? '' : $bso_number)."',`bso_series` = '".($action=='project' ? '' : $bso_series)."',`a7_number` = '".($action=='project' ? '' : $a7_number)."',`rsa_number` = '".$ais_request_identifier."',`project` = '".($action == 'project' ? '1' : '0')."' WHERE `md5_id` = '".$md5_id."'";
//echo $query;
//echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
//exit();
if(mysql_query($query)){
	$contract_id = mysql_fetch_assoc(mysql_query("SELECT * FROM `contract` WHERE `md5_id` = '".$md5_id."'"));
	$contract_id = $contract_id["md5_id"];
	if($action == 'add'){
		if(mysql_query("DELETE FROM `bso` WHERE `number`= '".$bso_number."'") && (isset($a7_number) ? mysql_query("DELETE FROM `a7` WHERE `number`= '".$a7_number."'") : (1 == 1)) ){
			//..
		} else {
			echo 'Произошла ошибка при удаление бланка БСО или бланка а7 из базы доступных бланков';
		}
		echo '<div class="alert alert-success text-center">Данные успешно добавлены!</div>';
		echo '<center><div class="btn-group btn-group-justified"><div class="btn-group"><a href="/print/statement.php?id='.$contract_id.'" target="_blank" class="btn btn-default" >Распечатать заявление</a></div><div class="btn-group"><a href="/print/bso.php?id='.$contract_id.'" target="_blank" class="btn btn-default">Распечатать полис</a></div><div class="btn-group"><a href="/print/a7.php?id='.$contract_id.'" target="_blank" class="btn btn-default" '.(isset($a7_number) ? '' : 'disabled="disabled"').'>Распечатать бланк А7</a></div></div></center>';
	}
	if($action == 'project'){
		echo '<div class="alert alert-success text-center">Проект договора сохранён!</div>';
		echo '<center><div class="btn-group btn-group-justified"><div class="btn-group"><a href="/print/statement.php?id='.$contract_id.'" target="_blank" class="btn btn-default" >Распечатать заявление</a></div><div class="btn-group"></div></div></center>';

	}
	unset($_SESSION["step_1"]);
	unset($_SESSION["calc"]);
}else{
	echo "<p class=\"text-danger\">Произошла ошибка при добавление договора в базу данных!</p>";
	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
}
//echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
?>



