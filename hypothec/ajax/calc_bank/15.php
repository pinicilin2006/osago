<?php
// echo '<pre>';
// print_r($_POST);
// echo '</pre>';
// echo 'сбербанк';
// echo $button_return;
$err_text = '';
//блок проверки данных
if($ins_prog_1){
	if(!$ins_property_type_1 && !$ins_property_type_2){
		$err_text .= "<li class=\"text-danger\">Не указан вид недвижимого имущества.</li>";
	}
	if($ins_property_type_1){
		if(!$ins_summa_house){
			$err_text .= "<li class=\"text-danger\">Отсутствует страховая сумма по строению.</li>";
		}
		if(!$property_type){
			$err_text .= "<li class=\"text-danger\">Не указана характеристика недвижимого имущества.</li>";
		}
		if(!$trim){
			$err_text .= "<li class=\"text-danger\">Не указано включать внутреннюю отделку и инженерное оборудование или нет.</li>";
		}
	}
	if($ins_property_type_2){
		if(!$ins_summa_earth){
			$err_text .= "<li class=\"text-danger\">Отсутствует страховая сумма по земельному участку.</li>";
		}	
	}
}

if($ins_prog_2){
	if(!$ins_summa_2){
		$err_text .= "<li class=\"text-danger\">Отсутствует страховая сумма по программе личного страхования.</li>";
	}
	if(!$titul_option){
		$err_text .= "<li class=\"text-danger\">Не указанно количество переходов права собственности в истории недвижимого имущества:</li>";
	}
}

if($ins_prog_3){
	if(!$ins_summa_3){
		$err_text .= "<li class=\"text-danger\">Отсутствует страховая сумма по программе личного страхования.</li>";
	}
	if(!$prog_3_type){
		$err_text .= "<li class=\"text-danger\">Не указан вид личного страховая.</li>";
	}
	if(!$prog_3_num){
		$err_text .= "<li class=\"text-danger\">Не указано количество застрахованных.</li>";
	}
	if($prog_3_num){
		for($n = 1;$n <= $prog_3_num;$n++){
			if(!${"date_birth_".$n}){
				$err_text .= "<li class=\"text-danger\">Не указанна дата рождения застрахованного №".$n."</li>";
			}
			if(age(${"date_birth_".$n}) < 18){
				$err_text .= "<li class=\"text-danger\">Указанная дата рождения застрахованного №".$n." меньше минимально допустимого возраста 18 лет.</li>";
			}			
			if(!${"sex_".$n}){
				$err_text .= "<li class=\"text-danger\">Не указан пол застрахованного №".$n."</li>";
			}
			if(!${"sport_".$n}){
				$err_text .= "<li class=\"text-danger\">Не указанно увлечение спортом застрахованного №".$n."</li>";
			}
			if(${"sport_".$n}){
				if(!${"sport_type_".$n}){
					$err_text .= "<li class=\"text-danger\">Не указан уровень увлечения спортом застрахованного №".$n."</li>";
				}
				if(!${"sport_category_".$n}){
					$err_text .= "<li class=\"text-danger\">Не указан вид спорта (категория) застрахованного №".$n."</li>";
				}								
			}
			if(!${"work_category_".$n}){
				$err_text .= "<li class=\"text-danger\">Не указана сфера деятельности (категория) застрахованного №".$n."</li>";
			}
			if($prog_3_type && $prog_3_type == 2){
				if(!${"health_".$n}){
					$err_text .= "<li class=\"text-danger\">Не указанно предоставление медицинского обследования застрахованного №".$n."</li>";
				}
				if(!${"disease_".$n}){
					$err_text .= "<li class=\"text-danger\">Не указанно имеются ли заболевания у застрахованного №".$n."</li>";
				}
				if(${"disease_".$n} == 'yes'){
					if(!$_POST{"disease_name_".$n}){
						$err_text .= "<li class=\"text-danger\">Не указаны заболевания у застрахованного №".$n."</li>";
					}					
				}									
			}	
		}
	}		
}

