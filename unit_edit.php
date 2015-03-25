<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}

if(!isset($_POST['unit_id']) || !isset($_SESSION["access"][5])){
	header("Location: index.php");
	exit;
}

// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
require_once('config.php');
require_once('function.php');
connect_to_base();
require_once('template/header.html');
$row=mysql_fetch_assoc(mysql_query("SELECT * FROM `unit` WHERE `unit_id` = '".mysql_real_escape_string($_POST["unit_id"])."'"));
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Редактировать подразделение:</h3>
	  			</div>	  			
	  			<div class="panel-body" id="user_data">
					<form class="form-horizontal col-sm-8 col-sm-offset-2" role="form" id="main_form">
					<input type="hidden" name="unit_id" value="<?php echo mysql_real_escape_string($_POST["unit_id"]) ?>">
					  <p class="help-block text-right"></span><small>* - поля обязательные для заполнения.</small></p>  

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="unit_full_name" name="unit_full_name" value="<?php echo $row["unit_full_name"]?>" placeholder="Название подразделения *" required>					     					    
					  </div>				  					  

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="unit_city" name="unit_city" value="<?php echo $row["unit_city"]?>" placeholder="Город *" required>					     					    
					  </div>

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="unit_address" name="unit_address" value="<?php echo $row["unit_address"]?>" placeholder="Адрес *" required>					      				    
					  </div>

					  <div class="form-group">
					  		<select class="form-control" name="unit_parent_id" required>
					  		<option value="" disabled>Укажите родительское подразделение *</option>
					  		<?php
					  		$query=mysql_query("SELECT * FROM `unit` WHERE active = 1 ORDER BY unit_full_name");
					  		while($row1 = mysql_fetch_assoc($query)){
								if($row1["unit_id"] != mysql_real_escape_string($_POST["unit_id"])){
									echo "<option value=\"$row1[unit_id]\"";
									echo ($row["unit_parent_id"] == $row1["unit_id"] ? " selected" : '');
									echo ">$row1[unit_full_name]";
								if($row1['unit_full_name'] == 'Физические лица'){
									$filial_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `unit` WHERE `unit_id` = '".$row1['unit_parent_id']."'"));
									echo ' ('.$filial_data['unit_full_name'].')';
								}									
									echo "</option>";
								}
							}
							?>    
							</select>
					  </div>

					  <hr align="center" size="2" />

					  <div class="form-group">
							<label class="checkbox-inline"><input type="checkbox" name="active" value="1" <?php echo($row["active"] == '1' ? ' checked' : '')?> >Подразделение активно</label>	    
					  </div>

					  <div class="form-group">
					      <button type="submit" class="btn btn-default">Редактировать подразделение</button>
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
//проверка данных формы
    $('#main_form').submit(function( event ) {
    	edit_unit();
    	return false;
    });
});

</script>

