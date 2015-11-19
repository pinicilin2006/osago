<script type="text/javascript">

$(document).ready(function(){
	$(".date_birth").datepicker({
	  dateFormat: "dd.mm.yy",
	  maxDate: "-18Y",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c-70:c"
	});
	$("#date_start").datepicker({
	  dateFormat: "dd.mm.yy",
	  minDate: "0",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c-70:c"
	});	
	$(".date_birth").mask('00.00.0000');
	$("#phone_number").mask('(000)000-00-00');
	
	$(document).on("keyup", ".only-number", function(){
		onlyDigits(this);
	});

	//Скрытие - отображение  поля для воода типа недвижмого имущества
	$(document).on("change", "#property_type_name", function(){
		var a = $(this).val();
		if(a == '5'){
			$("#property_other").slideDown();
		} else {
			$("#property_other").slideUp();
		}
	});
    //Прибавляем год к дате начал страхового периода
	$("#date_start").change(function(){ 
		var a = $(this).val();
		var arrStartDate = a.split('.');
		var startDate = new Date(arrStartDate[2], arrStartDate[1]-1, arrStartDate[0]);
		var endDate = startDate;
		endDate.setMonth(endDate.getMonth()+12);
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
		$("#date_end").val(end_date);		
	});

	


	//Действие при нажатие на кнопку расчёта
	$('#main_form').validate({ // initialize the plugin
		//Действие в том случае если все необходимые поля заполнены.
    	submitHandler: function(form) {
    	hypothec_polis_add();
    	return false; 
    	}
    });     
});
</script>