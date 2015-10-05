<?php
session_start();
if(!isset($_SESSION['user_id']) || !isset($_SESSION["access"][2])){
	header("Location: index.php");
	exit;
}
if(isset($_SESSION['kbm'])){
	unset($_SESSION['kbm']);
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
		<div class="col-md-12" id="user_data">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Расчёт стоимости полиса ОСАГО</h3>
	  			</div>
	  			<div class="panel-body">
					<form class="form-horizontal col-sm-10 col-sm-offset-1" role="form" id="main_form" method="post"> 
					  	
					  	<hr class="hr_line">
						  	
						  	<div class="form-group ">
						    	<label for="type_ins" class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Собственник ТС</small></label>
						    	<div class="col-sm-7" id="type_ins">
									
									<div class="radio">
								  		<label><input type="radio" name="type_ins" class="type_ins" value="phiz" checked><small>Физическое лицо</small></label>
									</div>

									<div class="radio">
								  		<label><input type="radio" name="type_ins" class="type_ins" value="ip"><small>Индивидуальный предприниматель</small></label>
									</div>

									<div class="radio">
								  		<label><input type="radio" name="type_ins" class="type_ins" value="jur"><small>Юридическое лицо</small></label>
									</div>	
						    	</div>
						  	</div>

					  	<hr class="hr_line">
					  
					  		<div class="form-group ">
					    		<label for="place_reg" class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Место регистрации ТС</small></label>
					    		<div class="col-sm-7" id="place_reg">
								
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
					    		<label for="citizenship" class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Место жительства (регистрации) собственника ТС - физического лица</small></label>
					    		<div class="col-sm-7" id="citizenship">
								
									<div class="radio">
									  	<label><input type="radio" name="citizenship" class="citizenship" value="1" checked><small>Российская федерация</small></label>
									</div>

									<div class="radio">
									  	<label><input type="radio" name="citizenship" class="citizenship" value="2"><small>Иностранное государство</small></label>
									</div>

								</div>	
					    	</div>				  					  

						  	<div class="form-group ig_hide">
						  	<hr>
						    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Территория преимущественного использования ТС</small></label>
						    	<div class="col-sm-7">							
									<select class="form-control input-sm" name="subject" id="subject" required>
							  		<option value="" disabled selected>Субъект РФ</option>
							  		<?php
							  			$query = mysql_query("SELECT * FROM `kt_subject` ORDER BY `name`");
							  			while ($row = mysql_fetch_assoc($query)) {
							  				echo '<option value='.$row["id"].'>'.$row["name"].'</option>';
							  			}
							  		?>
									</select>
									<div id="message_0"></div>
						    	</div>
						    	
						  	</div>					
					  					  
					  	

							<div class="form-group term_insurance">
							<hr >
						    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Срок страхования</small></label>
							    <div class="col-sm-7">							
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

							<div class="form-group" id="srok_year">
							<hr>
						    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Срок страхования</small></label>
							    <div class="col-sm-7">							
							    <b>1 год</b>
							    </div>
						  	</div>	

						<hr>
						  	<div class="form-group">
						    	<label for="year_manufacture" class="col-sm-5 control-label"><small>Год изготовления</small></label>
						    	<div class="col-sm-7">
						      		<select class="form-control input-sm" name="year_manufacture" id="year_manufacture" required>
						      		<?php
						      		$a = date("Y");
						      		for($x=$a;$x>=$a-115;$x--){
						      			echo '<option value='.$x.'>'.$x.'</option>';
						      		}
						      		?>
						      		</select>
						    	</div>
						  	</div>

					  	<hr>

							<div class="form-group ">
						    	<label  class="col-sm-5 control-label" ><small>Тип (категория) и назначение транспортного средства</small></label>
						    	<div class="col-sm-7">																			  		  
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
							<hr>
						    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Мощность двигателя (лошадиных сил)</small></label>
							    <div class="col-sm-7">							
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
					  	
							<div class="form-group ig_hide period_use">
							<hr>
						    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Период использования ТС</small></label>
							    <div class="col-sm-7">							
									<select class="form-control input-sm" name="period_use" id="period_use" required>
							  		<?php
							  			$query = mysql_query("SELECT * FROM `period_use` ORDER BY `id`");
							  			while ($row = mysql_fetch_assoc($query)) {
							  				echo '<option value='.$row["id"].' '.($row["id"] == 8 ? ' selected' : '').'>'.$row["name"].'</option>';
							  			}
							  		?>
									</select>
							    </div>
						  	</div>					  

							<div class="form-group ig_hide">
							<hr>
						    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Количество водителей, допущенных к управлению</small></label>
							    <div class="col-sm-7">							
									<div class="radio">
									  	<label><input type="radio" name="drivers" class="drivers" value="1" checked><small>Без ограничений</small></label>
									</div>
									<div class="radio" id="drivers_limit">
									  	<label><input type="radio" name="drivers" class="drivers" value="2"><small>Ограниченное количество</small></label>
									</div>
									<div id="message_1"></div>									
							    </div>
						  	</div>					  

					  	

							<div class="form-group kbm ig_hide">
							<hr>
						    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Класс КБМ</small></label>
							    <div class="col-sm-7">							
									<select class="form-control input-sm" name="class_kbm" id="kbm" required>
							  		<?php
							  			$query = mysql_query("SELECT * FROM `kbm` ORDER BY `id`");
							  			while ($row = mysql_fetch_assoc($query)) {
							  				echo '<option value='.$row["id"].' '.($row["id"] == '5' ? ' selected' : '').'>'.$row["name"].'</option>';
							  			}
							  		?>
									</select>
									<span class="help-block"><a href="/dkbm/index.html" target="_blank"><small>Запрос ТО/КБМ в АИС РСА</small></a><br><small class="text-danger"> (для автоматического заполнения полей необходимо делать запрос ТО и КБМ на первом шаге)</small></span>
							    </div>
						  	</div>					  

					  	

							<div class="form-group" id="trailer">
							<hr>
						    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Имеется прицеп</small></label>
							    <div class="col-sm-7">															
									<div class="radio">
										  	<label><input type="radio" name="trailer" value="1" checked><small>Нет</small></label>
									</div>
									<div class="radio">
										  	<label><input type="radio" name="trailer" value="2"><small>Да</small></label>
									</div>
							    </div>
						  	</div>

					  	<hr>


							<div class="form-group">
						    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Были ли грубые нарушения условий страхования в соответствии с п.3 ст.9 ФЗ «Об обязательном страховании гражданской ответственности владельцев транспортных средств»</small></label>
							    <div class="col-sm-7">															
									<div class="radio">
										  	<label><input type="radio" name="violations" value="1" checked><small>Нет</small></label>
									</div>
									<div class="radio">
										  	<label><input type="radio" name="violations" value="2"><small>Да</small></label>
									</div>
							    </div>
						  	</div>					  						  	

					  	<hr>					  	

					  	<div class="form-group">
					      	<button type="submit" class="btn btn-success btn-block">Рассчитать стоимость</button>
					  	</div>
					</form>
	  			</div>
			</div>
		</div>
		<div class="col-md-12">
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
// //Скртыие полей для гражданина иностранного государства
//     $(document).on("change", ".citizenship", function(){
//     	var a = $(this).val();
//     	if(a == '2'){
//     		$(".ig_hide").hide();
//     		$("#subject").prop('required',false);

//     	}else{
//     		$(".ig_hide").slideDown();
//     		$("#subject").prop('required',true);
//     	}
//     });	
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
//Отображение списка городов при возвращение назад
var a = $("#subject").val();
			$.ajax({
			  type: "POST",
			  url: '/ajax/subject_city.php',
			  data: "subject="+a,
			  success: function(data) {
			  	$('#message_0').html(data);
			  	
			  }
			});
//период использования транспортного средства
	//Для юриков доступно минимум 6 месяцев
	$(document).on("change", ".type_ins", function(){
		var a = $(this).val();
		if(a == 'jur'){
			//оставляем доступным вариант с неограниченным количеством водителей
			$('input:radio[name="drivers"]').filter('[value="1"]').prop('checked',true);
			$("#message_1").html('');
			$("#drivers_limit").hide();
			//период использования ТС
			for(x=1;x<4;x++){				
				$("#period_use option[value=" + x + "]").hide();
			}
			$('#period_use').val(4);
		} else {
			$("#drivers_limit").show(); 
			for(x=1;x<4;x++){
				$("#period_use option[value=" + x + "]").show();
			}
			$('#period_use').val(1);			
		}
	});	
//срок страхования при выборе места регистрации
	//приоткрытие страницы ставим значение по умолчанию для срока страхования
	$("#term_insurance").val(11);
	//скрываем изначально срок страхования до 20 дней включительно и весь блок срока страхования
	$("#term_insurance option[value=" + 12 + "]").hide();
		$(".term_insurance").hide();
	//скрываем и отображаем пункты в зависимости от выбора места регистрации
	$(document).on("change", ".place_reg", function(){
		place_reg($(this).val());
	});	

//Скрытие и отображение мощности двигателя в зависимости от выбора категории (отображаем мощность толкьо привыборе категории В)
	//При загрузки страницы по умолчанию скрываем мощность
	$(".capacity").hide();
	//Отображение/скрытие при выборе категории
	$(document).on("change", ".category", function(){
		var a = $(this).val();
		if(a == '2' || a == '3'){
			$(".capacity").slideDown();
			$("#trailer").slideUp();
		} else {
			$(".capacity").slideUp();
			$("#trailer").slideDown();
		}
	});	

//Выбор количества водителей при выборе ограниченного количества водителей допущенных к управлению транспортным средством
	//Скрытие/отображение водительского стажа водителя
	$(document).on("change", ".drivers", function(){
		var a = $(this).val();
		if(a == '2'){
			var b = '<select class="form-control input-sm driver_age" name="driver_1"><option value="1"><small>До 22 лет включительно со стажем вождения до 3 лет включительно</small></option><option value="2"><small>Более 22 лет со стажем вождения до 3 лет включительно</small></option><option value="3"><small>До 22 лет включительно со стажем вождения свыше 3 лет</small></option><option value="4"><small>Более 22 лет со стажем вождения свыше 3 лет</small></option></select><center><button type="button" class="btn btn-default btn-xs driver_plus" id="plus_1">Добавить водителя</button></center>';
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
    	if(a == '4'){
			var c = '<select class="form-control input-sm driver_age" id="driver_'+b+'" name="driver_'+b+'"><option value="1"><small>До 22 лет включительно со стажем вождения до 3 лет включительно</small></option><option value="2"><small>Более 22 лет со стажем вождения до 3 лет включительно</small></option><option value="3"><small>До 22 лет включительно со стажем вождения свыше 3 лет</small></option><option value="4"><small>Более 22 лет со стажем вождения свыше 3 лет</small></option></select><center><button type="button" class="btn btn-default btn-xs driver_minus" id="minus_'+b+'">Удалить водителя</button></center>';
 		} else {
			var c = '<select class="form-control input-sm driver_age" id="driver_'+b+'" name="driver_'+b+'"><option value="1"><small>До 22 лет включительно со стажем вождения до 3 лет включительно</small></option><option value="2"><small>Более 22 лет со стажем вождения до 3 лет включительно</small></option><option value="3"><small>До 22 лет включительно со стажем вождения свыше 3 лет</small></option><option value="4"><small>Более 22 лет со стажем вождения свыше 3 лет</small></option></select><center><button type="button" class="btn btn-default btn-xs driver_plus" id="plus_'+b+'">Добавить водителя</button><button type="button" class="btn btn-default btn-xs driver_minus" id="minus_'+b+'">Удалить водителя</button></center>';

 		}
 		$("#plus_"+a).remove();
 		$("#minus_"+a).remove();
 		$("#message_1").append(c);   	
	});
	//удаление водителей при нажатие на минус
	$(document).on("click", ".driver_minus", function(){
		var a = $("select.driver_age").length;
		var b = a - 1;
		if(b != '1'){
			var c = '<center><button type="button" class="btn btn-default btn-xs driver_plus" id="plus_'+b+'">Добавить водителя</button><button type="button" class="btn btn-default btn-xs driver_minus" id="minus_'+b+'">Удалить водителя</button></center>';
 		}else{
			var c = '<center><button type="button" class="btn btn-default btn-xs driver_plus" id="plus_'+b+'">Добавить водителя</button></center>'; 			
 		}
 		$("#driver_"+a).remove();
 		$("#plus_"+a).remove();
 		$("#minus_"+a).remove();
 		$("#message_1").append(c);		
	});

//проверка данных формы
    $('#main_form').submit(function( event ) {
    	calc_osago();
    	return false;
    });

///////////////////////////////////////////////////////////
});

</script>
