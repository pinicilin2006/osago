<script type="text/javascript">

$(document).ready(function(){
	$(".date_birth").datepicker({
	  dateFormat: "dd.mm.yy",
	  maxDate: "-18Y",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c-70:c"
	});	
	$(".date_birth").mask('00.00.0000');
	
	$(document).on("keyup", ".only-number", function(){
		onlyDigits(this);
	});

	$('#bank').change(function(event) {
		bank_change();
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