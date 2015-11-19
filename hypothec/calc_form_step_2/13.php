<?php
//форма для оформления ипотеки сбербанка
session_start();
if(!isset($_SESSION['user_id'])){
	echo '<center><span class="text-danger"><b>Закончилось время сессии. Необходимо выйти и снова войти в сервис.</b></span></center>';	
	exit;
}
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
?>
	<div class="col-md-12">
		<legend>Данные клиента:</legend>
		<fieldset>
			<div class="form-group">
			    <label for="second_name" class="col-sm-4 control-label"><small>Фамилия:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm phiz_name register phiz_name_format" name="second_name"  id="second_name" placeholder="Фамилия" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="first_name" class="col-sm-4 control-label"><small>Имя:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm phiz_name register phiz_name_format" name="first_name"  id="first_name" placeholder="Имя" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="third_name" class="col-sm-4 control-label"><small>Отчество:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm phiz_name register phiz_name_format" name="third_name"  id="third_name" placeholder="Отчество" required>
			    </div>
			</div>
		  	<div class="form-group">
		    	<label for="date_birth" class="col-sm-4 control-label"><small>Дата рождения</small></label>
		    	<div class="col-sm-6">
		      		<input type="text" class="form-control input-sm date_birth" name="date_birth" id="date_birth" placeholder="Дата рождения" required>
		    	</div>
		  	</div>
			<div class="form-group">
			    <label for="place_birth" class="col-sm-4 control-label"><small>Место рождения:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="place_birth"  id="place_birth" placeholder="Место рождения" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="place_work" class="col-sm-4 control-label"><small>Место работы:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="place_work"  id="place_work" placeholder="Место работы" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="phone_number" class="col-sm-4 control-label"><small>Номер телефона:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="phone_number"  id="phone_number" placeholder="Номер телефона" required>
			    </div>
			</div>	
			<div class="form-group">
			    <label for="inn" class="col-sm-4 control-label"><small>ИНН:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="inn"  id="inn" placeholder="Инн" required>
			    </div>
			</div>
			<hr class="hr_red_2">	
			<b>Место регистрации:</b>
			<hr>
			<div class="form-group">
			    <label for="index_registration" class="col-sm-4 control-label"><small>Индекс:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="index_registration"  id="index_registration" placeholder="Индекс" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="adress_registration" class="col-sm-4 control-label"><small>Адрес (край, область, район, город,  улица, номер дома, номер корпуса, номер квартиры)</small></label>
			    <div class="col-sm-6">
			      	<textarea rows="4" class="form-control" id="adress_registration" name="adress_registration" style="resize:none;" required></textarea>
			    </div>
			</div>
			<hr class="hr_red_2">
			<b>Данные паспорта:</b>
			<hr>
			<div class="form-group">
			    <label for="passport_series" class="col-sm-4 control-label"><small>Серия:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="passport_series"  id="passport_series" placeholder="Серия" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="passport_number" class="col-sm-4 control-label"><small>Номер:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="passport_number"  id="passport_number" placeholder="Номер" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="passport_organ" class="col-sm-4 control-label"><small>Орган выдавший документ:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="passport_organ"  id="passport_organ" placeholder="Орган выдавший документ" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="passport_date" class="col-sm-4 control-label"><small>Дата выдачи:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="passport_date"  id="passport_date" placeholder="Дата выдачи" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="passport_code" class="col-sm-4 control-label"><small>Код подразделения:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="passport_code"  id="passport_code" placeholder="Код подразделения" required>
			    </div>
			</div>																												  		
			<hr class="hr_red_2">	
			<b>Данные недвижимого имущества:</b>
			<hr>		
				
			<div class="form-group">
			    <label for="property_type_name" class="col-sm-4 control-label"><small>Тип недвижимого имущества:</small></label>
			    <div class="col-sm-6">
			      	<select name="property_type_name" id="property_type_name" class="form-control">
			      		<option value="1">жилой дом</option>
			      		<option value="2">квартира</option>
			      		<option value="3">комната</option>
			      		<option value="4">общежитие</option>
			      		<option value="5">иное</option>
			      	</select>
			    </div>
			</div>
			<div class="form-group" style="display:none" id="property_other"> 
			    <label for="property_type_name_other" class="col-sm-4 control-label"><small>Укажите тип недвижимости</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="property_type_name_other"  id="property_type_name_other" placeholder="Тип недвижимости" required>
			    </div>
			</div>					
			<div class="form-group">
			    <label for="property_full_name" class="col-sm-4 control-label"><small>Полное наименование объекта, согласно свидетельства:</small></label>
			    <div class="col-sm-6">
			      	<textarea rows="4" class="form-control" id="property_full_name" name="property_full_name" style="resize:none;" required></textarea>
			    </div>
			</div>
			<div class="form-group">
			    <label for="property_cadastral_number" class="col-sm-4 control-label"><small>Кадастровый номер:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="property_cadastral_number"  id="property_cadastral_number" placeholder="Кадастровый номер" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="property_gross_area" class="col-sm-4 control-label"><small>Общая площадь кв.м:</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="property_gross_area"  id="property_gross_area" placeholder="кв.м" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="property_adress_registration" class="col-sm-4 control-label"><small>Адрес (край, область, район, город,  улица, номер дома, номер корпуса, номер квартиры)</small></label>
			    <div class="col-sm-6">
			      	<textarea rows="4" class="form-control" id="property_adress_registration" name="property_adress_registration" style="resize:none;" required></textarea>
			    </div>
			</div>
			<div class="form-group">
			    <label for="property_right_of_possession" class="col-sm-4 control-label"><small>Право владения:</small></label>
			    <div class="col-sm-6">
			      	<select name="property_right_of_possession" id="property_right_of_possession" class="form-control">
			      		<option value="1">собственность</option>
			      		<option value="2">аренда</option>
			      	</select>
			    </div>
			</div>
			<div class="form-group">
			    <label for="property_actual_value" class="col-sm-4 control-label"><small>Действительная стоимость</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="property_actual_value"  id="property_actual_value" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="property_credit_summa" class="col-sm-4 control-label"><small>Сумма выданного кредита</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="property_credit_summa"  id="property_credit_summa" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="property_year" class="col-sm-4 control-label"><small>Год постройки дома</small></label>
			    <div class="col-sm-6">
			      	<input type="text" class="form-control input-sm" name="property_year"  id="property_year" required>
			    </div>
			</div>
			<div class="form-group">
			    <label for="property_characteristics" class="col-sm-4 control-label"><small>Характеристики дома:</small></label>
			    <div class="col-sm-6">
			      	<select name="property_characteristics" id="property_characteristics" class="form-control">
			      		<option value="1">деревянное</option>
			      		<option value="2">кирпичное</option>
			      		<option value="3">панельное</option>
			      		<option value="4">монолит ж/б</option>
			      		<option value="5">смешанного типа</option>
			      	</select>
			    </div>
			</div>
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





        	
	</div>	
	</fieldset>
</div>
