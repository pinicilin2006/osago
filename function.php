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
/**
 * Возвращает сумму прописью
 * @author runcore
 * @uses morph(...)
 *взял отсюда http://habrahabr.ru/post/53210/
 */
function num2str($num) {
    $nul='ноль';
    $ten=array(
        array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
        array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
    );
    $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
    $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
    $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
    $unit=array( // Units
        array('копейка' ,'копейки' ,'копеек',  1),
        array('рубль'   ,'рубля'   ,'рублей'    ,0),
        array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
        array('миллион' ,'миллиона','миллионов' ,0),
        array('миллиард','милиарда','миллиардов',0),
    );
    //
    list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub)>0) {
        foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit)-$uk-1; // unit key
            $gender = $unit[$uk][3];
            list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
            else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
        } //foreach
    }
    else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
    $out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
}

/**
 * Склоняем словоформу
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5) {
    $n = abs(intval($n)) % 100;
    if ($n>10 && $n<20) return $f5;
    $n = $n % 10;
    if ($n>1 && $n<5) return $f2;
    if ($n==1) return $f1;
    return $f5;
}

function check_browser(){
  $user_agent = $_SERVER['HTTP_USER_AGENT'];
  if(stristr($user_agent, 'MSIE 5.0') || stristr($user_agent, 'MSIE 6.0') || stristr($user_agent, 'MSIE 7.0') || stristr($user_agent, 'MSIE 8.0') || stristr($user_agent, 'Firefox/3.5.1')){
    echo 'Исользование данной версии браузера не допускается. Используйте более новую версию браузера.';
    exit();
  }
}

//Число в месяц прописью
function get_month($mnum){
    $mn = array("Январ", "Феврал", "Март", "Апрел", "Ма", "Июн", "Июл", "Август", "Сентябр", "Октябр", "Декабр");
    if ($mnum==3||$mnum==8){ $k="а"; } else { $k="я"; }
    return $mn[$mnum-1].$k;
}
?>
