<?php
session_start();
if(!isset($_SESSION['user_id']) || !isset($_SESSION["access"][7])){
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
		<div class="col-md-6 col-md-offset-3">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Выдача бланков A7</h3>
	  			</div>
	  			<div class="panel-body" id="user_data">
					<form class="form-horizontal col-sm-8 col-sm-offset-2" role="form" id="main_form" method="post"> 

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
					  
					  <div class="form-group" style="display:none" id="blank_data">
					  		<select id="range" class="form-control">
					  			<option value="1">Диапазон номеров</option>
					  			<option value="2">Не диапазон номеров</option>
					  		</select>
					  </div>

					  <div class="form-inline" id="message_1">
					  
 					  </div>
					  
					  <hr align="center" size="2" />

					  <div class="form-group">
					      <button type="submit" class="btn btn-default">Присвоить A7</button>
					  </div>

					</form>
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
<script type="text/javascript">
$(document).ready(function(){
//Маски ввода
	$('.range').mask('000000');	
//отображение списка пользователей подразделение
	$(document).on("change", "#unit", function(){
		var a = $(this).val();
			$.ajax({
			  type: "POST",
			  url: '/ajax/unit_user.php',
			  data: "id="+a,
			  success: function(data) {
			  	$('#message_0').html(data);
			  	$('#blank_data').slideDown();
			  	b = '<label class=\"control-label\" style=\"padding-top:0px\">Диапазон номеров</label> <input type=\"text\" name=\"a7_range_start\" class=\"range form-control input-sm\" placeholder=\"с\" style=\"height:25px\" required> <input type=\"text\" class=\"range form-control input-sm\" name=\"a7_range_end\" placeholder=\"по\" style=\"height:25px\" required>';
			  	$('#message_1').html(b);
			  }
			});
			return false;
	});
//отображнеия либо диапазона либо единичного номера полиса
	$(document).on("change", "#range", function(){
		var a = $(this).val();
		var b = '';
		if(a == '1'){
			b = '<label class=\"control-label\" style=\"padding-top:0px\">Диапазон номеров</label> <input type=\"text\" name=\"a7_range_start\" class=\"range form-control input-sm\" placeholder=\"с\" style=\"height:25px\"> <input type=\"text\" class=\"range form-control input-sm\" name=\"a7_range_end\" placeholder=\"по\" style=\"height:25px\">';
		} else {
			b = '<label class=\"control-label\" style=\"padding-top:0px\">Номер бланка</label> <input type=\"text\" name=\"a7_number[]\" class=\"range form-control input-sm\" style=\"height:25px\" required> <span style="font-size:8px;top:0px" class="plus glyphicon glyphicon-plus"></span><br>';
		}
		$('#message_1').html(b);
	});

//добавление добавление поля с номером БСО
	$(document).on("click", ".plus", function(){
		var a = '<label class=\"control-label\" style=\"padding-top:0px\">Номер бланка</label> <input type=\"text\" name=\"a7_number[]\" class=\"range form-control input-sm\" style=\"height:25px\" required> <span style="font-size:8px;top:0px" class="plus glyphicon glyphicon-plus"></span><br>';
		$('#message_1').append(a);
	});
//проверка данных формы
    $('#main_form').submit(function( event ) {
    	add_a7();
    	return false;
    });
});

</script>