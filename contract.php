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
if(isset($_SESSION['access'][10])){
	$query = "SELECT * FROM `contract`";
}
elseif(isset($_SESSION['access'][6])){
	$query = "SELECT * FROM `contract` WHERE `unit_id` = '".$_SESSION['unit_id']."' ORDER BY `id`";
} else {
	$query = "SELECT * FROM `contract` WHERE `user_id` = '".$_SESSION['user_id']."' ORDER BY `id`";
}
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
		  			<div class="table-responsive">
		    			<table class='table table-hover table-responsive table-condensed table-bordered'>
		    				<thead>
		    					<tr>
				    				<th>№</th>
				    				<th>Дата заключения договора</th>
				    				<th>Страхователь</th>
				    				<th>№ БСО</th>
				    				<th>Дата/время начала действия договора</th>
				    				<th>Дата/время окончания действия договора</th>
				    				<th>Страховой тариф</th>
				    				<th>Статус договора</th>
				    				<th>Действие</th>
				    			</tr>
			    			</thead>
			    			<tbody>
<?php
$query = mysql_query($query);
while($row = mysql_fetch_assoc($query)){
	if($row['project'] == '1' && $row['annuled'] == '0'){
		echo '<tr class="info">';	
	}
	if($row['project'] == '0' && $row['annuled'] == '0'){
		echo '<tr class="success">';
	}
	if($row['annuled'] == '1'){
		echo '<tr class="danger">';	
	}	
	echo "<td>".$row['id']."</td>";
	echo "<td>".date('d.m.Y', strtotime($row["time_create"]))."</td>";
	$insurer_data = mysql_fetch_assoc(mysql_query("SELECT * FROM `".($row["insurer_type"] == 1 ? "contact_phiz" : "contact_jur")."` WHERE `id` = '".$row["insurer_id"]."'"));
	if($row["insurer_type"] == 1){
		echo "<td>".$insurer_data['second_name']." ".$insurer_data['first_name']." ".$insurer_data['third_name']."</td>";
	} 
	if($row["insurer_type"] == 2){
		echo "<td>".$insurer_data['jur_name']."</td>";
	}
	echo "<td>".$row['bso_number']."</td>"; 
	echo "<td>".$row['start_date']." / ".$row['start_time']."</td>"; 
	echo "<td>".$row['end_date']." / 23:59</td>";
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
  <ul class="dropdown-menu" role="menu">
    <li><a href="/print/statement.php?id='.$row['md5_id'].'" target="_blank"><small>Распечатать <br>заявление</a></small></li>
    <li class="divider" style="margin:0 0"></li>
    <li><a href="/print/bso.php?id='.$row['md5_id'].'" target="_blank"><small>Распечатать <br>БСО</small></a></li>
    <li class="divider" style="margin:0 0"></li>
    <li><a href="/print/a7.php?id='.$row['md5_id'].'" target="_blank"><small>Распечатать <br>бланк А7</small></a></li>
    <li class="divider" style="margin:0 0"></li>';
    if($row['annuled'] == '0'){
    	echo '<li><a href="/method/anul.php?id='.$row['md5_id'].'"><small>Аннулировать</small></a></li><li class="divider" style="margin:0 0"></li>';
	}
    if($row['project'] == '1' && $row['annuled'] == '0'){
    	echo '<li><a href="/edit_osago.php?id='.$row['md5_id'].'"><small>Редактировать</small></a></li><li class="divider" style="margin:0 0"></li>';
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
