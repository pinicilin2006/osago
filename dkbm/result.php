<?php
session_start();
if(isset($_SESSION['kbm'])){
	unset($_SESSION['kbm']);
}
if(!isset($_SESSION['user_id'])){
	exit();
}
  function isnull($text, $if_null, $need_conv) {
    if (isset($text) && $text != null) {
      if (strlen($text) > 0) {
	if ($need_conv == 1) {
	  return iconv("UTF-8", "cp1251", $text);
	} else {
	  return $text;
	}
      }
    }

    return $if_null;
  }

  $rep_date = isnull($_POST["rep_date"], "<не указана>", 1);
  $type = (isset($_POST["type"]))?$_POST["type"]:-1;
  $is_phy = (isset($_POST["is_phy"]))?$_POST["is_phy"]:-1;
  $own_name = isnull($_POST["own_name"], "<не указан>", 1);
  $own_birth = isnull($_POST["own_birth"], "<не указан>", 1);
  $own_doc = (isset($_POST["own_doc"]))?$_POST["own_doc"]:-1;
  $own_ser = isnull($_POST["own_ser"], "<не указан>", 1);
  $own_num = isnull($_POST["own_num"], "<не указан>", 1);
  $own_inn = isnull($_POST["own_inn"], "<не указан>", 1);
  $is_resident = (isset($_POST["is_resident"]))?$_POST["is_resident"]:-1;
  $lim = (isset($_POST["lim"]))?$_POST["lim"]:-1;
  $vin = isnull($_POST["vin"], "<не указан>", 1);
  $body_num = isnull($_POST["body_num"], "<не указан>", 1);
  $chassis_num = isnull($_POST["chassis_num"], "<не указан>", 1);
  $lic = isnull($_POST["lic_plate"], "<не указан>", 1);
  $drv_count = (isset($_POST["drv_count"])?$_POST["drv_count"]:0);
  // echo '<pre>';
  // print_r($_POST);
  // echo '</pre>';
  if ($type < 0) {
    $msg = "Не указан тип запроса!";
    $err = 1;
  }
  else if (($type != 1) && ($lim < 0)) {
    $msg = "Не указан признак ограничения лиц, допущенных к управлению ТС!";
    $err = 1;
  }
  else if (($type != 1) && ($lim == 0) && ($is_phy < 0)) {
    $msg = "Не указан тип собственника ТС!";
    $err = 1;
  }
  else if (($drv_count == 0) && ($lim == 1) && ($type != 1)) {
    $msg = "Не указан ниодин водитель для ограниченного кол-ва лиц, допущенных к управлению ТС!";
    $err = 1;
  }
  else if (($type != 1) && ($lim == 0) && ($is_phy == 0) && ($is_resident < 0)) {

    $msg = "Не указан признак резидентности!";
    $err = 1;
  }
  else {
    $msg = "";
    $err = 0;
  }

  // Invoke drivers to the internal array
  if (($drv_count != 0) && ($err != 1)) {
    $i = 1;
    $k = 0;
    while ($i <= $drv_count) {
      $drv[$i-1] = array("fio" => isnull($_POST["fio".$i], "", 1), "birth" => $_POST["birth".$i], "ser" => isnull($_POST["ser".$i], "", 1), "num" => $_POST["num".$i], "kbm_first" => null, "kbm" => null, "kbm_val" => null, "loss" => null);
      //Готовлю данные для отправки в форму на втором этапе осаго
      $_SESSION['kbm']["fio_".($drv_count-$k)] = trim($_POST["fio".($i)]);
      $_SESSION['kbm']["birth_".($drv_count-$k)] = trim($_POST["birth".($i)]);
      $_SESSION['kbm']["ser_".($drv_count-$k)] = trim($_POST["ser".($i)]);
      $_SESSION['kbm']["num_".($drv_count-$k)] = trim($_POST["num".($i)]);
      $i++;
      $k++;
    }
  }

  $c_date = date("r");
  
  $html = '
