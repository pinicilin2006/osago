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
<div class="form-group">
	<label  class="col-sm-5 control-label" ><small>Размер страховой суммы</small></label>
	<div class="col-sm-4">
	<input type="text" class="form-control input-sm only-number" name="ins_summa" required>
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
			$query = mysql_query("SELECT * FROM `hypothec_earth_tb` WHERE `active` = 1 AND `id_bank` = $id_bank");
			while ($row = mysql_fetch_assoc($query)) {
	  		echo '<div class="radio">
			  <label><input type="radio" name="property_type" class="property_type" value="earth"><small>'.$row["name"].'</small></label>
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
<hr>
<div class="form-group">
	<button type="submit" id="button_submit" class="btn btn-success btn-block">Рассчитать стоимость</button>
</div>