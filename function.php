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
    echo '<center><span class="error">Использование данной версии браузера не допускается. Используйте более новую версию браузера.</span></senter>';
    exit();
  }
}

//Число в месяц прописью
function get_month($mnum){
    $mn = array("Январ", "Феврал", "Март", "Апрел", "Ма", "Июн", "Июл", "Август", "Сентябр", "Октябр", "Ноябр", "Декабр");
    if ($mnum==3||$mnum==8){ $k="а"; } else { $k="я"; }
    return $mn[$mnum-1].$k;
}

//Генератор паролей
function generate_password($number)
  {
    $arr = array('a','b','c','d','e','f',
                 'g','h','i','j','k','l',
                 'm','n','o','p','r','s',
                 't','u','v','x','y','z',
                 'A','B','C','D','E','F',
                 'G','H','I','J','K','L',
                 'M','N','O','P','R','S',
                 'T','U','V','X','Y','Z',
                 '1','2','3','4','5','6',
                 '7','8','9','0');
    // Генерируем пароль
    $pass = "";
    for($i = 0; $i < $number; $i++)
    {
      // Вычисляем случайный индекс массива
      $index = rand(0, count($arr) - 1);
      $pass .= $arr[$index];
    }
    $pass .= rand(0, 100);
    return $pass;
  }

//Рожаем из массива запрос
function create_sql_insert($massive){
  if(is_array($massive)){
    $k = '';//наименование полей
    $v = '';//значение полей
    foreach ($massive as $key => $value) {
      $k .= $key.',';
      $v .= "'".mysql_real_escape_string($value)."',";
    }
    $k = substr($k, 0, -1);
    $v = substr($v, 0, -1);
    return '('.$k.') VALUES('.$v.')'; 
  }
}

/**
 * Функция проверяет правильность инн
 *
 * @param string $inn
 * @return bool
 */
function is_valid_inn( $inn )
{
    if ( preg_match('/\D/', $inn) ) return false;
    
    $inn = (string) $inn;
    $len = strlen($inn);
    
    if ( $len === 10 )
    {
        return $inn[9] === (string) (((
            2*$inn[0] + 4*$inn[1] + 10*$inn[2] + 
            3*$inn[3] + 5*$inn[4] +  9*$inn[5] + 
            4*$inn[6] + 6*$inn[7] +  8*$inn[8]
        ) % 11) % 10);
    }
    elseif ( $len === 12 )
    {
        $num10 = (string) (((
             7*$inn[0] + 2*$inn[1] + 4*$inn[2] +
            10*$inn[3] + 3*$inn[4] + 5*$inn[5] + 
             9*$inn[6] + 4*$inn[7] + 6*$inn[8] +
             8*$inn[9]
        ) % 11) % 10);
        
        $num11 = (string) (((
            3*$inn[0] +  7*$inn[1] + 2*$inn[2] +
            4*$inn[3] + 10*$inn[4] + 3*$inn[5] +
            5*$inn[6] +  9*$inn[7] + 4*$inn[8] +
            6*$inn[9] +  8*$inn[10]
        ) % 11) % 10);
        
        return $inn[11] === $num11 && $inn[10] === $num10;
    }    
    return false;
}

//Проверка на корректность даты
function valid_date($date) {
    return preg_match('/^(\\d{2})\\.(\\d{2})\\.(\\d{4})$/', $date, $m)
        && checkdate($m[2], $m[1], $m[3]);
}
?>
