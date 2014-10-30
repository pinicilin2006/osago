<?php
session_start();
if(!isset($_SESSION['user_id']) || !isset($_SESSION["access"][2])){
	header("Location: index.php");
	exit;
}
if(!isset($_SESSION["calc"]) || !isset($_SESSION["step_1"])){
	header("Location: osago.php");
	exit;	
}
// echo "<pre>";
// print_r($_SESSION["step_1"]);
// echo "</pre>";
require_once('config.php');
require_once('function.php');
connect_to_base();
require_once('template/header.html');
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="col-md-6 col-md-offset-3" id="user_data">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Оформление полиса ОСАГО</h3>
	  			</div>
	  			<div class="panel-body">
					<form class="form-horizontal col-sm-10 col-sm-offset-1" role="form" id="main_form" method="post" required> 
					 <h4><b>Данные страхователя</b></h4>
						<div class="form-group" id="owner">
							<hr class="hr_line">
					    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Страхователь</small></label>
						    <div class="col-sm-7">															
								<div class="radio">
									  	<label><input type="radio" name="owner" class="owner" value="1" checked><small>Физическое лицо / индивидуальный предприниматель</small></label>
								</div>
								<div class="radio">
									  	<label><input type="radio" name="owner" class="owner" value="2"><small>Юридическое лицо</small></label>
								</div>
								<hr class="hr_line">
						    </div>
					  	</div>

					<div id="phiz">				  	
						  	<div class="form-group">
						    	<label for="second_name" class="col-sm-4 control-label"><small>Фамилия</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="second_name" id="second_name" placeholder="Фамилия" required>
						    	</div>
						  	</div>

						  	<div class="form-group">
						    	<label for="first_name" class="col-sm-4 control-label"><small>Имя</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="first_name" id="first_name" placeholder="Имя" required>
						    	</div>
						  	</div>

						  	<div class="form-group">
						    	<label for="third_name" class="col-sm-4 control-label"><small>Отчество</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="third_name" id="third_name" placeholder="Отчество" required>
						    	</div>
						  	</div>

						  	<div class="form-group">
						    	<label for="date_birth" class="col-sm-4 control-label"><small>Дата рождения</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm date_birth" name="date_birth" id="date_birth" placeholder="Дата рождения" required>
						    	</div>
						  	</div>
						  	<hr class="hr_line">
						  	<div class="form-group" style="padding-top:2%">
						    	<label for="date_birth" class="col-sm-4 control-label"><small>Документ, удостоверяющий личность</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="doc_name" id="doc_name" placeholder="Наименование" value="Паспорт РФ" required>
						      		<input type="text" class="form-control input-sm" name="doc_series" id="doc_series" placeholder="Серия" required>
						      		<input type="text" class="form-control input-sm" name="doc_number" id="doc_number" placeholder="Номер" required>
						      		<input type="text" class="form-control input-sm" name="doc_place" id="doc_place" placeholder="Орган, выдавший документ" required>
						      		<input type="text" class="form-control input-sm" name="doc_date" id="doc_date" placeholder="Дата выдачи" required>
						    	</div>
						  	</div>
						  	<hr class="hr_line">
						  	<div class="form-group" style="padding-top:2%">
						    	<label for="date_birth" class="col-sm-4 control-label"><small>Гражданство</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="citizenship" id="citizenship" placeholder="Гражданство" value="Российская федерация" required>
						    	</div>
						  	</div>				  	
						  	<hr class="hr_line">
						  	<div class="form-group" style="padding-top:2%">
						    	<label for="country_birth" class="col-sm-4 control-label"><small>Место рождения</small></label>
						    	<div class="col-sm-8">
									<select class="form-control input-sm" name="place_birth" required>
							  			<option value="" disabled>Выберите страну</option>
								  		<?php
								  		$query=mysql_query("SELECT * FROM `country` WHERE `checked` = '1' ORDER BY `name`");
								  		while($row = mysql_fetch_assoc($query)){
											echo '<option value="'.$row["id"].'" '.($row["id"] == '165' ? ' selected' : '').' >'.$row["name"].'</option>';
										}
										?>    
									</select>
									<input type="text" class="form-control input-sm" name="place_birth_city" placeholder="Дополнительная информация (область, район, населенный пункт)" required>				      		
						    	</div>
						  	</div>

				  	</div>

				  	<div id="jur" style="display:none">

						  	<div class="form-group">
						    	<label for="jur_name" class="col-sm-4 control-label"><small>Наименования юр. лица (полностью)</small></label>
						    	<div class="col-sm-8" style="padding-top:2%">
						      		<input type="text" class="form-control input-sm" name="jur_name" id="jur_name" placeholder="Наименования юр. лица (полностью)" required>
						    	</div>
						  	</div>
						  	<hr class="hr_line">
						  	<div class="form-group">
						    	<label for="jur_name" class="col-sm-4 control-label"><small>Свидетельство о регистрации юридического лица</small></label>
						    	<div class="col-sm-8" style="padding-top:2%">
						      		<input type="text" class="form-control input-sm" name="jur_name" id="jur_series" placeholder="Серия" required>
						      		<input type="text" class="form-control input-sm" name="jur_name" id="jur_number" placeholder="Номер" required>
						    	</div>
						  	</div>
						  	<hr class="hr_line">
						  	<div class="form-group">
						    	<label for="jur_name" class="col-sm-4 control-label"><small>ИНН юридического лица</small></label>
						    	<div class="col-sm-8" style="padding-top:2%">
						      		<input type="text" class="form-control input-sm" name="jur_inn" id="jur_inn" placeholder="Номер" required>
						    	</div>
						  	</div>				  	
				  					  					  					  	
					
					</div>	  	

				  	<hr class="hr_line">
					<div class="form-group">
				    	<label class="col-sm-4 control-label"><small>Место жительства (регистрации)</small></label>
				    	<div class="col-sm-8" style="padding-top:2%">
							<select class="form-control input-sm" name="subject" id="subject" required>
					  			<option value="" disabled>Субъект РФ</option>
						  		<?php
						  		$query=mysql_query("SELECT * FROM `kt_subject` ORDER BY `name`");
						  		while($row = mysql_fetch_assoc($query)){
									echo '<option value="'.$row["id_fias"].'" >'.$row["name"].'</option>';
								}
								?>    
							</select>
							<div id="message_0"></div>
							<div id="message_4" style="display:none">								
						      		<input type="text" class="form-control input-sm" name="house" id="house" placeholder="Номер дома" required>
						      		<input type="text" class="form-control input-sm" name="housing" id="housing" placeholder="Корпус">
						      		<input type="text" class="form-control input-sm" name="apartment" id="apartment" placeholder="Номер квартиры">						    						    	
							</div>			      		
				    	</div>
				  	</div>				  				  	
				  	<hr class="hr_line"><br>
				  	<div class="form-group">
				    	<label for="date_birth" class="col-sm-4 control-label"><small>Телефон</small></label>
				    	<div class="col-sm-8">
				      		<input type="text" class="form-control input-sm" name="phone" id="phone" required>
				    	</div>
				  	</div>					  	
				  	<hr class="hr_line">

						<div class="form-group">
					    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Страхователь является собственником</small></label>
						    <div class="col-sm-7">															
								<div class="radio-inline">
									  	<label><input type="radio" name="insisown" class="insisown" value="1" checked><small>Да</small></label>
								</div>
								<div class="radio-inline">
									  	<label><input type="radio" name="insisown" class="insisown" value="2"><small>Нет</small></label>
								</div>
						    </div>
					  	</div>
				  	

				  	<div id="owner_data" style="display:none">

				  	<hr>
						<h4><b>Данные собственника</b></h4>
						 <hr> 	
						
						<?php 
						if($_SESSION["step_1"]["type_ins"] != 'jur'){
						?>										  	
						  	<div class="form-group">
						    	<label for="second_name" class="col-sm-4 control-label"><small>Фамилия</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="owner_second_name" id="owner_second_name" placeholder="Фамилия" required>
						    	</div>
						  	</div>

						  	<div class="form-group">
						    	<label for="first_name" class="col-sm-4 control-label"><small>Имя</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="owner_first_name" id="owner_first_name" placeholder="Имя" required>
						    	</div>
						  	</div>

						  	<div class="form-group">
						    	<label for="third_name" class="col-sm-4 control-label"><small>Отчество</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="owner_third_name" id="owner_third_name" placeholder="Отчество" required>
						    	</div>
						  	</div>

						  	<div class="form-group">
						    	<label for="date_birth" class="col-sm-4 control-label"><small>Дата рождения</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm date_birth" name="owner_date_birth" id="owner_date_birth" placeholder="Дата рождения" required>
						    	</div>
						  	</div>
						  	<hr class="hr_line">
						  	<div class="form-group" style="padding-top:2%">
						    	<label for="date_birth" class="col-sm-4 control-label"><small>Документ, удостоверяющий личность</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="owner_doc_name" id="owner_doc_name" placeholder="Наименование" value="Паспорт РФ" required>
						      		<input type="text" class="form-control input-sm" name="owner_doc_series" id="owner_doc_series" placeholder="Серия" required>
						      		<input type="text" class="form-control input-sm" name="owner_doc_number" id="owner_doc_number" placeholder="Номер" required>
						      		<input type="text" class="form-control input-sm" name="owner_doc_place" id="owner_doc_place" placeholder="Орган, выдавший документ" required>
						      		<input type="text" class="form-control input-sm" name="owner_doc_date" id="owner_doc_date" placeholder="Дата выдачи" required>
						    	</div>
						  	</div>
						  	<hr class="hr_line">
						  	<div class="form-group" style="padding-top:2%">
						    	<label for="date_birth" class="col-sm-4 control-label"><small>Гражданство</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="owner_citizenship" id="owner_citizenship" placeholder="Гражданство" value="Российская федерация" required>
						    	</div>
						  	</div>				  	
						  	<hr class="hr_line">
						  	<div class="form-group" style="padding-top:2%">
						    	<label for="country_birth" class="col-sm-4 control-label"><small>Место рождения</small></label>
						    	<div class="col-sm-8">
									<select class="form-control input-sm" name="owner_place_birth" required>
							  			<option value="" disabled>Выберите страну</option>
								  		<?php
								  		$query=mysql_query("SELECT * FROM `country` WHERE `checked` = '1' ORDER BY `name`");
								  		while($row = mysql_fetch_assoc($query)){
											echo '<option value="'.$row["id"].'" '.($row["id"] == '165' ? ' selected' : '').' >'.$row["name"].'</option>';
										}
										?>    
									</select>
									<input type="text" class="form-control input-sm" name="owner_place_birth_city" placeholder="Дополнительная информация (область, район, населенный пункт)" required>				      		
						    	</div>
						  	</div>

					  	<?php
					  	}
					  	?>

						<?php 
						if($_SESSION["step_1"]["type_ins"] == 'jur'){
						?>

						  	<div class="form-group">
						    	<label for="jur_name" class="col-sm-4 control-label"><small>Наименования юр. лица (полностью)</small></label>
						    	<div class="col-sm-8" style="padding-top:2%">
						      		<input type="text" class="form-control input-sm" name="owner_jur_name" id="owner_jur_name" placeholder="Наименования юр. лица (полностью)" required>
						    	</div>
						  	</div>
						  	<hr class="hr_line">
						  	<div class="form-group">
						    	<label for="jur_name" class="col-sm-4 control-label"><small>Свидетельство о регистрации юридического лица</small></label>
						    	<div class="col-sm-8" style="padding-top:2%">
						      		<input type="text" class="form-control input-sm" name="owner_jur_name" id="owner_jur_series" placeholder="Серия" required>
						      		<input type="text" class="form-control input-sm" name="owner_jur_name" id="owner_jur_number" placeholder="Номер" required>
						    	</div>
						  	</div>
						  	<hr class="hr_line">
						  	<div class="form-group">
						    	<label for="jur_name" class="col-sm-4 control-label"><small>ИНН юридического лица</small></label>
						    	<div class="col-sm-8" style="padding-top:2%">
						      		<input type="text" class="form-control input-sm" name="owner_jur_inn" id="owner_jur_inn" placeholder="Номер" required>
						    	</div>
						  	</div>				  	
					  					  					  					  	
					  	<?php
					  	}
					  	?>

					  	<hr class="hr_line">
						<div class="form-group">
					    	<label class="col-sm-4 control-label"><small>Место жительства (регистрации)</small></label>
					    	<div class="col-sm-8" style="padding-top:2%">
								<select class="form-control input-sm" name="owner_subject" id="owner_subject" required>
						  			<option value="" disabled>Субъект РФ</option>
							  		<?php
							  		$query=mysql_query("SELECT * FROM `kt_subject` ORDER BY `name`");
							  		while($row = mysql_fetch_assoc($query)){
										echo '<option value="'.$row["id_fias"].'" >'.$row["name"].'</option>';
									}
									?>    
								</select>
								<div id="owner_message_0"></div>
								<div id="owner_message_4" style="display:none">								
							      		<input type="text" class="form-control input-sm" name="owner_house" id="owner_house" placeholder="Номер дома" required>
							      		<input type="text" class="form-control input-sm" name="owner_housing" id="owner_housing" placeholder="Корпус">
							      		<input type="text" class="form-control input-sm" name="owner_apartment" id="owner_apartment" placeholder="Номер квартиры">						    						    	
								</div>			      		
					    	</div>
					  	</div>				  				  	
					  	<hr class="hr_line"><br>
					  	<div class="form-group">
					    	<label for="date_birth" class="col-sm-4 control-label"><small>Телефон</small></label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control input-sm" name="owner_phone" id="owner_phone" required>
					    	</div>
					  	</div>					  					  	
				  	</div>
				  	<hr class="hr_line">
				  	<h4><b>Данные транспортного средства</b></h4>
				  	<hr>					  	

						<div class="form-group ">
					    	<label  class="col-sm-4 control-label" style="word-wrap:break-word;"><small>Марка ТС</small></label>
					    	<div class="col-sm-8">							
								<select class="form-control input-sm" name="mark" id="mark" required>
						  		<option value="" disabled selected>Выберите марку ТС</option>
						  		<?php
						  			$query = mysql_query("SELECT * FROM `mark` ORDER BY `name`");
						  			while ($row = mysql_fetch_assoc($query)) {
						  				echo '<option value='.$row["rsa_mark_id"].'>'.$row["name"].'</option>';
						  			}
						  		?>
								</select>
								<div id="message_mark"></div>
					    	</div>
					  	</div>


					  	<div class="form-group">
					      	<button type="submit" class="btn btn-success btn-block">Рассчитать стоимость</button>
					  	</div>
					</form>
	  			</div>
			</div>
		</div>
		<div class="col-md-4 col-md-offset-4">
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
	$('.date_birth').mask('00.00.0000');
	$('#doc_date').mask('00.00.0000');
	$('#phone').mask('(000)000-00-00',{placeholder: "(___)___-__-__"} );
	$('#owner_doc_date').mask('00.00.0000');
	$('#owner_phone').mask('(000)000-00-00',{placeholder: "(___)___-__-__"} );		
