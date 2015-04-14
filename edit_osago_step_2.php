<?php
session_start();
if(!isset($_SESSION['user_id']) || !isset($_SESSION["access"][2])){
	header("Location: index.php");
	exit;
}
if(!isset($_SESSION["calc"]) || !isset($_SESSION["step_1"])){
	header("Location: osago.php");
	exit;	
}
if(!isset($_SESSION['user_id']) || !isset($_GET["id"]) || empty($_GET["id"])){
	echo "<p class=\"text-danger text-center\">Договор с запрашиваемым id не найден в базе данных</p>";
	exit();
}
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
require_once('config.php');
require_once('function.php');
connect_to_base();
require_once('template/header.html');
$id = mysql_real_escape_string($_GET['id']);
if(isset($_SESSION["access"][3])){
	$query = "SELECT * FROM `contract` WHERE `md5_id` = '".$id."' AND `project` = '1' AND `annuled` = '0'";
}else{
	$query = "SELECT * FROM `contract` WHERE `md5_id` = '".$id."' AND `project` = '1' AND `annuled` = '0' AND `unit_id` = '".$_SESSION["unit_id"]."' AND `user_id` = '".$_SESSION["user_id"]."'";
}
//echo $query;
if(mysql_num_rows(mysql_query($query))<1){
	echo "<p class=\"text-danger text-center\">Договор с запрашиваемым id не найден в базе данных</p>";
	exit();
}
$contract_data = mysql_fetch_assoc(mysql_query($query));
//Получаем данные страхователя
$insurer_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `".($contract_data["insurer_type"] == 1 ? "contact_phiz" : "contact_jur")."` WHERE `id` = '".$contract_data["insurer_id"]."'"));
//Получаем данные собственника
$owner_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `".($contract_data["owner_type"] == 1 ? "contact_phiz" : "contact_jur")."` WHERE `id` = '".$contract_data["owner_id"]."'"));
//Данные по второму шагу оформления полиса
$step_2_data = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $contract_data['step_2_data']);//боримся с проблемой unserialize если есть кавычки
$step_2_data = unserialize($step_2_data);
//Данные по транспортному средству
$vehicle_data = unserialize($contract_data['vehicle_data']);
$category_code = array(
	'1' => 1,
	'2' => 2,
	'3' => 2,
	'4' => 3,
	'5' => 3,
	'6' => 4,
	'7' => 4,
	'8' => 4,
	'9' => 6,
	'10' => 5,
	'11' => 7
);
?>

<style type="text/css">
	#top {
	  bottom: 0;
	  cursor: pointer;
	  display: none;
	  font-size: 200%;
	  position: fixed;
	  right: 0;
	}
