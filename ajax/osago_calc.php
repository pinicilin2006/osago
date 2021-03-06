<?php
session_start();
// if(!isset($_SESSION['user_id'])){
// 	header("Location: login.php");
// 	exit;
// }
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// exit();
require_once('../config.php');
require_once('../function.php');
//require_once('../template/header.html');
connect_to_base();
$err_text='';
if($_SESSION["step_1"]){
	unset($_SESSION["step_1"]);
}
if($_SESSION["calc"]){
	unset($_SESSION["calc"]);
}
foreach($_POST as $key => $val){
	if(empty($val)){
		continue;
	}
	$$key = mysql_escape_string($val);
	$_SESSION["step_1"]["$key"] = mysql_escape_string($val);
}
//exit();
//проверяем на наличие переданных данных
$err_text = '';
if(!isset($_SESSION['user_id'])){
	$err_text .= "<li class=\"text-danger\">Отсутствуют данные сессии. Вам необходимо выйти из программы и снова в неё войти.</li>";
}
if(!$type_ins){
	$err_text .= "<li class=\"text-danger\">Не указан собственника ТС</li>";
}
if(!$place_reg){
	$err_text .= "<li class=\"text-danger\">Не указано место регистрации ТС</li>";
}
if(!$citizenship){
	$err_text .= "<li class=\"text-danger\">Не указано гражданство собственника ТС</li>";
}
if(!$subject && $place_reg != 2){
	$err_text .= "<li class=\"text-danger\">Не указана территория примущественного использования ТС</li>";
}
if($subject && !$city){
	if(mysql_num_rows(mysql_query("SELECT * FROM `kt_city` WHERE `id_subject` = '".$subject."'")) > 0){
		$err_text .= "<li class=\"text-danger\">Не указан город примущественного использования ТС</li>";
	}
}
if(!$category){
	$err_text .= "<li class=\"text-danger\">Не указан тип (категория) и назначение ТС</li>";
}
if(!$period_use && $place_reg != 2){
	$err_text .= "<li class=\"text-danger\">Не указан период использования ТС</li>";
}
if(!$drivers && $place_reg != 2){
	$err_text .= "<li class=\"text-danger\">Не указано количество водителей, допущенных к управлению ТС</li>";
}
if($drivers && $drivers == 2 && !$driver_1 && $place_reg != 2){
	$err_text .= "<li class=\"text-danger\">Не выбран ни один водитель, допущенный к управлению ТС</li>";
}
if(!$trailer){
	$err_text .= "<li class=\"text-danger\">Отсутствует информация о прицепе</li>";
}
if(!$violations){
	$err_text .= "<li class=\"text-danger\">Отсутствует информация о грубых нарушениях</li>";
}
if(!empty($err_text)){
	echo "<br><p><ol>$err_text</ol></p><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();
}

//Коэффициенты
$tb = 1;//базовый тариф
$kt = 1;//коэфф КТ (за территорию)
$kbm = 1;//коэфф КБМ
$ko = 1;//коэфф КО (количество допущенных лиц к управлению)
$kvs =1;//коэфф КВС (возраст и стаж водителя)
$km = 1;//коэфф КМ (мощность двигателя)
$kpr = 1;//коэфф КПр (прицеп)
$ks = 1;//коэфф КС (период использования)
$kp = 1;//коэфф КП (срок страхования)
$kn = 1;//Коэфф КН (грубые нарушения)

// //закрепляем регионы за одним из списков
// $list_subject_1 = array(50,56,62,71);
// $list_subject_2 = array(2,3,23,27,28,55,63,65,68,1,17,45,4,5,6,7,8,9,10,11,11,13,14,16,18,19,20,21,22,24,25,29,30,31,32,33,34,35,36,37,38,39,40,41,44,46,47,48,49,51,52,54,58,59,60,61,64,66,67,69,70,72,73,75,77,80,81,82,84,86);
// $list_subject_3 = array(42,26,43,57,15,76,78,79,53,74,83,85);

