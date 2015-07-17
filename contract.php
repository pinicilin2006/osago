<?php
session_start();
if(!isset($_SESSION['user_id'])){
	header("Location: login.php");
	exit;
}
	unset($_SESSION["step_1"]);
	unset($_SESSION["calc"]);
// echo "<pre>";
// print_r($_POST);
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
							<form class="form-inline" method="post" role="search" id="filter_form">
									<div class="form-group block">
									<span  data-toggle="tooltip" title="При выборе двух разных дат, будут отображены все договоры, заключенные в период между этими датами. Если выбрать одинаковую дату в обоих полях, то отобразятся договора за это число. если поля не заполнять то отобразятся договоры за всё время. Если заполнить первое поле, то отобразятся договоры заключенные в периодс с этого числа по настоящее время. если заполнить только второе поле, то отобразятся договоры заключенные по этой число включительно."><b>Дата заключения договора(ов):</b></span>
									    <input type="text" class="form-control date_filter" id="date_1" name="date_create_1" value='<?php (isset($_POST['date_create_1']) ? print($_POST['date_create_1']) : print(date('d.m.Y', strtotime("-1 month")))) ?>' placeholder="Договор заключён с" data-toggle="tooltip" title="Дата заключения договора страхования. Начальная дата.">								    
									    <input type="text" class="form-control date_filter" id="date_2" name="date_create_2" value='<?php (isset($_POST['date_create_2']) ? print($_POST['date_create_2']) : print(date('d.m.Y'))) ?>' placeholder="Договор заключён по" data-toggle="tooltip" title="Дата заключения договора страхования. Конечная дата.">								    
									</div>								
									<div class="form-group">
									    <input type="text" class="form-control date_filter" id="date_3" name="date_end" value="<?php echo(isset($_POST['date_end']) ? $_POST['date_end'] : '')?>" placeholder="Договор действует до" data-toggle="tooltip" title="Дата окончания страхового периода">
									    
									</div>
									<div class="form-group">
									    <input type="text" class="form-control" name="bso_number" placeholder="№ БСО" value="<?php echo(isset($_POST['bso_number']) ? $_POST['bso_number'] : '')?>" data-toggle="tooltip" title="Номер выданного бланка строгой отчётности">
									    
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
										    <input type="checkbox" id="status_1" name="status_project" checked>Проект
										</label>
										<label class="checkbox-inline">
										    <input type="checkbox" id="status_2" name="status_ready" checked>Оформлен
										</label>
										<label class="checkbox-inline">
										    <input type="checkbox" id="status_3" name="status_annuled" checked>Аннулирован
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
							  				echo '<option value='.$agent["user_id"].' '.($_POST['agent'] && $_POST['agent'] == $agent["user_id"] ? 'selected' : '').'>'.$agent["second_name"].' '.$agent["first_name"].' '.$agent["third_name"].'</option>';
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
$('[data-toggle="tooltip"]').tooltip({
	placement : 'auto',
});
<?php
if($_POST){
	if(!$_POST['status_project']){
		echo '$("#status_1").prop("checked", false);';
	}
	if(!$_POST['status_ready']){
		echo '$("#status_2").prop("checked", false);';
	}
	if(!$_POST['status_annuled']){
		echo '$("#status_3").prop("checked", false);';
	}		
}
?>
</script>						  			
	  				<div id="message">