</style>
<div id="top"><span class="glyphicon glyphicon-arrow-up"></span></div>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="col-md-8 col-md-offset-2" id="user_data">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Оформление полиса ОСАГО</h3>
	  			</div>
	  			<div class="panel-body">
					<form class="form-horizontal col-sm-10 col-sm-offset-1" role="form" id="main_form" method="post"> 
					 <input type="hidden" name="md5_id" value="<?php echo $id ?>">
					 <h4><b>Данные страхователя</b></h4>
						<div class="form-group" id="owner">
							<hr class="hr_red">
					    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Страхователь</small></label>
						    <div class="col-sm-7">															
								<div class="radio">
									  	<label><input type="radio" name="insurer" class="insurer" value="1" <?php echo ($contract_data["insurer_type"] == '1' ? ' checked' : '') ?>><small>Физическое лицо / индивидуальный предприниматель</small></label>
								</div>
								<div class="radio">
									  	<label><input type="radio" name="insurer" class="insurer" value="2" <?php echo ($contract_data["insurer_type"] == '2' ? ' checked' : '') ?>><small>Юридическое лицо</small></label>
								</div>
								
						    </div>
					  	</div>
						<hr>
					<div id="phiz" <?php echo ($step_2_data['insurer'] == '2' ? 'style="display:none"' : '')?>>				  	
						  	<div class="form-group">
						    	<label for="second_name" class="col-sm-4 control-label"><small>Фамилия</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm phiz_name" name="second_name" id="second_name" value="<?php echo ($contract_data["insurer_type"] == '1' ? $insurer_data['second_name'] : '') ?>" placeholder="Фамилия" required>
						    	</div>
						  	</div>

						  	<div class="form-group">
						    	<label for="first_name" class="col-sm-4 control-label phiz_name"><small>Имя</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="first_name" id="first_name" value="<?php echo ($contract_data["insurer_type"] == '1' ? $insurer_data['first_name'] : '') ?>" placeholder="Имя" required>
						    	</div>
						  	</div>

						  	<div class="form-group">
						    	<label for="third_name" class="col-sm-4 control-label phiz_name"><small>Отчество</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="third_name" id="third_name" value="<?php echo ($contract_data["insurer_type"] == '1' ? $insurer_data['third_name'] : '') ?>" placeholder="Отчество" required>
						    	</div>
						  	</div>

						  	<div class="form-group">
						    	<label for="date_birth" class="col-sm-4 control-label phiz_name"><small>Дата рождения</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm date_birth" name="date_birth" id="date_birth" value="<?php echo ($contract_data["insurer_type"] == '1' ? $insurer_data['date_birth'] : '') ?>" placeholder="Дата рождения" required>
						    	</div>
						  	</div>
						  	<hr class="hr_line">
						  	<div class="form-group" style="padding-top:2%">
						    	<label class="col-sm-4 control-label"><small>Документ, удостоверяющий личность</small></label>
						    	<div class="col-sm-8">
						      	<select class="form-control input-sm" name="doc_name" id="doc_name" required>
						  		<?php
						  		$query=mysql_query("SELECT * FROM `document` WHERE `active` = 1 ORDER BY `name`");
						  		while($row = mysql_fetch_assoc($query)){
									echo '<option value="'.$row["id"].'" '.($row["id"] == $insurer_data['doc_name'] ? 'selected' : '').' >'.$row["name"].'</option>';
								}
								?>    
								</select>
						      		<input type="text" class="form-control input-sm" name="doc_series" id="doc_series" value="<?php echo ($contract_data["insurer_type"] == '1' ? $insurer_data['doc_series'] : '') ?>" placeholder="Серия" required>
						      		<input type="text" class="form-control input-sm" name="doc_number" id="doc_number" value="<?php echo ($contract_data["insurer_type"] == '1' ? $insurer_data['doc_number'] : '') ?>" placeholder="Номер" required>
						    	</div>
						  	</div>						  	
				  	</div>

				  	<div id="jur" <?php echo ($step_2_data['insurer'] == '1' ? 'style="display:none"' : '')?>>

						  	<div class="form-group">
						    	<label for="jur_name" class="col-sm-4 control-label"><small>Наименования юр. лица (полностью)</small></label>
						    	<div class="col-sm-8" style="padding-top:2%">
						      		<input type="text" class="form-control input-sm" name="jur_name" id="jur_name" value='<?php echo ($contract_data["insurer_type"] == '2' ? $insurer_data['jur_name'] : '') ?>' placeholder="Наименования юр. лица (полностью)" required>
						    	</div>
						  	</div>
						  	<hr class="hr_line">
						  	<div class="form-group">
						    	<label class="col-sm-4 control-label"><small>Свидетельство о регистрации юридического лица</small></label>
						    	<div class="col-sm-8" style="padding-top:2%">
						      		<input type="text" class="form-control input-sm" name="jur_series" value='<?php echo ($contract_data["insurer_type"] == '2' ? $insurer_data['jur_series'] : '') ?>' id="jur_series" placeholder="Серия" required>
						      		<input type="text" class="form-control input-sm" name="jur_number" value='<?php echo ($contract_data["insurer_type"] == '2' ? $insurer_data['jur_number'] : '') ?>' id="jur_number" placeholder="Номер" required>
						    	</div>
						  	</div>
						  	<hr class="hr_line">
						  	<div class="form-group">
						    	<label for="jur_inn" class="col-sm-4 control-label"><small>ИНН юридического лица</small></label>
						    	<div class="col-sm-8" style="padding-top:2%">
						      		<input type="text" class="form-control input-sm" name="jur_inn" value='<?php echo ($contract_data["insurer_type"] == '2' ? $insurer_data['jur_inn'] : '') ?>' id="jur_inn" placeholder="Номер" required>
						    	</div>
						  	</div>				  	
				  					  					  					  	
					
					</div>	  	

				  	<hr class="hr_line">
					<div class="form-group">
				    	<label class="col-sm-4 control-label"><small>Место жительства (регистрации)</small></label>
				    	<div class="col-sm-8" style="padding-top:2%">
							<select class="form-control input-sm" name="subject" id="subject" required>
					  			<option value="" disabled>Субъект РФ</option>
						  		<?php
						  		$query=mysql_query("SELECT * FROM `kt_subject` ORDER BY `name`");
						  		while($row = mysql_fetch_assoc($query)){
									echo '<option value="'.$row["id_fias"].'" '.($insurer_data['subject'] == $row["id_fias"] ? ' selected' : '').'>'.$row["name"].'</option>';
								}
								?>    
							</select>
							<div id="message_0"></div>
							<div id="message_4" style="display:none">								
						      		<input type="text" class="form-control input-sm" name="house" value='<?php echo $insurer_data['house'] ?>' id="house" placeholder="Номер дома" required>
						      		<input type="text" class="form-control input-sm" name="housing" value='<?php echo $insurer_data['housing'] ?>' id="housing" placeholder="Корпус">
						      		<input type="text" class="form-control input-sm" name="apartment" value='<?php echo $insurer_data['apartment'] ?>' id="apartment" placeholder="Номер квартиры">						    						    	
							</div>			      		
				    	</div>
				  	</div>				  				  	
				  	<hr class="hr_line"><br>
				  	<div class="form-group">
				    	<label for="phone" class="col-sm-4 control-label"><small>Телефон</small></label>
				    	<div class="col-sm-8">
				      		<input type="text" class="form-control input-sm" name="phone" value='<?php echo $insurer_data['phone'] ?>' id="phone" required>
				    	</div>
				  	</div>					  	
				  	<hr class="hr_line">

						<div class="form-group">
					    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Страхователь является собственником</small></label>
						    <div class="col-sm-7">															
								<div class="radio-inline">
									  	<label><input type="radio" name="insisown" class="insisown" value="1" <?php echo ($contract_data['insurer_id'] == $contract_data['owner_id'] ? ' checked' : '') ?>><small>Да</small></label>
								</div>
								<div class="radio-inline">
									  	<label><input type="radio" name="insisown" class="insisown" value="2" <?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] ? ' checked' : '') ?>><small>Нет</small></label>
								</div>
						    </div>
					  	</div>
				  	

				  	<div id="owner_data" <?php echo ($contract_data['insurer_id'] == $contract_data['owner_id'] ? ' style="display:none"' : '') ?>>

				  	<hr>
						<h4><b>Данные собственника</b></h4>
						 <hr class="hr_red">	
						
						<?php 
						if($_SESSION["step_1"]["type_ins"] != 'jur'){
						?>										  	
						  	<div class="form-group">
						    	<label for="owner_second_name" class="col-sm-4 control-label"><small>Фамилия</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="owner_second_name" id="owner_second_name" value='<?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] && $contract_data["owner_type"] == '1' ? $owner_data['second_name'] : '') ?>' placeholder="Фамилия" required>
						    	</div>
						  	</div>

						  	<div class="form-group">
						    	<label for="owner_first_name" class="col-sm-4 control-label"><small>Имя</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="owner_first_name" id="owner_first_name" value='<?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] && $contract_data["owner_type"] == '1' ? $owner_data['first_name'] : '') ?>' placeholder="Имя" required>
						    	</div>
						  	</div>

						  	<div class="form-group">
						    	<label for="owner_third_name" class="col-sm-4 control-label"><small>Отчество</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="owner_third_name" id="owner_third_name" value='<?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] && $contract_data["owner_type"] == '1' ? $owner_data['third_name'] : '') ?>' placeholder="Отчество" required>
						    	</div>
						  	</div>

						  	<div class="form-group">
						    	<label for="owner_date_birth" class="col-sm-4 control-label"><small>Дата рождения</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm date_birth" name="owner_date_birth" value='<?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] && $contract_data["owner_type"] == '1' ? $owner_data['date_birth'] : '') ?>' id="owner_date_birth" placeholder="Дата рождения" required>
						    	</div>
						  	</div>
						  	<hr class="hr_line">
						  	<div class="form-group" style="padding-top:2%">
						    	<label for="owner_doc" class="col-sm-4 control-label"><small>Документ, удостоверяющий личность</small></label>
						    	<div class="col-sm-8" id="owner_doc">
						      	<select class="form-control input-sm" name="owner_doc_name" id="owner_doc_name" required>
						  		<?php
						  		$query=mysql_query("SELECT * FROM `document` WHERE `active` = 1 ORDER BY `name`");
						  		while($row = mysql_fetch_assoc($query)){
									echo '<option value="'.$row["id"].'" '.($contract_data['insurer_id'] != $contract_data['owner_id'] && $row["id"] == $owner_data['doc_name'] ? 'selected' : '').' >'.$row["name"].'</option>';
								}
								?>    
								</select>
						      		<input type="text" class="form-control input-sm" name="owner_doc_series" value='<?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] && $contract_data["owner_type"] == '1' ? $owner_data['doc_series'] : '') ?>' id="owner_doc_series" placeholder="Серия" required>
						      		<input type="text" class="form-control input-sm" name="owner_doc_number" value='<?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] && $contract_data["owner_type"] == '1' ? $owner_data['doc_number'] : '') ?>' id="owner_doc_number" placeholder="Номер" required>
						    	</div>
						  	</div>
					  	<?php
					  	}
					  	?>

						<?php 
						if($_SESSION["step_1"]["type_ins"] == 'jur'){
						?>

						  	<div class="form-group">
						    	<label for="owner_jur_name" class="col-sm-4 control-label"><small>Наименования юр. лица (полностью)</small></label>
						    	<div class="col-sm-8" style="padding-top:2%">
						      		<input type="text" class="form-control input-sm" name="owner_jur_name" value='<?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] && $contract_data["owner_type"] == '2' ? $owner_data['jur_name'] : '') ?>' id="owner_jur_name" placeholder="Наименования юр. лица (полностью)" required>
						    	</div>
						  	</div>
						  	<hr class="hr_line">
						  	<div class="form-group">
						    	<label for="jur_name" class="col-sm-4 control-label"><small>Свидетельство о регистрации юридического лица</small></label>
						    	<div class="col-sm-8" style="padding-top:2%" id="jur_name">
						      		<input type="text" class="form-control input-sm" name="owner_jur_name" id="owner_jur_series" value='<?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] && $contract_data["owner_type"] == '2' ? $owner_data['jur_series'] : '') ?>' placeholder="Серия" required>
						      		<input type="text" class="form-control input-sm" name="owner_jur_name" id="owner_jur_number" value='<?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] && $contract_data["owner_type"] == '2' ? $owner_data['jur_number'] : '') ?>' placeholder="Номер" required>
						    	</div>
						  	</div>
						  	<hr class="hr_line">
						  	<div class="form-group">
						    	<label for="owner_jur_inn" class="col-sm-4 control-label"><small>ИНН юридического лица</small></label>
						    	<div class="col-sm-8" style="padding-top:2%">
						      		<input type="text" class="form-control input-sm" name="owner_jur_inn" value='<?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] && $contract_data["owner_type"] == '2' ? $owner_data['jur_inn'] : '') ?>' id="owner_jur_inn" placeholder="Номер" required>
						    	</div>
						  	</div>				  	
					  					  					  					  	
					  	<?php
					  	}
					  	?>

					  	<hr class="hr_line">
						<div class="form-group">
					    	<label class="col-sm-4 control-label"><small>Место жительства (регистрации)</small></label>
					    	<div class="col-sm-8" style="padding-top:2%">
								<select class="form-control input-sm" name="owner_subject" id="owner_subject" required>
						  			<option value="" disabled>Субъект РФ</option>
							  		<?php
							  		$query=mysql_query("SELECT * FROM `kt_subject` ORDER BY `name`");
							  		while($row = mysql_fetch_assoc($query)){
										echo '<option value="'.$row["id_fias"].'" '.($contract_data['insurer_id'] != $contract_data['owner_id'] && $row["id_fias"] == $owner_data['subject'] ? ' selected' : '').'>'.$row["name"].'</option>';
									}
									?>    
								</select>
								<div id="owner_message_0"></div>
								<div id="owner_message_4" style="display:none">								
							      		<input type="text" class="form-control input-sm" name="owner_house" value='<?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] ? $owner_data['house'] : '') ?>' id="owner_house" placeholder="Номер дома" required>
							      		<input type="text" class="form-control input-sm" name="owner_housing" value='<?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] ? $owner_data['housing'] : '') ?>' id="owner_housing" placeholder="Корпус">
							      		<input type="text" class="form-control input-sm" name="owner_apartment" value='<?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] ? $owner_data['apartment'] : '') ?>' id="owner_apartment" placeholder="Номер квартиры">						    						    	
								</div>			      		
					    	</div>
					  	</div>				  				  	
					  	<hr class="hr_line"><br>
					  	<div class="form-group">
					    	<label for="date_birth" class="col-sm-4 control-label"><small>Телефон</small></label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control input-sm" name="owner_phone" value='<?php echo ($contract_data['insurer_id'] != $contract_data['owner_id'] ? $owner_data['phone'] : '') ?>' id="owner_phone">
					    	</div>
					  	</div>					  					  	
				  	</div>
				  	<hr class="hr_line">
				  	

				  	<h4><b>Данные транспортного средства</b></h4>
				  	<hr class="hr_red">					  	

						<div class="form-group ">
					    	<label  class="col-sm-4 control-label" style="word-wrap:break-word;"><small>Марка ТС</small></label>
					    	<div class="col-sm-8">							
								<select class="form-control input-sm" name="mark" id="mark" required>
						  		<option value="" disabled>Выберите марку ТС</option>
						  		<?php
						  			$query = mysql_query("SELECT * FROM `mark` ORDER BY `name`");
						  			while ($row = mysql_fetch_assoc($query)) {
						  				echo '<option value='.$row["rsa_mark_id"].' '.($row['rsa_mark_id'] == $vehicle_data['mark'] ? ' selected' : '').'>'.$row["name"].'</option>';
						  			}
						  		?>
								</select>
								<div id="message_mark"></div>
					    	</div>
					  	</div>

						<div class="form-group">
					    	<label  class="col-sm-5 control-label" style="word-wrap:break-word;"><small>Соответствует данным в ПТС</small></label>
						    <div class="col-sm-7">															
								<div class="radio-inline">
									  	<label><input type="radio" name="auto_in_pts" class="auto_in_pts" value="1" <?php echo (!isset($step_2_data['auto_in_pts']) || $step_2_data['auto_in_pts'] == '1' ? ' checked' : ' ')?>><small>Да</small></label>
								</div>
								<div class="radio-inline">
									  	<label><input type="radio" name="auto_in_pts" class="auto_in_pts" value="2" <?php echo (isset($step_2_data['auto_in_pts']) && $step_2_data['auto_in_pts'] == '2' ? ' checked' : ' ')?>><small>Нет</small></label>
								</div>
						    </div>
					  	</div>

						<div <?php echo ((isset($step_2_data['auto_in_pts']) && $step_2_data['auto_in_pts'] == '1') || !isset($step_2_data['auto_in_pts']) ? 'style="display:none"' : '')?> id='block_pts'>
						  	<div class="form-group">
						    	<label for="mark_pts" class="col-sm-4 control-label"><small>Марка в ПТС</small></label>
						    	<div class="col-sm-2">
						      		<input type="text" class="form-control input-sm" name="mark_pts" value='<?php echo (isset($step_2_data['auto_in_pts']) && $step_2_data['auto_in_pts'] == '2' ? $vehicle_data['mark_pts'] : '')?>' id="mark_pts" required>
						    	</div>
						  	</div>
						  	<div class="form-group">
						    	<label for="model_pts" class="col-sm-4 control-label"><small>Модель в ПТС</small></label>
						    	<div class="col-sm-2">
						      		<input type="text" class="form-control input-sm" name="model_pts" value='<?php echo (isset($step_2_data['auto_in_pts']) && $step_2_data['auto_in_pts'] == '2' ? $vehicle_data['model_pts'] : '')?>' id="model_pts" required>
						    	</div>
						  	</div>
					  	</div>

						<div class="form-group ">
					    	<label  class="col-sm-4 control-label" style="word-wrap:break-word;"><small>Категория ТС</small></label>
					    	<div class="col-sm-8">							
								<select class="form-control input-sm" name="category" id="category" required>
						  		<?php
						  			$query = mysql_query("SELECT * FROM `category_code` WHERE `group` = '".$category_code[$_SESSION["step_1"]["category"]]."' ORDER BY `id`");
						  			while ($row = mysql_fetch_assoc($query)) {
						  				echo '<option value='.$row["id"].' '.($row['id'] == $vehicle_data['category'] ? ' selected' : '').'>'.$row["name"].'</option>';
						  			}
						  		?>
								</select>
					    	</div>
					  	</div>


					  	
					  	<div class="form-group">
					    	<label for="vin" class="col-sm-4 control-label"><small>Идентификационный номер ТС (VIN)</small></label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control input-sm empty_data_input" name="vin" value='<?php echo $vehicle_data['vin']?>' id="vin" maxlength="17" required>
					      		<input type="checkbox" class="empty_data" <?php echo ($vehicle_data['vin'] == 'Отсутствует' ? ' checked' : '')?>><label><small>Отсутствует</small></label>
					    	</div>				    	
					  	</div>						  	
					  	
					  	<div class="form-group">
					    	<label for="power" class="col-sm-4 control-label"><small>Мощность двигателя ТС (л.с.)</small></label>
					    	<div class="col-sm-2">
					      		<input type="text" class="form-control input-sm" name="power" value='<?php echo $vehicle_data['power']?>' id="power" required>
					    	</div>
					  	</div>

					  	
					  	<div class="form-group">
					    	<label for="chassis" class="col-sm-4 control-label"><small>Шасси (рама) №</small></label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control input-sm empty_data_input" name="chassis" value='<?php echo $vehicle_data['chassis']?>' id="chassis" maxlength="17">
					      		<input type="checkbox" class="empty_data" <?php echo ($vehicle_data['chassis'] == 'Отсутствует' ? ' checked' : '')?>><label><small>Отсутствует</small></label>
					    	</div>				    	
					  	</div>

					  	
					  	<div class="form-group">
					    	<label for="trailer" class="col-sm-4 control-label"><small>Кузов (прицеп) №</small></label>
					    	<div class="col-sm-4">
					      		<input type="text" class="form-control input-sm empty_data_input" name="trailer" value='<?php echo $vehicle_data['trailer']?>' id="trailer" maxlength="17">
					      		<input type="checkbox" class="empty_data" <?php echo ($vehicle_data['trailer'] == 'Отсутствует' ? ' checked' : '')?>><label><small>Отсутствует</small></label>					      						      		
					    	</div>					    	
					  	</div>
					  	
					  	<?php
					  	if($_SESSION["step_1"]["category"] == 4 || $_SESSION["step_1"]["category"] == 5){
					  	?>
					  	
					  	<div class="form-group">
					    	<label for="max_weight" class="col-sm-4 control-label"><small>Разрешенная максимальная масса</small></label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control input-sm" name="max_weight" id="max_weight" value='<?php echo (isset($vehicle_data['max_weight']) ? $vehicle_data['max_weight'] : '') ?>' placeholder="кг" required>
					    	</div>
					  	</div>
					  	
					  	<?php
					  	}
					  	?>


					  	<?php
					  	if($_SESSION["step_1"]["category"] == 6 || $_SESSION["step_1"]["category"] == 7 || $_SESSION["step_1"]["category"] == 8 || $_SESSION["step_1"]["category"] == 9 || $_SESSION["step_1"]["category"] == 10){
					  	?>
					  	
					  	<div class="form-group">
					    	<label for="number_seats" class="col-sm-4 control-label"><small>Количество пассажирских мест</small></label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control input-sm" name="number_seats" value='<?php echo (isset($vehicle_data['number_seats']) ? $vehicle_data['number_seats'] : '') ?>' id="number_seats" required>
					    	</div>
					  	</div>
					  	
					  	<?php
					  	}
					  	?>					  	

					  	<div class="form-group">
					    	<label for="auto_doc_type" class="col-sm-4 control-label"><small>Документ о регистрации ТС</small></label>
					    	<div class="col-sm-8">
					      		<select class="form-control input-sm" id="auto_doc_type" name="auto_doc_type">
						  		<?php
						  			$query = mysql_query("SELECT * FROM `document_auto` WHERE `active` = '1' ORDER BY `id`");
						  			while ($row = mysql_fetch_assoc($query)) {
						  				echo '<option value='.$row["id"].' '.($row['id'] == $vehicle_data['auto_doc_type'] ? ' selected' : '').'>'.$row["name"].'</option>';
						  			}
						  		?>
					      		</select>
					    	</div>
					  	</div>

					  	<div class="form-group">
					    	<label for="auto_doc_series" class="col-sm-4 control-label"><small>Серия</small></label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control input-sm" name="auto_doc_series" value='<?php echo $vehicle_data['auto_doc_series'] ?>' id="auto_doc_series" required>
					    	</div>
					  	</div>


					  	<div class="form-group">
					    	<label for="auto_doc_number" class="col-sm-4 control-label"><small>Номер</small></label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control input-sm" name="auto_doc_number" value='<?php echo $vehicle_data['auto_doc_number'] ?>' id="auto_doc_number" required>
					    	</div>
					  	</div>					  	


					  	<div class="form-group">
					    	<label for="auto_doc_date" class="col-sm-4 control-label"><small>Дата выдачи</small></label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control input-sm" name="auto_doc_date" value='<?php echo $vehicle_data['auto_doc_date'] ?>' id="auto_doc_date" required>
					    	</div>
					  	</div>
					  	<?php
					  	if((((date("Y")-$_SESSION["step_1"]["year_manufacture"])<3) && ($_SESSION["step_1"]["category"] == 1 || $_SESSION["step_1"]["category"] == 2)) || ($_SESSION["step_1"]["category"] == 3 || $_SESSION["step_1"]["category"] == 6 || $_SESSION["step_1"]["category"] == 7 || $_SESSION["step_1"]["category"] == 8 || $_SESSION["step_1"]["category"] == 9 || $_SESSION["step_1"]["category"] == 10) || ((date("Y")-$_SESSION["step_1"]["year_manufacture"]) == 0 && ($_SESSION["step_1"]["category"] == 4 || $_SESSION["step_1"]["category"] == 5)) || $_SESSION["step_1"]["place_reg"] == 3){
					  		//....
					  	} else { 
					  	?>
						<hr>
					  	<legend style="font-size:14px"><b><em>Диагностическая карта, свидетельствующая о прохождении ТО:</em></b></legend>
					  	<div class="form-group">
					    	<label for="auto_diag_card_number" class="col-sm-4 control-label"><small>Номер:</small></label>
						      	<div class="col-sm-8">
						      	<input type="text" class="form-control input-sm" name="auto_diag_card_number" id="auto_diag_card_number" value='<?php echo (isset($step_2_data['auto_diag_card_number']) ? $step_2_data['auto_diag_card_number'] : '')?>' placeholder="Номер" maxlength="21" required>
						      	</div>
					  	</div>
					  	<div class="form-group">
					    	<label for="auto_diag_card_next_date" class="col-sm-4 control-label"><small>Срок действия, до:</small></label>
						      	<div class="col-sm-8">
						      	<input type="text" class="form-control input-sm" name="auto_diag_card_next_date" value='<?php echo (isset($step_2_data['auto_diag_card_next_date']) ? $step_2_data['auto_diag_card_next_date'] : '')?>' id="auto_diag_card_next_date" placeholder="Дата очередного технического осмотра" required>
					      		<span class="help-block"><a href="/dkbm/index.html" target="_blank"><small>Запрос ТО в АИС РСА</small></a></span>
						      	</div>
					  	</div>
					  	<hr>				  	
					  	<?php
					  	}
					  	?>

					  	<div class="form-group">
					    	<label for="auto_reg_number" class="col-sm-4 control-label"><small>Государственный регистрационный знак</small></label>
					    	<div class="col-sm-8">
					      		<input type="text" class="form-control input-sm empty_data_input" name="auto_reg_number" value='<?php echo $vehicle_data['auto_reg_number'] ?>' id="auto_reg_number" maxlength="11" required>
					      		<input type="checkbox" class="empty_data" <?php echo ($vehicle_data['auto_reg_number'] == 'Отсутствует' ? ' checked' : '')?>><label><small>Отсутствует</small></label>					      		
					    	</div>					    	
					  	</div>


					  	<div class="form-group">
					    	<label for="purpose_use" class="col-sm-4 control-label"><small>Цель использования ТС</small></label>
					    	<div class="col-sm-8">
					      		<select class="form-control input-sm" id="purpose_use" name="purpose_use">
					      		<option value="1" <?php echo ($vehicle_data['purpose_use'] == '1' ? ' selected' : '') ?>>личная</option>
					      		<option value="2" <?php echo ($vehicle_data['purpose_use'] == '2' ? ' selected' : '') ?>>учебная езда</option>
					      		<option value="3" <?php echo ($vehicle_data['purpose_use'] == '3' ? ' selected' : '') ?>>такси</option>
					      		<option value="4" <?php echo ($vehicle_data['purpose_use'] == '4' ? ' selected' : '') ?>>перевозка опасных и легко воспламеняющихся грузов</option>
					      		<option value="5" <?php echo ($vehicle_data['purpose_use'] == '5' ? ' selected' : '') ?>>прокат/краткосрочная аренда</option>
					      		<option value="6" <?php echo ($vehicle_data['purpose_use'] == '6' ? ' selected' : '') ?>>регулярные пассажирские перевозки/ перевозки пассажиров по заказам</option>
					      		<option value="7" <?php echo ($vehicle_data['purpose_use'] == '7' ? ' selected' : '') ?>>дорожные и специальные транспортные средства</option>
					      		<option value="8" <?php echo ($vehicle_data['purpose_use'] == '8' ? ' selected' : '') ?>>экстренные и коммунальные службы</option>
					      		<option value="9" <?php echo ($vehicle_data['purpose_use'] == '9' ? ' selected' : '') ?>>прочее</option>
					      		</select>
					    	</div>
					  	</div>
						
					  	<hr class="hr_line">					  	
						<div class="form-group">
					    	<label for="osago_old" class="col-sm-4 control-label"><small>Предыдущий договор обязательного страхования гражданской ответственности владельцев транспортных средств в отношении указанного транспортного средства:</small></label>
						    	<div id="osago_old">
							    	<div class="col-sm-8" style="padding-top:2%">
							      		<input type="text" class="form-control input-sm" name="osago_old_series" value='<?php echo ($step_2_data['osago_old_series']) ?>' id="osago_old_series" placeholder="серия" maxlength="3">
							      		<input type="text" class="form-control input-sm" name="osago_old_number" value='<?php echo ($step_2_data['osago_old_number']) ?>' id="osago_old_number" placeholder="номер" maxlength="10">
							      		<input type="text" class="form-control input-sm" name="osago_old_name" value='<?php echo ($step_2_data['osago_old_name']) ?>' id="osago_old_name" placeholder="страховщик">
							    	</div>
							    </div>
					  	</div>					  	
					  	<hr>


				  	<?php
				  	if($_SESSION["step_1"]["drivers"] == 2){
				  	?>
				  	<h4><b>К управлению транспортным средством допущены:</b></h4>
				  	<hr class="hr_red">
				  	<?php	
				  		for($x=1;$x<6;$x++){
				  			if(isset($_SESSION["step_1"]["driver_$x"])){
				  				

				  	?>
	

					  	<div class="form-group">
					    	<label for="driver_<?php echo $x ?>" class="col-sm-4 control-label"><small>Данные водителя №<?php echo $x ?>:</small></label>
					    	<div class="col-sm-8" id="driver_<?php echo $x ?>">
					      		<input type="text" class="form-control input-sm" id="driver_<?php echo $x ?>_second_name" name="driver_<?php echo $x ?>_second_name" value="<?php echo $step_2_data["driver_".$x."_second_name"] ?>" placeholder="Фамилия" required>
					      		<input type="text" class="form-control input-sm" id="driver_<?php echo $x ?>_first_name" name="driver_<?php echo $x ?>_first_name" value="<?php echo $step_2_data["driver_".$x."_first_name"] ?>" placeholder="Имя" required>
					      		<input type="text" class="form-control input-sm" id="driver_<?php echo $x ?>_third_name" name="driver_<?php echo $x ?>_third_name" value="<?php echo $step_2_data["driver_".$x."_third_name"] ?>" placeholder="Отчество" required>
					      		<input type="text" class="form-control input-sm date_birth" id="driver_<?php echo $x ?>_date_birth" name="driver_<?php echo $x ?>_date_birth" placeholder="Дата рождения" value="<?php echo $step_2_data["driver_".$x."_date_birth"] ?>" required>
					      		<input type="text" class="form-control input-sm" name="driver_<?php echo $x ?>_series" placeholder="Серия водительского удостоврения" value="<?php echo $step_2_data["driver_".$x."_series"] ?>" required>
					      		<input type="text" class="form-control input-sm" name="driver_<?php echo $x ?>_number" placeholder="Номер водительского удостовренеия" value="<?php echo $step_2_data["driver_".$x."_number"] ?>" required>
					      		<input type="text" class="form-control input-sm" name="driver_<?php echo $x ?>_experience" value="<?php echo $step_2_data["driver_".$x."_experience"] ?>" placeholder="Стаж управления ТС соответствующей категории, полных лет" required>
					    	</div>
					  	</div>
					  	<hr>				  			

				  	<?php	
					  		}
				  		}
				  	}
				  	?>	
						<hr class="hr_red">
						  	<div class="form-group">
						    	<label class="col-sm-4 control-label"><small>Срок действия договора страхования</small></label>
						    	<div class="col-sm-4" style="padding-top:2%">
						      		<input type="text" class="form-control input-sm period_data" name="start_date" id="start_date" value="<?php echo $contract_data['start_date'] ?>" placeholder="Дата начала действия договора" required>
						      		<input type="text" class="form-control input-sm" name="start_time" id="start_time" value="00:00" placeholder="Время начала действия договора" disabled required>	
						    	</div>	
						    	<div class="col-sm-4" style="padding-top:2%">
						      		<input type="text" class="form-control input-sm period_data" name="end_date" id="end_date" value="<?php 
						      					if($_SESSION["step_1"]["place_reg"] == 3){
									      			echo date('d.m.Y', strtotime("$contract_data[start_date] + 20 days"));
									      		} elseif ($_SESSION["step_1"]["place_reg"] == 1) {
									      			if($_SESSION["step_1"]["period_use"] == 1){
									      				echo date('d.m.Y', strtotime("$contract_data[start_date] + 3 months - 1 day"));
									      			}
									      			if($_SESSION["step_1"]["period_use"] == 2){
									      				echo date('d.m.Y', strtotime(" $contract_data[start_date] + 4 months - 1 day"));
									      			}
									      			if($_SESSION["step_1"]["period_use"] == 3){
									      				echo date('d.m.Y', strtotime("$contract_data[start_date] + 5 months - 1 day"));
									      			}
									      			if($_SESSION["step_1"]["period_use"] == 4){
									      				echo date('d.m.Y', strtotime("$contract_data[start_date] + 6 months - 1 day"));
									      			}
									      			if($_SESSION["step_1"]["period_use"] == 5){
									      				echo date('d.m.Y', strtotime("$contract_data[start_date] + 7 months - 1 day"));
									      			}
									      			if($_SESSION["step_1"]["period_use"] == 6){
									      				echo date('d.m.Y', strtotime("$contract_data[start_date] + 6 months - 1 day"));
									      			}
									      			if($_SESSION["step_1"]["period_use"] == 7){
									      				echo date('d.m.Y', strtotime("$contract_data[start_date] + 9 months - 1 day"));
									      			}
									      			if($_SESSION["step_1"]["period_use"] == 8){
									      				echo date('d.m.Y', strtotime("$contract_data[start_date] + 1 years - 1 day"));
									      			}						      									      									      									      									      									      									      			
									      		} elseif ($_SESSION["step_1"]["place_reg"] == 2){
									      			if($_SESSION["step_1"]["term_insurance"] == 1){
									      				echo date('d.m.Y', strtotime("$contract_data[start_date] + 15 days - 1 day"));
									      			}
									      			if($_SESSION["step_1"]["term_insurance"] == 2){
									      				echo date('d.m.Y', strtotime("$contract_data[start_date] + 1 month - 1 day"));
									      			}
									      			if($_SESSION["step_1"]["term_insurance"] == 3){
									      				echo date('d.m.Y', strtotime("$contract_data[start_date] + 2 months - 1 day"));
									      			}
									      			if($_SESSION["step_1"]["term_insurance"] == 4){		
									      				echo date('d.m.Y', strtotime("$contract_data[start_date] + 3 months - 1 day"));										      				
									      			}
									      			if($_SESSION["step_1"]["term_insurance"] == 5){
									      				if($_SESSION["step_1"]["period_use"] == 1){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 3 months - 1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 2){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 4 months - 1 day"));
									      				}									      				
									      			}
									      			if($_SESSION["step_1"]["term_insurance"] == 6){
									      				if($_SESSION["step_1"]["period_use"] == 1){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 3 months - 1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 2){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 4 months - 1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 3){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date]+ 5 months - 1 day"));
									      				}									      													      				
									      			}
									      			if($_SESSION["step_1"]["term_insurance"] == 7){
									      				if($_SESSION["step_1"]["period_use"] == 1){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 3 months - 1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 2){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 4 months - 1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 3){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 5 months - 1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 4){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 6 months - 1 day"));
									      				}									      													      													      				
									      			}
									      			if($_SESSION["step_1"]["term_insurance"] == 8){
									      				if($_SESSION["step_1"]["period_use"] == 1){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 3 months - 1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 2){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 4 months - 1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 3){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 5 months - 1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 4){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 6 months - 1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 5){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 7 months -1 day"));
									      				}									      													      													      													      				
									      			}
									      			if($_SESSION["step_1"]["term_insurance"] == 9){
									      				if($_SESSION["step_1"]["period_use"] == 1){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 3 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 2){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 4 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 3){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 5 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 4){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 6 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 5){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 7 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 6){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 8 months -1 day"));
									      				}									      													      													      													      													      				
									      			}
									      			if($_SESSION["step_1"]["term_insurance"] == 10){
									      				if($_SESSION["step_1"]["period_use"] == 1){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 3 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 2){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 4 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 3){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 5 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 4){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 6 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 5){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 7 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 6){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 8 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 7){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 9 months -1 day"));
									      				}									      													      													      													      													      													      				
									      			}
									      			if($_SESSION["step_1"]["term_insurance"] == 11){
									      				if($_SESSION["step_1"]["period_use"] == 1){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 3 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 2){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 4 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 3){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 5 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 4){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 6 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 5){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 7 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 6){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 8 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 7){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 9 months -1 day"));
									      				}
									      				if($_SESSION["step_1"]["period_use"] == 8){
									      					echo date('d.m.Y', strtotime("$contract_data[start_date] + 12 months -1 day"));
									      				}									      													      													      													      													      													      													      				
									      			}									      												      												      												      												      												      												      												      												      												      			
									      		}						      		      		
						      		?>" placeholder="Дата окончания действия договора" required readonly>
						      		<input type="time" class="form-control input-sm" name="end_time" id="end_time" value="23:59" placeholder="Время окончания действия договора" required readonly>				      		
						    	</div>							    					    	
						  	</div>
							<div class="form-group">
					    	<label for="auto_used" class="col-sm-4 control-label"><small>Транспортное средство будет использоваться:</small></label>
						    	<div id="auto_used">
							    	<div class="col-sm-8" style="padding-top:2%">
							    		<div class="col-sm-6">
							      			<input type="text" class="form-control input-sm auto_used" name="auto_used_start_1" value='<?php echo (isset($step_2_data['auto_used_start_1']) ? $step_2_data['auto_used_start_1'] : '') ?>' id="auto_used_start_1" placeholder="c" required>
							      		</div>
							      		<div class="col-sm-6">
							      			<input type="text" class="form-control input-sm auto_used" name="auto_used_end_1" value='<?php echo (isset($step_2_data['auto_used_end_1']) ? $step_2_data['auto_used_end_1'] : '') ?>' id="auto_used_end_1" placeholder="по" required>
							      		</div>
							      		<div class="col-sm-6">
							      			<input type="text" class="form-control input-sm auto_used" name="auto_used_start_2" value='<?php echo (isset($step_2_data['auto_used_start_2']) ? $step_2_data['auto_used_start_2'] : '') ?>' id="auto_used_start_2" placeholder="c">
							      		</div>
							      		<div class="col-sm-6">
							      			<input type="text" class="form-control input-sm auto_used" name="auto_used_end_2" value='<?php echo (isset($step_2_data['auto_used_end_2']) ? $step_2_data['auto_used_end_2'] : '') ?>' id="auto_used_end_2" placeholder="по">
							      		</div>
							      		<div class="col-sm-6">
							      			<input type="text" class="form-control input-sm auto_used" name="auto_used_start_3" value='<?php echo (isset($step_2_data['auto_used_end_3']) ? $step_2_data['auto_used_end_3'] : '') ?>' id="auto_used_start_3" placeholder="c">
							      		</div>
							      		<div class="col-sm-6">
							      			<input type="text" class="form-control input-sm auto_used" name="auto_used_end_3" value='<?php echo (isset($step_2_data['auto_used_end_3']) ? $step_2_data['auto_used_end_3'] : '') ?>' id="auto_used_end_3" placeholder="по">
							      		</div>
							    	</div>