//Получаем ТБ
$tb_query = mysql_fetch_assoc(mysql_query("SELECT * FROM `category` WHERE `id` = '".$category."'"));
if($place_reg !=2){
	//Получаем группу к которой относится регион
	$group = '';
	$subject_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `kt_subject` WHERE `id` = '".$subject."'"));
	$group = $subject_data['group'];
	if(isset($city) && mysql_num_rows(mysql_query("SELECT * FROM `kt_city` WHERE `id` = '".$city."' AND `group` > 0"))){
		$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `kt_city` WHERE `id` = '".$city."'"));
		$group = $city_data['group'];
	}
	/////////////////////////////////////////////////////////////////////////////////////////////////////////
	$tb = $tb_query['tb_'.($type_ins == 'phiz' || $type_ins == 'ip' ? 'phiz' : 'jur').'_'.$group];
} else {
	$tb = $tb_query['tb_'.($type_ins == 'phiz' || $type_ins == 'ip' ? 'phiz' : 'jur').'_4'];	
}
//Получаем КТ
if($place_reg !=2){
	$kt_query = mysql_fetch_assoc(mysql_query("SELECT * FROM `".(isset($city) ? 'kt_city' : 'kt_subject')."` WHERE `id` = '".(isset($city) ? $city : $subject)."'"));
	$kt = $kt_query['koef_'.($category != 11  ? '1' : '2')];
} else {
	$kt = 1.7;//Для ТС иностраного государства
}
//Получаем КБМ
if($place_reg == 1){
	$kbm_query = mysql_fetch_assoc(mysql_query("SELECT * FROM `kbm` WHERE `id` = '".$class_kbm."'"));
	$kbm = $kbm_query["koef"];
}

//Получаем КО для ТС РФ
$ko = ($drivers == '1' || ($type_ins == 'jur') ? 1.8 : 1);
//Для ТС не  РФ
if($place_reg == '2'){
	$ko = ($type_ins == 'jur' ? 1.8 : 1);
}
//Получаем КВС
if($drivers == '2'){
	$kvs_massive = array();
	for($x=1;$x<6;$x++){
		if(isset(${"driver_".$x})){
			if(${"driver_".$x} == 1){
				$kvs_massive[] = 1.8;
			}
			if(${"driver_".$x} == 2){
				$kvs_massive[] = 1.7;
			}
			if(${"driver_".$x} == 3){
				$kvs_massive[] = 1.6;
			}
			if(${"driver_".$x} == 4){
				$kvs_massive[] = 1;
			}			
		}
	}
	$kvs = max($kvs_massive);
}
//Для ТС не РФ
if($place_reg == '2'){
	$kvs = ($type_ins == 'phiz' || $type_ins == 'ip' ? 1.7 : 1);
}

//Получаем КМ
if($category == '2' || $category == '3'){
	$km_query = mysql_fetch_assoc(mysql_query("SELECT * FROM `capacity` WHERE `id` = '".$capacity."'"));
	$km = $km_query["koef"];
}

//Получаем КПр
if($trailer == '2'){
	if($category == '1' || (($category == '2' || $category == '2') && $type_ins == 'jur')){
		$kpr = 1.16;
	}
	if($category == '4'){
		$kpr = 1.4;
	}
	if($category == '5'){
		$kpr = 1.25;
	}
	if($category == '11'){
		$kpr = 1.24;
	} 	 	 
}

//Получаем КС
$ks_query = mysql_fetch_assoc(mysql_query("SELECT * FROM `period_use` WHERE `id` = '".$period_use."'"));
$ks = $ks_query["koef"];

//Получаем КП
if($place_reg != '1'){
	$kp_query = mysql_fetch_assoc(mysql_query("SELECT * FROM `term_insurance` WHERE `id` = '".$term_insurance."'"));
	$kp = $kp_query["koef"];
}

//Получаем КН
if($violations == '2'){
	$kn = 1.5;
}

