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
require_once('../config.php');
require_once('../function.php');
connect_to_base();
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
if(!$_POST['status_project'] && !$_POST['status_ready'] && !$_POST['status_annuled']){
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
$status_result = ' AND ';
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
if(mysql_num_rows(mysql_query($query))<1){
	echo "<p class=\"text-danger text-center\">Отсутствуют договора в базе данных!</p>";
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
