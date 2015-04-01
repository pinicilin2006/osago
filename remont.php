<?php

// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
// require_once('config.php');
// require_once('function.php');
// connect_to_base();
require_once('template/header_login.html');
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="col-md-6 col-md-offset-3">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">ВНИМАНИЕ! ТЕХНИЧЕСКИЕ РАБОТЫ!</h3>
	  			</div>
	  			<div class="panel-body" id="user_data">
	  			<ul style="padding:0px">
	  			<li class="thumbnail"><center>Сервис заработает после обновления тарифов.</center></li>
		  		</ul>
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