<!-- 							    	<div class="col-sm-4" style="padding-top:2%">
							      		<input type="text" class="form-control input-sm auto_used" name="auto_used_end_1" id="auto_used_end_1" placeholder="по" required>
							      		<hr class="hr_line">
							      		<input type="text" class="form-control input-sm auto_used" name="auto_used_end_2" id="auto_used_end_2" placeholder="по">
							      		<hr class="hr_line">
							      		<input type="text" class="form-control input-sm auto_used" name="auto_used_end_3" id="auto_used_end_3" placeholder="по">
							    	</div> -->
							    </div>
					  	</div>						  						  					  					  						  						  	
					  	<hr class="hr_red">
						  	<div class="form-group">
						    	<label for="bso_number" class="col-sm-4 control-label"><small>Номер выдаваемого полиса</small></label>
						    	<div class="col-sm-8">
									<select class="form-control input-sm" name="bso_number">
							  			<option value="">Выберите номер бланка</option>
								  		<?php
								  		$query=mysql_query("SELECT * FROM `bso` WHERE ".(isset($_SESSION["access"][8]) ? "`user_id` = $_SESSION[user_id]" : "`unit_id` = $_SESSION[unit_id]")." ORDER BY `number`");
								  		while($row = mysql_fetch_assoc($query)){
											echo '<option value="'.$row["number"].'" >'.$row["number"].'</option>';
										}
										?>    
									</select>	      		
						    	</div>
						  	</div>

						  	<hr class="hr_line">
							<div class="form-group" style="padding-top:2%">
						    	<label for="a7_number" class="col-sm-4 control-label"><small>Номер выдаваемого бланка А7</small></label>
						    	<div class="col-sm-8">
									<select class="form-control input-sm" name="a7_number">
							  			<option value="no">Бланк А7 не используется</option>
								  		<?php
								  		$query=mysql_query("SELECT * FROM `a7` WHERE ".(isset($_SESSION["access"][8]) ? "`user_id` = $_SESSION[user_id]" : "`unit_id` = $_SESSION[unit_id]")." ORDER BY `number`");
								  		while($row = mysql_fetch_assoc($query)){
											echo '<option value="'.$row["number"].'" >'.$row["number"].'</option>';
										}
										?>    
									</select>	      		
						    	</div>
						  	</div>
					  	<hr class="hr_red">

						  	<div class="form-group">
						    	<label for="special_notes" class="col-sm-4 control-label"><small>Особые отметки</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="special_notes" value='<?php echo $step_2_data['special_notes'] ?>' id="special_notes">
						    	</div>
						  	</div>

						<hr class="hr_red">
						  	<div class="form-group">
						    	<label for="ais_request_identifier" class="col-sm-4 control-label"><small>Идентификатор запроса КБМ/ТО</small></label>
						    	<div class="col-sm-8">
						      		<input type="text" class="form-control input-sm" name="ais_request_identifier" value='<?php echo $contract_data['rsa_number'] ?>' id="ais_request_identifier">
						    	</div>
						  	</div>

						<hr class="hr_red">
					  	<div class="form-group">
					      	<div class="col-sm-6">
					      		<button type="submit" name="action" value="add" class="btn btn-primary btn-block">Оформить полис</button>
					      	</div>
					      	<div class="col-sm-6">	
					      		<button type="submit" name="action" value="project" class="btn btn-danger btn-block">Сохранить как проект</button>
					      	</div>
					  	</div>
					</form>
	  			</div>
			</div>
		</div>
		<div class="col-md-4 col-md-offset-4">
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
var top_show = 150; // В каком положении полосы прокрутки начинать показ кнопки "Наверх"
var delay = 1000; // Задержка прокрутки
$(document).ready(function(){

/////////////////////////////////////////////////////////
$(window).scroll(function () { // При прокрутке попадаем в эту функцию
      /* В зависимости от положения полосы прокрукти и значения top_show, скрываем или открываем кнопку "Наверх" */
      if ($(this).scrollTop() > top_show) $('#top').fadeIn();
      else $('#top').fadeOut();
    });
    $('#top').click(function () { // При клике по кнопке "Наверх" попадаем в эту функцию
      /* Плавная прокрутка наверх */
      $('body, html').animate({
        scrollTop: 0
      }, delay);
    });

/////////////////////////////////////////////////////////
	$('.date_birth').mask('00.00.0000');
	$('#doc_date').mask('00.00.0000');
	$('#phone').mask('(000)000-00-00',{placeholder: "(___)___-__-__"} );
	$('#owner_doc_date').mask('00.00.0000');
	$('#owner_phone').mask('(000)000-00-00',{placeholder: "(___)___-__-__"} );
	$('#year_manufacture').mask('0000');
	$('#power').mask('0000');
	$('.auto_used').mask('00.00.0000');	
	$('#auto_doc_date').mask('00.00.0000');	
	$('#auto_diag_card_next_date').mask('00.00.0000');	
	$('#start_date').mask('00.00.0000');
	$('#start_time').mask('00:00');
//Календарик	
	$( ".date_birth" ).datepicker({
	  dateFormat: "dd.mm.yy",
	  maxDate: "-18Y",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c-70:c"
	});
	$( "#doc_date" ).datepicker({
	  dateFormat: "dd.mm.yy",
	  maxDate: "0d",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c-70:c"
	});
	$( "#owner_doc_date" ).datepicker({
	  dateFormat: "dd.mm.yy",
	  maxDate: "0d",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c-70:c"
	});
	$( "#auto_doc_date" ).datepicker({
	  dateFormat: "dd.mm.yy",
	  maxDate: "0d",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c-70:c"
	});
	$( ".auto_used" ).datepicker({
	  dateFormat: "dd.mm.yy",
	  minDate: "0d",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c:c+10"
	});
	$( "#auto_diag_card_next_date" ).datepicker({
	  dateFormat: "dd.mm.yy",
	  minDate: "0d",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c:c+10"
	});	
	$( "#start_date" ).datepicker({
	  dateFormat: "dd.mm.yy",
	  minDate: "0d",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c:c+10",
	  showOn: "focus"
	});
//////////////////////////////СТРАХОВАТЕЛЬ ДАННЫЕ РЕГИСТРАЦИИ////////////////////////////////////////////////		
//Отображение адреса при загрузке договора на редактирование для страхователя
		$('#message_0').html('');
		$.ajax({
		  type: "POST",
		  url: '/ajax/fias.php',
		  data: "subject="+<?php echo $insurer_data['subject'] ?>,
		  success: function(data) {
		  	$('#message_0').html(data);
		  	$('#message_4').hide();
		  	$('#aoid').val('<?php echo $insurer_data['aoid'] ?>');
		  	change_aoid();
		  	//change_street();  			  	
		  }
		});

function change_aoid(){
			var a = $('#aoid').val();
			var b = $('#subject').val();
			//$('#message_1').html(a);
			$('#message_1').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias.php',
				  data: {aoid: a, subject: b},
				  success: function(data) {
				  	$('#message_1').html(data);
				  	$('#city').val('<?php echo $insurer_data['city'] ?>');
				  	change_street(); 
				  }
				});
				return false;	
}
function change_street(){
			var a = $('#city').val();
			$('#message_2').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias.php',
				  data: {city: a},
				  success: function(data) {
				  	$('#message_2').html(data);
				  	$('#street').val('<?php echo $insurer_data['street'] ?>');
				  	$('#message_4').show();
				  }
				});
				return false;
}
////////////////////////////////////////////////////////////

	//отображение списка городов субъекта для страхователя
		$(document).on("change", "#subject", function(){
			var a = $(this).val();
			$('#message_0').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias.php',
				  data: "subject="+a,
				  success: function(data) {
				  	$('#message_0').html(data);
				  	$('#message_4').hide();
				  	
				  }
				});
				return false;
		});

	//отображение списка населённых пунктов для страхователя
		$(document).on("change", "#aoid", function(){
			var a = $(this).val();
			var b = $('#subject').val();
			//$('#message_1').html(a);
			$('#message_1').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias.php',
				  data: {aoid: a, subject: b},
				  success: function(data) {
				  	$('#message_1').html(data);
				  	
				  }
				});
				return false;
		});

	//отображение списка улиц для населённых пунктов для страхователя
		$(document).on("change", "#city", function(){
			var a = $(this).val();
			$('#message_2').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias.php',
				  data: {city: a},
				  success: function(data) {
				  	$('#message_2').html(data);
				  	
				  }
				});
				return false;
		});

	//отображение списка домов для улицы страхователя
		$(document).on("change", "#street", function(){
			$('#message_4').show();
				return false;
		});	
