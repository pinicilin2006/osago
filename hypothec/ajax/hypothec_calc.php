<?php
session_start();
if(!isset($_SESSION['user_id'])){
	echo '<center><span class="text-danger"><b>Закончилось время сессии. Необходимо выйти и снова войти в сервис.</b></span></center>';	
	exit;
}
$button_return = "<button type=\"button\" class=\"btn btn-danger btn-block\" id=\"button_return\" onclick=\"button_return();\">Вернутся к расчёту</button>";
if(!isset($_POST['bank'])){
	echo '<center><span class="text-danger"><b>Не выбран банк. Расчёт невозможен.</b></span></center>';	
	echo $button_return;
	exit;
}
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
require_once('../../config.php');
require_once('../../function.php');
//require_once('../template/header.html');
connect_to_base();
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
//Подключаем файл с расчётом в зависимости от выбранного банка
$id_bank = $bank;
$calc_file = 'calc_bank/'.$id_bank.'.php';
if(!file_exists($calc_file)){
	echo '<center><span class="text-danger"><b>Для данного банка отсутствует расчёт.</b></span></center>';	
	echo $button_return;
	exit;
}
require_once($calc_file);
//exit;
echo '
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">Результат расчёта стоимости полиса ипотечного страхования:</h3>
	</div>
  	<div class="panel-body">';
echo '
<div class="row">
	<div  class="col-md-2 col-md-offset-5">
	<ul>
	<li><b>Страховая сумма: </b>'.number_format($ins_summa, 2, '.', ' ').'</li>
	<li><b>Итоговый коэффициент: </b>'.$koef.'</li>
	<li><b>Итоговый страховой тариф: </b> <span class="text-danger"><b>'.$tarif.'</b></span></li>
	</ul>
	</div>
</div>
<hr>
';

echo '<a href="/osago_step_2.php" class="btn btn-success btn-block " role="button" disabled>Оформить полис</a>';
echo $button_return;
echo '</div></div>';
?>


