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
	<div class="col-md-6">
		<legend>Данные клиента:</legend>
		<fieldset>
			<div class="form-group">
			    <label for="second_name" class="col-sm-4 control-label"><small>Фамилия:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm phiz_name register phiz_name_format" name="second_name"  id="second_name" placeholder="Фамилия" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="second_name" class="col-sm-4 control-label"><small>Имя:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm phiz_name register phiz_name_format" name="first_name"  id="first_name" placeholder="Имя" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="second_name" class="col-sm-4 control-label"><small>Отчество:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm phiz_name register phiz_name_format" name="third_name"  id="third_name" placeholder="Отчество" required>
			    </div>
			</div>
		  	<div class="form-group">
		    	<label for="date_birth" class="col-sm-4 control-label"><small>Дата рождения</small></label>
		    	<div class="col-sm-6">
		      		<input type="text" class="form-control input-sm date_birth" name="date_birth" id="date_birth" placeholder="Дата рождения" required>
		    	</div>
		  	</div>	
		</fieldset>
	</div>
	<div class="col-md-6">asdasdasd</div>
</div>