<?php	  					
//Забираем данные 
if(isset($_SESSION['access'][6])){
	$query = "SELECT * FROM `contract` WHERE `unit_id` = '".$_SESSION['unit_id']."'";
	$query_unit = mysql_query("SELECT * FROM `unit` WHERE `unit_parent_id` = '".$_SESSION['unit_id']."'");
	while ($query_unit_data = mysql_fetch_assoc($query_unit)) {
			$query .=' OR `unit_id` = '.$query_unit_data['unit_id'];
		}
} else {
	$query = "SELECT * FROM `contract` WHERE `user_id` = '".$_SESSION['user_id']."'";
}
if(isset($_SESSION['access'][10])){
	$query = "SELECT * FROM `contract` WHERE id > 0";
}
//Проверка на ошибки в полях фильтра/////////////////////////////////
$error = '';
if($_POST['date_create_1'] && $_POST['date_create_2']){
	if(strtotime($_POST['date_create_1']) > strtotime($_POST['date_create_2'])){
		$error .= "<p class=\"text-danger text-center\">При выборке догоров за определённый период, дата окончания периода не может быть меньше даты начала периода!</p>";
	}
}
if($_POST['bso_number'] && !is_numeric($_POST['bso_number'])){
	$error .= "<p class=\"text-danger text-center\">Номер бланка должен содержать только цифры.</p>";
}
if($_POST && !$_POST['status_project'] && !$_POST['status_ready'] && !$_POST['status_annuled']){
	$error .= "<p class=\"text-danger text-center\">Необходимо выбрать как минимум один статус договора.</p>";	
}
if($error){
	echo $error;
	exit();
}
//////////////////////////////////////////////////////////////////////
//Дополняем запрос данными из фильтра
//Согласно датам по периоду
$period = '';
if($_POST['date_create_1'] && !$_POST['date_create_2']){
	$period = " AND time_create >'".date("Y-m-d", strtotime($_POST['date_create_1']))."'";
}
if(!$_POST['date_create_1'] && $_POST['date_create_2']){
	$period = " AND time_create <'".date("Y-m-d", strtotime($_POST['date_create_2'] . "+1 day"))."'";
}
if($_POST['date_create_1'] && $_POST['date_create_2']){
	$period = " AND time_create >'".date("Y-m-d", strtotime($_POST['date_create_1']))."'"." AND time_create <'".date("Y-m-d", strtotime($_POST['date_create_2'] . "+1 day"))."'";
}
if(!$_POST){
	$period = " AND time_create >'".date("Y-m-d", strtotime("-1 month"))."'"." AND time_create <'".date("Y-m-d", strtotime("+1 day"))."'";
}
$query .= $period;
///////////////////////////
if($_POST['date_end']){
	$query .= " AND end_date ='".$_POST['date_end']."' ";
}
if($_POST['bso_number']){
	$query .= " AND bso_number ='".$_POST['bso_number']."' ";
}
if($_POST['agent']){
	$query .= " AND user_id='".$_POST['agent']."' ";
}
$status = '';
if($_POST['status_project']){
	$status[]= " project = 1 ";
}
if($_POST['status_annuled']){
	$status[]= " annuled = 1 ";
}
if($_POST['status_ready']){
	$status[]= " (project = 0 AND annuled = 0) ";
}
$k=1;
$status_num = count($status);
$status_result = (empty($_POST) ? '' :' AND ');
foreach ($status as $key => $val) {
	if($k == 1 && $status_num > 1){
		$status_result .=" ($val";
	}
	if($k == 1 && $status_num == 1){
		$status_result .=" $val ";
		break;
	}	
	if($k > 1 && $k <= $status_num){
		$status_result .=" OR $val ";
	}
	if($k == $status_num){
		$status_result .= ")";
	}
	$k++;
}
$query .= $status_result.' ORDER BY `id`';
//echo $query;
if(mysql_num_rows(mysql_query($query))<1){
	echo "<p class=\"text-danger text-center\">Отсутствуют договора в базе данных за выбранный период!</p>";
	exit();
}
?>
<div class="table-responsive">
<table class='table table-hover table-responsive table-condensed table-bordered' id='contract_table'>
	<thead>
		<tr>
			<th style = 'cursor: pointer;'>№ <span class="glyphicon glyphicon-sort"></span></th>
			<th style = 'cursor: pointer;'>Дата заключения договора <span class="glyphicon glyphicon-sort pull-right"></span></th>
			<th style = 'cursor: pointer;'>Ф.И.О. агента <span class="glyphicon glyphicon-sort pull-right"></span></th>
			<th style = 'cursor: pointer;'>Страхователь <span class="glyphicon glyphicon-sort pull-right"></span></th>
			<th style = 'cursor: pointer;'>№ БСО <span class="glyphicon glyphicon-sort pull-right"></span></th>
			<th style = 'cursor: pointer;'>Дата начала действия договора <span class="glyphicon glyphicon-sort pull-right"></span></th>
			<th style = 'cursor: pointer;'>Дата окончания действия договора <span class="glyphicon glyphicon-sort pull-right"></span></th>
			<th style = 'cursor: pointer;'>Страховой тариф <span class="glyphicon glyphicon-sort pull-right"></span></th>
			<th style = 'cursor: pointer;'>Статус договора <span class="glyphicon glyphicon-sort pull-right"></span></th>
			<th style = 'cursor: pointer;'>Действие <span class="glyphicon glyphicon-sort pull-right"></span></th>
		</tr>
	</thead>
	<tbody>
