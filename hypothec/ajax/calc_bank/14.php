<?php
// echo 'сбербанк';
// echo $button_return;
$err_text = '';
//блок проверки данных
if($ins_prog_1){
	if(!$ins_summa_1){
		$err_text .= "<li class=\"text-danger\">Отсутствует страховая сумма по имущественному страхованию.</li>";
	}
	if(!$property_type){
		$err_text .= "<li class=\"text-danger\">Не указана характеристика недвижимого имущества.</li>";
	}
	if(!$trim && $property_type != 'earth'){
		$err_text .= "<li class=\"text-danger\">Не указано включать внутреннюю отделку и инженерное оборудование или нет.</li>";
	}
}
if($ins_prog_2){
	if(!$ins_summa_2){
		$err_text .= "<li class=\"text-danger\">Отсутствует страховая сумма по программе титул.</li>";
	}
	if(!$titul_option){
		$err_text .= "<li class=\"text-danger\">Не указанно количество переходов права собственности в истории недвижимого имущества:</li>";
	}
}
if(!empty($err_text)){
	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\">$button_return</p>";
	exit();
}
//Расчёт
//Расчёт по имущественному страхованию
if($ins_prog_1){
	$tb = 1;
	$koef_age = 1;//Если объект недвижимости старше 40 лет
	$koef_fire = 1;//Имеются источники огня.
	$koef_earth_danger = 1;//Если объект недвижимости старше 40 лет
	$koef_earth_fire = 1;//Имеются источники огня.	
	$formula ='';
	//Получаем базовый тариф
	$query = mysql_query("SELECT * FROM ".($property_type == 'earth' ? '`hypothec_earth_tb`' : '`hypothec_house_tb`')." WHERE `id_bank` = ".$id_bank." AND `active` = 1 ".($property_type == 'earth' ? '' : ' AND `id` = '.$property_type));
	if(mysql_num_rows($query)<1){
		echo '<center><span class="text-danger"><b>Ошибка! Не удалось получить базовый тариф. Обратитесь к администратору.</b></span></center>';	
		echo $button_return;
		exit;	
	}
	$tb_data = mysql_fetch_assoc($query);
	$tb = ($property_type == 'earth' ? $tb_data['koef'] : $tb_data['koef_'.$trim]);
	//Получаем доп коэффициенты для строений
	if($property_type != 'earth'){
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
	}
	//Получаем доп коэффициенты для земельных участков
	if($property_type == 'earth'){
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
	}

	if($formula){
		$formula = $tb.$formula.' = ';
	}
	$koef = round($tb * $koef_age * $koef_fire * $koef_earth_fire * $koef_earth_danger ,2);
	$tarif = round(($ins_summa_1 / 100 )* $koef, 2);

	$_SESSION['calc'][1]['koef'] = $koef;
	$_SESSION['calc'][1]['tb'] = $tb;
	$_SESSION['calc'][1]['koef_age'] = $koef_age;
	$_SESSION['calc'][1]['koef_fire'] = $koef_fire;
	$_SESSION['calc'][1]['koef_earth_danger'] = $koef_earth_danger;
	$_SESSION['calc'][1]['koef_earth_fire'] = $koef_earth_fire;
	$_SESSION['calc'][1]['tarif'] = $tarif;
	$calc_result = '
	<div class="row">
		<div  class="col-md-3 col-md-offset-5">		
		<b>Расчёт по имущественному страхованию:</b>
		<hr class="hr_line">
		<em>
		<ul>
		<li>Страховая сумма: '.number_format($ins_summa_1, 2, '.', ' ').'</li>
		<li>Итоговый коэффициент: '.$formula.$koef.'</li>
		<li>Итоговый страховой тариф:  <span class="text-danger"><b>'.$tarif.'</b></span></li>
		</ul>
		</em>
		<hr class="hr_red">
		</div>
	</div>
	';
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
	$_SESSION['calc'][2]['koef'] = $titul_koef;
	$_SESSION['calc'][2]['tarif'] = $tarif;
	$calc_result .= '
	<div class="row">
		<div class="col-md-3 col-md-offset-5">
		<b>Расчёт по страхованию титула:</b>
		<hr class="hr_line">
		<em>		
		<ul>
		<li>Страховая сумма: '.number_format($ins_summa_2, 2, '.', ' ').'</li>
		<li>Итоговый коэффициент: '.$titul_koef.'</li>
		<li>Итоговый страховой тариф:  <span class="text-danger"><b>'.$tarif.'</b></span></li>
		</ul>
		</em>
		<hr class="hr_red">
		</div>
	</div>
	';	
}



?>