///////////////////////////////////////////////////////////////////////////////////////////////////////

///////////////////////////СОБСТВЕННИК ДАННЫЕ РЕГИСТРАЦИИ//////////////////////////////////////////////		
//Отображение адреса при загрузке договора на редактирование для страхователя
		$('#owner_message_0').html('');
		$.ajax({
		  type: "POST",
		  url: '/ajax/fias_owner.php',
		  data: "owner_subject="+<?php echo $owner_data['subject'] ?>,
		  success: function(data) {
		  	$('#owner_message_0').html(data);
		  	$('#owner_message_4').hide();
		  	$('#owner_aoid').val('<?php echo $owner_data['aoid'] ?>');
		  	owner_change_aoid();
		  	//change_street();  			  	
		  }
		});

function owner_change_aoid(){
			var a = $('#owner_aoid').val();
			var b = $('#owner_subject').val();
			//$('#message_1').html(a);
			$('#owner_message_1').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias_owner.php',
				  data: {owner_aoid: a, owner_subject: b},
				  success: function(data) {
				  	$('#owner_message_1').html(data);
				  	$('#owner_city').val('<?php echo $owner_data['city'] ?>');
				  	owner_change_street(); 
				  }
				});
				return false;	
}
function owner_change_street(){
			var a = $('#owner_city').val();
			$('#owner_message_2').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias_owner.php',
				  data: {owner_city: a},
				  success: function(data) {
				  	$('#owner_message_2').html(data);
				  	$('#owner_street').val('<?php echo $owner_data['street'] ?>');
				  	$('#owner_message_4').show();
				  }
				});
				return false;
}
///////////////////////////////////////////////////////////////////////////////////////////////
	//отображение списка городов субъекта для собственника
		$(document).on("change", "#owner_subject", function(){
			var a = $(this).val();
			$('#owner_message_0').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias_owner.php',
				  data: "owner_subject="+a,
				  success: function(data) {
				  	$('#owner_message_0').html(data);
				  	$('#owner_message_4').hide();
				  	
				  }
				});
				return false;
		});
	//отображение списка населённых пунктов для собственника
		$(document).on("change", "#owner_aoid", function(){
			var a = $(this).val();
			var b = $('#owner_subject').val();
			$('#owner_message_1').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias_owner.php',
				  data: {owner_aoid: a, owner_subject: b},
				  success: function(data) {
				  	$('#owner_message_1').html(data);
				  	
				  }
				});
				return false;
		});

	//отображение списка улиц для населённых пунктов для собственника
		$(document).on("change", "#owner_city", function(){
			var a = $(this).val();
			$('#owner_message_2').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/fias_owner.php',
				  data: {owner_city: a},
				  success: function(data) {
				  	$('#owner_message_2').html(data);
				  	
				  }
				});
				return false;
		});

	//отображение списка домов для улицы для собственника
		$(document).on("change", "#owner_street", function(){
			$('#owner_message_4').show();
				return false;
		});	
