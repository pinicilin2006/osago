<?php
// echo 'сбербанк';
// echo $button_return;
$err_text = '';
//блок проверки данных
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
if(!empty($err_text)){
	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\">$button_return</p>";
	exit();
}
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
	$_SESSION['calc']['house']['koef'] = $koef;
	$_SESSION['calc']['house']['tb'] = $tb;
	$_SESSION['calc']['house']['koef_age'] = $koef_age;
	$_SESSION['calc']['house']['koef_fire'] = $koef_fire;
	$_SESSION['calc']['house']['tarif'] = $tarif;
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
	$query = mysql_query("SELECT * FROM `hypothec_earth_tb` WHERE `id_bank` = ".$id_bank." AND `active` = 1");
	if(mysql_num_rows($query)<1){
		echo '<center><span class="text-danger"><b>Ошибка! Не удалось получить базовый тариф (земля). Обратитесь к администратору.</b></span></center>';	
		echo $button_return;
		exit;	
	}
	$tb_data = mysql_fetch_assoc($query);
	$tb = $tb_data['koef'];
	$tarif = round(($ins_summa_earth / 100 )* $tb, 2);
	$total_tarif = $total_tarif + $tarif;
	$_SESSION['calc']['earth']['tb'] = $tb;
	$_SESSION['calc']['earth']['tarif'] = $tarif;
	$calc_result .= '
	<div class="row">
		<div  class="col-md-3 col-md-offset-5">
		<b>Страхование недвижимого имущества - земельные участки:</b>
		<hr class="hr_line">
		<em>
		<ul>
		<li>Страховая сумма: '.number_format($ins_summa_earth, 2, '.', ' ').'</li>
		<li>Итоговый коэффициент: '.$tb.'</li>
		<li>Итоговый страховой тариф:  <span class="text-danger"><b>'.$tarif.'</b></span></li>
		</ul>
		</em>
		<hr class="hr_red_2">
		</div>
	</div>
	';	
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
