<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}

if(!isset($_POST['user']) || !isset($_SESSION["access"][5])){
	header("Location: index.php");
	exit;
}

// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
require_once('config.php');
require_once('function.php');
connect_to_base();
require_once('template/header.html');
$row=mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `user_id` = '".mysql_real_escape_string($_POST["user"])."'"));
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Добавить пользователя:</h3>
	  			</div>	  			
	  			<div class="panel-body" id="user_data">
					<form class="form-horizontal col-sm-8 col-sm-offset-2" role="form" id="main_form">
					  <input type="hidden" name="user" value="<?php echo mysql_real_escape_string($_POST["user"]) ?>">
					  <p class="help-block text-right"></span><small>* - поля обязательные для заполнения.</small></p>  

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="second_name" name="second_name" value="<?php echo $row["second_name"]?>" placeholder="Фамилия" required>					    
					  </div>
					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="first_name" name="first_name" value="<?php echo $row["first_name"]?>" placeholder="Имя" required>					    
					  </div>

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="third_name" name="third_name" value="<?php echo $row["third_name"]?>" placeholder="Отчество" required>					    
					  </div>					  					  

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="date_birth" name="date_birth" value="<?php echo $row["date_birth"]?>" placeholder="Дата рождения" required>					    
					  </div>

					  <div class="form-group">
							<label class="radio-inline"><input type="radio" name="sex" value="m" <?php echo ($row["sex"] == 'm' ? ' checked' : '')?>>м</label>	    
							<label class="radio-inline"><input type="radio" name="sex" value="f" <?php echo ($row["sex"] == 'f' ? ' checked' : '')?>>ж</label>
					  </div>

					  <div class="form-group has-feedback">					    					    
					      <input type="text	" class="form-control input-sm" id="phone" name="phone" value="<?php echo $row["phone"]?>" placeholder="Телефон">					    
					  </div>

					  <div class="form-group">					    					    
					      <input type="email" class="form-control input-sm" id="email" name="email" value="<?php echo $row["email"]?>" placeholder="Email">
					      <p class="help-block"><small>Восстановление пароля возможно только при указанном email.</small></p>					    
					  </div>

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="login" name="login" placeholder="Логин" value="<?php echo $row["login"]?>" required>					    
					  </div>

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="password" name="password" placeholder="Пароль">
					      <p class="help-block"><small>Если поле не заполнить, то пароль останется прежним.</small></p>					    
					  </div>

					  <div class="form-group">
					  		<select class="form-control" name="unit" required>
					  		<option value="" disabled selected>Выберите подразделение *</option>
					  		<?php
					  		$unit=mysql_fetch_assoc(mysql_query("SELECT * FROM `user_unit` WHERE `user_id` = '".mysql_real_escape_string($_POST["user"])."'"));
					  		$query=mysql_query("SELECT * FROM `unit` ORDER BY unit_full_name");
					  		while($row1 = mysql_fetch_assoc($query)){
								echo "<option value=\"$row1[unit_id]\" ";
								echo ($unit["unit_id"] == $row1["unit_id"] ? " selected " : '');
								echo ">$row1[unit_full_name]";
								if($row1['unit_full_name'] == 'Физические лица'){
									$filial_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `unit` WHERE `unit_id` = '".$row1['unit_parent_id']."'"));
									echo ' ('.$filial_data['unit_full_name'].')';
								}
								echo "</option>";
							}
							?>    
							</select>
					  </div>

					  <hr align="center" size="2" />
					  
					  <div class="form-group">
					  		<?php
					  		$rights=mysql_fetch_assoc(mysql_query("SELECT * FROM `user_rights` WHERE `user_id` = '".mysql_real_escape_string($_POST["user"])."'"));					  		
					  		$query=mysql_query("SELECT * FROM `rights` WHERE active = 1 ORDER BY priority");
					  		while($row2 = mysql_fetch_assoc($query)){
								echo "<label class=\"checkbox-inline\"><input type=\"checkbox\" name=\"rights[]\" value=\"$row2[id]\"";
								echo (mysql_num_rows(mysql_query("SELECT * FROM `user_rights` WHERE `user_id` = '".mysql_real_escape_string($_POST["user"])."' AND `rights` = '".$row2["id"]."'")) > 0 ? ' checked' : '');
								echo ">$row2[name]</label><br>";
							}
							?>    
					  </div>

					  <hr align="center" size="2" />

					  <div class="form-group">
							<label class="checkbox-inline"><input type="checkbox" name="active" value="1" <?php echo ($row["active"] == 1 ? ' checked' : '')?>>Учётная запись активна</label>	    
					  </div>

					  <div class="form-group">
					      <button type="submit" class="btn btn-default">Редактировать пользователя</button>
					  </div>



					</form>
	  			</div>
	  			<div id="message"></div>
			</div>
		</div>
	</div>
</div>
<div class="footer  text-center">
	<small>©<?php echo date("Y") ?>. <a href="https://www.sngi.ru">Страховое общество «Сургутнефтегаз».</a> Все права защищены.</small>
</div>
</body>
</html>
<script type="text/javascript">
$(document).ready(function(){
//Маски ввода
	$('#phone').mask('(000)000-00-00');
	$('#date_birth').mask('00.00.0000');	
//Календарик	
	$( "#date_birth" ).datepicker({
	  dateFormat: "dd.mm.yy",
	  maxDate: "-18Y",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c-70:c"
	});
//Ввод только английского и цифра в пароле
	$('#password').bind('keyup blur',function(){ 
    	$(this).val( $(this).val().replace(/[А-Яа-я]/g,'') );
    	 }
	);
//проверка данных формы
    $('#main_form').submit(function( event ) {
    	edit_user();
    	return false;
    });
});

</script>