///////////////////////////////////////////////////////////////////////////////////////////////





//Отображение выбора вида собственника
	$(document).on("change", ".insisown", function(){
		var a = $(this).val();
		if(a=='2'){
			// $('input:radio[name="owner"]').filter('[value="1"]').prop('checked',true);
			// $("#jur").slideUp();
			// $("#phiz").slideDown();
			//$("#owner").slideDown();
			$("#owner_data").slideDown();
		} else {
			$("#owner_data").slideUp();
			//$("#owner").slideUp();
		}
	});

//Отображение полей для ввода данных собственника в зависимости от выбранного типа собственника
	$(document).on("change", ".insurer", function(){
		var a = $(this).val();
		if(a == '1'){
			$("#jur").slideUp();
			$("#phiz").slideDown();
		} else {
			$("#phiz").slideUp();
			$("#jur").slideDown();			
		}
	});

//отображение списка моделей для определённой марки
		$(document).on("change", "#mark", function(){
			var a = $(this).val();
			$('#message_mark').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/model.php',
				  data: "mark="+a,
				  success: function(data) {
				  	$('#message_mark').html(data);
				  	
				  }
				});
				return false;
		});
//отображение списка моделей при загрузки страницы
			$('#message_mark').html('');
				$.ajax({
				  type: "POST",
				  url: '/ajax/model.php',
				  data: "mark="+<?php echo $vehicle_data['mark']?>,
				  success: function(data) {
				  	$('#message_mark').html(data);
				  	$('#model').val(<?php echo $vehicle_data['model']?>);
				  }
				});
