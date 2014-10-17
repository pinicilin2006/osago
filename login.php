<?php
session_start();
if(isset($_SESSION['user_id'])){
	header("Location: index.php");
	exit;
}
require_once('config.php');
require_once('function.php');
require_once('template/header_login.html');
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
                </form>
                <br>
                <div class="text-center"><p class="text-danger"><?php echo (isset($_SESSION["login_error_message"]) ? $_SESSION["login_error_message"] : '') ?></p></div>                       
            </div>
        </div>
    </div>
</div>