<HTML xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
  <HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <META http-equiv="Author" content="Shubkin E.B. aka Conner">
    <META http-equiv="Last-Modified" content="'.$c_date.'">
    <META http-equiv="Pragma" content="no-cache">

    <META name="description" content="Результат запроса данных по КБМ и прохождению ТО в АИС РСА">

    <LINK type="text/css" href="style.css" rel="stylesheet">

    <TITLE>Результат запроса КБМ/ТО в АИС РСА</TITLE>
  </HEAD>

  <BODY>
    <TABLE id=TblRes width=100% height=100% border=0 valign=top cellpadding=0 cellspacing=0 style="position: relative; left: 30px">
      <TR height=70px>
	<TD align=left valign=middle width=280px><IMG src="images/logo.png" alt="Logo" height=70px width=274px></TD>
	<TD align=right valign=middle>
	  <!--<A href="javascript:window.print()" title="Напечатать страницу"><IMG src="images/print_printer.png" alt="Print" height=20px width=20px></A>-->
	</TD>
      </TR>
      <TR>
	<TD colspan=2 align=left valign=top>
  ';
  
  // Query data from AIS RSA
  if ($err != 1) {
    if ($type == 0) {
      $ts = 'КБМ';
    } else if ($type == 1) {
      $ts = 'ТО';
    }
    else {
      $ts = 'КБМ+ТО';
    }

    if ($lim != 0) {
      $l = "Нет";
    }
    else {
      $l = "Да";
    }

    $kbm = null;
    $kbm_val = null;
    $loss = null;
    $prev_pol_info = null;
    $ticket_exists = 0;
    $date_next_to = null;
    $ticket_num = null;
    $ticket_date = null;
    $kbm_id = null;
    $to_id = null;
    $id = 0;

    include ('./connector.inc');

    $sql_txt = "SELECT TO_CHAR(SYSDATE, 'DD.MM.YYYY') AS DAT FROM DUAL";
    $sql = oci_parse($conn, $sql_txt);
    oci_execute($sql, OCI_DEFAULT);
    oci_fetch($sql);
    $date_q = oci_result($sql, "DAT");

    // request KBM info
    if ($type != 1) {
      $sql_txt = "
	DECLARE
	  PackID NUMBER;
	  ID     NUMBER;
	  PID 	 NUMBER;
	  C_ID 	 NUMBER;
	  O_ID 	 NUMBER;
	  Msg 	 VARCHAR2(20000);
	BEGIN
	  PackID :=AIS_RSA.PKG_CORE.Create_Package(NULL, NULL, 'Запрос КБМ через WEB', TO_CHAR(SYSDATE, 'DD.MM.YYYY HH24:MI:SS'), 30);
	  IF PackID IS NULL THEN
	    RAISE_APPLICATION_ERROR(-20000, 'Не удалось создать пакет!');
	  END IF;

	  C_ID :=AIS_RSA.PKG_LOAD_TBL.Add_Car_Ident(PackID, 
		 ".(($lic == "<не указан>")?"NULL":"'".$lic."'").",
		 ".(($vin == "<не указан>")?"NULL":"'".$vin."'").",
		 ".(($body_num == "<не указан>")?"NULL":"'".$body_num."'").",
		 ".(($chassis_num == "<не указан>")?"NULL":"'".$chassis_num."'")."
	  );

	  IF C_ID IS NULL THEN
	    RAISE_APPLICATION_ERROR(-20000, 'Не удалось создать идентификатор ТС!');
	  END IF;
      ";


      if (($is_phy == 1) && ($lim == 0)) {
	$sql_txt .= "
	  O_ID :=AIS_RSA.PKG_LOAD_TBL.Add_Physical_Person(PackID, '".$own_name."', TO_DATE('".$own_birth."', 'DD.MM.YYYY'), NULL, NULL, NULL, NULL, NULL);

	  IF O_ID IS NULL THEN
	    RAISE_APPLICATION_ERROR(-20000, 'Не удалось создать запись с идентификацией владельца ТС!');
	  END IF;

	  ID :=AIS_RSA.PKG_LOAD_TBL.Add_Owner_Info(O_ID, NULL, NULL, NULL);

	  IF ID IS NULL THEN
	    RAISE_APPLICATION_ERROR(-20000, 'Не удалось зарегистрировать ФЛ в качестве владельца ТС!');
	  END IF;

	  ID :=AIS_RSA.PKG_LOAD_TBL.Add_Main_Doc(O_ID, ".$own_doc.", '".(($own_ser == "<не указан>")?"NULL": $own_ser)."', '".(($own_num == "<не указан>")?"NULL": $own_num)."', NULL);

	  IF ID IS NULL THEN
	    RAISE_APPLICATION_ERROR(-20000, 'Не удалось зарегистрировать документ, удостоверяющий личность владельца ТС!');
	  END IF;
	";
      }
      else if (($is_phy == 0) && ($lim == 0)) {
	$sql_txt .= "
	  O_ID :=AIS_RSA.PKG_LOAD_TBL.Add_Judical_Person(PackID, '".$own_name."', '".$own_inn."', ".$is_resident.", NULL, NULL, NULL, NULL, NULL);
	  
	  IF O_ID IS NULL THEN
	    RAISE_APPLICATION_ERROR(-20000, 'Не удалось создать запись с идентификацией владельца ТС!');
	  END IF;

	  ID :=AIS_RSA.PKG_LOAD_TBL.Add_Owner_Info(O_ID, NULL, NULL, NULL);
	  
	  IF ID IS NULL THEN
	    RAISE_APPLICATION_ERROR(-20000, 'Не удалось зарегистрировать ФЛ в качестве владельца ТС!');
	  END IF;
	";
      }
      else {
	$sql_txt .= "
	  O_ID :=NULL;
	";
      }

      if ($drv_count > 0) {
	$i = 0;
	while ($i < $drv_count) {		
	  $sql_txt .= "
	  PID :=AIS_RSA.PKG_LOAD_TBL.Add_Driver(PackID, '".$drv[$i]["fio"]."', TO_DATE('".$drv[$i]["birth"]."', 'DD.MM.YYYY'), NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

	  IF PID IS NULL THEN
	    RAISE_APPLICATION_ERROR(-20000, 'Не удалось создать запись с водителем ТС!');
	  END IF;

	  ID :=AIS_RSA.PKG_LOAD_TBL.Add_Driver_Doc(PID, ".((strlen($drv[$i]["ser"]) == 0)?"NULL":"'".$drv[$i]["ser"]."'").", '".$drv[$i]["num"]."', NULL);

	  IF PID IS NULL THEN
	    RAISE_APPLICATION_ERROR(-20000, 'Не удалось создать запись с ВУ!');
	  END IF;
	  ";
	  $i++;
	}
      }
      
      $sql_txt .= "
	  ID :=AIS_RSA.PKG_LOAD_TBL.Add_Calc_KBM(PackID, TO_DATE('".$rep_date."', 'DD.MM.YYYY'), ".$lim.", C_ID, O_ID);
	  
	  ID :=AIS_RSA.PKG_EXCHANGE.Exchange(PackID, 1);

	  IF ID = 0 THEN
	    BEGIN
	      SELECT L.MESSAGE
	      INTO   Msg
	      FROM   AIS_RSA.LOG L
	      WHERE
		     (L.LOG_ID = (SELECT MAX(L_.LOG_ID)
				  FROM   AIS_RSA.LOG L_
				  WHERE
					 (L_.PACKAGE_ID = PackID)));
	    EXCEPTION
	      WHEN NO_DATA_FOUND THEN
		Msg :=NULL;
	    END;

	    IF Msg IS NULL THEN
	      Msg :='Internal gateway error!';
	    END IF;

	    RAISE_APPLICATION_ERROR(-20000, Msg);
	  ELSE
	    SELECT P.STATUS_MSG, P.RSA_STATUS_ID
	    INTO   Msg, ID
	    FROM   AIS_RSA.PACKAGES P
	    WHERE
		   (P.PACKAGE_ID = PackID);

	    IF ID < 0 THEN
	      BEGIN
		SELECT L.MESSAGE
		INTO   Msg
		FROM   AIS_RSA.LOG L
		WHERE
		       (L.LOG_ID = (SELECT MAX(L_.LOG_ID)
			 	    FROM   AIS_RSA.LOG L_
			 	    WHERE
					   (L_.PACKAGE_ID = PackID)));
	      EXCEPTION
		WHEN NO_DATA_FOUND THEN
		  Msg :=NULL;
	      END;

	      IF Msg IS NULL THEN
		Msg :='Internal gateway error!!';
	      END IF;

	      RAISE_APPLICATION_ERROR(-20000, Msg);
	    ELSIF ID NOT IN (613, 612, 20, 21, 3) THEN
	      RAISE_APPLICATION_ERROR(-20000, Msg);
	    END IF;
	  END IF;

	  :I :=PackID;
	  :Err :=NULL;
	EXCEPTION
	  WHEN OTHERS THEN
	    IF PackID IS NOT NULL THEN
	      AIS_RSA.PKG_CORE.Drop_Package(PackID);
	    END IF;
--	    :Err :='Запрос КБМ: '||SQLERRM||' ('||PackID||')';
	    :Err :='Запрос КБМ: '||SQLERRM;
	    :I :=NULL;
	END;
      ";
//die($sql_txt);

      $sql_txt = str_replace(chr(13), " ", $sql_txt);
      $sql = oci_parse($conn, $sql_txt);

      if (!oci_bind_by_name($sql, ":I", $id, 50, SQLT_INT) || !oci_bind_by_name($sql, ":Err", $msg, 2000)) {
	$msg = "Запрос КБМ: oci_bind_by_name fault!";
	$err = 1;
      }
      else {
	oci_execute($sql, OCI_DEFAULT);
      }

      if (strlen($msg) > 0) {
       $err = 1;
       oci_close($conn);
      }
      else {
	$sql_txt = "
	SELECT C.POLICY_KBM, C.POL_SER, C.POL_NUM, C.INSURER_NAME,
	       REPLACE(TO_CHAR(C.POLICY_KBM_VALUE), ',', '.') AS POLICY_KBM_VALUE,
	       P.RSA_ID AS KBM_ID,
	       ".((($lim == 0) && ($type != 1))?"TRIM(TO_CHAR(SUBSTR(P.RESP_XML, INSTR(UPPER(P.RESP_XML), '<LOSSAMOUNT>')+12, INSTR(UPPER(P.RESP_XML), '</LOSSAMOUNT>')-INSTR(UPPER(P.RESP_XML), '<LOSSAMOUNT>')-12)))":"NULL")." AS POLICY_LOSS
	FROM   AIS_RSA.A_CALC C,
	       AIS_RSA.PACKAGES P
	WHERE
	       (C.PACKAGE_ID = ".$id.") AND
	       (UPPER(C.REQUEST_TYPE) = 'KBM') AND
	       (C.PACKAGE_ID = P.PACKAGE_ID)
	";

	//die($sql_txt);
	$sql_txt = str_replace(chr(13), " ", $sql_txt);
	$sql = oci_parse($conn, $sql_txt);
	oci_execute($sql, OCI_DEFAULT);

	if (oci_fetch($sql)) {
	 $kbm = oci_result($sql, "POLICY_KBM");
	 $kbm_val = oci_result($sql, "POLICY_KBM_VALUE");
	 $prev_pol_info = "№".oci_result($sql, "POL_SER")." ".oci_result($sql, "POL_NUM").", СК ".oci_result($sql, "INSURER_NAME");
	 $kbm_id = oci_result($sql, "KBM_ID");
	 $loss = oci_result($sql, "POLICY_LOSS");
	}
	else {
	  $msg = "Запрос КБМ: fetch error!";
	  $err = 1;
	  oci_close($conn);
	}
      }

      if (($err != 1) && ($drv_count > 0)) {
       $sql_txt = "
        SELECT LP.FIO, TO_CHAR(LP.DATE_BIRTH, 'DD.MM.YYYY') AS BIRTH, D.KBM_NEXT_LEVEL AS KBM, D.KBM_FIRST_LEVEL AS KBM_FIRST,
	       REPLACE(TO_CHAR(D.KBM_VALUE), ',', '.') AS KBM_VALUE, D.LOSS_AMOUNT AS LOSS
        FROM   AIS_RSA.A_DRIVER D,
	       AIS_RSA.L_PHYSICAL_PERSON LP
        WHERE
	       (D.PACKAGE_ID = ".$id.") AND
	       (D.L_PHYSICAL_PERSON_ID = LP.L_PHYSICAL_PERSON_ID)
       ";

       $sql_txt = str_replace(chr(13), " ", $sql_txt);
       $sql = oci_parse($conn, $sql_txt);
       oci_execute($sql, OCI_DEFAULT);

       $err = 1;
       while (oci_fetch($sql)) {
	$i = 0;
	$err = 0;
	while ($i < $drv_count) {
	  $f = oci_result($sql, "FIO");
	  $b = oci_result($sql, "BIRTH");
	  if (($drv[$i]["fio"] == $f) && ($drv[$i]["birth"] == $b)) {
	   $drv[$i]["kbm"] = oci_result($sql, "KBM");
	   $drv[$i]["kbm_first"] = oci_result($sql, "KBM_FIRST");
	   $drv[$i]["kbm_val"] = oci_result($sql, "KBM_VALUE");
	   $drv[$i]["loss"] = oci_result($sql, "LOSS");
	   break;
	  }
	  $i++;
	}
       }
       if ($err == 1) {
	oci_close($conn);
	$msg = "Запрос КБМ: fetch error!!";
       }
      }
    }

    // Request TO info
    if (($type != 0) && ($err == 0)) {
      $sql_txt = "
	DECLARE
	  ID NUMBER;
	BEGIN
	  ID :=AIS_RSA.Get_To_Auto(
		 ".(($lic == "<не указан>")?"NULL":"'".$lic."'").",
		 ".(($vin == "<не указан>")?"NULL":"'".$vin."'").",
		 ".(($body_num == "<не указан>")?"NULL":"'".$body_num."'").",
		 ".(($chassis_num == "<не указан>")?"NULL":"'".$chassis_num."'")."
	  );

	  IF ID IS NULL THEN
	    RAISE_APPLICATION_ERROR(-20000, 'Internal server error!');
	  END IF;
	  
	  :I :=ID;
	  :Err :=NULL;
	EXCEPTION
	  WHEN OTHERS THEN
	    :Err :='Запрос ТО: '||SQLERRM;
	    :I :=NULL;
	END;
      ";
      
      //die($sql_txt);
      $sql_txt = str_replace(chr(13), " ", $sql_txt);
      $sql = oci_parse($conn, $sql_txt);

      if (!oci_bind_by_name($sql, ":I", $id, 50, SQLT_INT) || !oci_bind_by_name($sql, ":Err", $msg, 2000)) {
	$msg = "Запрос ТО: oci_bind_by_name fault!";
	$err = 1;
      }
      else {
	oci_execute($sql, OCI_DEFAULT);
      }

      if (strlen($msg) > 0) {
       $err = 1;
       oci_close($conn);
      }
      else {
       $sql_txt = "
	SELECT C.TICKET_EXISTED, TO_CHAR(C.DATE_NEXT_TO, 'DD.MM.YYYY') AS DATE_NEXT_TO, C.TICKET_SER, C.TICKET_NUM, TO_CHAR(C.TICKET_DIAG_DATE, 'DD.MM.YYYY') AS TICKET_DATE,
	       (SELECT P.RSA_ID
	        FROM   AIS_RSA.PACKAGES P
	        WHERE
	               (P.PACKAGE_ID = C.PACKAGE_ID)) AS TO_ID
	FROM   AIS_RSA.A_CALC C
	WHERE
	       (C.A_CALC_ID = ".$id.")
       ";

       $sql_txt = str_replace(chr(13), " ", $sql_txt);
       $sql = oci_parse($conn, $sql_txt);
       oci_execute($sql, OCI_DEFAULT);

       if (!oci_fetch($sql)) {
	 $err = 1;
	 $msg = "Запрос ТО: fetch error!";
       }
       else {
	 $ticket_exists = oci_result($sql, "TICKET_EXISTED");
	 $date_next_to = oci_result($sql, "DATE_NEXT_TO");

	 $ticket_num = oci_result($sql, "TICKET_SER")." ".oci_result($sql, "TICKET_NUM");
	 $ticket_date = oci_result($sql, "TICKET_DATE");
	 $to_id = oci_result($sql, "TO_ID");
       }
      }
    }

    // Close ORACLE connection
    if ($err == 0) {
      oci_close($conn);
    }
  }

  // Create result
  if ($err == 0) {
    $html .= "
      <TABLE cols=3 width=610px border=0 valign=top align=left cellpadding=0 cellspacing=0>
	<TR>
	  <TD width=250px><A href=\"javascript:window.print()\" title=\"Напечатать страницу\"><IMG src=\"images/print_printer.png\" alt=\"Print\" height=20px width=20px></A></TD>
	  <TD width=80px>&nbsp;</TD>
	  <TD width=280px>&nbsp;</TD>
	</TR>
	<TR>
	  <TD colspan=3 align=left valign=top>
	    <LABEL>
	      <B>Дата запроса: </B>".$date_q."&nbsp;&nbsp;&nbsp;
	      <B>Дата начала договора: </B>".$rep_date."&nbsp;&nbsp;&nbsp;
	      <B>Тип запроса: </B>".$ts."
	    </LABEL>
	  </TD>
	</TR>
	<TR>
	  <TD colspan=3 align=left valign=top>
	    <LABEL><B>Идентификатор запроса КБМ/ТО: </B>".$kbm_id."/".$to_id."</LABEL>
	  </TD>
	</TR>
	<TR>
	  <TD colspan=3 align=left valign=top>
	    <LABEL><B>Неограниченное кол-во лиц, допущенных к управлению: </B>".$l."</LABEL>
	  </TD>
	</TR>
	<TR>
	  <TD>&nbsp;</TD>
	</TR>
    ";

    if (($type != 1) && ($lim == 0)) {
      $html .= "
	<TR>
	  <TD colspan=3 align=left valign=top>
	    <LABEL><B>Собственник ТС: </B>".iconv("cp1251","UTF-8", $own_name)." (".(($is_phy == 1)?"д.р. ".$own_birth:"ИНН ".$own_inn).")</LABEL>
	  </TD>
	</TR>
	<TR>
	  <TD>&nbsp;</TD>
	</TR>
      ";
    }

    if (($type != 0) || ($lim == 0)) {
      $html .= "
	<TR>
	  <TD align=left valign=top>
	    <LABEL><B>Транспортное средство</B></LABEL><BR>
	    <LABEL>VIN: ".($_POST["vin"] ? $_POST["vin"] : '<не указан>')."</LABEL>
	  </TD>
	  <TD colspan=2 align=left valign=top>
	    <LABEL><B>&nbsp;</B></LABEL><BR>
	    <LABEL>Гос.номер: ".($_POST["lic_plate"] ? $_POST["lic_plate"] : '<не указан>')."</LABEL>
	  </TD>
	</TR>
	<TR>
	  <TD align=left valign=top>
	    <LABEL>Номер кузова: ".($_POST["body_num"] ? $_POST["body_num"] : '<не указан>')."</LABEL>
	  </TD>
	  <TD colspan=2 align=left valign=top>
	    <LABEL>Номер шасси: ".($_POST["chassis_num"] ? $_POST["chassis_num"] : '<не указан>')."</LABEL>
	  </TD>
	</TR>
	<TR>
	  <TD>&nbsp;</TD>
	</TR>
      ";
    }

    if ($type != 1) {
      $html .= "
	<TR>
	  <TD align=left valign=top>
	    <LABEL><B>КБМ полиса: </B>".isnull($kbm, "&nbsp;", 0)."</LABEL><BR>
	  </TD>
	  <TD colspan=2 align=left valign=top>
	    <LABEL><B>Значение КБМ полиса: </B></LABEL>
	    <LABEL>".isnull($kbm_val, "&nbsp;", 0)."</LABEL>
	  </TD>
	</TR>
      ";

      if ($lim == 1) {
	$html .= "
	<TR>
	  <TD colspan=3 align=left valign=top>
	    <LABEL><B>Предыдущий полис: </B></LABEL>
	    <LABEL>".$prev_pol_info."</LABEL>
	  </TD>
	</TR>
	<TR>
	  <TD>&nbsp;</TD>
	</TR>
	";
      }
      else {
	$html .= "
	<TR>
	  <TD colspan=2 align=left valign=top>
	    <LABEL><B>Предыдущий полис: </B></LABEL>
	    <LABEL>".$prev_pol_info."</LABEL>
	  </TD>
	  <TD align=left valign=top>
	    <LABEL><B>Кол-во убытков: </B>".$loss."</LABEL>
	  </TD>
	</TR>
	<TR>
	  <TD>&nbsp;</TD>
	</TR>
	";

      }
    }

    if ($type != 0) {
      if ($ticket_exists == 1) {
	$te = "Есть";
      }
      else if ($ticket_exists == 0) {
	$te = "Нет";
      }
      else {
	$te = "&nbsp;";
      }
      
      $html .= "
	<TR>
	  <TD align=left valign=top>
	    <LABEL><B>Действующий талон ТО: </B>".$te."</LABEL><BR>
	    <LABEL>".(($ticket_exists == 1)?" (№".$ticket_num.(($ticket_date == null)?"&nbsp;":" от ".$ticket_date).")":"&nbsp;")."</LABEL>
	  </TD>
	  <TD colspan=2 align=left valign=top>
	    <LABEL><B>Дата следующего прохождения ТО: </B></LABEL><BR>
	    <LABEL>".isnull($date_next_to, "&nbsp;", 0)."</LABEL>
	  </TD>
	</TR>
	<TR>
	  <TD>&nbsp;</TD>
	</TR>
      ";
    }

    if ($drv_count > 0) {
      $html .= "
	<TR>
	  <TD align=left valign=top>
	    <LABEL><B>Допущенные к управлению ТС</B></LABEL>
	  </TD>
	</TR>
	<TR>
	  <TD colspan=3 align=left valign=top>
	    <TABLE cols=5 width=610px border=1 align=left valign=top cellpadding=0 cellspacing=0>
	      <TR>
		<TD class=drv rowspan=2 width=200px valign=middle align=center>
		  <LABEL>Ф.И.О.</LABEL>
		</TD>
		<TD class=drv rowspan=2 width=70px valign=middle align=center>
		  <LABEL>Дата рождения</LABEL>
		</TD>
		<TD class=drv colspan=2 width=140px valign=middle align=center>
		  <LABEL>Водительское удостоверение</LABEL>
		</TD>
		<TD class=drv colspan=3 width=150px valign=middle align=center>
		  <LABEL>КБМ</LABEL>
		</TD>
		<TD class=drv rowspan=2 width=50px valign=middle align=center>
		  <LABEL>Кол-во убытков</LABEL>
		</TD>
	      </TR>
	      <TR>
		<TD class=drv width=60px valign=middle align=center>
		  <LABEL>Серия</LABEL>
		</TD>
		<TD class=drv width=80px valign=middle align=center>
		  <LABEL>Номер</LABEL>
		</TD>
		<TD class=drv width=50px valign=middle align=center>
		  <LABEL>Предыдущий</LABEL>
		</TD>
		<TD class=drv width=50px valign=middle align=center>
		  <LABEL>Текущий</LABEL>
		</TD>
		<TD class=drv width=50px valign=middle align=center>
		  <LABEL>Значение</LABEL>
		</TD>
	      </TR>
      ";

      $i = 0;
      while ($i < $drv_count) {
	$html .= "
	      <TR>
		<TD class=drv valign=top align=left>
		  <LABEL>".iconv("cp1251","UTF-8", $drv[$i]["fio"])."</LABEL>
		</TD>
		<TD class=drv valign=top align=center>
		  <LABEL>".isnull($drv[$i]["birth"], "&nbsp;", 0)."</LABEL>
		</TD>
		<TD class=drv valign=top align=left>
		  <LABEL>".iconv("cp1251","UTF-8", $drv[$i]["ser"])."</LABEL>
		</TD>
		<TD class=drv valign=top align=left>
		  <LABEL>".isnull($drv[$i]["num"], "&nbsp;", 0)."</LABEL>
		</TD>
		<TD class=drv valign=top align=center>
		  <LABEL>".isnull($drv[$i]["kbm_first"], "&nbsp;", 0)."</LABEL>
		</TD>
		<TD class=drv valign=top align=center>
		  <LABEL>".isnull($drv[$i]["kbm"], "&nbsp;", 0)."</LABEL>
		</TD>
		<TD class=drv valign=top align=center>
		  <LABEL>".isnull($drv[$i]["kbm_val"], "&nbsp;", 0)."</LABEL>
		</TD>
		<TD class=drv valign=top align=center>
		  <LABEL>".isnull($drv[$i]["loss"], "&nbsp;", 0)."</LABEL>
		</TD>
	      </TR>
	";
	$i++;
      }

      $html .= "
	    </TABLE>
	  </TD>
	</TR>
      ";
    }
    $html .= "
      </TABLE>
    ";
  }
  else
  {
   $html .= "
     <H1>Ошибка</H1><BR>
     <LABEL>".iconv("cp1251","UTF-8", $msg)."</LABEL>
   ";
  }

  $html .= '
	</TD>
      </TR>
    </TABLE>
  </BODY>
  <HEAD>
    <META http-equiv="Pragma" content="no-cache">
  </HEAD>
</HTML>  
  ';
//Пишем в сессию данные которые переносим на след шаг
/////////////////////////////////////////////////////////////////////
$_SESSION['kbm']['own_name'] = ($_POST["own_name"] ? trim($_POST["own_name"]) : '');
$_SESSION['kbm']['own_birth'] = ($_POST["own_birth"] ? trim($_POST["own_birth"]) : '');
$_SESSION['kbm']['own_doc'] = ($_POST["own_doc"] ? trim($_POST["own_doc"]) : '');
$_SESSION['kbm']['own_ser'] = ($_POST["own_ser"] ? trim($_POST["own_ser"]) : '');
$_SESSION['kbm']['own_num'] = ($_POST["own_num"] ? trim($_POST["own_num"]) : '');
$_SESSION['kbm']['own_inn'] = ($_POST["own_inn"] ? trim($_POST["own_inn"]) : '');
$_SESSION['kbm']['own_vin'] = ($_POST["vin"] ? trim($_POST["vin"]) : '');
$_SESSION['kbm']['body_num'] = ($_POST["body_num"] ? trim($_POST["body_num"]) : '');
$_SESSION['kbm']['lic_plate'] = ($_POST["lic_plate"] ? trim($_POST["lic_plate"]) : '');
$_SESSION['kbm']['chassis_num'] = ($_POST["chassis_num"] ? trim($_POST["chassis_num"]) : '');
$_SESSION['kbm']['kbm_id'] = trim($kbm_id);
$_SESSION['kbm']['to_id'] = trim($to_id);
$_SESSION['kbm']['to_num'] = trim($ticket_num);
$_SESSION['kbm']['to_next_date'] = trim($date_next_to);
//////////////////////////////////////////////////////////////////
//Output result code
print <<<HERE
$html
HERE;
?>