//Заполняем поле словом "Отсутствует" в случае отметки соответтсвующего чекбокса
	$(document).on("change", ".empty_data", function(){
		if($(this).prop("checked")){
			$(this).prevAll('.input-sm:first').val('Отсутствует');
		}else{
			$(this).prevAll('.input-sm:first').val('');
		}
	});
//Отображаем поля если марка и модель не соответвуют написанному в ПТС
	$(document).on("change", ".auto_in_pts", function(){
		var a = $(this).val();
		if(a == '2'){
			$("#block_pts").slideDown();
		} else {
			$("#block_pts").slideUp();
		}
	});	
//Убираем галочку с чекбокса при фокусе на поле
	$(document).on("keydown", ".empty_data_input", function(){
		if($(this).nextAll('.empty_data:first').prop("checked")){
			$(this).nextAll('.empty_data:first').prop("checked", false)
		}
	});

//Заполняем дату окончания действия договора страхования
	$(document).on("change", "#start_date", function(){
		var a = $(this).val();//дата начала действия
		var b = "<?php echo date("d.m.Y") ?>";//дата сегодня
		var c = "<?php echo $_SESSION["step_1"]["term_insurance"]?>";
		var timeNow = new Date();
 		var arrStartDate = a.split('.');
 		var arrTodayDate = b.split('.');
		var startDate = new Date(arrStartDate[2], arrStartDate[1]-1, arrStartDate[0]);
 		var todayDate = new Date(arrTodayDate[2], arrTodayDate[1]-1, arrTodayDate[0]); 				
		//alert(startDate+'-'+todayDate);
		//если дата меньше текущей
		if(startDate < todayDate){
			$(this).val('');
			$("#start_time").prop("disabled", true)
			$("#start_time").val('00:00');
			return false;
		}
		//если дата больше либо равна текущей
		if(startDate > todayDate || a == b){
			//alert(startDate);
			var endDate = startDate;
			var arr = [0,15,1,2,3,4,5,6,7,8,9,12,20];
			var srok = arr[c];
			if(c == '1' || c == '12'){//прибавляем дни
				endDate.setDate(endDate.getDate()+srok);
			} else {//прибавляем месяцы
				endDate.setMonth(endDate.getMonth()+srok);
			}
			endDate.setDate(endDate.getDate()-1);
			var dd = endDate.getDate();
			if(dd<10){
				dd = '0'+dd;
			}		
			var mm = endDate.getMonth()+1;
			if(mm<10){
				mm = '0'+mm;
			}				
			var yyyy = endDate.getFullYear();
			var end_date = dd+'.'+mm+'.'+yyyy;
			$("#end_date").val(end_date);
		}
		//работа сполем времени начала действия договора если дата равна сегодняшней
		if(a == b){
			timeNow.setMinutes(timeNow.getMinutes() + 5);
			var hours = timeNow.getHours();
			if(hours < 10){
				hours = '0'+hours;
			}
			var minutes = timeNow.getMinutes();
			if(minutes < 10){
				minutes = '0'+minutes;
			}
			var hhmm = hours+':'+minutes;
			$("#start_time").prop("disabled", false)
			$("#start_time").val(hhmm);
		} else {
			$("#start_time").prop("disabled", true)
			$("#start_time").val('00:00');
		}		 
	});
