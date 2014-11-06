<?php
function connect_to_base() {
global $dbhost, $dbuser, $dbpass, $dbbase;
mysql_connect($dbhost, $dbuser, $dbpass) OR DIE("Не удалось установить соединение с базой данных");
mysql_select_db($dbbase) OR DIE("Не найдена база $dbbase");
mysql_query("SET NAMES 'utf8'");
}
function age($date) {
	$day = (int)date('d', strtotime($date));
	$month = (int)date('m', strtotime($date));
	$year = (int)date('Y', strtotime($date));
    if (is_integer($day) && is_integer($month) && is_integer($year)){
        $month_age=date("m")-$month;
        if($month_age < 0){
          $year_age=(date("Y")-$year)-1;
        }
        elseif ($month_age == 0) {
          $day_age=date("d")-$day;
          if($day_age >= 0) {
            $year_age=date("Y")-$year;
          }
          else {$year_age=(date("Y")-$year)-1;}
        }
        else {$year_age=date("Y")-$year;}
        $age=&$year_age;
        return $age;
    }
    else {return -1;}
}

function check_browser(){
  $user_agent = $_SERVER['HTTP_USER_AGENT'];
  if(stristr($user_agent, 'MSIE 5.0') || stristr($user_agent, 'MSIE 6.0') || stristr($user_agent, 'MSIE 7.0') || stristr($user_agent, 'MSIE 8.0') || stristr($user_agent, 'Firefox/3.5.1')){
    echo 'Исользование данной версии браузера не допускается. Используйте более новую версию браузера.';
    exit();
  }
}
?>
