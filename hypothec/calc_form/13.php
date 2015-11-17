<?php
//форма для расчёт ипотеки сбербанка
$id_bank = 13;
session_start();
if(!isset($_SESSION['user_id'])){
	echo '<center><span class="text-danger"><b>Закончилось время сессии. Необходимо выйти и снова войти в сервис.</b></span></center>';	
	exit;
}
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
require_once('../../config.php');
require_once('../../function.php');
connect_to_base();
?>
<div class="form-group ">
	<label  class="col-sm-5 control-label" ><small>Вид недвижимого имущества:</small></label>
	<div class="col-sm-7">
		<div class="checkbox">
			<label><input type="checkbox" name="ins_property_type_1" class="ins_property_type" value="1"><small>Страхование недвижимого имущества, за исключением земельных участков</small></label>
		</div>
		<div class="checkbox">
			<label><input type="checkbox" name="ins_property_type_2" class="ins_property_type" value="2"><small>Страхование недвижимого имущества - земельные участки (не находящиеся вблизи водоёмов)</small></label>
		</div>		
	</div>
</div>
<hr class="hr_red_2">
<div id="ins_type_1" style="display:none">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title"><em>Страхование недвижимого имущества, за исключением земельных участков</em></div>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label  class="col-sm-5 control-label" ><small>Размер страховой суммы</small></label>
				<div class="col-sm-4">
				<input type="text" class="form-control input-sm only-number" name="ins_summa_house" required>
				</div>
			</div>
			<div class="form-group ">
				<label  class="col-sm-5 control-label" ><small>Характеристика недвижимого имущества:</small></label>
				<div class="col-sm-7">																			  		  
					<?php
						$query = mysql_query("SELECT * FROM `hypothec_house_tb` WHERE `active` = 1 AND `id_bank` = $id_bank");
						$i = 0;
						while ($row = mysql_fetch_assoc($query)) {
						$i++;	
				  		echo '<div class="radio">
						  <label><input type="radio" name="property_type" class="property_type" value="'.$row["id"].'"'.($i == 1 ? ' checked' : '').' ><small>'.$row["name"].'</small></label>
						</div>';
						}			
					?>
					<hr>
					<div id="house_option">
						<div class="radio">
							<label><input type="radio" name="trim" value="1" checked><small>Не включая внутреннюю отделку и инженерное оборудование</small></label>
						</div>
						<div class="radio">
							<label><input type="radio" name="trim" value="2"><small>Включая внутреннюю отделку и инженерное оборудование</small></label>
						</div>
						<hr>
						<div class="checkbox">
							<label><input type="checkbox" name="house_age" value="1"><small>С момента постройки объекта недвижимости прошло 40 лет</small></label>
						</div>
						<div class="checkbox">
							<label><input type="checkbox" name="house_fire" value="1"><small>В объекте недвижимости имеются источники открытого огня (камин, печь) или газовый баллон</small></label>
						</div>
					</div>				
				</div>
			</div>
		</div>
	</div>
</div>
<div id="ins_type_2" style="display:none">
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title"><em>Страхование недвижимого имущества - земельные участки (не находящиеся вблизи водоёмов)</em></div>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label  class="col-sm-5 control-label" ><small>Размер страховой суммы:</small></label>
				<div class="col-sm-4">
					<input type="text" class="form-control input-sm only-number" name="ins_summa_earth" required>
				</div>
			</div>
		</div>
	</div>
</div>
<hr>
<div class="form-group">
	<button type="submit" id="button_calc_1" class="btn btn-success btn-block" style="display:none">Рассчитать стоимость</button>
</div>