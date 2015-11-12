<script type="text/javascript">

$(document).ready(function(){
	$(".date_birth").mask('00.00.0000');
	
	$(document).on("keyup", ".only-number", function(){
		onlyDigits(this);
	});

	$('#bank').change(function(event) {
		bank_change();
	});

	$(document).on("change", ".property_type", function(){
		if($(this).val() != 'earth'){
			$("#house_option").slideDown(400);
			$("#earth_option").slideUp(400);
		} else {
			$("#house_option").slideUp(400);			
			$("#earth_option").slideDown(400);
		}

	});

	//Действия при смене программы страхования для снгб и прочих банков
	$(document).on("change", ".ins_prog", function(){
		if($(this).is(":checked")){//отображение соответствующего блока
			//alert('adada');
			$("#prog_"+$(this).val()).slideDown();
		} else {
			$("#prog_"+$(this).val()).slideUp();
		}
		if($(".ins_prog").is(":checked")){
			$("#button_calc").slideDown();
		} else {
			$("#button_calc").slideUp();
		}
	});

	//Рисуем поля в зависимости от количества выбранных человек
	$(document).on("change", "#prog_3_num", function(){//при смене количества застрахованных
		get_field();
	});
	
	//Отображаем блок с данными по спорты при выборе увлечения спортом
	$(document).on("change", ".sport", function(){
		var a = $(this).val();
		var b = $(this).attr("id");
		if(a == 'yes'){
			$("."+b).slideDown();
		} else {
			$("."+b).slideUp();
		}
	});

	$(document).on("change", ".prog_3_type", function(){
		var a = $(this).val();
		if(a == 1){
			$(".medical").slideUp();
		} else {
			$(".medical").slideDown();
		}
	});

	//Отображаем или скрываем блок с заболеваниями
	$(document).on("change", ".disease", function(){
		var a = $(this).val();
		var b = $(this).attr("id");
		if(a == 'yes'){
			$("."+b).slideDown();
		} else {
			$("."+b).slideUp();
		}
	});


	//Действие при нажатие на кнопку расчёта
	$('#main_form').validate({ // initialize the plugin
		//Действие в том случае если все необходимые поля заполнены.
    	submitHandler: function(form) {
    	calc_hypothec();
    	return false; 
    	}
    });     
});
</script>