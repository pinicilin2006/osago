<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}
if(!isset($_SESSION["access"][5])){
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
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="col-md-4 col-md-offset-4">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Добавить подразделение:</h3>
	  			</div>	  			
	  			<div class="panel-body" id="user_data">
					<form class="form-horizontal col-sm-8 col-sm-offset-2" role="form" id="main_form">
					
					  <p class="help-block text-right"></span><small>* - поля обязательные для заполнения.</small></p>  

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="unit_full_name" name="unit_full_name" placeholder="Название подразделения *" required>					      			    
					  </div>				  					  

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="unit_city" name="unit_city" placeholder="Город *" required>					      			    
					  </div>

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="unit_address" name="unit_address" placeholder="Адрес *" required>					      				    
					  </div>

					  <div class="form-group">
					  		<select class="form-control" name="unit_parent_id" required>
					  		<option value="" disabled selected>Укажите родительское подразделение *</option>
					  		<?php
					  		$query=mysql_query("SELECT * FROM `unit` WHERE active = 1 ORDER BY unit_full_name");
					  		while($row = mysql_fetch_assoc($query)){
								echo "<option value=\"$row[unit_id]\" >$row[unit_full_name]</option>";
							}
							?>    
							</select>
					  </div>

					  <hr align="center" size="2" />

					  <div class="form-group">
							<label class="checkbox-inline"><input type="checkbox" name="active" value="1" checked>Подразделение активно</label>	    
					  </div>

					  <div class="form-group">
					      <button type="submit" class="btn btn-default">Добавить подразделение</button>
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
    	add_unit();
    	return false;
    });
});

</script>
