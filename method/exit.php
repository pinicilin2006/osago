<?php
session_start();
// Unset все переменные сессии.
$_SESSION = array();
// Наконец, разрушить сессию.
session_destroy();
//Валим логинится
header("Location: ../login.php");
?>