//Календарик	
	$( ".date_birth" ).datepicker({
	  dateFormat: "dd.mm.yy",
	  maxDate: "-18Y",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c-70:c"
	});
	$( "#doc_date" ).datepicker({
	  dateFormat: "dd.mm.yy",
	  maxDate: "0d",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c-70:c"
	});
	$( "#owner_doc_date" ).datepicker({
	  dateFormat: "dd.mm.yy",
	  maxDate: "0d",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c-70:c"
	});

	
//////////////////////////////СТРАХОВАТЕЛЬ ДАННЫЕ РЕГИСТРАЦИИ////////////////////////////////////////////////		
	//отображение списка городов субъекта для страхователя
		$(document).on("change", "#subject", function(){
			var a = $(this).val();
			$('#message_0').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias.php',
				  data: "subject="+a,
				  success: function(data) {
				  	$('#message_0').html(data);
				  	
				  }
				});
				return false;
		});
	//отображение списка населённых пунктов для страхователя
		$(document).on("change", "#aoid", function(){
			var a = $(this).val();
			var b = $('#subject').val();
			//$('#message_1').html(a);
			$('#message_1').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias.php',
				  data: {aoid: a, subject: b},
				  success: function(data) {
				  	$('#message_1').html(data);
				  	
				  }
				});
				return false;
		});

	//отображение списка улиц для населённых пунктов для страхователя
		$(document).on("change", "#city", function(){
			var a = $(this).val();
			//$('#message_1').html(a);
			$('#message_2').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias.php',
				  data: {city: a},
				  success: function(data) {
				  	$('#message_2').html(data);
				  	
				  }
				});
				return false;
		});

	//отображение списка домов для улицы страхователя
		$(document).on("change", "#street", function(){
			$('#message_4').show();
				return false;
		});	
