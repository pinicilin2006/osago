<?php
if(!isset($_SESSION['user_id'])){
	$err_text .= "<li class=\"text-danger\">Отсутствуют данные сессии. Вам необходимо выйти из программы и снова в неё войти.</li>";
}
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
	if(($key == 'bank_number') || ($key == 'bank_date') || ($key == 'bank_amount')||$key=='time_create'){
	} else {
		$step_2[$key] = $$key;
	}	
}

if(!$insurer){
	$err_text .= "<li class=\"text-danger\">Не указан тип страхователя</li>";
}

# Если указали способ оплаты банковский документ 
if ($a7_type_paid == 3){
	if (!$bank_number){
		$err_text .= "<li class=\"text-danger\">Не указан Номер банковского документа</li>";
	}
	if (!$bank_date){
		$err_text .= "<li class=\"text-danger\">Не указана Дата банковского документа</li>";
	}
	if (!$bank_amount){
		$err_text .= "<li class=\"text-danger\">Не указана Сумма банковского документа</li>";
	}
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
	if(!valid_date($date_birth) || age($date_birth)> 150){
		$err_text .= "<li class=\"text-danger\">Дата рождения собственника указанна не верно</li>";
	}		
	if($first_name && $second_name && $third_name && $date_birth && mysql_num_rows(mysql_query("SELECT * FROM `bad_people` WHERE first_name = '".trim($first_name)."' AND second_name = '".trim($second_name)."' AND third_name = '".trim($third_name)."' AND date_of_birth = '".$date_birth."'"))>0){
		$err_text .= "<li class=\"text-danger\">Страхователь находится в списке людей, страхование которых запрещено!</li>";
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
	if($jur_inn && !is_valid_inn($jur_inn)){
		$err_text .= "<li class=\"text-danger\">Не верно указан ИНН</li>";
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
// if(!$phone){
// 	$err_text .= "<li class=\"text-danger\">Не указан номер телефона</li>";
// }
//Проверка собственника если он отличается от страхователя
if($insisown == 2){
	//Если собственник физ лицо
	if($insurer == 1){
		if(!$owner_second_name){
			$err_text .= "<li class=\"text-danger\">Не указана фамилия собственника</li>";
		}
		if(!$owner_first_name){
			$err_text .= "<li class=\"text-danger\">Не указано имя собственника</li>";
		}
		if(!$owner_third_name){
			$err_text .= "<li class=\"text-danger\">Не указано отчество собственника</li>";
		}
		if(!$owner_date_birth){
			$err_text .= "<li class=\"text-danger\">Не указана дата рождения собственника</li>";
		}
		if(!valid_date($owner_date_birth) || age($owner_date_birth)>150){
			$err_text .= "<li class=\"text-danger\">Дата рождения собственника указанна не верно</li>";
		}	
		if($owner_first_name && $owner_second_name && $owner_third_name && $owner_date_birth && mysql_num_rows(mysql_query("SELECT * FROM `bad_people` WHERE first_name = '".trim($owner_first_name)."' AND second_name = '".trim($owner_second_name)."' AND third_name = '".trim($owner_third_name)."' AND date_of_birth = '".$owner_date_birth."'"))>0){
			$err_text .= "<li class=\"text-danger\">Страхователь находится в списке людей, страхование которых запрещено!</li>";
		}
		if(!$owner_doc_name){
			$err_text .= "<li class=\"text-danger\">Не указано наименование документа удостоверяющего личность собственника</li>";
		}
		if(!$owner_doc_series){
			$err_text .= "<li class=\"text-danger\">Не указана серия документа удостоверяющего личность собственника</li>";
		}
		if(!$owner_doc_number){
			$err_text .= "<li class=\"text-danger\">Не указан номер документа удостоверяющего личность собственника</li>";
		}			
	}
}
//Проверка на присутствие допущенных людей в списке лец страховать которых нельзя
//Данные по водителям
if($_SESSION["step_1"]["drivers"] == 2){
	for($x=1;$x<6;$x++){
		if(isset($_SESSION["step_1"]["driver_$x"])){
			if(mysql_num_rows(mysql_query("SELECT * FROM `bad_people` WHERE first_name = '".trim(${"driver_".$x."_first_name"})."' AND second_name = '".trim(${"driver_".$x."_second_name"})."' AND third_name = '".trim(${"driver_".$x."_third_name"})."' AND date_of_birth = '".${"driver_".$x."_date_birth"}."'"))>0){
				$err_text .= '<li class="text-danger">Страхование '.${"driver_".$x."_second_name"}.' '.${"driver_".$x."_first_name"}.' '.${"driver_".$x."_third_name"}.' запрещенно!</li>';
			}
		}
	}
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
if(!$auto_reg_number){
	$err_text .= "<li class=\"text-danger\">Не указан номер государственного регистрационного знака</li>";
}
if(!$auto_doc_type){
	$err_text .= "<li class=\"text-danger\">Не указано название документа о регистрации ТС</li>";
}
if(!$auto_doc_series){
	$err_text .= "<li class=\"text-danger\">Не указана серия документа о регистрации ТС</li>";
}
if($_SESSION['step_1']['place_reg'] == 1 && $auto_doc_series && iconv_strlen($auto_doc_series,'UTF-8') > 4){
	$err_text .= "<li class=\"text-danger\">Не верно указана серия документа о регистрации ТС</li>";
}
if(!$auto_doc_number){
	$err_text .= "<li class=\"text-danger\">Не указан номер документа о регистрации ТС</li>";
}
if($_SESSION['step_1']['place_reg'] == 1 && $auto_doc_number && iconv_strlen($auto_doc_number,'UTF-8') > 6){
	$err_text .= "<li class=\"text-danger\">Не верно указан номер документа о регистрации ТС</li>";
}
if(!$auto_doc_date){
	$err_text .= "<li class=\"text-danger\">Не указана дата выдачи документа о регистрации ТС</li>";
}
if(!valid_date($auto_doc_date)){
	$err_text .= "<li class=\"text-danger\">Не верно указана дата выдачи документа о регистрации ТС</li>";
}
if(!$start_date){
	$err_text .= "<li class=\"text-danger\">Не указана дата начала действия договора страхования</li>";
}
if(!valid_date($start_date)){
	$err_text .= "<li class=\"text-danger\">Дата начала действия договора страхования указана неверно</li>";
}
if($auto_diag_card_next_date && !strtotime($auto_diag_card_next_date)){
	$err_text .= "<li class=\"text-danger\">Не верно указана дата срока действия диагностической карты</li>";
}
if($_SESSION['step_1']['category'] == 2 || $_SESSION['step_1']['category'] == 3){
	if($_SESSION['step_1']['capacity'] == 1 && $power > 50){
		$err_text .= "<li class=\"text-danger\">Неверно указана мощность транспортного средства</li>";
	}
	if($_SESSION['step_1']['capacity'] == 2 && ($power <= 50 || $power > 70)){
		$err_text .= "<li class=\"text-danger\">Неверно указана мощность транспортного средства</li>";
	}	
	if($_SESSION['step_1']['capacity'] == 3 && ($power <= 70 || $power > 100)){
		$err_text .= "<li class=\"text-danger\">Неверно указана мощность транспортного средства</li>";
	}
	if($_SESSION['step_1']['capacity'] == 4 && ($power <= 100 || $power > 120)){
		$err_text .= "<li class=\"text-danger\">Неверно указана мощность транспортного средства</li>";
	}
	if($_SESSION['step_1']['capacity'] == 5 && ($power <= 120 || $power > 150)){
		$err_text .= "<li class=\"text-danger\">Неверно указана мощность транспортного средства</li>";
	}
	if($_SESSION['step_1']['capacity'] == 6 && $power < 150){
		$err_text .= "<li class=\"text-danger\">Неверно указана мощность транспортного средства</li>";
	}
	if($power < 23 || $power > 2719){
		$err_text .= "<li class=\"text-danger\">Неверно указана мощность транспортного средства. Минимально допустимое занчение 23, максимальное 2719</li>";
	}				
}
//Ограничения по весу для категорий С
if($_SESSION['step_1']['category'] == 4){
	// if($max_weight < 3500){//отключил проверку на минимальный вес по звонку Чашкова 7.10.2015
	// 	$err_text .= "<li class=\"text-danger\">Разрешённая максимальная масса не может быть меньше 3500 кг</li>";
	// }
	if($max_weight > 16000){
		$err_text .= "<li class=\"text-danger\">Разрешённая максимальная масса не может быть более 16000 кг</li>";
	}	
}
if($_SESSION['step_1']['category'] == 5){
	if($max_weight < 16000){
		$err_text .= "<li class=\"text-danger\">Разрешённая максимальная масса не может быть меньше 16000 кг</li>";
	}
	if($max_weight > 100000){
		$err_text .= "<li class=\"text-danger\">Разрешённая максимальная масса не может быть более 100000 кг</li>";
	}	
}
//Ограничение по количеству пассажирских мест для категории D
if($_SESSION['step_1']['category'] == 6){
	if($number_seats < 8){
		$err_text .= "<li class=\"text-danger\">Минимальное число пассажиров не может быть меньше 6</li>";
	}
	if($number_seats > 16){
		$err_text .= "<li class=\"text-danger\">Максимальное число пассажиров не может быть более 16</li>";
	}	
}
if($_SESSION['step_1']['category'] == 7){
	if($number_seats < 16){
		$err_text .= "<li class=\"text-danger\">Максимальное число пассажиров не может быть меньше 16</li>";
	}
	if($number_seats > 200){
		$err_text .= "<li class=\"text-danger\">Максимальное число пассажиров не может быть более 200</li>";
	}	
}
//Проверка на возраст водителя
//Данные по водителям
if($_SESSION["step_1"]["drivers"] == 2){
	for($x=1;$x<6;$x++){
		if(isset($_SESSION["step_1"]["driver_$x"])){
			if(empty(${"driver_".$x."_first_name"})){
				$err_text .= "<li class=\"text-danger\">Не указано имя водителя №".$x."</li>";
			}
			if(empty(${"driver_".$x."_second_name"})){
				$err_text .= "<li class=\"text-danger\">Не указана фамилия водителя №".$x."</li>";
			}
			if(empty(${"driver_".$x."_third_name"})){
				$err_text .= "<li class=\"text-danger\">Не указано отчество водителя №".$x."</li>";
			}
			if(empty(${"driver_".$x."_date_birth"})){
				$err_text .= "<li class=\"text-danger\">Не указана дата рождения водителя №".$x."</li>";
			}
			if(empty(${"driver_".$x."_series"})){
				$err_text .= "<li class=\"text-danger\">Не указана серия водительского удостоверения водителя №".$x."</li>";
			}
			if(empty(${"driver_".$x."_number"})){
				$err_text .= "<li class=\"text-danger\">Не указан номер водительского удостоверения водителя №".$x."</li>";
			}
			if(empty(${"driver_".$x."_experience"}) && ${"driver_".$x."_experience"} != 0){
				$err_text .= "<li class=\"text-danger\">Не указан стаж водителя №".$x."</li>";
			}
			if(valid_date(${"driver_".$x."_date_birth"})){
				if(age(${"driver_".$x."_date_birth"}) < 14){
					$err_text .= "<li class=\"text-danger\">Возраст водителя №".$x." не может быть меньше 14 лет</li>";
				}
				if(age(${"driver_".$x."_date_birth"}) > 150){
					$err_text .= "<li class=\"text-danger\">Возраст водителя №".$x." не может быть больше 150 лет</li>";
				}
			} else {
				$err_text .= "<li class=\"text-danger\">Дата рождения водителя №".$x." указанна неверно</li>";
			}
			if((age(${"driver_".$x."_date_birth"}) - ${"driver_".$x."_experience"}) < 18 && $_SESSION['step_1']['category'] > 1){
				$err_text .= "<li class=\"text-danger\">Неверно указан стаж либо дата рождения, водителя №".$x."</li>";
			}
			if($_SESSION['step_1']['driver_'.$x] == 1 && (age(${"driver_".$x."_date_birth"}) > 22 || ${"driver_".$x."_experience"} > 3)){
				$err_text .= "<li class=\"text-danger\">Неверно указан стаж, либо дата рождения, либо данные по водителю  №".$x." на этапе расчёта или оформления (ошибка 1)</li>";
			}
			if($_SESSION['step_1']['driver_'.$x] == 2 && (age(${"driver_".$x."_date_birth"}) < 22 || ${"driver_".$x."_experience"} > 3)){
				$err_text .= "<li class=\"text-danger\">Неверно указан стаж, либо дата рождения, либо данные по водителю  №".$x." на этапе расчёта или оформления (ошибка 2)</li>";
			}
			if($_SESSION['step_1']['driver_'.$x] == 3 && (age(${"driver_".$x."_date_birth"}) > 22 || ${"driver_".$x."_experience"} < 3)){
				$err_text .= "<li class=\"text-danger\">Неверно указан стаж, либо дата рождения, либо данные по водителю  №".$x." на этапе расчёта или оформления (ошибка 3)</li>";
			}
			if($_SESSION['step_1']['driver_'.$x] == 4 && (age(${"driver_".$x."_date_birth"}) < 22 || ${"driver_".$x."_experience"} < 3)){
				$err_text .= "<li class=\"text-danger\">Неверно указан стаж, либо дата рождения, либо данные по водителю  №".$x." на этапе расчёта или оформления (ошибка 4)</li>";
			}																											
		}
	}
}
// if(!$start_time){
// 	$err_text .= "<li class=\"text-danger\">Не указано время начала действия договора страхования</li>";
// }
//Проверка на минимальный период ипользования ТС для физ лиц
if($_SESSION['step_1']['type_ins'] == 'phiz' && $_SESSION['step_1']['place_reg'] != 3){
	//Первый период использования
	if($auto_used_start_1 && $auto_used_end_1){		
		if(!valid_date($auto_used_start_1)){
				$err_text .= "<li class=\"text-danger\">Дата начала периода использования №1 ТС указанна неверно</li>";			
		}		
		if(!valid_date($auto_used_end_1)){
				$err_text .= "<li class=\"text-danger\">Дата окончания периода использования №1 ТС указанна неверно</li>";			
		}		
		if(strtotime($auto_used_start_1) > strtotime($auto_used_end_1)){
			$err_text .= "<li class=\"text-danger\">Дата окончания периода использования №1 ТС не может быть меньше даты начала периода использования ТС №1</li>";
		}
		//Првоеряем на соблюдения условия минимального периода
		if(strtotime(date('d.m.Y', strtotime($auto_used_start_1 . "+3 months -1 day"))) > strtotime($auto_used_end_1)){
			$err_text .= "<li class=\"text-danger\">Минимальный период использования №1 ТС не может быть меньше трёх месяцев</li>";
		}
	}
	//Второй период использования
	if($auto_used_start_2 && $auto_used_end_2){		
		if(!valid_date($auto_used_start_2)){
				$err_text .= "<li class=\"text-danger\">Дата начала периода использования №2 ТС указанна неверно</li>";			
		}
		if(strtotime($auto_used_start_2) < strtotime($auto_used_end_1)){
				$err_text .= "<li class=\"text-danger\">Дата начала периода использования №2 ТС не может быть меньше даты окончания периода использования №1 ТС</li>";
		}		
		if(!valid_date($auto_used_end_2)){
				$err_text .= "<li class=\"text-danger\">Дата окончания периода использования №2 ТС указанна неверно</li>";			
		}		
		if(strtotime($auto_used_start_2) > strtotime($auto_used_end_2)){
			$err_text .= "<li class=\"text-danger\">Дата окончания периода использования №2 ТС не может быть меньше даты начала периода использования ТС №2</li>";
		}
		//Првоеряем на соблюдения условия минимального периода
		if(strtotime(date('d.m.Y', strtotime($auto_used_start_2 . "+3 months -1 day"))) > strtotime($auto_used_end_2)){
			$err_text .= "<li class=\"text-danger\">Минимальный период использования №2 ТС не может быть меньше трёх месяцев</li>";
		}
	}
	//Третий период использования
	if($auto_used_start_3 && $auto_used_end_3){		
		if(!valid_date($auto_used_start_3)){
				$err_text .= "<li class=\"text-danger\">Дата начала периода использования №3 ТС указанна неверно</li>";			
		}
		if(strtotime($auto_used_start_3) < strtotime($auto_used_end_2)){
				$err_text .= "<li class=\"text-danger\">Дата начала периода использования №3 ТС не может быть меньше даты окончания периода использования №2 ТС</li>";
		}				
		if(!valid_date($auto_used_end_3)){
				$err_text .= "<li class=\"text-danger\">Дата окончания периода использования №3 ТС указанна неверно</li>";			
		}		
		if(strtotime($auto_used_start_3) > strtotime($auto_used_end_3)){
			$err_text .= "<li class=\"text-danger\">Дата окончания периода использования №3 ТС не может быть меньше даты начала периода использования ТС №3</li>";
		}
		//Првоеряем на соблюдения условия минимального периода
		if(strtotime(date('d.m.Y', strtotime($auto_used_start_3 . "+3 months -1 day"))) > strtotime($auto_used_end_3)){
			$err_text .= "<li class=\"text-danger\">Минимальный период использования №3 ТС не может быть меньше трёх месяцев</li>";
		}
	}		
}
//Првоерка формата государственного регистрационного номера ТС
if($_SESSION['step_1']['place_reg'] == '1' && $auto_reg_number != 'Отсутствует'){
	$num_format = "/^[а-яА-Я][0-9]{3}[а-яА-Я]{2}[0-9]{2,3}$|^[а-яА-Я]{2}[0-9]{5,6}$|^[0-9]{4}[а-яА-Я][0-9]{2}$|^[0-9]{3}[а-яА-Я]{1,2}[0-9]{3,5}$|^[0-9]{3,4}[а-яА-Я][0-9]{2}$/u";
	$num_format_2 = "/^[0-9]{4}[а-яА-Я]{2}[0-9]{2,3}$|^[а-яА-Я]{2}[0-9]{6,7}$/u";
	if((!preg_match($num_format, $auto_reg_number) && $_SESSION['step_1']['category'] > 1) || (!preg_match($num_format_2, $auto_reg_number) && ($_SESSION['step_1']['category'] == 1 || $_SESSION['step_1']['category'] == 11))){
		$err_text .= "<li class=\"text-danger\">Государственный регистрационный номер ТС указан неверно.</li>";
	}
}
if(!$md5_id){
	$err_text .= "<li class=\"text-danger\">Не указан уникальный идентификатор полиса</li>";
}
if($action == 'add'){
	if(!$bso_number){
		$err_text .= "<li class=\"text-danger\">Не указан номер БСО</li>";
	}
	if(!$ais_request_identifier && $_SESSION['step_1']['place_reg'] != 3){
		$err_text .= "<li class=\"text-danger\">Не указан номер запроса в АИС РСА</li>";
	}
	if(!$ais_request_identifier && $_SESSION['step_1']['place_reg'] == 3){
		$ais_request_identifier = '';
	}
	//Защита от перевода в статус оформленно договора если у него дата начала страхового периода меньше чем нынешняя дата - 1 день
	//Задел на будущее
	// if(strtotime($start_date) < strtotime(date(d.m.Y), "-1 days")){
	// 	$err_text .= "<li class=\"text-danger\"Дата начала действия страхового периода не указана неверно</li>";
	// }	
}
?>