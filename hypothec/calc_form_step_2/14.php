<?php
//форма для оформления ипотеки сбербанка
session_start();
if(!isset($_SESSION['user_id'])){
	echo '<center><span class="text-danger"><b>Закончилось время сессии. Необходимо выйти и снова войти в сервис.</b></span></center>';	
	exit;
}
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
?>
<hr class="hr_red_2">
<div class="form-group">
	<div class="col-sm-6">
		<button type="submit" name="action" value="add" class="btn btn-primary btn-block">Оформить полис</button>
	</div>
	<div class="col-sm-6">	
		<button type="submit" name="action" value="project" class="btn btn-danger btn-block">Сохранить как проект</button>
	</div>
</div>