///////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////СОБСТВЕННИК ДАННЫЕ РЕГИСТРАЦИИ//////////////////////////////////////////////		
	//отображение списка городов субъекта для собственника
		$(document).on("change", "#owner_subject", function(){
			var a = $(this).val();
			$('#owner_message_0').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias_owner.php',
				  data: "owner_subject="+a,
				  success: function(data) {
				  	$('#owner_message_0').html(data);
				  	
				  }
				});
				return false;
		});
	//отображение списка населённых пунктов для собственника
		$(document).on("change", "#owner_aoid", function(){
			var a = $(this).val();
			var b = $('#owner_subject').val();
			$('#owner_message_1').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias_owner.php',
				  data: {owner_aoid: a, owner_subject: b},
				  success: function(data) {
				  	$('#owner_message_1').html(data);
				  	
				  }
				});
				return false;
		});

	//отображение списка улиц для населённых пунктов для собственника
		$(document).on("change", "#owner_city", function(){
			var a = $(this).val();
			$('#owner_message_2').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias_owner.php',
				  data: {owner_city: a},
				  success: function(data) {
				  	$('#owner_message_2').html(data);
				  	
				  }
				});
				return false;
		});

	//отображение списка домов для улицы для собственника
		$(document).on("change", "#owner_street", function(){
			$('#owner_message_4').show();
				return false;
		});	
