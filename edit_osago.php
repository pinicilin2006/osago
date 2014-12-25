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
if(!isset($_SESSION['user_id']) || !isset($_GET["id"]) || empty($_GET["id"])){
	echo "<p class=\"text-danger text-center\">Договор с запрашиваемым id не найден в базе данных</p>";
	exit();
}
$id = mysql_real_escape_string($_GET['id']);
if(isset($_SESSION["access"][3])){
	$query = "SELECT * FROM `contract` WHERE `md5_id` = '".$id."' AND `project` = '0' AND `annuled` = '0'";
}else{
	$query = "SELECT * FROM `contract` WHERE `md5_id` = '".$id."' AND `project` = '0' AND `annuled` = '0' AND `unit_id` = '".$_SESSION["unit_id"]."' AND `user_id` = '".$_SESSION["user_id"]."'";
}
if(mysql_num_rows(mysql_query($query))<1){
	echo "<p class=\"text-danger text-center\">Договор с запрашиваемым id не найден в базе данных</p>";
	exit();
}
//echo $query;
$contract_data = mysql_fetch_assoc(mysql_query($query));
$calc_data = unserialize($contract_data['calc_data']);
// echo '<pre>';
// print_r($calc_data);
// echo '</pre>';
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
								  		<label><input type="radio" name="type_ins" class="type_ins" value="phiz" <?php echo ($calc_data['type_ins'] == 'phiz' ? ' checked' : '') ?> ><small>Физическое лицо</small></label>
									</div>

									<div class="radio">
								  		<label><input type="radio" name="type_ins" class="type_ins" value="ip" <?php echo ($calc_data['type_ins'] == 'ip' ? ' checked' : '') ?>><small>Индивидуальный предприниматель</small></label>
									</div>

									<div class="radio">
								  		<label><input type="radio" name="type_ins" class="type_ins" value="jur" <?php echo ($calc_data['type_ins'] == 'jur' ? ' checked' : '') ?>><small>Юридическое лицо</small></label>
									</div>	
						    	</div>
						  	</div>

					  	<hr class="hr_line">
					  
					  		<div class="form-group ">
					    		<label for="place_reg" class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Место регистрации ТС</small></label>
					    		<div class="col-sm-7" id="place_reg">
								
									<div class="radio">
									  	<label><input type="radio" name="place_reg" class="place_reg" value="1" <?php echo ($calc_data['place_reg'] == '1' ? ' checked' : '') ?>><small>Российская федерация</small></label>
									</div>

									<div class="radio">
									  	<label><input type="radio" name="place_reg" class="place_reg" value="2" <?php echo ($calc_data['place_reg'] == '2' ? ' checked' : '') ?>><small>Иностранное государство</small></label>
									</div>

									<div class="radio">
									  	<label><input type="radio" name="place_reg" class="place_reg" value="3" <?php echo ($calc_data['place_reg'] == '3' ? ' checked' : '') ?>><small>ТС следует к месту регистрации</small></label>
									</div>

								</div>	
					    	</div>

					  	<hr class="hr_line">

					  		<div class="form-group ">
					    		<label for="citizenship" class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Гражданство собственника ТС</small></label>
					    		<div class="col-sm-7" id="citizenship">
								
									<div class="radio">
									  	<label><input type="radio" name="citizenship" class="citizenship" value="1" <?php echo ($calc_data['citizenship'] == '1' ? ' checked' : '') ?>><small>Российская федерация</small></label>
									</div>

									<div class="radio">
									  	<label><input type="radio" name="citizenship" class="citizenship" value="2" <?php echo ($calc_data['citizenship'] == '2' ? ' checked' : '') ?>><small>Иностранное государство</small></label>
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
							  				echo '<option value='.$row["id"].' '.(isset($calc_data['subject']) && $calc_data['subject'] == $row["id"] ? 'selected' : '' ).'>'.$row["name"].'</option>';
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
							  				echo '<option value='.$row["id"].' '.($calc_data['term_insurance'] == $row["id"] ? 'selected' : '' ).'>'.$row["name"].'</option>';
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
						      		for($x=$a;$x>=$a-120;$x--){
						      			echo '<option value='.$x.' '.($calc_data['year_manufacture'] == $x ? 'selected' : '').'>'.$x.'</option>';
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
										  <label><input type="radio" name="category" class="category" value="'.$row["id"].'"'.($i == $calc_data['category'] ? ' checked' : '').' ><small>'.$row["name"].'</small></label>
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
							  				echo '<option value='.$row["id"].' '.($calc_data['capacity'] == $row["id"] ? 'selected' : '').'>'.$row["name"].'</option>';
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
							  				echo '<option value='.$row["id"].' '.($row["id"] == $calc_data['period_use'] ? ' selected' : '').'>'.$row["name"].'</option>';
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
									  	<label><input type="radio" name="drivers" class="drivers" value="1" <?php echo ($calc_data['drivers'] == '1' ? ' checked' : '')?> ><small>Без ограничений</small></label>
									</div>
									<div class="radio" id="drivers_limit">
									  	<label><input type="radio" name="drivers" class="drivers" value="2" <?php echo ($calc_data['drivers'] == '2' ? ' checked' : '')?>><small>Ограниченное количество</small></label>
									</div>
									<div id="message_1">
										<?php
										if($calc_data['drivers'] == 2){
											$i = 0;
											for($x=1;$x<6;$x++){
												if(isset($calc_data["driver_$x"])){
													$i++;
													echo '<select class="form-control input-sm driver_age" name="driver_'.$x.'">';
													echo '<option value="1" '.($calc_data["driver_$x"] == '1' ? 'selected' : '').'><small>До 22 лет включительно со стажем вождения до 3 лет включительно</small></option>';
													echo '<option value="2" '.($calc_data["driver_$x"] == '2' ? 'selected' : '').'><small>Более 22 лет со стажем вождения до 3 лет включительно</small></option>';
													echo '<option value="3" '.($calc_data["driver_$x"] == '3' ? 'selected' : '').'><small>До 22 лет включительно со стажем вождения свыше 3 лет</small></option>';
													echo '<option value="4" '.($calc_data["driver_$x"] == '4' ? 'selected' : '').'><small>Более 22 лет со стажем вождения свыше 3 лет</small></option>';
													echo '</select>';
												}
											}
											echo '<center>';
											if($i<5){
												echo '<span id="plus_'.$i.'" title="Добавить водителя" style="font-size:14px;top:0px" class="driver_plus glyphicon glyphicon-plus"></span>';
											}
											if($i>1){
												echo '<span id="minus_'.$i.'" title="Удалить водителя" style="font-size:14px;top:0px;margin:0px 0px 0px 10px" class="driver_minus glyphicon glyphicon-minus"></span>';
											}
											echo '</center>';
										}
										?>
									</div>									
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
							  				echo '<option value='.$row["id"].' '.($row["id"] == $calc_data['class_kbm'] ? ' selected' : '').'>'.$row["name"].'</option>';
							  			}
							  		?>
									</select>
									<span class="help-block"><a href="https://start.sngi.ru/kbm/osago_query.php" target="_blank"><small>Запрос КБМ в АИС РСА</small></a></span>
							    </div>
						  	</div>					  

					  	<hr>

							<div class="form-group">
						    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Имеется прицеп</small></label>
							    <div class="col-sm-7">															
									<div class="radio">
										  	<label><input type="radio" name="trailer" value="1" <?php echo ($calc_data['trailer'] == '1' ? ' checked' : '')?>><small>Нет</small></label>
									</div>
									<div class="radio">
										  	<label><input type="radio" name="trailer" value="2" <?php echo ($calc_data['trailer'] == '2' ? ' checked' : '')?>><small>Да</small></label>
									</div>
							    </div>
						  	</div>

					  	<hr>


							<div class="form-group">
						    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Были ли грубые нарушения условий страхования в соответствии с п.3 ст.9 ФЗ «Об обязательном страховании гражданской ответственности владельцев транспортных средств»</small></label>
							    <div class="col-sm-7">															
									<div class="radio">
										  	<label><input type="radio" name="violations" value="1" <?php echo ($calc_data['violations'] == '1' ? ' checked' : '')?>><small>Нет</small></label>
									</div>
									<div class="radio">
										  	<label><input type="radio" name="violations" value="2" <?php echo ($calc_data['violations'] == '2' ? ' checked' : '')?>><small>Да</small></label>
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
			  	<?php
			  	if(isset($calc_data['city'])){
			  	?>
			  	$('#city').val(<?php echo $calc_data['city'] ?>);
			  	<?php } ?>
			  }
			});
