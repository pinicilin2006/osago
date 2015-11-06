<?php

// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
require_once('config.php');
require_once('function.php');
connect_to_base();
//Часть  переправляющая на страницу с логином
	if(mysql_num_rows(mysql_query("SELECT * FROM `service_status` WHERE `id` = '1' AND `checked` = '1'")) == '1'){
		header("Location: login.php");
		exit;
	}
/////////////////////////////////////////////
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
	  			<li class="thumbnail"><center>Сервис временно не доступен. Попробуйте позже.</center></li>
		  		</ul>
	  			</div>
			</div>
			<div id="message"></div>
		</div>
	</div>
</div>
<?php require_once('template/footer.html') ?>
</body>
</html>