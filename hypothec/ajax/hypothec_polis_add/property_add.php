<?php
//Запихиваем данные по объекту недвижимости в таблицу
$query = "INSERT INTO `hypothec_property`(`property_type_name`, `property_type_name_other`, `property_full_name`, `property_cadastral_number`, `property_gross_area`, `property_adress_registration`, `property_right_of_possession`, `property_actual_value`, `property_credit_summa`, `property_year`, `property_characteristics`) 
VALUES ('".$property_type_name."','".$property_type_name_other."','".$property_full_name."','".$property_cadastral_number."','".$property_gross_area."','".$property_adress_registration."','".$property_right_of_possession."','".$property_actual_value."','".$property_credit_summa."','".$property_year."','".$property_characteristics."')";
if(mysql_query($query)){
	$property_id = mysql_insert_id();
} else {
	echo '<p class="text-danger text-center">Произошла ошибка при добавление данных по объекту недвижимости в базу данных</p>';
	exit();		
}
?>