if(!empty($err_text)){
	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\">$button_return</p>";
	exit();
}
//Расчёт
$total_tarif = 0;
//Расчёт по имущественному страхованию
if($ins_prog_1){
	//Расчёт
	$total_tarif = 0;
	$tb = 1;
	$t = 1;//Тариф итоговый
	$koef_age = 1;//Если объект недвижимости старше 40 лет
	$koef_fire = 1;//Имеются источники огня.
	$formula ='';
	$calc_result = '';
	//Рассчёты
	if($ins_property_type_1){
		$query = mysql_query("SELECT * FROM `hypothec_house_tb` WHERE `id_bank` = ".$id_bank." AND `active` = 1 AND `id` = ".$property_type);
		if(mysql_num_rows($query)<1){
			echo '<center><span class="text-danger"><b>Ошибка! Не удалось получить базовый тариф. Обратитесь к администратору.</b></span></center>';	
			echo $button_return;
			exit;	
		}
		$tb_data = mysql_fetch_assoc($query);
		$tb = $tb_data['koef_'.$trim];
		//Получаем доп коэффициенты для строений
			if($house_age){
				$query = mysql_query("SELECT * FROM `hypothec_house_age_koef` WHERE `id_bank` = ".$id_bank." AND `active` = 1");
				if(mysql_num_rows($query)<1){
				echo '<center><span class="text-danger"><b>Ошибка! Не удалось получить тариф за возраст здания. Обратитесь к администратору.</b></span></center>';	
				echo $button_return;
				exit;			
				}
				$koef_age_data = mysql_fetch_assoc($query);
				$koef_age = $koef_age_data['koef'];
				$formula .= '*'.$koef_age;
			}
			if($house_fire){
				$query = mysql_query("SELECT * FROM `hypothec_house_fire_koef` WHERE `id_bank` = ".$id_bank." AND `active` = 1");
				if(mysql_num_rows($query)<1){
				echo '<center><span class="text-danger"><b>Ошибка! Не удалось получить тариф за источник открытого огня. Обратитесь к администратору.</b></span></center>';	
				echo $button_return;
				exit;			
				}
				$koef_fire_data = mysql_fetch_assoc($query);
				$koef_fire = $koef_fire_data['koef'];
				$formula .= '*'.$koef_fire;
			}	
		if($formula){
			$formula = $tb.$formula.' = ';
		}
		$koef = round($tb * $koef_age * $koef_fire ,2);
		$tarif = round(($ins_summa_house / 100 )* $koef, 2);
		$total_tarif = $total_tarif + $tarif;
		$_SESSION['calc'][1]['house']['koef'] = $koef;
		$_SESSION['calc'][1]['house']['tb'] = $tb;
		$_SESSION['calc'][1]['house']['koef_age'] = $koef_age;
		$_SESSION['calc'][1]['house']['koef_fire'] = $koef_fire;
		$_SESSION['calc'][1]['house']['tarif'] = $tarif;
		$calc_result .= '
		<div class="row">
			<div  class="col-md-3 col-md-offset-5">
			<b>Страхование недвижимого имущества, за исключением земельных участков:</b>
			<hr class="hr_line">
			<em>
			<ul>
			<li>Страховая сумма: '.number_format($ins_summa_house, 2, '.', ' ').'</li>
			<li>Итоговый коэффициент: '.$formula.$koef.'</li>
			<li>Итоговый страховой тариф:  <span class="text-danger"><b>'.$tarif.'</b></span></li>
			</ul>
			</em>
			<hr class="hr_red_2">
			</div>
		</div>
		';
	}
	if($ins_property_type_2){
		$formula = '';
		$koef_earth_fire = 1;
		$koef_earth_danger = 1;
		$query = mysql_query("SELECT * FROM `hypothec_earth_tb` WHERE `id_bank` = ".$id_bank." AND `active` = 1");
		if(mysql_num_rows($query)<1){
			echo '<center><span class="text-danger"><b>Ошибка! Не удалось получить базовый тариф (земля). Обратитесь к администратору.</b></span></center>';	
			echo $button_return;
			exit;	
		}
		$tb_data = mysql_fetch_assoc($query);
		$tb = $tb_data['koef'];
		if($earth_fire){
			$query = mysql_query("SELECT * FROM `hypothec_earth_fire_koef` WHERE `id_bank` = ".$id_bank." AND `active` = 1");
			if(mysql_num_rows($query)<1){
			echo '<center><span class="text-danger"><b>Ошибка! Не удалось получить тариф за источник открытого огня. Обратитесь к администратору.</b></span></center>';	
			echo $button_return;
			exit;			
			}
			$koef_earth_fire_data = mysql_fetch_assoc($query);
			$koef_earth_fire = $koef_earth_fire_data['koef'];
			$formula .= '*'.$koef_earth_fire;
		}
		if($earth_danger){
			$query = mysql_query("SELECT * FROM `hypothec_earth_danger_koef` WHERE `id_bank` = ".$id_bank." AND `active` = 1");
			if(mysql_num_rows($query)<1){
			echo '<center><span class="text-danger"><b>Ошибка! Не удалось получить тариф за наличие объектов повышенной опасности. Обратитесь к администратору.</b></span></center>';	
			echo $button_return;
			exit;			
			}
			$koef_earth_danger_data = mysql_fetch_assoc($query);
			$koef_earth_danger = $koef_earth_danger_data['koef'];
			$formula .= '*'.$koef_earth_danger;
		}		
		if($formula){
			$formula = $tb.$formula.' = ';
		}
		$koef = round($tb * $koef_earth_fire * $koef_earth_danger ,2);		
		$tarif = round(($ins_summa_earth / 100 )* $koef, 2);
		$total_tarif = $total_tarif + $tarif;
		$_SESSION['calc'][1]['earth']['tb'] = $tb;
		$_SESSION['calc'][1]['earth']['koef_earth_fire'] = $koef_earth_fire;
		$_SESSION['calc'][1]['earth']['koef_earth_danger'] = $koef_earth_danger;
		$_SESSION['calc'][1]['earth']['koef'] = $koef;
		$_SESSION['calc'][1]['earth']['tarif'] = $tarif;
		$calc_result .= '
		<div class="row">
			<div  class="col-md-3 col-md-offset-5">
			<b>Страхование недвижимого имущества - земельные участки:</b>
			<hr class="hr_line">
			<em>
			<ul>
			<li>Страховая сумма: '.number_format($ins_summa_earth, 2, '.', ' ').'</li>
			<li>Итоговый коэффициент: '.$formula.$koef.'</li>
			<li>Итоговый страховой тариф:  <span class="text-danger"><b>'.$tarif.'</b></span></li>
			</ul>
			</em>
			<hr class="hr_red_2">
			</div>
		</div>
		';	
	}
}