<?php
$query = mysql_query($query);
while($row = mysql_fetch_assoc($query)){
	if($row['project'] == '1' && $row['annuled'] == '0'){
		echo '<tr class="info '.$row['user_id'].' '.date('d-m-Y', strtotime($row["time_create"])).'">';	
	}
	if($row['project'] == '0' && $row['annuled'] == '0'){
		echo '<tr class="success '.$row['user_id'].' '.date('d-m-Y', strtotime($row["time_create"])).'">';
	}
	if($row['annuled'] == '1'){
		echo '<tr class="danger '.$row['user_id'].' '.date('d-m-Y', strtotime($row["time_create"])).'">';	
	}	
	echo "<td>".$row['id']."</td>";
	echo "<td>".date('d.m.Y', strtotime($row["time_create"]))."</td>";
	$user_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `user` WHERE `user_id` = '".$row['user_id']."'"));
	echo "<td>".$user_data['second_name']." ".$user_data['first_name']." ".$user_data['third_name']."</td>";
	$insurer_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `".($row["insurer_type"] == 1 ? "contact_phiz" : "contact_jur")."` WHERE `id` = '".$row["insurer_id"]."'"));
	if($row["insurer_type"] == 1){
		echo "<td>".$insurer_data['second_name']." ".$insurer_data['first_name']." ".$insurer_data['third_name']."</td>";
	} 
	if($row["insurer_type"] == 2){
		echo "<td>".$insurer_data['jur_name']."</td>";
	}
	echo "<td>".$row['bso_number']."</td>"; 
	echo "<td>".$row['start_date']."</td>"; 
	echo "<td>".$row['end_date']."</td>";
	$calc_result = unserialize($row['calc_result']);
	echo "<td>".$calc_result['t']."</td>";
	echo "<td>";
	if($row['annuled'] == '1'){
		echo "Аннулирован";
	} else {
		echo ($row['project'] == '0' ? 'Оформлен' : 'Проект');
	}
	echo "</td>";
	echo '<td>
<div class="btn-group">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">Действие <span class="caret"></span></button>
  <ul class="dropdown-menu" role="menu">';
    if($row['project'] == '1' && $row['annuled'] == '0'){
    	echo '<li><a href="/edit_osago.php?id='.$row['md5_id'].'"><small>Редактировать</small></a></li><li class="divider" style="margin:0 0"></li>';
    	echo '<li><a href="/method/del.php?id='.$row['md5_id'].'"><small>Удалить</small></a></li><li class="divider" style="margin:0 0"></li>';
	}
echo '<li><a href="/print/statement.php?id='.$row['md5_id'].'" target="_blank"><small>Заявление</a></small></li>
    <li class="divider" style="margin:0 0"></li>
    <li><a href="/print/bso.php?id='.$row['md5_id'].'" target="_blank"><small>БСО</small></a></li>
    <li class="divider" style="margin:0 0"></li>
    <li><a href="/print/a7.php?id='.$row['md5_id'].'" target="_blank"><small>Бланк А7</small></a></li>
    <li class="divider" style="margin:0 0"></li>';
    if($row['annuled'] == '0' && $row['project'] == '0'){
    	echo '<li><a href="/method/anul.php?id='.$row['md5_id'].'"><small>Аннулировать</small></a></li><li class="divider" style="margin:0 0"></li>';
	}
    if($row['project'] == '1' && $row['annuled'] == '0'){
    	echo '<li><a href="/method/active.php?id='.$row['md5_id'].'"><small>Перевести в статус <br> "Оформлен"</small></a></li><li class="divider" style="margin:0 0"></li>';
	}		
echo '<li><a href="/edit_osago.php?id='.$row['md5_id'].'&prolongation=1"><small>Копировать полис</small></a></li><li class="divider" style="margin:0 0"></li>';	
echo '</ul>
</div>
	</td>';
	echo "</tr>";
}
?>		    				
		</tbody>
	</table>
</div>




	  				</div>					    	
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

$(document).ready(function(){
 $("#contract_table").tablesorter();  
//     $('#filter_form').submit(function( event ) {
//     	$("#message").html('');
//     	contract_table();
//     	return false;
//     });
// /////////////////////////////////////////	
// 	contract_table(); 		
});	
</script>