//период использования транспортного средства
	//Для юриков доступно минимум 6 месяцев
	$(document).on("change", ".type_ins", function(){
		type_ins($(this).val());
	});
	type_ins('<?php echo $calc_data['type_ins']?>');	
//срок страхования при выборе места регистрации
	//приоткрытие страницы ставим значение по умолчанию для срока страхования
	//$("#term_insurance").val(11);
	//скрываем изначально срок страхования до 20 дней включительно и весь блок срока страхования
	$("#term_insurance option[value=" + 12 + "]").hide();
		$(".term_insurance").hide();
	//скрываем и отображаем пункты в зависимости от выбора места регистрации
	$(document).on("change", ".place_reg", function(){
		place_reg($(this).val());
	});	
	place_reg(<?php echo $calc_data['place_reg']?>);
//Скрытие и отображение мощности двигателя в зависимости от выбора категории (отображаем мощность толкьо привыборе категории В)
	//При загрузки страницы по умолчанию скрываем мощность
	$(".capacity").hide();
	//Отображение/скрытие при выборе категории
	$(document).on("change", ".category", function(){
		show_capacity($(this).val());
	});	
	show_capacity(<?php echo $calc_data['category']?>);
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
//выставляем значение для периода использования
$('#period_use').val(<?php echo $calc_data['period_use']?>);
//проверка данных формы
    $('#main_form').submit(function( event ) {
    	calc_osago();
    	return false;
    });

///////////////////////////////////////////////////////////
});

</script>
