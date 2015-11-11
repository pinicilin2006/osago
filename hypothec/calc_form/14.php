<?php
//форма для расчёта ипотеки снгб
//форма для расчёт ипотеки сбербанка
$id_bank = 14;
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
	<label  class="col-sm-5 control-label" ><small>Программа страхования:</small></label>
	<div class="col-sm-7">																			  		  
			<div class="checkbox">
				<label><input type="checkbox" name="ins_prog_1" class="ins_prog" value="1"><small>Имущественное страхование</small></label>
			</div>
			<div class="checkbox">
				<label><input type="checkbox" name="ins_prog_2" class="ins_prog" value="2"><small>Страхование титула</small></label>
			</div>
			<div class="checkbox">
				<label><input type="checkbox" name="ins_prog_3" class="ins_prog" value="3"><small>Личное страхование</small></label>
			</div>										
	</div>
</div>


<div style="display:none" id='prog_1'>
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title">Имущественное страхование</div>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label  class="col-sm-5 control-label" ><small>Размер страховой суммы по имущественному страхованию:</small></label>
				<div class="col-sm-3">
				<input type="text" class="form-control input-sm only-number" name="ins_summa_1" required>
				</div>
			</div>
			<div class="form-group">
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
					<div id="earth_option" style="display:none">
						<div class="checkbox">
							<label><input type="checkbox" name="earth_fire" value="1"><small>Опасные, горючие, легковоспламеняющиеся вещества на участке</small></label>
						</div>
						<div class="checkbox">
							<label><input type="checkbox" name="earth_danger" value="1"><small>Объекты повышенной опасности, химических, нефтехимических и т.п. производств в непосредственной близости от земельного участка</small></label>
						</div>
					</div>						
				</div>
			</div>
		</div>
	</div>
</div>


<div style="display:none" id='prog_2'>
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title">Страхование титула</div>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label  class="col-sm-5 control-label" ><small>Размер страховой суммы по страхованию титула:</small></label>
				<div class="col-sm-3">
				<input type="text" class="form-control input-sm only-number" name="ins_summa_2" required>
				</div>
			</div>
			<div class="form-group">
				<label  class="col-sm-5 control-label" ><small>Количество переходов права собственности в истории недвижимого имущества:</small></label>
				<div class="col-sm-3">
					<select class="form-control input-sm" name="titul_option" required>																			  		  
					<?php
						$query = mysql_query("SELECT * FROM `hypothec_titul_tb` WHERE `active` = 1 AND `id_bank` = $id_bank");
						while ($row = mysql_fetch_assoc($query)) {	
							echo '<option value='.$row["id"].'>'.$row["name"].'</option>';
						}			
					?>
					</select>					
				</div>
			</div>
		</div>
	</div>
</div>


<div style="display:none" id='prog_3'>
	<div class="panel panel-default">
		<div class="panel-heading">
			<div class="panel-title">Личное страхование</div>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label  class="col-sm-5 control-label" ><small>Размер страховой суммы по личному страхованию:</small></label>
				<div class="col-sm-3">
				<input type="text" class="form-control input-sm only-number" name="ins_summa_3" required>
				</div>
			</div>
			<hr>
			<div class="form-group">
				<label  class="col-sm-5 control-label" ><small>Вид страхования:</small></label>
				<div class="col-sm-7">
					<div class="radio">
						<label><input type="radio" name="prog_3_type" class="prog_3_type" value="1" checked><small>Страхование смерти и утраты трудоспобности в результате несчастного случая</small></label>
					</div>
					<div class="radio">
						<label><input type="radio" name="prog_3_type" class="prog_3_type" value="2"><small>Страхование смерти и утраты трудоспобности в результате несчастного случая и/или болезни</small></label>
					</div>				
				</div>
			</div>
			<hr>			
			<div class="form-group">
				<label  class="col-sm-5 control-label" ><small>Количество застрахованных:</small></label>
				<div class="col-sm-3">
					<select class="form-control input-sm" name="prog_3_num" id="prog_3_num" required>
					<optgroup>
					<option disabled selected></option>																			  		  
					<?php
						for($x = 1; $x < 11; $x++){
							echo '<option value='.$x.'>'.$x.'</option>';
						}			
					?>
					</optgroup>
					</select>					
				</div>
			</div>
			<hr>
			<hr class="hr_red_2">
			<div id="field"></div>
		</div>
	</div>
</div>
