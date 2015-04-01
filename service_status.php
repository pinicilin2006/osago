<?php
session_start();
if(!isset($_SESSION['user_id']) || !isset($_SESSION["access"][5]) || !$_POST['status']){
	header("Location: index.php");
	exit;
}
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
require_once('config.php');
require_once('function.php');
connect_to_base();
$message='';
if(mysql_num_rows(mysql_query("SELECT * FROM `service_status` WHERE `id` = '".$_POST['status']."'")) == 0){
	$message='Не обнаруженно статуса с таким ID';
}else{
	if(mysql_query("UPDATE `service_status` SET `checked` = '1' WHERE `id` = '".$_POST['status']."'")){
		mysql_query("UPDATE `service_status` SET `checked` = '0' WHERE `id` <> '".$_POST['status']."'");
		$status = mysql_fetch_assoc(mysql_query("SELECT * FROM `service_status` WHERE `checked` = '1'"));
		$message = $status['name'];
	} else {
		$message='Произошла ошибка при изменение статуса работы сервиса.';
	}
}
/////////////////////////////////////////////
require_once('template/header.html');
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="col-md-6 col-md-offset-3">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Статус работы сервиса ОСАГО</h3>
	  			</div>
	  			<div class="panel-body" id="user_data">
	  			<ul style="padding:0px">
	  			<li class="thumbnail"><center>Статус выполнения: <?php echo $message ?></center></li>
		  		</ul>
	  			</div>
			</div>
			<div id="message"></div>
		</div>
	</div>
</div>
<div class="footer text-center">
	<small>©<?php echo date("Y") ?>. <a href="https://www.sngi.ru">Страховое общество «Сургутнефтегаз».</a> Все права защищены.</small>
</div>
</body>
</html>
