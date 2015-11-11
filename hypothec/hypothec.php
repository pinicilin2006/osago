<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: ../login.php");
	exit;
}
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
require_once('../config.php');
require_once('../function.php');
connect_to_base();
require_once('../template/header.html');
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="col-md-12" id="user_data">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Расчёт стоимости полиса "Комплексного ипотечного страхования"</h3>
	  			</div>
	  			<div class="panel-body">
					<form class="form-horizontal col-sm-10 col-sm-offset-1" role="form" id="main_form" method="post"> 
						  	<div class="form-group">
						  	<hr>
						    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Выберите банк:</small></label>
						    	<div class="col-sm-4">							
									<select class="form-control input-sm" name="bank" id="bank" required>
							  		<option value="" disabled selected>Наименование банка:</option>
							  		<?php
							  			$query = mysql_query("select * from rights r, user_rights u where u.user_id=".$_SESSION['user_id']." and u.rights = r.id and r.active=1 and r.id_product=2");
							  			while ($row = mysql_fetch_assoc($query)) {
							  				echo '<option value='.$row["id"].'>'.$row["name"].'</option>';
							  			}
							  		?>
									</select>
						    	</div>						    	
						  	</div>
						  	<hr>
						  	<div id="bank_field"></div>											  						  			  						  						  							  	
					</form>
	  			</div>
			</div>
		</div>
		<div class="col-md-12">
			<div id="message"></div>
		</div>
	</div>
</div>
<?php require_once('../template/footer.html') ?>
</body>
<?php require_once('modal.html') ?>
</html>
<?php require_once('../js/hypothec.js') ?>