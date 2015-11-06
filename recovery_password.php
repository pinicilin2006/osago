<?php
session_start();
if(isset($_SESSION['user_id'])){
	header("Location: index.php");
	exit;
}
require_once('config.php');
require_once('function.php');
require_once('template/header_login.html');
if($_SESSION['attempt'] > 10){
	echo '<center><p class="text-danger">Количество попыток закончилось. Попробуйте позже.</p></center>';
	exit();
}

check_browser();
?>
<div class="container">
    <div class="row">
        <div class="col-sm-4 col-sm-offset-4">	
            <form role="form" action="method/recovery_password.php" method="post">
				<fieldset>
				<legend>Отправить пароль</legend>
					<div class="radio">
				  		<label><input type="radio" name="type_recovery" class="type_recovery" value="phone" checked><small>на телефон</small></label>
					</div>
					<div class="radio">
				  		<label><input type="radio" name="type_recovery" class="type_recovery" value="email"><small>на электронный адрес</small></label>
					</div> 
				  	<br> 				  	
		                <input type="text" class="form-control input-sm action" name="phone" id="phone" placeholder="телефон">
		                <input type="text" class="form-control input-sm action" name="email" id="email" placeholder="email" style="display:none">
		            <hr>
                <button class="btn btn-primary btn-block" type="submit" id="button" disabled="disabled">Отправить</button>					
				</fieldset>
			</form>				           
        </div>
    </div>
</div>
<div id='footer'>
<?php require_once('template/footer.html') ?>
</div>
<script type="text/javascript">
$(document).ready(function(){
$('#phone').mask('(999)999-99-99');
	$(document).on("change", ".type_recovery", function(){
		var a = $(this).val();
		if(a == 'phone'){
			$('#phone').slideDown();
			$('#email').slideUp();
		} else {
			$('#phone').slideUp();
			$('#email').slideDown();			
		}
	});
	$(document).on("change keyup", ".action", function(){
		var a = $('#phone').val();
		var b = $('#email').val();
		if(a == '' && b == ''){
			$("button").attr("disabled", "disabled");
		} else {
			$("button").removeAttr("disabled");
		}
	});
});
</script>