<?php
session_start();
if(!isset($_SESSION['user_id']) || !isset($_SESSION["access"][4])){
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
		<div class="col-md-12">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Возврат бланков БСО</h3>
	  			</div>
	  			<div class="panel-body" id="user_data">
					<form class="form-horizontal col-sm-6 col-sm-offset-3" role="form" id="main_form" method="post">

					  <div class="form-group">
					  		<select class="form-control" name="unit" id="unit" required>
					  		<option value="" disabled selected>Выберите подразделение *</option>
					  		<?php
					  		$unit_main=mysql_fetch_assoc(mysql_query("SELECT * FROM `unit`,`user_unit` WHERE `user_id` = '".$_SESSION['user_id']."' AND unit.unit_id = user_unit.unit_id"));
					  		echo "<option value=\"".$unit_main["unit_id"]."\">".$unit_main["unit_full_name"]."</option>";
							$unit_children = mysql_query("SELECT * FROM `unit` WHERE `unit_parent_id` = '".$unit_main["unit_id"]."'");
							while($row = mysql_fetch_assoc($unit_children)){
								echo "<option value=\"".$row["unit_id"]."\">".$row["unit_full_name"]."</option>";
							}
							?>    
							</select>
					  </div>

					  <div class="form-group" id="message_0"></div>		
					  <div class="form-group" id="message_1"></div>					  
					  
					  <hr align="center" size="2" />

					  <div class="form-group">
					      <button type="submit" class="btn btn-default">Возврат выбранных БСО</button>
					  </div>

					</form>
	  			</div>
			</div>
			<div id="message"></div>
		</div>
	</div>
</div>
<?php require_once('template/footer.html') ?>
</body>
</html>
<script type="text/javascript">
$(document).ready(function(){
//Маски ввода
	$('.range').mask('0000000000');	
//отображение списка пользователей подразделение
	$(document).on("change", "#unit", function(){
		var a = $(this).val();
			//получаем список пользователей подразделения
			$.ajax({
			  type: "POST",
			  url: '/ajax/unit_user.php',
			  data: "id="+a,
			  success: function(data) {
			  	$('#message_0').html(data);
			  }
			});
			//получаем список БСО подразделения
			$.ajax({
			  type: "POST",
			  url: '/ajax/bso_list.php',
			  data: "unit="+a,
			  success: function(data) {
			  	$('#message_1').html(data);
			  }
			});
			return false;
	});

	$(document).on("change", "#user_id", function(){
		var a = $(this).val();
			//получаем список БСО подразделения
			$.ajax({
			  type: "POST",
			  url: '/ajax/bso_list.php',
			  data: "user_id="+a,
			  success: function(data) {
			  	$('#message_1').html(data);
			  }
			});
			return false;
	});


//Выбор всех номеров
	$(document).on("click", "#select_all", function(){
		if(this.checked){
			$('.bso').prop('checked', true);
		} else {
			$('.bso').prop('checked', false);
		}
	});

//проверка данных формы
    $('#main_form').submit(function( event ) {
    	return_bso();
    	return false;
    });
});

</script>

