<?php
require_once('../../config.php');
require_once('../../function.php');
include("../../ibs_connector_2.php");
connect_to_base();
$query_all_contract = mysql_query("SELECT * FROM `contract` WHERE `project` = '0' AND `annuled` = '0'");
	while($rows = mysql_fetch_assoc($query_all_contract)){
		if(isset($params)){
			unset($params);
		}
		if(mysql_num_rows(mysql_query("SELECT * FROM `export_to_ibs` WHERE `md5_id` = '".$rows['md5_id']."'")) > 0){
			continue;
		}
		//массив с параметрами для замены в документе.
		$params = array();
		$contract_data = $rows;
		//Получаем данные страхователя
		$insurer_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `".($contract_data["insurer_type"] == 1 ? "contact_phiz" : "contact_jur")."` WHERE `id` = '".$contract_data["insurer_id"]."'"));
		//Данные по второму шагу оформления полиса
		$step_2_data = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $contract_data['step_2_data']);//боримся с проблемой unserialize если есть кавычки
		$step_2_data = unserialize($step_2_data);
		//если физ лицо
		if($contract_data["insurer_type"] == 1){
			$params['INSURER_PHIZ_FIRST_NAME'] = $insurer_data['first_name'];
			$params['INSURER_PHIZ_SECOND_NAME'] = $insurer_data['second_name'];
			$params['INSURER_PHIZ_THIRD_NAME'] = $insurer_data['third_name'];
			$params['INSURER_PHIZ_DATE_BIRTH'] = $insurer_data['date_birth'];
			//получаем название документа
			$name_document = mysql_fetch_assoc(mysql_query("SELECT * FROM `document` WHERE `id` = '".$insurer_data["doc_name"]."'"));
			$params['INSURER_PHIZ_DOC_NAME'] = $name_document['name'];
			/////////////////////////////////////////////////////
			$params['INSURER_PHIZ_DOC_SERIES'] = $insurer_data['doc_series'];	
			$params['INSURER_PHIZ_DOC_NUMBER'] = $insurer_data['doc_number'];
			
			//получаем индекс и улицу
			// $street_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_7` WHERE `aoguid` = '".$insurer_data["street"]."'"));
			// $params['[INDEX]'] = $street_data['postalcode'];;
			$params['INSURER_PHIZ_STREET'] = $street_data['shortname']." ".$street_data['formalname'];
			$params['INSURER_KLADR_ID'] = $street_data['code'];
			//////////////////////////////////
			
			//Получаем населённый пункт и район
			if(!empty($insurer_data['city'])){
				$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_6` WHERE `aoid` = '".$insurer_data["city"]."'"));
			} else {
				$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoid` = '".$insurer_data["aoid"]."'"));
			}
			$params['INSURER_PHIZ_CITY'] = $city_data["shortname"].' '.$city_data['formalname'];
			///////////////////////////////////////////////////////
			//Получаем район
			if(!empty($insurer_data['city'])){
				$destrict_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoguid` = '".$city_data['parentguid']."'"));
				$params['INSURER_PHIZ_AOID'] = $destrict_data['shortname']." ".$destrict_data['formalname'];
			}else {
				//$params['[DISTRICT]'] = '------';
			}
			//Получаем область
			$subject_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `kt_subject` WHERE `id_fias` = '".$insurer_data["subject"]."'"));
			$params['INSURER_PHIZ_SUBJECT'] = $subject_data['name'];
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$params['INSURER_PHIZ_HOUSE'] = $insurer_data['house'];
			$params['INSURER_PHIZ_HOUSING'] = (empty($insurer_data['housing']) ? '' : $insurer_data['housing']);
			$params['INSURER_PHIZ_APARTMENT'] = (empty($insurer_data['apartment']) ? '' : $insurer_data['apartment']);
			$params['INSURER_PHIZ_PHONE'] = $insurer_data['phone'];


		}


		//Если юридическое лицо
		if($contract_data["insurer_type"] == 2){
			$params['INSURER_JUR_NAME'] = $insurer_data['jur_name'];
			$params['INSURER_JUR_INN'] = $insurer_data['jur_inn'];
			//получаем название документа
			//$params['[NAME_DOCUMENT]'] = 'Cвидетельство о регистрации юридического лица';
			/////////////////////////////////////////////////////
			$params['INSURER_JUR_SERIES'] = $insurer_data['jur_series'];	
			$params['INSURER_JUR_NUMBER'] = $insurer_data['jur_number'];
			
			//получаем индекс и улицу
			$street_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_7` WHERE `aoguid` = '".$insurer_data["street"]."'"));
			//$params['[INDEX]'] = $street_data['postalcode'];;
			$params['INSURER_JUR_STREET'] = $street_data['shortname']." ".$street_data['formalname'];
			$params['INSURER_KLADR_ID'] = $street_data['code'];
			//////////////////////////////////
			
			//Получаем населённый пункт и район
			if(!empty($insurer_data['city'])){
				$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_6` WHERE `aoid` = '".$insurer_data["city"]."'"));
			} else {
				$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoid` = '".$insurer_data["aoid"]."'"));
			}
			$params['INSURER_JUR_CITY'] = $city_data["shortname"].' '.$city_data['formalname'];
			///////////////////////////////////////////////////////
			//Получаем район
			if(!empty($insurer_data['city'])){
				$destrict_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoguid` = '".$city_data['parentguid']."'"));
				$params['INSURER_JUR_AOID'] = $destrict_data['shortname']." ".$destrict_data['formalname'];
			}else {
				//$params['[DISTRICT]'] = '------';
			}
			//Получаем область
			$subject_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `kt_subject` WHERE `id_fias` = '".$insurer_data["subject"]."'"));
			$params['INSURER_JUR_SUBJECT'] = $subject_data['name'];
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$params['INSURER_JUR_HOUSE'] = $insurer_data['house'];
			$params['INSURER_JUR_HOUSING'] = (empty($insurer_data['housing']) ? '' : $insurer_data['housing']);
			$params['INSURER_JUR_APARTMENT'] = (empty($insurer_data['apartment']) ? '' : $insurer_data['apartment']);
			$params['INSURER_JUR_PHONE'] = $insurer_data['phone'];

		}
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		$params['START_DATE'] = $contract_data['start_date'];
		$params['END_DATE'] = $contract_data['end_date'];


		//Данные по собственнику
		$owner_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `".($contract_data["owner_type"] == 1 ? "contact_phiz" : "contact_jur")."` WHERE `id` = '".$contract_data["owner_id"]."'"));
		//если физ лицо
		if($contract_data["owner_type"] == 1){
			$params['OWNER_PHIZ_FIRST_NAME'] = $owner_data['first_name'];
			$params['OWNER_PHIZ_SECOND_NAME'] = $owner_data['second_name'];
			$params['OWNER_PHIZ_THIRD_NAME'] = $owner_data['third_name'];
			$params['OWNER_PHIZ_DATE_BIRTH'] = $owner_data['date_birth'];
			//получаем название документа
			$name_document = mysql_fetch_assoc(mysql_query("SELECT * FROM `document` WHERE `id` = '".$owner_data["doc_name"]."'"));
			$params['OWNER_PHIZ_DOC_NAME'] = $name_document['name'];
			/////////////////////////////////////////////////////
			$params['OWNER_PHIZ_DOC_SERIES'] = $owner_data['doc_series'];	
			$params['OWNER_PHIZ_DOC_NUMBER'] = $owner_data['doc_number'];
			
			//получаем индекс и улицу
			$street_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_7` WHERE `aoguid` = '".$owner_data["street"]."'"));
			//$params['[OWNER_INDEX]'] = $street_data['postalcode'];;
			$params['OWNER_PHIZ_STREET'] = $street_data['shortname'].". ".$street_data['formalname'];
			$params['OWNER_KLADR_ID'] = $street_data['code'];
			//////////////////////////////////
			
			//Получаем населённый пункт и район
			if(!empty($owner_data['city'])){
				$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_6` WHERE `aoid` = '".$owner_data["city"]."'"));
			} else {
				$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoid` = '".$owner_data["aoid"]."'"));
			}
			$params['OWNER_PHIZ_CITY'] = $city_data["shortname"].'. '.$city_data['formalname'];
			///////////////////////////////////////////////////////
			//Получаем район
			if(!empty($owner_data['city'])){
				$destrict_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoguid` = '".$city_data['parentguid']."'"));
				$params['OWNER_PHIZ_AOID'] = $destrict_data['shortname']." ".$destrict_data['formalname'];
			}else {
				//$params['[OWNER_DISTRICT]'] = '------';
			}
			//Получаем область
			$subject_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `kt_subject` WHERE `id_fias` = '".$owner_data["subject"]."'"));
			$params['OWNER_PHIZ_SUBJECT'] = $subject_data['name'];
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$params['OWNER_PHIZ_HOUSE'] = $owner_data['house'];
			$params['OWNER_PHIZ_HOUSING'] = (empty($owner_data['housing']) ? '' : $owner_data['housing']);
			$params['OWNER_PHIZ_APARTMENT'] = (empty($owner_data['apartment']) ? '' : $owner_data['apartment']);
			$params['OWNER_PHIZ_PHONE'] = $owner_data['phone'];

		}


		//Если юридическое лицо
		if($contract_data["owner_type"] == 2){
			$params['OWNER_JUR_NAME'] = $owner_data['jur_name'];
			$params['OWNER_JUR_INN'] = $owner_data['jur_inn'];
			//получаем название документа
			//$params['[OWNER_NAME_DOCUMENT]'] = 'Cвидетельство о регистрации юридического лица';
			/////////////////////////////////////////////////////
			$params['OWNER_JUR_SERIES'] = $owner_data['jur_series'];	
			$params['OWNER_JUR_NUMBER'] = $owner_data['jur_number'];
			
			//получаем индекс и улицу
			$street_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_7` WHERE `aoguid` = '".$owner_data["street"]."'"));
			//$params['[OWNER_INDEX]'] = $street_data['postalcode'];;
			$params['OWNER_JUR_STREET'] = $street_data['shortname']." ".$street_data['formalname'];
			$params['OWNER_KLADR_ID'] = $street_data['code'];
			//////////////////////////////////
			
			//Получаем населённый пункт и район
			if(!empty($owner_data['city'])){
				$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_6` WHERE `aoid` = '".$owner_data["city"]."'"));
			} else {
				$city_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoid` = '".$owner_data["aoid"]."'"));
			}
			$params['OWNER_JUR_CITY'] = $city_data["shortname"].' '.$city_data['formalname'];
			///////////////////////////////////////////////////////
			//Получаем район
			if(!empty($owner_data['city'])){
				$destrict_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `d_fias_addrobj_3_4` WHERE `aoguid` = '".$city_data['parentguid']."'"));
				$params['OWNER_JUR_AOID'] = $destrict_data['shortname']." ".$destrict_data['formalname'];
			}else {
				//$params['[OWNER_DISTRICT]'] = '------';
			}
			//Получаем область
			$subject_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `kt_subject` WHERE `id_fias` = '".$insurer_data["subject"]."'"));
			$params['OWNER_JUR_SUBJECT'] = $subject_data['name'];
			////////////////////////////////////////////////////////////////////////////////////////////////////////////
			$params['OWNER_JUR_HOUSE'] = $owner_data['house'];
			$params['OWNER_JUR_HOUSING'] = (empty($owner_data['housing']) ? '' : $owner_data['housing']);
			$params['OWNER_JUR_APARTMENT'] = (empty($owner_data['apartment']) ? '' : $owner_data['apartment']);
			$params['OWNER_JUR_PHONE'] = $owner_data['phone'];

		}

		//Данные по автомобилю
		$vehicle_data = unserialize($contract_data['vehicle_data']);
		$calc_data = unserialize($contract_data['calc_data']);
		//Получаем марку и модель
		if(isset($vehicle_data['mark_pts'])){
			$mark = $vehicle_data['mark_pts'];
			$model = $vehicle_data['model_pts'];
		}else{
			$mark = mysql_fetch_assoc(mysql_query("SELECT * FROM `mark` WHERE `rsa_mark_id`='".$vehicle_data['mark']."'"));
			$mark = $mark['name'];
			$model = mysql_fetch_assoc(mysql_query("SELECT * FROM `model` WHERE `rsa_model_id`='".$vehicle_data['model']."'"));
			$model = $model['name'];
			$params['RSA_MARK_ID'] = $vehicle_data['mark'];
			$params['RSA_MODEL_ID'] = $vehicle_data['model'];
		}
		$category = mysql_fetch_assoc(mysql_query("SELECT * FROM `category_code` WHERE `id`='".$vehicle_data['category']."'"));
		$category = $category['name'];
		$params['VEHICLE_MARK'] = $mark;
		$params['VEHICLE_MODEL'] = $model;
		$params['VEHICLE_CATEGORY'] = $category;
		$params['VEHICLE_VIN'] = $vehicle_data['vin'];
		$params['VEHICLE_POWER'] = $vehicle_data['power'];
		//$params['POWER_K'] = round($vehicle_data['power']/1.36, 2);
		$params['VEHICLE_MAX_WEIGHT'] = (isset($vehicle_data['max_weight']) ? $vehicle_data['max_weight'] : '');
		$params['VEHICLE_NUMBER_SEATS'] = (isset($vehicle_data['number_seats']) ? $vehicle_data['number_seats'] : '');
		$params['VEHICLE_CHASSIS'] = $vehicle_data['chassis'];
		$params['VEHICLE_TRAILER'] = $vehicle_data['trailer'];
		$doc_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `document_auto` WHERE `id` = ".$vehicle_data['auto_doc_type']));
		$params['VEHICLE_DOC_TYPE'] = $doc_data['name'];
		$params['VEHICLE_DOC_SERIES'] = $vehicle_data['auto_doc_series'];
		$params['VEHICLE_DOC_NUMBER'] = $vehicle_data['auto_doc_number'];
		$params['VEHICLE_DOC_DATE'] = $vehicle_data['auto_doc_date'];
		$params['VEHICLE_REG_NUMBER'] = $vehicle_data['auto_reg_number'];
		$params['VEHICLE_DIAG_CARD_NUMBER'] = (isset($step_2_data['auto_diag_card_number']) ? $step_2_data['auto_diag_card_number'] : '');
		$params['VEHICLE_DIAG_CARD_NEXT_DATE'] = (isset($step_2_data['auto_diag_card_next_date']) ? $step_2_data['auto_diag_card_next_date'] : '');
		// if($calc_data["category"] != '2' && $calc_data["category"] != '3'){
		// 	$params['TRAILER_YES'] = ($calc_data['trailer'] == 2 ? '<w:sym w:font="Wingdings" w:char="F0FE"/>' : '<w:sym w:font="Wingdings" w:char="F0A8"/>');
		// 	$params['TRAILER_NO'] = ($calc_data['trailer'] == 1 ? '<w:sym w:font="Wingdings" w:char="F0FE"/>' : '<w:sym w:font="Wingdings" w:char="F0A8"/>');
		// }else{
		// 	$params['TRAILER_YES'] = '<w:sym w:font="Wingdings" w:char="F0A8"/>';
		// 	$params['TRAILER_NO'] = '<w:sym w:font="Wingdings" w:char="F0A8"/>';
		// }
		// for($x=1;$x<10;$x++){
		// 	if($vehicle_data["purpose_use"] == $x){
		// 		$params['['.$x.']'] = '<w:sym w:font="Wingdings" w:char="F0FE"/>';
		// 	} else {
		// 		$params['['.$x.']'] = '<w:sym w:font="Wingdings" w:char="F0A8"/>';
		// 	}
		// }
		$purpose_use = mysql_fetch_assoc(mysql_query("SELECT * FROM `purpose_use` WHERE `id` = ".$vehicle_data["purpose_use"]));
		$params['VEHICLE_PURPOSE_USE'] = $purpose_use['name'];
		// $params['[NO_LIMIT]'] = ($calc_data['drivers'] == 1 ? '<w:sym w:font="Wingdings" w:char="F0FE"/>' : '<w:sym w:font="Wingdings" w:char="F0A8"/>');
		// $params['[LIMIT]'] = ($calc_data['drivers'] == 2 ? '<w:sym w:font="Wingdings" w:char="F0FE"/>' : '<w:sym w:font="Wingdings" w:char="F0A8"/>');
		if($calc_data['drivers'] == 2){
			$drivers_data = unserialize($contract_data['drivers_data']);
		}
		//$params_drivers = '';
		$params['CALC_DATA_DRIVERS_NUM'] = $drivers_data['number_of_drivers'];
		$params['START_PERIOD_USE_1'] = $step_2_data['auto_used_start_1'];
		$params['END_PERIOD_USE_1'] = $step_2_data['auto_used_end_1'];
		$params['START_PERIOD_USE_2'] = (isset($step_2_data['auto_used_start_2']) && isset($step_2_data['auto_used_end_2']) ? $step_2_data['auto_used_start_2'] : '');
		$params['END_PERIOD_USE_2'] = (isset($step_2_data['auto_used_start_2']) && isset($step_2_data['auto_used_end_2']) ? $step_2_data['auto_used_end_2'] : '');
		$params['START_PERIOD_USE_3'] = (isset($step_2_data['auto_used_start_3']) && isset($step_2_data['auto_used_end_3']) ? $step_2_data['auto_used_start_3'] : '');
		$params['END_PERIOD_USE_3'] = (isset($step_2_data['auto_used_start_3']) && isset($step_2_data['auto_used_end_3']) ? $step_2_data['auto_used_end_3'] : '');
		$params['OSAGO_OLD_SERIES'] = (isset($step_2_data['osago_old_series']) ? $step_2_data['osago_old_series'] : '');
		$params['OSAGO_OLD_NUMBER'] = (isset($step_2_data['osago_old_number']) ? $step_2_data['osago_old_number'] : '');
		$params['OSAGO_OLD_NAME'] = (isset($step_2_data['osago_old_name']) ? $step_2_data['osago_old_name'] : '');
		$params['A7_NUMBER'] = (isset($step_2_data['a7_number']) ? $step_2_data['a7_number'] : '');
		$params['BSO_NUMBER'] = (isset($step_2_data['bso_number']) ? $step_2_data['bso_number'] : '');
		$params['BSO_SERIES'] = (isset($step_2_data['bso_number']) ? $contract_data['bso_series'] : '');
		$params['DATE_CREATE'] = date('d.m.Y', strtotime($contract_data["time_create"]));
		$calc_result = unserialize($contract_data['calc_result']);
		$params['CALC_RESULT_TB'] = $calc_result['tb'];
		$params['CALC_RESULT_KT'] = $calc_result['kt'];
		$params['CALC_RESULT_KBM'] = $calc_result['kbm'];
		$params['CALC_RESULT_KVS'] = $calc_result['kvs'];
		$params['CALC_RESULT_KS'] = $calc_result['ks'];
		$params['CALC_RESULT_KP'] = $calc_result['kp'];
		$params['CALC_RESULT_KM'] = $calc_result['km'];
		$params['CALC_RESULT_KPR'] = $calc_result['kpr'];
		$params['CALC_RESULT_KN'] = $calc_result['kn'];
		$params['CALC_RESULT_KO'] = $calc_result['ko'];
		$params['CALC_RESULT_T'] = $calc_result['t'];
		$params['AIS_REQUEST'] = $contract_data['rsa_number'];
		$params['SPECIAL_NOTES'] = $contract_data['special_notes'];
		$params['CALC_DATA_CITIZENSHIP'] = ($calc_data['citizenship'] == 1 ? 'Российская Федерация' : 'Иностранное государство');
		$params['CALC_DATA_TYPE_INS']  = $calc_data['type_ins'];
		if($calc_data['place_reg'] == '1'){
			$params['CALC_DATA_PLACE_REG'] = 'Российская федерация';
		}
		if($calc_data['place_reg'] == '2'){
			$params['CALC_DATA_PLACE_REG'] = 'Иностранное государство';
		}
		if($calc_data['place_reg'] == '3'){
			$params['CALC_DATA_PLACE_REG'] = 'ТС следует к месту регистрации';
		}
		$params['CALC_DATA_YEAR_MANUFACTURE'] = $calc_data['year_manufacture'];

		if($calc_data['place_reg'] != '2'){
			$subject_name = mysql_fetch_assoc(mysql_query("SELECT * FROM `kt_subject` WHERE `id` = ".$calc_data['subject']));
			$params['CALC_DATA_SUBJECT'] = $subject_name['name'];
			$city_name = mysql_fetch_assoc(mysql_query("SELECT * FROM `kt_city` WHERE `id` = ".$calc_data['city']));
			$params['CALC_DATA_CITY'] = $city_name['name'];
			$params['SUBJECT_KLADR_ID'] = $city_name['kladr'];
		}

		$term_insurance = mysql_fetch_assoc(mysql_query("SELECT * FROM `term_insurance` WHERE `id` = ".$calc_data['term_insurance']));
		$params['CALC_DATA_TERM_INSURANCE'] = $term_insurance['name'];
		$period_use = mysql_fetch_assoc(mysql_query("SELECT * FROM `period_use` WHERE `id` = ".$calc_data['period_use']));
		$params['CALC_DATA_PERIOD_USE'] = $period_use['name'];
		
		$params['MD5_ID'] = $contract_data['md5_id'];

		//определяем вид страхователя
		$unit_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `unit` WHERE `unit_id` = '".$contract_data['unit_id']."'"));
		$agent_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `user_id` = '".$contract_data['user_id']."'"));
		$params['AGENT_FIRST_NAME'] = $agent_data['first_name'];
		$params['AGENT_SECOND_NAME'] = $agent_data['second_name'];
		$params['AGENT_THIRD_NAME'] = $agent_data['third_name'];
		$params['AGENT_DATE_BIRTH'] = $agent_data['date_birth'];
		$params['AGENT_SEX'] = $agent_data['sex'];
		$params['UNIT_NAME'] = $unit_data['unit_full_name'];
		$params['UNIT_CITY'] = $unit_data['unit_city'];
		$params['IBS_DEPARTMENT_ID'] = ($contract_data["insurer_type"] == 1 ? $unit_data['ibs_department_phiz_id'] : $unit_data['ibs_department_jur_id']);
		$params['IBS_SALES_CHANNEL_ID'] = $unit_data['ibs_sales_channel_id'];
		$params['IBS_SALES_POINT_ID'] = $unit_data['ibs_sales_point_id'];
		if(!empty($agent_data['id_in_ibs'])){
			$params['IBS_AGENT_ID'] = $agent_data['id_in_ibs'];
		} else {
			$params['IBS_AGENT_ID'] = $unit_data['id_in_ibs'];
		}
		$unit_parent = mysql_fetch_assoc(mysql_query("SELECT * FROM `unit` WHERE `unit_id` = ".$unit_data['unit_parent_id']));
		$params['FILIAL_NAME'] = $unit_parent['unit_full_name'];
		$params['TIME_CREATE_CONTRACT'] = $contract_data['time_create'];
		if(isset($oracle_query)){
			unset($oracle_query);
		}
		$oracle_query = array();
		$oracle_query[] = 'INSERT INTO export_table '.create_sql_insert($params);
		for($x=1;$x<6;$x++){
			if($calc_data['drivers'] == 2 && $x<=$drivers_data['number_of_drivers']){
				unset($params_drivers);
				$params_drivers['FIRST_NAME'] = $drivers_data['driver_'.$x.'_first_name'];
				$params_drivers['SECOND_NAME'] = $drivers_data['driver_'.$x.'_second_name'];
				$params_drivers['THIRD_NAME'] = $drivers_data['driver_'.$x.'_third_name'];
				$params_drivers['DATE_BIRTH'] = $drivers_data['driver_'.$x.'_date_birth'];
				$params_drivers['SERIES'] =  $drivers_data['driver_'.$x.'_series'];
				$params_drivers['NUM'] =  $drivers_data['driver_'.$x.'_number'];
				$params_drivers['EXPERIENCE'] = ''.$drivers_data['driver_'.$x.'_experience'].'';
				$params_drivers['MD5_ID'] = ''.$contract_data['md5_id'].'';
				$oracle_query[] = "INSERT INTO export_table_drivers ".create_sql_insert($params_drivers);
			}
		}		
		// echo '<pre>';
		// print_r($oracle_query);
		// echo '</pre>';
		// exit();
	//Запихиваем данные в IBS
		foreach ($oracle_query as $key => $val) {
			$query_in_oracle = oci_parse($conn, $val);
			if(!$query_in_oracle){
				//echo 'asdasasd';
			}
			if(oci_execute($query_in_oracle)){
				if($key == 0){
					mysql_query("INSERT INTO `export_to_ibs` (md5_id) VALUES('".$contract_data['md5_id']."')");
				}
				//echo 'OK';
				// if(!mysql_query("UPDATE `contract` SET `export_to_ibs` = 1")){
				// 	//echo 'Не получилось изменить статус отправки данных договора в систему IBS  в базе mysql';
				// }
			} else {
				// echo $contract_data['id'];
			 //    $e = oci_error($query_in_oracle);  // Для обработки ошибок oci_execute
			 //    print htmlentities($e['message']);
			 //    echo "<br>";
			    // print "\n<pre>\n";
			    // print htmlentities($e['sqltext']);
			    // printf("\n%".($e['offset']+1)."s", "^");
			    // print  "\n</pre>\n";
			    // echo htmlentities($e['message']);
			    // exit;				
			}
		}
	}
?>
