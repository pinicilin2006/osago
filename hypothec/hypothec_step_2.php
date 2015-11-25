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
$id_bank = $_SESSION['step_1']['bank'];
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="col-md-10 col-md-offset-1" id="user_data">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Оформление полиса "Комплексного ипотечного страхования"</h3>
	  			</div>
	  			<div class="panel-body">
					<form class="form-horizontal col-sm-10 col-sm-offset-1" role="form" id="main_form" method="post">
					 <input type="hidden" name="md5_id" value="<?php echo md5(date("F j, Y, g:i:s "))?>"> 
					<?php require_once('calc_form_step_2/'.$id_bank.'.php') ?>
					<hr class="hr_red_2">	
								<b>Срок действия договора:</b>
								<hr>
								<div class="form-group">
								    <label for="date_start" class="col-sm-4 control-label"><small>12 месяцев с 00 часов 00 минут</small></label>
								    <div class="col-sm-3">
								      	<input type="text" class="form-control input-sm" name="date_start"  id="date_start" required readonly="readonly" value="<?php echo date("d.m.Y", mktime(0, 0, 0, date("m"), date("d")+1,   date("Y"))) ?>">
								    </div>
								</div>
								<div class="form-group">
								    <label for="date_end" class="col-sm-4 control-label"><small>по 24 часа 00 минут</small></label>
								    <div class="col-sm-3">
								      	<input type="text" class="form-control input-sm" name="date_end"  id="date_end" required readonly="readonly" value="<?php echo date("d.m.Y", mktime(0, 0, 0, date("m")+12, date("d")+1,   date("Y"))) ?>">
								    </div>
								</div>			 
							</fieldset>
						</div>
					<div class="col-md-12">
						<hr class="hr_red_2">
						<div class="form-group">
							<div class="col-sm-6">
								<button type="submit" name="action" value="add" class="btn btn-primary btn-block">Оформить полис</button>
							</div>
							<div class="col-sm-6">	
								<button type="submit" name="action" value="project" class="btn btn-danger btn-block">Сохранить как проект</button>
							</div>
						</div>
					</div>
					</form>															  						  			  						  						  							  						
	  			</div>
			</div>		
		</div>
		<div class="col-md-12">
			<div id="message"></div>
		</div>
	</div>
<?php require_once('../template/footer.html') ?>
</body>
</html>
<?php require_once('../js/hypothec_'.$id_bank.'.js') ?>