if($ins_prog_2){
	$query = mysql_query("SELECT * FROM `hypothec_titul_tb` WHERE `id_bank` = ".$id_bank." AND `active` = 1 AND `id` = ".$titul_option);
		if(mysql_num_rows($query)<1){
			echo '<center><span class="text-danger"><b>Ошибка! Не удалось получить коэффициент по программе титул. Обратитесь к администратору.</b></span></center>';	
			echo $button_return;
			exit;			
		}
	$titul_data = mysql_fetch_assoc($query);
	$titul_koef = $titul_data['koef'];
	$tarif = round(($ins_summa_2 / 100) * $titul_koef, 2);
	$total_tarif = $total_tarif + $tarif;
	$_SESSION['calc'][2]['koef'] = $titul_koef;
	$_SESSION['calc'][2]['tarif'] = $tarif;
	$calc_result .= '
	<div class="row">
		<div class="col-md-3 col-md-offset-5">
		<b>Страхованию титула:</b>
		<hr class="hr_line">
		<em>		
		<ul>
		<li>Страховая сумма: '.number_format($ins_summa_2, 2, '.', ' ').'</li>
		<li>Итоговый коэффициент: '.$titul_koef.'</li>
		<li>Итоговая страховая премия:  <span class="text-danger"><b>'.$tarif.'</b></span></li>
		</ul>
		</em>
		<hr class="hr_red_2">
		</div>
	</div>
	';	
}