//Рассчёт страховой премии
if($category == '2' || $category == '3'){
	if($place_reg == '1'){
		if($type_ins != 'jur'){
			$t = $tb * $kt * $kbm * $kvs * $ko * $km * $ks * $kn;
			$formula = 'Т = ТБ * КТ * КБМ * КВС * КО * КМ * КС * КН';
			$koef = "<br>ТБ = $tb <br> КТ = $kt <br> КБМ = $kbm <br> КВС = $kvs <br> КО = $ko <br> КМ = $km <br> КС = $ks <br> КН = $kn";
		} else {
			$t = $tb * $kt * $kbm * $ko * $km * $ks * $kn * $kpr;
			$formula = 'Т = ТБ * КТ * КБМ * КО * КМ * КС * КН * КПр';
			$koef = "<br>ТБ = $tb <br> КТ = $kt <br> КБМ = $kbm <br> КО = $ko <br> КМ = $km <br> КС = $ks <br> КН = $kn <br> КПр = $kpr";			
		}
	}
	if($place_reg == '2'){
		if($type_ins != 'jur'){
			$t = $tb * $kt * $kbm * $kvs * $ko * $km * $kp * $kn;
			$formula = 'Т = ТБ * КТ * КБМ * КВС * КО * КМ * КП * КН';
			$koef = "<br>ТБ = $tb <br> КТ = $kt <br> КБМ = $kbm <br> КВС = $kvs <br> КО = $ko <br> КМ = $km <br> КП = $kp <br> КН = $kn";
		} else {
			$t = $tb * $kt * $kbm * $ko * $km * $kp * $kn * $kpr;
			$formula = 'Т = ТБ * КТ * КБМ * КО * КМ * КП * КН * КПр';
			$koef = "<br>ТБ = $tb <br> КТ = $kt <br> КБМ = $kbm <br> КО = $ko <br> КМ = $km <br> КП = $kp <br> КН = $kn <br> КПр = $kpr";			
		}
	}
	if($place_reg == '3'){
		if($type_ins != 'jur'){
			$t = $tb * $kvs * $ko * $km * $kp;
			$formula = 'Т = ТБ * КВС * КО * КМ * КП';
			$koef = "<br>ТБ = $tb <br> КВС = $kvs <br> КО = $ko <br> КМ = $km <br> КП = $kp";
		} else {
			$t = $tb * $ko * $km * $kp * $kpr;
			$formula = 'Т = ТБ * КО * КМ * КП * КПр';
			$koef = "<br>ТБ = $tb <br> КО = $ko <br> КМ = $km <br> КП = $kp <br> КПр = $kpr";			
		}
	}		
} else {
	if($place_reg == '1'){
		if($type_ins != 'jur'){
			$t = $tb * $kt * $kbm * $kvs * $ko * $ks * $kn * $kpr;
 			$formula = 'Т = ТБ * КТ * КБМ * КВС * КО * КС * КН * КПр';
			$koef = "<br>ТБ = $tb <br> КТ = $kt <br> КБМ = $kbm <br> КВС = $kvs <br> КО = $ko <br> КС = $ks <br> КН = $kn <br> КПр = $kpr";
		} else {
			$t = $tb * $kt * $kbm * $ko * $ks * $kn * $kpr;
			$formula = 'Т = ТБ * КТ * КБМ * КО * КС * КН * КПр';
			$koef = "<br>ТБ = $tb <br> КТ = $kt <br> КБМ = $kbm <br> КО = $ko <br> КС = $ks <br> КН = $kn <br> КПр = $kpr";			
		}
	}
	if($place_reg == '2'){
		if($type_ins != 'jur'){
			$t = $tb * $kt * $kbm * $kvs * $ko * $kp * $kn * $kpr;
			$formula = 'Т = ТБ * КТ * КБМ * КВС * КО * КП * КН * КПр';
			$koef = "<br>ТБ = $tb <br> КТ = $kt <br> КБМ = $kbm <br> КВС = $kvs <br> КО = $ko <br> КП = $kp <br> КН = $kn <br> КПр = $kpr";
		} else {
			$t = $tb * $kt * $kbm * $ko * $kp * $kn * $kpr;
			$formula = 'Т = ТБ * КТ * КБМ * КО * КП * КН * КПр';
			$koef = "<br>ТБ = $tb <br> КТ = $kt <br> КБМ = $kbm <br> КО = $ko <br> КП = $kp <br> КН = $kn <br> КПр = $kpr";			
		}
	}
	if($place_reg == '3'){
		if($type_ins != 'jur'){
			$t = $tb * $kvs * $ko * $kp * $kpr;
			$formula = 'Т = ТБ * КВС * КО * КП * КПр';
			$koef = "<br>ТБ = $tb <br> КВС = $kvs <br> КО = $ko <br> КП = $kp <br> КПр = $kpr";
		} else {
			$t = $tb * $ko * $kp * $kpr;
			$formula = 'Т = ТБ * КО * КП * КПр';
			$koef = "<br>ТБ = $tb <br> КО = $ko <br> КП = $kp <br> КПр = $kpr";			
		}
	}
}
$t = round($t, 2);
//Ограничиваем максимальную сумму страхового тарифа
$t_max = $tb * $kt * ($kn != 1 ? 5 : 3);
$t_max = round($t_max, 2);
if($t > $t_max){
	$t = $t_max;
}
//Занаосим данные в сессию
$_SESSION["calc"]["t"] = $t;
$_SESSION["calc"]["tb"] = $tb;
$_SESSION["calc"]["kt"] = $kt;
$_SESSION["calc"]["kbm"] = $kbm;
$_SESSION["calc"]["ko"] = $ko;
$_SESSION["calc"]["kvs"] = $kvs;
$_SESSION["calc"]["km"] = $km;
$_SESSION["calc"]["kpr"] = $kpr;
$_SESSION["calc"]["ks"] = $ks;
$_SESSION["calc"]["kp"] = $kp;
$_SESSION["calc"]["kn"] = $kn;

