<?php
session_start();
if(!isset($_SESSION['user_id']) || !isset($_SESSION["access"][11])){
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
	    			<h3 class="panel-title">Новости проекта</h3>
	  			</div>
	  			<div class="panel-body" id="user_data">
					<form class="form-horizontal col-sm-8 col-sm-offset-2" role="form" id="main_form" method="post"> 
					  	<div class="form-group">
					    	<label for="news" class=" control-label">Текст новости:</label>
					    	<div>
					      		<textarea class="form-control" rows="3" name="news" id="news" required></textarea>
					    	</div>
					  	</div>
					  <div class="form-group">
					      <button type="submit" class="btn btn-default">Добавить новость</button>
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


//проверка данных формы
    $('#main_form').submit(function( event ) {
    	add_news();
    	return false;
    });
});

</script>