<script type="text/javascript">
function bank_change(){
	var a = $('#bank').val();
	$('#bank_field').load('calc_form/'+a+'.php');
}


$(document).ready(function(){
	$(document).on("keyup", ".only-number", function(){
		onlyDigits(this);
	});

	$('#bank').change(function(event) {
		bank_change();
	});

	$(document).on("change", ".property_type", function(){
		if($(this).val() != 'earth'){
			$("#house_option").slideDown(400);
		} else {
			$("#house_option").slideUp(400);
		}

	});

    $('#main_form').submit(function( event ) {
    	alert('eaeaew');
    	return false;
    });	
});
</script>