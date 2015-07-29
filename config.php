<?php
//Данные для подключения к mysql
$dbhost = "localhost";
$dbbase = "osago";
$dbuser = "osago";
$dbpass = "aq1sw2de3";
///////////////////////////////
//Данные для подключения к oracle
$oracle_host    = "10.250.245.73"; // Имя машины, где размещена СУБД Oracle
$oracle_sid     = "sngi"; // Имя сервиса(SID, SERVICE_NAME) экземпляра СУБД Oracle
$oracle_port    = 1521 ; // Порт для работы с СУБД ORACLE
$oracle_db_user = "ins";
$oracle_db_psw  = "adtf332";
/////////////////////////////////
$send_message = array(
	'husainov_aa@sngi.ru',
	'aksenov_pv@sngi.ru', 
);
//Отключаем отображение неважных сообщений в логах
//ini_set('error_reporting', 'E_ALL ^ E_NOTICE');
?>
