<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}
	unset($_SESSION["step_1"]);
	unset($_SESSION["calc"]);
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
require_once('config.php');
require_once('function.php');
connect_to_base();
require_once('template/header.html');
//Забираем данные 
if(isset($_SESSION['access'][6])){
	$query_unit = mysql_query("SELECT * FROM `unit` WHERE `unit_parent_id` = '".$_SESSION['unit_id']."'");
	$data_for_query_agent = '';
	while ($query_unit_data = mysql_fetch_assoc($query_unit)) {
		$data_for_query_agent .=' OR user_unit.unit_id = '.$query_unit_data['unit_id'];
	}
	$query_agent = mysql_query("SELECT * FROM `user`, `user_unit` WHERE user.user_id = user_unit.user_id AND (user_unit.unit_id = '".$_SESSION['unit_id']."' ".$data_for_query_agent.") ORDER BY `second_name`");
}
if(isset($_SESSION['access'][10])){
	$query_agent = mysql_query("SELECT * FROM `user` ORDER BY `second_name`");
}	
require_once('template/header.html');
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Ранее заключённые договора</h3>
	  			</div>
	  			<div class="panel-body">
					<div id="filter">
						<div class="panel panel-danger">
					      	<div class="panel-heading">
					        	<h3 class="panel-title">Параметры выборки договоров:<span class="glyphicon glyphicon-question-sign pull-right" data-toggle="modal" data-target="#filter_help"></span></h3>
					      	</div>
					      	<div class="panel-body">
							<form class="form-inline " role="search" id="filter_form">
									<div class="form-group block">
									<span  data-toggle="tooltip" title="При выборе двух разных дат, будут отображены все договоры, заключенные в период между этими датами. Если выбрать одинаковую дату в обоих полях, то отобразятся договора за это число. если поля не заполнять то отобразятся договоры за всё время. Если заполнить первое поле, то отобразятся договоры заключенные в периодс с этого числа по настоящее время. если заполнить только второе поле, то отобразятся договоры заключенные по этой число включительно."><b>Дата заключения договора(ов):</b></span>
									    <input type="text" class="form-control date_filter" id="date_1" name="date_create_1" value='<?php print(date('d.m.Y', strtotime("-1 month"))) ?>' placeholder="Договор заключён с" data-toggle="tooltip" title="Дата заключения договора страхования. Начальная дата.">								    
									    <input type="text" class="form-control date_filter" id="date_2" name="date_create_2" value='<?php print(date('d.m.Y')) ?>' placeholder="Договор заключён по" data-toggle="tooltip" title="Дата заключения договора страхования. Конечная дата.">								    
									</div>								
									<div class="form-group">
									    <input type="text" class="form-control date_filter" id="date_3" name="date_end" placeholder="Договор действует до" data-toggle="tooltip" title="Дата окончания страхового периода">
									    
									</div>
									<div class="form-group">
									    <input type="text" class="form-control" name="bso_number" placeholder="№ БСО" data-toggle="tooltip" title="Номер выданного бланка строгой отчётности">
									    
									</div>
<!-- 									<div class="form-group">
									    <input type="text" class="form-control" name="second_name" placeholder="Фамилия страхователя" data-toggle="tooltip" title="Фамилия страхователя">
									    
									</div> -->
<!-- 									<div class="form-group">
									    <input type="text" class="form-control" name="auto_number" placeholder="Гос. номер ТС" data-toggle="tooltip" title="Государственный регистрационный номер транспортного средства">
									    
									</div> -->
									<div class="form-group block" >
										<span class="" data-toggle="tooltip" title="Статус договора страхования"><b>Статус:</b></span>
										<label class="checkbox-inline">
										    <input type="checkbox" name="status_project" checked>Проект
										</label>
										<label class="checkbox-inline">
										    <input type="checkbox" name="status_ready" checked>Оформлен
										</label>
										<label class="checkbox-inline">
										    <input type="checkbox" name="status_annuled" checked>Аннулирован
										</label>																		
									</div>
								<?php
									if($query_agent){
								?>
								<div class="form-group">
									<select name="agent" class="form-control selectpicker">
							  		<option value="0">Все агенты</option>
							  		<?php
							  			//$query_agent = mysql_query("SELECT * FROM `user` ORDER BY `second_name`");
							  			while ($agent = mysql_fetch_assoc($query_agent)) {
							  				echo '<option value='.$agent["user_id"].'>'.$agent["second_name"].' '.$agent["first_name"].' '.$agent["third_name"].'</option>';
							  			}
							  		?>
									</select>							
								</div>
								<?php		
									}
								?>
								
								<div class="form-group">																																	  
							  		<button type="submit" class="btn btn-danger">Фильтр</button>
							  	</div>
							  	
							</form>		
					      	</div>
					    </div>																				
					</div>	  			
	  				<div id="message"></div>					    	
		    	</div>
			</div>
		</div>
	</div>
</div>
<!-- Modal -->
<div class="modal fade" id="filter_help" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Пояснение по фильтру договоров</h4>
      </div>
      <div class="modal-body">
        <p><b>Поля не являются обязательными для заполнения.</b></p><p>Для просмотра всех договоров необходимо оставить пустыми поля "Договор заключён с" и "Договор заключён по".<br> Для просмотров договоров за один день необходимо выбрать одинаковые даты в полях "Договор заключен с" и "Договор заключен по". <br>При заполнение поля "Договор заключен с" и не заполнение поля "Договор заключен по" будут отбражены договоры заключённые с выбранного числа по настоящее время. При не заполнение поля "Договор заключен с" и заполнение поля "Договор заключен по", буду отображены договоры заключённые по введённое число включительно.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
      </div>
    </div>
  </div>
</div>
</body>
</html>
<script type="text/javascript">
$('.date_filter').mask('00.00.0000');
$('#date_1').datepicker({
	dateFormat: "dd.mm.yy",
	  maxDate: "0d",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c-70:c"
});
$('#date_2').datepicker({
	dateFormat: "dd.mm.yy",
	  maxDate: "0d",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c-70:c"
});
$('#date_3').datepicker({
	dateFormat: "dd.mm.yy",
	  changeYear: true,
	  changeMonth: true,
});
$(document).ready(function(){
	$('[data-toggle="tooltip"]').tooltip({
		placement : 'auto',
	});  
    $('#filter_form').submit(function( event ) {
    	$("#message").html('');
    	contract_table();
    	return false;
    });
/////////////////////////////////////////	
	contract_table(); 		
});	
</script>