///////////////////////////////////////////////////////////////////////////////////////////////





//Отображение выбора вида собственника
	$(document).on("change", ".insisown", function(){
		var a = $(this).val();
		if(a=='2'){
			$('input:radio[name="owner"]').filter('[value="1"]').prop('checked',true);
			$("#jur").slideUp();
			$("#phiz").slideDown();
			//$("#owner").slideDown();
			$("#owner_data").slideDown();
		} else {
			$("#owner_data").slideUp();
			//$("#owner").slideUp();
		}
	});

//Отображение полей для ввода данных собственника в зависимости от выбранного типа собственника
	$(document).on("change", ".owner", function(){
		var a = $(this).val();
		if(a == '1'){
			$("#jur").slideUp();
			$("#phiz").slideDown();
		} else {
			$("#phiz").slideUp();
			$("#jur").slideDown();			
		}
	});

//отображение списка моделей для определённой марки
		$(document).on("change", "#mark", function(){
			var a = $(this).val();
			$('#message_mark').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/model.php',
				  data: "mark="+a,
				  success: function(data) {
				  	$('#message_mark').html(data);
				  	
				  }
				});
				return false;
		});

//проверка данных формы
    $('#main_form').submit(function( event ) {
    	
    });

///////////////////////////////////////////////////////////
});

</script>