//Отслеживаем изменение время старта
	$(document).on("change", "#start_time", function(){
		var timeNow = new Date();
		timeNow.setMinutes(timeNow.getMinutes() + 5);
		var hhNow = timeNow.getHours();
		if(hhNow < 10){
			hhNow = '0'+hhNow;
		}
		var mmNow = timeNow.getMinutes();
		if(mmNow < 10){
			mmNow = '0'+mmNow;
		}		
		var timeStart = $(this).val().split(':');
		var hhStart = timeStart[0];
		var mmStart = timeStart[1];
		if(hhNow < hhStart){
			return false;
		}else if(hhNow == hhStart){
			if(mmNow < mmStart){
				return false;
			} else {
				$("#start_time").val(hhNow+':'+mmNow);
			}
		} else {
			$("#start_time").val(hhNow+':'+mmNow);
		}
	});
//Копируем дату начала и окончания в период использования ТС
$(document).on("change", ".period_data", function(){
	$("#auto_used_start_1").val($("#start_date").val());
	//$("#auto_used_end_1").val($("#end_date").val());
	period_use_end(<?php echo ($_SESSION["step_1"]["place_reg"] == 3 ? '9' : $_SESSION["step_1"]["period_use"]) ?>);
});

$(document).on("change", "#auto_used_start_1", function(){
//Смена даты окончания периода использования при смене даты начала периода использования
	period_use_end(<?php echo ($_SESSION["step_1"]["place_reg"] == 3 ? '9' : $_SESSION["step_1"]["period_use"]) ?>);
});

<?php
if($_SESSION["step_1"]["drivers"] == 2){
?>
//Автозаполнение водителя
	$(document).on("change", ".phiz_name", function(){
		$("#driver_1_first_name").val($("#first_name").val());
		$("#driver_1_second_name").val($("#second_name").val());
		$("#driver_1_third_name").val($("#third_name").val());
		$("#driver_1_date_birth").val($("#date_birth").val());
	});
<?php
}
?>	
//проверка данных формы
	$('#main_form').validate({ // initialize the plugin
    	//Делаем ajax запрос на добавление данных полиса в базу в том случае если все необходимые поля заполнены.
    	submitHandler: function(form) {
    	edit_polis();
    	return false; 
    	}
    }); 
    // $('#main_form').submit(function( event ) {
    // 	add_polis();
    // 	return false;    	
    // });

///////////////////////////////////////////////////////////
});

</script>

