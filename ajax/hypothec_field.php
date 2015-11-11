<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}
if(!isset($_POST['num']) || !is_numeric($_POST['num']) || !isset($_POST['prog_type']) || !is_numeric($_POST['prog_type']) || !isset($_POST['id_bank']) || !is_numeric($_POST['id_bank'])){
	echo '<center><span class="text-danger">Произошла ошибка при передачи данных для формирования полей</span></center>';
	exit;
}
// echo "<pre>";
// print_r($_POST);
// echo "</pre>";
// exit();
require_once('../config.php');
require_once('../function.php');
//require_once('../template/header.html');
connect_to_base();
$num = $_POST['num'];
$id_bank = $_POST['id_bank'];
$visible = ($_POST['prog_type'] == 1 ? "style=\"display:none\"" : "");
$message = '';
for($x=1;$x<=$num;$x++){
	$message .= '  
		<legend><h6><b><em>Застрахованный №'.$x.':</em></b></h6></legend>
		<div class="form-group">
			<label  class="col-sm-5 control-label" ><small>Дата рождения:</small></label>
			<div class="col-sm-3">
				<input type="text" class="form-control input-sm date_birth" name="date_birth_'.$x.'" required>
			</div>					
		</div>
		<hr class="hr_line">
		<div class="form-group">
			<label  class="col-sm-5 control-label" ><small>Пол:</small></label>
			<div class="col-sm-7">
				<div class="radio">
					<label class="radio-inline"><input type="radio" name="sex_'.$x.'" value="male" checked><small>Мужской</small></label>
					<label class="radio-inline"><input type="radio" name="sex_'.$x.'" value="female"><small>Женский</small></label>
				</div>
			</div>
		</div>
		<hr class="hr_line">
		<div class="form-group">
			<label  class="col-sm-5 control-label" ><small>Увлечение спортом:</small></label>
			<div class="col-sm-7">
				<div class="radio">
					<label class="radio-inline"><input type="radio" class="sport" id="sport_option_yes_'.$x.'" name="sport_'.$x.'" value="yes"><small>Да</small></label>
					<label class="radio-inline"><input type="radio" class="sport" id="sport_option_no_'.$x.'" name="sport_'.$x.'" value="no" checked><small>Нет</small></label>
				</div>
			</div>
		</div>
		<hr class="hr_line">
		<div class="sport_option_yes_'.$x.' sport_option_no_'.$x.'" style="display:none">
			<div class="form-group">
				<label class="col-sm-5 control-label" ><small>Уровень увлечения спортом:</small></label>
				<div class="col-sm-7">
					<div class="radio">
						<label class="radio-inline"><input type="radio" name="sport_type_'.$x.'" value="1" checked><small>Любительский</small></label>
						<label class="radio-inline"><input type="radio" name="sport_type_'.$x.'" value="2"><small>Профессиональный</small></label>
					</div>
				</div>
			</div>
			<hr class="hr_line">
			<div class="form-group">
				<label  class="col-sm-5 control-label"><small>Вид спорта (категория):</small></label>

				<div class="col-sm-7">
					<div class="radio">';
					$query = mysql_query("SELECT * FROM `hypothec_sport_koef` WHERE `id_bank` = ".$id_bank." AND `active` = 1");
					//echo mysql_num_rows($query);
					$i = 1;
					while($row = mysql_fetch_assoc($query)){
						$message .= '<label class="radio-inline"><input type="radio" name="sport_category_'.$x.'" value="'.$row['id'].'" '.($i == 1 ? ' checked' : '').'><small>'.$i.'</small></label>';
						$i++;
					}

						$message .=	'<label class="radio-inline"><span class="glyphicon glyphicon-question-sign" data-toggle="modal" data-target="#modal_sport"></label>

					</div>
				</div>
			</div>
			<hr class="hr_line">			
		</div>
		<hr class="hr_line">
		<div class="form-group">
			<label  class="col-sm-5 control-label"><small>Сфера деятельности (категория):</small></label>
			<div class="col-sm-7">
				<div class="radio">';
				$query = mysql_query("SELECT * FROM `hypothec_work_koef` WHERE `id_bank` = ".$id_bank." AND `active` = 1");
				//echo mysql_num_rows($query);
				$i = 1;
				while($row = mysql_fetch_assoc($query)){
					$message .= '<label class="radio-inline"><input type="radio" name="work_category_'.$x.'" value="'.$row['id'].'" '.($i == 1 ? ' checked' : '').'><small>'.$i.'</small></label>';
					$i++;
				}

					$message .=	'<label class="radio-inline"><span class="glyphicon glyphicon-question-sign" data-toggle="modal" data-target="#modal_work"></label>

				</div>
			</div>
		</div>
		<hr class="hr_line">
		<div class="medical" '.$visible.'>
			<div class="form-group">
				<label class="col-sm-5 control-label" ><small>Предоставление медицинского обследования:</small></label>
				<div class="col-sm-7">
					<div class="radio">
						<label class="radio-inline"><input type="radio" name="health_'.$x.'" value="1" checked><small>Да</small></label>
						<label class="radio-inline"><input type="radio" name="health_'.$x.'" value="2"><small>Нет</small></label>
					</div>
				</div>
			</div>
			<hr class="hr_line">
			<div class="form-group">
				<label class="col-sm-5 control-label" ><small>Имеются заболевания:</small></label>
				<div class="col-sm-7">
					<div class="radio">
						<label class="radio-inline"><input type="radio" name="disease_'.$x.'" class="disease" id="disease_yes" value="yes"><small>Да</small></label>
						<label class="radio-inline"><input type="radio" name="disease_'.$x.'" class="disease" id="disease_no" value="no" checked><small>Нет</small></label>
					</div>
				</div>
			</div>
			<hr class="hr_line">
			<div class="form-group disease_yes disease_no" style="display:none">
				<label  class="col-sm-5 control-label"><small>Заболевания:</small></label>
				<div class="col-sm-7">
					<div class="checkbox">';
					$query = mysql_query("SELECT * FROM `hypothec_health_koef` WHERE `id_bank` = ".$id_bank." AND `active` = 1");
					//echo mysql_num_rows($query);
					while($row = mysql_fetch_assoc($query)){
						$message .= '<label class="checkbox"><input type="checkbox" name="disease_name_'.$x.'[]" value="'.$row['id'].'"><small>'.$row['name'].'</small></label>';
					}
						$message .=	'
					</div>
				</div>
			</div>
			<hr class="hr_line">									
		</div>
		<hr class="hr_red_2">	
	';
}
echo $message;

?>



