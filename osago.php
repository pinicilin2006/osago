<?php
session_start();
if(!isset($_SESSION['user_id']) || !isset($_SESSION["access"][2])){
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
<style type="text/css">
  #category option {
 width: 400px;
  margin: 0;
  padding: 0;
  }
</style>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="col-md-6 col-md-offset-3">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Рассчёт стоимости полиса ОСАГО</h3>
	  			</div>
	  			<div class="panel-body" id="user_data">
					<form class="form-horizontal col-sm-10 col-sm-offset-1" role="form" id="main_form" method="post"> 
					<p class="help-block text-left"></span><small>* - поля обязательные для заполнения.</small></p>  
					  	
					  	<hr class="hr_line">
						  	
						  	<div class="form-group ">
						    	<label for="type_ins" class="col-sm-4 control-label" style="word-wrap:break-word;">Собственник ТС</label>
						    	<div class="col-sm-8" id="type_ins">
									
									<div class="radio">
								  		<label><input type="radio" name="type_ins" value="phiz" checked><small>Физическое лицо</small></label>
									</div>

									<div class="radio">
								  		<label><input type="radio" name="type_ins" value="ip"><small>Индивидуальный предприниматель</small></label>
									</div>

									<div class="radio">
								  		<label><input type="radio" name="type_ins" value="jur"><small>Юридическое лицо</small></label>
									</div>	
						    	</div>
						  	</div>

					  	<hr class="hr_line">
					  
					  		<div class="form-group ">
					    		<label for="place_reg" class="col-sm-4 control-label" style="word-wrap:break-word;">Место регистрации ТС</label>
					    		<div class="col-sm-8" id="place_reg">
								
									<div class="radio">
									  	<label><input type="radio" name="place_reg" class="place_reg" value="1" checked><small>Российская федерация</small></label>
									</div>

									<div class="radio">
									  	<label><input type="radio" name="place_reg" class="place_reg" value="2"><small>Иностранное государство</small></label>
									</div>

									<div class="radio">
									  	<label><input type="radio" name="place_reg" class="place_reg" value="3"><small>ТС следует к месту регистрации</small></label>
									</div>

								</div>	
					    	</div>
					  					  
					  
					  	<hr class="hr_line">

						  	<div class="form-group ">
						    	<label  class="col-sm-4 control-label" style="word-wrap:break-word;">Территория преимущественного использования ТС </label>
						    	<div class="col-sm-8"  style="padding-top:2%">							
									<select class="form-control input-sm" name="subject" id="subject" required>
							  		<option value="" disabled selected>Субъект РФ</option>
							  		<?php
							  			$query = mysql_query("SELECT * FROM `kt_subject` ORDER BY `name`");
							  			while ($row = mysql_fetch_assoc($query)) {
							  				echo '<option value='.$row["id"].'>'.$row["name"].'</option>';
							  			}
							  		?>
									</select>
								
						    	</div>
						    	<div id="message_0"></div>
						  	</div>					
					  					  
					  	<hr class="hr_line">

							<div class="form-group ">
						    	<label  class="col-sm-4 control-label" style="word-wrap:break-word;">Срок страхования</label>
							    <div class="col-sm-8"  style="padding-top:2%">							
									<select class="form-control input-sm" name="term_insurance" id="term_insurance" required>
							  		<?php
							  			$query = mysql_query("SELECT * FROM `term_insurance` ORDER BY `id`");
							  			while ($row = mysql_fetch_assoc($query)) {
							  				echo '<option value='.$row["id"].'>'.$row["name"].'</option>';
							  			}
							  		?>
									</select>
							    </div>
						  	</div>					  

					  	<hr class="hr_line">

							<div class="form-group ">
						    	<label  class="col-sm-4 control-label" style="word-wrap:break-word;">Тип (категория) и назначение транспортного средства</label>
						    	<div class="col-sm-8"  style="padding-top:2%">																			  		  
						  		<?php
						  			$query = mysql_query("SELECT * FROM `category` ORDER BY `id`");
						  			$i = 0;
						  			while ($row = mysql_fetch_assoc($query)) {
						  			$i++;	
								  		echo '<div class="radio">
										  <label><input type="radio" name="category" class="category" value="'.$row["id"].'"'.($i == 1 ? ' checked' : '').' ><small>'.$row["name"].'</small></label>
										</div><hr class="hr_line">';
						  			}
						  		?>
						    	</div>
						  	</div>

					  	

							<div class="form-group capacity">
							<hr class="hr_line">
						    	<label  class="col-sm-4 control-label" style="word-wrap:break-word;">Мощность двигателя (лошадиных сил)</label>
							    <div class="col-sm-8"  style="padding-top:2%">							
									<select class="form-control input-sm" name="capacity" id="capacity" required>
							  		<?php
							  			$query = mysql_query("SELECT * FROM `capacity` ORDER BY `id`");
							  			while ($row = mysql_fetch_assoc($query)) {
							  				echo '<option value='.$row["id"].'>'.$row["name"].'</option>';
							  			}
							  		?>
									</select>
							    </div>
						  	</div>					  

					  	<hr class="hr_line">

							<div class="form-group ">
						    	<label  class="col-sm-4 control-label" style="word-wrap:break-word;">Период использования ТС</label>
							    <div class="col-sm-8"  style="padding-top:2%">							
									<select class="form-control input-sm" name="period_use" id="period_use" required>
							  		<?php
							  			$query = mysql_query("SELECT * FROM `period_use` ORDER BY `id`");
							  			while ($row = mysql_fetch_assoc($query)) {
							  				echo '<option value='.$row["id"].'>'.$row["name"].'</option>';
							  			}
							  		?>
									</select>
							    </div>
						  	</div>					  

					  	<hr class="hr_line">

							<div class="form-group ">
						    	<label  class="col-sm-4 control-label" style="word-wrap:break-word;">Количество водителей, допущенных к управлению</label>
							    <div class="col-sm-8"  style="padding-top:2%">							
									<div class="radio">
									  	<label><input type="radio" name="drivers" class="drivers" value="1" checked><small>Без ограничений</small></label>
									</div>
									<div class="radio">
									  	<label><input type="radio" name="drivers" class="drivers" value="2"><small>Ограниченное количество</small></label>
									</div>
									<div id="message_1"></div>									
							    </div>
						  	</div>					  

					  	<hr class="hr_line">

					  	<hr>					  	

					  	<div class="form-group">
					      	<button type="submit" class="btn btn-default">Рассчитать стоимость</button>
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

