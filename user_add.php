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
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Добавить пользователя:</h3>
	  			</div>	  			
	  			<div class="panel-body" id="user_data">
					<form class="form-horizontal col-sm-8 col-sm-offset-2" role="form" id="main_form">
					
					  <p class="help-block text-right"></span><small>* - поля обязательные для заполнения.</small></p>  

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm fio" id="second_name" name="second_name" placeholder="Фамилия *" required>					    
					  </div>
					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm fio" id="first_name" name="first_name" placeholder="Имя *" required>					    
					  </div>

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm fio" id="third_name" name="third_name" placeholder="Отчество *" required>					    
					  </div>					  					  

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="date_birth" name="date_birth" placeholder="Дата рождения *" required>					    
					  </div>

					  <div class="form-group">
							<label class="radio-inline"><input type="radio" name="sex" value="m">м</label>	    
							<label class="radio-inline"><input type="radio" name="sex" value="f" checked>ж</label>
					  </div>

					  <div class="form-group has-feedback">					    					    
					      <input type="text	" class="form-control input-sm" id="phone" name="phone" placeholder="Телефон">					    
					  </div>

					  <div class="form-group">					    					    
					      <input type="email" class="form-control input-sm" id="email" name="email" placeholder="Email">
					      <p class="help-block"><small>Восстановление пароля возможно только при указанном email либо номере телефона.</small></p>					    
					  </div>

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="login" name="login" placeholder="Логин *" required>					    
					  </div>

					  <div class="form-group has-feedback">					    					    
					      <input type="text" class="form-control input-sm" id="password" name="password" value='<?php echo generate_password(8)?>' placeholder="Пароль *" required>
					      <p class="help-block"><small>Английский язык, минимум 6 символов, минимум одна буква и одна цифра.</small></p>					    
					  </div>
					  <div class="form-group">
					  		<select class="form-control" name="unit" required>
					  		<option value="" disabled selected>Выберите подразделение *</option>
					  		<?php
					  		$query=mysql_query("SELECT * FROM `unit` ORDER BY unit_parent_id");
					  		while($row1 = mysql_fetch_assoc($query)){
								echo "<option value=\"$row1[unit_id]\" ";
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
					  		$query=mysql_query("SELECT * FROM `rights` WHERE active = 1 ORDER BY priority");
					  		while($row = mysql_fetch_assoc($query)){
								echo "<label class=\"checkbox-inline\"><input type=\"checkbox\" name=\"rights[]\" value=\"$row[id]\" >$row[name]</label><br>";
							}
							?>    
					  </div>

					  <hr align="center" size="2" />

					  <div class="form-group">
							<label class="checkbox-inline"><input type="checkbox" name="active" value="1" checked>Учётная запись активна</label>	    
					  </div>

					  <div class="form-group">
					      <button type="submit" class="btn btn-default">Добавить пользователя</button>
					  </div>



					</form>
	  			</div>
	  			<div id="message"></div>
			</div>
		</div>
	</div>
</div>
<?php require_once('template/footer.html') ?>
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
//Получаем подразделения филиала
	$(document).on("change", "#filial", function(){
		var a = $(this).val();
			$.ajax({
			  type: "POST",
			  url: '/ajax/filial_unit.php',
			  data: "id="+a,
			  success: function(data) {
			  	$('#message_1').html(data);
			  	
			  }
			});
			return false;
	})
//Заполняем поле с логином при заполнение полей с фио
	var rusChars = new Array('А','Б','В','Г','Д','Е','Ё','Ж','З','И','Й','К','Л','М','Н','О','П','Р','С','Т','У','Ф','Х','Ц','Ч','Ш','Щ','Ъ','Ы','Ь','Э','Ю','Я','а','б','в','г','д','е','ё','ж','з','и','й','к','л','м','н','о','п','р','с','т','у','ф','х','ц','ч','ш','щ','ъ','ы','ь','э','ю','\я',' ');
	/* var transChars = new Array('A','B','V','G','D','E','YE','ZH','Z','I','Y','K','L','M','N','O','P','R','S','T','U','F','KH','TS','CH','SH','SHCH','\`','Y','\'','E','YU','YA','a','b','v','g','d','e','ye','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','kh','ts','ch','sh','shch','\`','y','\'','e','yu','ya','_'); */
	var transChars = new Array('a','b','v','g','d','e','ye','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','kh','ts','ch','sh','shch','','y','','e','yu','ya','a','b','v','g','d','e','ye','zh','z','i','y','k','l','m','n','o','p','r','s','t','u','f','kh','ts','ch','sh','shch','','y','','e','yu','ya','_');
	function convert(from){
	var to = new String();
	var len = from.length;
	var character, isRus;
	for(i=0; i < len; i++){
		character = from.charAt(i,1);
		isRus = false;
		for(j=0; j < rusChars.length; j++){
			if(character == rusChars[j]){
				isRus = true;
				break;
			}
		}
		to += (isRus) ? transChars[j] : character;
	}
	return to;
	//$('#login').val(to);
}	
$('.fio').change(function(){
	if($('#first_name').val() === '' || $('#second_name').val() === '' || $('#third_name').val() === ''){
		return false;
	}
	var a = convert($('#first_name').val()[0]);
	var b = convert($('#second_name').val());
	var c = convert($('#third_name').val()[0]);
	// var a_tr = convert(a);
	// var b_tr = convert(b);
	// var c_tr = convert(c);
	var login = b+'_'+a+c;
	$('#login').val(login);
	
});	
//проверка данных формы
    $('#main_form').submit(function( event ) {
    	add_user();
    	return false;
    });
});

</script>