<?php
require_once('config.php');
require_once('function.php');
connect_to_base();
//Часть закрывающая на ремонт сайт
	if(mysql_num_rows(mysql_query("SELECT * FROM `service_status` WHERE `id` = '2' AND `checked` = '1'")) == '1' && !$_GET['admin']=='admin'){
		header("Location: remont.php");
		exit;
	}
/////////////////////////////////
session_start();
if(isset($_SESSION['user_id'])){
	header("Location: index.php");
	exit;
}
require_once('template/header_login.html');
check_browser();
?>
<div class="container">
    <div class="row">
        <div class="col-md-3 col-md-offset-4">
            <h3 class="text-center login-title">Введите ваш логин и пароль:</h3>
            <hr>
            <div class="account-wall">
                <form action="method/login.php" method="post">
                <input type="text" class="form-control input-sm" name="login" placeholder="Логин" required autofocus>
                <input type="password" class="form-control input-sm" name="password" placeholder="Пароль" required>
                <button class="btn  btn-primary btn-block" type="submit">Войти</button>
                <br>
                <p class='text-right'><a href='/recovery_password.php'><small>Восстановить пароль</small></a></p>
                </form>
                <br>
                <div class="text-center"><p class="text-danger"><?php echo (isset($_SESSION["login_error_message"]) ? $_SESSION["login_error_message"] : '') ?></p></div>                       
            </div>
        </div>
    </div>
</div>
<div id='footer'>
<?php require_once('template/footer.html') ?>
</div>