<div id="dialog">
  	<p>    
    	Количество водителей при ограниченном количестве водителей не может быть более 5!
  	</p>
</div>

</html>
<script type="text/javascript">
$(document).ready(function(){
/////////////////////////////////////////////////////////
//включаем подсказки
    $(".driver_plus").tooltip({
      	show: {
        delay: 0
      }
    });
    $(".driver_minus").tooltip({
      	show: {
        delay: 0
      }
    });    	
//отображение списка городов субъекта
	$(document).on("change", "#subject", function(){
		var a = $(this).val();
			$.ajax({
			  type: "POST",
			  url: '/ajax/subject_city.php',
			  data: "subject="+a,
			  success: function(data) {
			  	$('#message_0').html(data);
			  	
			  }
			});
			return false;
	});

//срок страхования при выборе места регистрации
	//скрываем изначально срок страхования до 20 дней включительно
	$("#term_insurance option[value=" + 12 + "]").hide();
	//скрываем и отображаем пункты в зависимости от выбора места регистрации
	$(document).on("change", ".place_reg", function(){
		var a = $(this).val();
		if(a == 3){
			for(x=1;x<12;x++){
				$("#term_insurance option[value=" + x + "]").hide();				
			}
			$('#term_insurance').val(12);
		} else {
			for(x=1;x<12;x++){
				$("#term_insurance option[value=" + x + "]").show();				
			}
			$("#term_insurance option[value=" + 12 + "]").hide();
			$('#term_insurance').val(1);			
		}
	});	

//Скрытие и отображение мощности двигателя в зависимости от выбора категории (отображаем мощность толкьо привыборе категории В)
	//При загрузки страницы по умолчанию скрываем мощность
	$(".capacity").hide();
	//Отображение/скрытие при выборе категории
	$(document).on("change", ".category", function(){
		var a = $(this).val();
		if(a == '2' || a == '3'){
			$(".capacity").slideDown();
		} else {
			$(".capacity").slideUp();
		}
	});	

//Выбор количества водителей при выборе ограниченного количества водителей допущенных к управлению транспортным средством
	//Скрытие/отображение водительского стажа водителя
	$(document).on("change", ".drivers", function(){
		var a = $(this).val();
		if(a == '2'){
			var b = '<select class="form-control input-sm driver_age" name="driver_1"><option value="1"><small>До 22 лет включительно со стажем вождения до 3 лет включительно</small></option><option value="2"><small>Более 22 лет со стажем вождения до 3 лет включительно</small></option><option value="3"><small>До 22 лет включительно со стажем вождения свыше 3 лет</small></option><option value="4"><small>Более 22 лет со стажем вождения свыше 3 лет</small></option></select><center><span id="plus_1" title="Добавить водителя" style="font-size:14px;top:0px" class="driver_plus glyphicon glyphicon-plus"></span></center>';
			$("#message_1").html(b);	
		} else {
			$("#message_1").html('');
		}
	});
	//добавление водителей при нажатие на плюсик
	$(document).on("click", ".driver_plus", function(){
		var a = $("select.driver_age").length;
		// if(a == 5){
		// 	$( "#dialog" ).dialog();
		// 	return;
		// }
    	var b = a + 1;
    	if(a == 4){
			var c = '<select class="form-control input-sm driver_age" id="driver_'+b+'" name="driver_'+b+'"><option value="1"><small>До 22 лет включительно со стажем вождения до 3 лет включительно</small></option><option value="2"><small>Более 22 лет со стажем вождения до 3 лет включительно</small></option><option value="3"><small>До 22 лет включительно со стажем вождения свыше 3 лет</small></option><option value="4"><small>Более 22 лет со стажем вождения свыше 3 лет</small></option></select><center><span id="minus_'+b+'" title="Удалить водителя" style="font-size:14px;top:0px" class="driver_minus glyphicon glyphicon-minus"></span></center>';
 		} else {
			var c = '<select class="form-control input-sm driver_age" id="driver_'+b+'" name="driver_'+b+'"><option value="1"><small>До 22 лет включительно со стажем вождения до 3 лет включительно</small></option><option value="2"><small>Более 22 лет со стажем вождения до 3 лет включительно</small></option><option value="3"><small>До 22 лет включительно со стажем вождения свыше 3 лет</small></option><option value="4"><small>Более 22 лет со стажем вождения свыше 3 лет</small></option></select><center><span id="plus_'+b+'" title="Добавить водителя" style="font-size:14px;top:0px" class="driver_plus glyphicon glyphicon-plus"></span><span id="minus_'+b+'" title="Удалить водителя" style="font-size:14px;top:0px;margin:0px 0px 0px 10px" class="driver_minus glyphicon glyphicon-minus"></span></center>';

 		}
 		$("#plus_"+a).remove();
 		$("#minus_"+a).remove();
 		$("#message_1").append(c);   	
	});
	//удаление водителей при нажатие на минус
	$(document).on("click", ".driver_minus", function(){
		var a = $("select.driver_age").length;
		var b = a - 1;
		if(b != 1){
			var c = '<center><span id="plus_'+b+'" title="Добавить водителя" style="font-size:12px;top:0px" class="driver_plus glyphicon glyphicon-plus"></span><span id="minus_'+b+'" title="Удалить водителя" style="font-size:14px;top:0px;margin:0px 0px 0px 10px" class="driver_minus glyphicon glyphicon-minus"></span></center>';
 		}else{
			var c = '<center><span id="plus_'+b+'" title="Добавить водителя" style="font-size:12px;top:0px" class="driver_plus glyphicon glyphicon-plus"></span>'; 			
 		}
 		$("#driver_"+a).remove();
 		$("#plus_"+a).remove();
 		$("#minus_"+a).remove();
 		$("#message_1").append(c);		
	});
//проверка данных формы
    $('#main_form').submit(function( event ) {

    });

///////////////////////////////////////////////////////////
});

</script>
