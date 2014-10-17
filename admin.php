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
		<div class="col-md-12">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Общая структура, подразделения и пользователи.</h3>
	  			</div>	  			
	  			<div class="panel-body">
	  				<div class="col-md-6">
	  					 Страховое общество "Сургутнефтегаз" <span list_id="1" class="list_user glyphicon glyphicon-user"></span> <span style="font-size:8px;top:0px" id="1" class="plus glyphicon glyphicon-plus"></span>
	  					<div id="message_1"></div>
	  				</div>
					<div class="col-md-6">
						<div id="user_data"></div>
	  				</div>
	  			</div>
	  			<div id="message"></div>
			</div>
		</div>
	</div>
</div>
<div class="footer  text-center">
	<small>©<?php echo date("Y") ?>. <a href="https://www.sngi.ru">Страховое общество «Сургутнефтегаз».</a> Все права защищены.</small>
</div>
</body>
</html>
<script type="text/javascript">
$(document).ready(function(){
	//отображение списка дочерних подразделений
	$(document).on("click", ".plus", function(){
		var a = $(this).attr("id");
		//если горит плюс то раскрываем список
		if($(this).hasClass("glyphicon-plus")){	
			$(this).removeClass("glyphicon-plus");
			$(this).addClass("glyphicon-minus");
			$.ajax({
			  type: "POST",
			  url: '/ajax/unit_list.php',
			  data: "id="+a,
			  success: function(data) {
			  	$("#message_"+a).html(data);
			  }
			});
		} else {
			$("#message_"+a).html('');
			$(this).removeClass("glyphicon-minus");
			$(this).addClass("glyphicon-plus");
		};
			return false;
	});

	//отображения списка пользователей подразделения
	$(document).on("click", ".list_user", function(){
		var a = $(this).attr("list_id");
		//alert(a);
		$.ajax({
		  type: "POST",
		  url: '/ajax/user_list.php',
		  data: "id="+a,
		  success: function(data) {
		  	$("#user_data").html(data);
		  }
		});
		return false;
	});

});

</script>

