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
	$query = "SELECT * FROM `contract` WHERE `unit_id` = '".$_SESSION['unit_id']."'";
	$query_unit = mysql_query("SELECT * FROM `unit` WHERE `unit_parent_id` = '".$_SESSION['unit_id']."'");
	$data_for_query_agent = '';
	while ($query_unit_data = mysql_fetch_assoc($query_unit)) {
			$query .=' OR `unit_id` = '.$query_unit_data['unit_id'];
			$data_for_query_agent .=' OR user_unit.unit_id = '.$query_unit_data['unit_id'];
		}
		$query .= ' ORDER BY `id`';
	$query_agent = mysql_query("SELECT * FROM `user`, `user_unit` WHERE user.user_id = user_unit.user_id AND (user_unit.unit_id = '".$_SESSION['unit_id']."' ".$data_for_query_agent.") ORDER BY `second_name`");
} else {
	$query = "SELECT * FROM `contract` WHERE `user_id` = '".$_SESSION['user_id']."' ORDER BY `id`";
	$query_agent = mysql_query("SELECT * FROM `user` WHERE `user_id` = '".$_SESSION['user_id']."'");
}
if(isset($_SESSION['access'][10])){
	$query = "SELECT * FROM `contract` ORDER BY `id`";
	$query_agent = mysql_query("SELECT * FROM `user` ORDER BY `second_name`");
}
//echo $query;
if(mysql_num_rows(mysql_query($query))<1){
	echo "<p class=\"text-danger text-center\">Отсутствуют договора в базе данных!</p>";
	exit();
}
?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span12">
			<div class="panel panel-default">
	  			<div class="panel-heading">
	    			<h3 class="panel-title">Ранее заключённые договора</h3>
	  			</div>
	  			<div class="panel-body">
	  			<hr class="hr_red">	
								<label>Фильтры: по агенту</label>						    							
									<select name="agent" id="agent" class="filter" style="border-radius:4px">
							  		<option value="0">Все агенты</option>
							  		<?php
							  			//$query_agent = mysql_query("SELECT * FROM `user` ORDER BY `second_name`");
							  			while ($agent = mysql_fetch_assoc($query_agent)) {
							  				echo '<option value='.$agent["user_id"].'>'.$agent["second_name"].' '.$agent["first_name"].' '.$agent["third_name"].'</option>';
							  			}
							  		?>
									</select>
									<label>по дате заключения:</label><input type="text" id="date_create" style="border-radius:4px" class="filter">
									<!-- <label>по статусу договора</label>
									<select name='status' class='filter' id='status' style="border-radius:4px">
										<option></option>
										<option value='success'>оформлен</option>
										<option value='info'>проект</option>
										<option value='danger'>аннулирован</option>
									</select> -->
									<hr class="hr_red">															    	
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
</body>
</html>
<script type="text/javascript">
$('#date_create').mask('00-00-0000');
$('#date_create').datepicker({
	dateFormat: "dd-mm-yy",
	  maxDate: "0d",
	  changeYear: true,
	  changeMonth: true,
	  yearRange: "c-70:c"
});
//$('#doc_date').mask('00.00.0000');
$(document).ready(function(){
//Фильтр по агенту
$(document).on("change", ".filter", function(){
	var a = $('#agent').val();
	var b = $('#date_create').val();
	if(a == '0'){
		$('tbody tr').show();
		$('tbody tr:not(.'+b+')').hide();
		$('.'+b).show();
		return false;
	}
	$('tbody tr').show();
	$('tbody tr:not(.'+a+')').hide();
	$('tbody tr:not(.'+b+')').hide();

});
/////////////////////////////////////////	
	$("#contract_table").tablesorter();    		
});	
</script>