echo '
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Результат расчёта стоимости полиса ОСАГО</h3>
	</div>
  	<div class="panel-body">';
echo '
<b>Базовый страховой тариф и коэффициенты:</b>'.$koef.'
<hr>
<b>Формула расчёта:</b> '.$formula.'
<hr>
<b>Итоговый страховой тариф:</b> <span class="text-danger"><b>'.$t.'</b></span>
<hr>
';
if($t == 0){
	echo "<br><ol class='text-danger text-center'>Произошла ошибка при расчёте страховой премии. Страховая премия не может быть равна нулю!</ol><p class=\"text-center\"><button type=\"button\" class=\"btn btn-danger\" id=\"button_return\" onclick=\"button_return();\">Назад</button></p>";
	exit();
}
//echo "<ul><li><h4>Коэффициенты:</h4>$koef</li><li><h4>Формула расчёта:</h4> $formula</li><li><h4>Итоговый страховой тариф:</h4> $t</li></ul><hr>";
//echo "<p class=\"text-center\"><button type=\"button\" class=\"btn btn-success\" >Оформить полис</button></p>";
if($driver_5){
	unset($_SESSION["calc"]);
	echo "<div class='text-danger text-center'><b>Водителей больше 4. Оформление только в офисе компании, т.к. необходимо ставить печати на оборотной стороне полиса.</b></div><hr>";
} else {
	if(isset($md5_id)){
		echo '<a href="/edit_osago_step_2.php?id='.$md5_id.'" class="btn btn-success btn-block " role="button">Продолжить редактирование договора</a>';
	} else {
		echo '<a href="/osago_step_2.php" class="btn btn-success btn-block " role="button">Оформить полис</a>';
	}
}
echo "<button type=\"button\" class=\"btn btn-danger btn-block\" id=\"button_return\" onclick=\"button_return();\">Вернутся к расчёту</button>";
echo '</div></div>';
?>