if($ins_prog_3){
	for($n = 1;$n <= $prog_3_num;$n++){
		$koef = 1;
		$koef_sport = 1;
		$koef_work = 1;
		$koef_health = 1;
		$koef_medical_examination = 1;
		$age = age(${"date_birth_".$n});
		//Основной коэффициент
		$query = mysql_query("SELECT * FROM `hypothec_private_".$prog_3_type."_tb` WHERE `age` = ".$age." AND `id_bank` = ".$id_bank);
		if(mysql_num_rows($query)<1){
			echo '<center><span class="text-danger"><b>Ошибка! Не удалось получить коэффициент по личному страхованию для застрахованного №'.$n.'. Обратитесь к администратору.</b></span></center>';	
			echo $button_return;
			exit;			
		}
		$koef_data = mysql_fetch_assoc($query);
		$koef = $koef_data[${"sex_".$n}];
		//Коэффициент за спорт
		if(${"sport_".$n} == 'yes'){
			$query = mysql_query("SELECT * FROM `hypothec_sport_koef` WHERE `id` = ".${"sport_category_".$n}." AND `id_bank` = ".$id_bank." AND `active` = 1");
			if(mysql_num_rows($query)<1){
				echo '<center><span class="text-danger"><b>Ошибка! Не удалось получить коэффициент за спорт для застрахованного №'.$n.'. Обратитесь к администратору.</b></span></center>';	
				echo $button_return;
				exit;			
			}
			$koef_sport_data = mysql_fetch_assoc($query);
			$koef_sport = (${"sport_type_".$n} == 1 ? $koef_sport_data['koef_1'] : $koef_sport_data['koef_2']);			
		}
		//Коэффициент за труд
		$query = mysql_query("SELECT * FROM `hypothec_work_koef` WHERE `id` = ".${"work_category_".$n}." AND `id_bank` = ".$id_bank." AND `active` = 1");
		if(mysql_num_rows($query)<1){
			echo '<center><span class="text-danger"><b>Ошибка! Не удалось получить коэффициент за труд для застрахованного №'.$n.'. Обратитесь к администратору.</b></span></center>';	
			echo $button_return;
			exit;			
		}
		$koef_work_data = mysql_fetch_assoc($query);
		$koef_work = $koef_work_data['koef'];
		//Коэффициенты за здоровье
		if($prog_3_type == '2'){
			if(${"disease_".$n} == 'yes'){
				//Коэффициент за болезни
				if($age < 60){
					$k_name = 'koef_1';
				}
				if($age > 59 && $age < 66){
					$k_name = 'koef_2';
				}
				if($age > 65){
					$k_name = 'koef_3';
				}
				$query = "SELECT MAX(`".$k_name."`) AS koef FROM `hypothec_health_koef` WHERE `id` IN (";
				foreach ($_POST{"disease_name_".$n} as $key => $val) {
					$query .= $val.",";
				}
				$query = substr($query, 0, -1);
				$query .= ")";
				$query = mysql_query($query);
				if(mysql_num_rows($query)<1){
					echo '<center><span class="text-danger"><b>Ошибка! Не удалось получить коэффициент за здоровье для застрахованного №'.$n.'. Обратитесь к администратору.</b></span></center>';	
					echo $button_return;
					exit;			
				}
				$koef_health_data = mysql_fetch_assoc($query);
				$koef_health = $koef_health_data['koef'];								
			}
			//Коэффициент за медицинское обследование
			$query = mysql_query("SELECT * FROM `hypothec_medical_examination_koef` WHERE `id` = ".${"health_".$n});
			if(mysql_num_rows($query)<1){
				echo '<center><span class="text-danger"><b>Ошибка! Не удалось получить коэффициент за прохождение медицинского о для застрахованного №'.$n.'. Обратитесь к администратору.</b></span></center>';	
				echo $button_return;
				exit;			
			}		
			$koef_medical_examination_data = mysql_fetch_assoc($query);
			$koef_medical_examination = $koef_medical_examination_data['koef'];
		}
	//Производим расчёт
		$koef_all = round($koef * $koef_sport * $koef_work * $koef_health * $koef_medical_examination , 2);
		$ins_summa_personal = round($ins_summa_3 / $prog_3_num, 2);
		$tarif = round(($ins_summa_personal / 100) * $koef_all, 2);
		$total_tarif = $total_tarif + $tarif;
		$_SESSION['calc'][3][$n]['koef'] = $koef;
		$_SESSION['calc'][3][$n]['koef_sport'] = $koef_sport;
		$_SESSION['calc'][3][$n]['koef_work'] = $koef_work;
		$_SESSION['calc'][3][$n]['koef_health'] = $koef_health;
		$_SESSION['calc'][3][$n]['koef_medical_examination'] = $koef_medical_examination;
		$_SESSION['calc'][3][$n]['koef_all'] = $koef_all;
		$_SESSION['calc'][3][$n]['tarif'] = $tarif;
		$calc_result .= '
		<div class="row">
			<div class="col-md-3 col-md-offset-5">
			<b>Личное страхование.</b> Застрахованный №'.$n.':
			<hr class="hr_line">
			<em>		
			<ul>
			<li>Страховая сумма: '.number_format($ins_summa_personal, 2, '.', ' ').'</li>
			<li>Итоговый коэффициент: '.$koef_all.'</li>
			<li>Итоговая страховая премия:  <span class="text-danger"><b>'.$tarif.'</b></span></li>
			</ul>
			</em>
			<hr class="hr_red_2">
			</div>
		</div>
		';			
	}	
}

//Выводим общую стоимсоть полиса
if($total_tarif > 0){
	$calc_result .= '
		<div class="row">
			<div class="col-md-3 col-md-offset-5">
			<b><h4>Общая стоимость полиса: <span class="text-danger">'.number_format($total_tarif, 2, '.', ' ').'</span></b></h4>
			<hr class="hr_red_2">
			</div>
		</div>
		';			
}


?>
