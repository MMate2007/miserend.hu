<?php

$head='<html><head><title>VPP - H�rporta</title><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2"><link rel="stylesheet" href="design/alap/img/style.css" type="text/css"></head><body bgcolor="#FFFFFF" text="#000000">';

$head1='<html><head><title>VPP - H�rporta</title><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2"><meta http-equiv="refresh" content="1;URL=http://www.hirporta.hu/send.php?op=bezar"><link rel="stylesheet" href="design/alap/img/style.css" type="text/css"></head><body bgcolor="#FFFFFF" text="#000000">';

$foot='</body></html>';

include_once('config.inc');
dbconnect();

function index($uzenet) {
	global $db_name,$_GET,$_POST,$head,$foot;

	$id=$_POST['id'];
	if(empty($id)) $id=$_GET['id'];
	$cimemail=$_POST['cimemail'];
	$cimnev=$_POST['cimnev'];
	$kuldemail=$_POST['kuldemail'];
	$kuldnev=$_POST['kuldnev'];
	$kuzenet=$_POST['kuzenet'];
	

	if($id<1 or !is_numeric($id)) {
		echo "<html><body><script><!-- JavaScript k�d elrejt�se \n close(); \n // --></SCRIPT></body></html>";
		exit;
	}
	echo $head;
	if(!empty($uzenet)) echo "<span class=alap><font color=red>$uzenet</font></span>";
    echo "<table width=450 border=0 cellpadding=0 cellspacing=0><tr><td valign=top>";

     echo "<table width=100% border=0 cellpadding=4 cellspacing=4>";
      echo "<tr><td colspan=2><span class=alcim>";
      echo "H�r tov�bbk�ld�se:</span>";
	  if(!$lekerdez=mysql_db_query($db_name,"select cim from hirek where id='$id' and ok='i'")) echo "HIBA!<br>$query<br>".mysql_error();
	  list($cim)=mysql_fetch_row($lekerdez);
	  
	  if(empty($cim)) {
		  echo "<html><body><script><!-- JavaScript k�d elrejt�se \n close(); \n // --></SCRIPT></body></html>";
		  exit;
	  }

	  echo "<br><span class=kiscim>$cim</span>";		  
	  echo '</td></tr>';
      echo "<form name=form1 method=post>";
      echo "<input type=hidden name=op value=sending>";
      echo "<input type=hidden name=id value=$id>";
      echo "<tr><td valign=top class=alap>C�mzett email c�me:</td><td><input type=text name=cimemail value='$cimemail' size=40></td></tr>";
      echo "<tr><td valign=top class=alap>C�mzett neve:</td><td><input type=text name=cimnev value='$cimnev' size=40></td></tr>";
      echo "<tr><td valign=top class=alap>Saj�t email c�med:</td><td><input type=text name=kuldemail value='$kuldemail' size=40></td></tr>";
      echo "<tr><td valign=top class=alap>A Te neved:</td><td><input type=text name=kuldnev value='$kuldnev' size=40></td></tr>";
      echo "<tr><td valign=top class=alap>�zenet:<br>Ha k�v�nsz valamit �zenni</td><td><textarea name=kuzenet cols=30 rows=4>$kuzenet</textarea></td></tr>";


      echo "<tr><td>&nbsp;</td><td><input type=submit value=K�ld�s class=urlap> &nbsp; <input type=reset value=T�r�l class=urlap></td></tr>";
      echo "</form>";
     echo "</td></tr></table>";

    echo "</td></tr></table>";
	echo $foot;
}

function sending() {
	global $_POST,$head1,$foot,$db_name;

	$hiba=false;

	$id=$_POST['id'];
	$cimemail=$_POST['cimemail'];
	$cimnev=$_POST['cimnev'];
	$kuldemail=$_POST['kuldemail'];
	$kuldnev=$_POST['kuldnev'];
	$kuzenet=$_POST['kuzenet'];

	if($id<1 or !is_numeric($id)) {
		echo "<html><body><script><!-- JavaScript k�d elrejt�se \n close(); \n // --></SCRIPT></body></html>";
		exit;
	}


	//Email ellen�rz�s
	$domain=strstr($cimemail,'@');
	$mennyi1=strlen($cimemail);
	$mennyi2=strlen($domain);
	$domain=substr($domain,1);
	
	if(!checkdnsrr($domain, "MX") and !checkdnsrr($domain, "A")) {
		$uzenet.="<br>- neml�tez� domian n�v";
		$hiba=true;
	}
	if(!strstr($cimemail,'@')) {
		$uzenet.="<br>- az emailc�mb�l hi�nyzik a @";
		$hiba=true;
	}
	if($mennyi1==$mennyi2) {
		$uzenet.="<br>- az emailc�mb�l hi�nyzik a @ el�tti r�sz";
		$hiba=true;
	}
	
	if(!$hiba) {
		$link="?hir=$id";

	    $to=$cimemail;
		$from=$kuldemail;
		if(empty($cimnev)) $cimnev='C�mzett';
		if(empty($kuldemail)) $from='web@hirporta.hu';
	    if(!empty($kuldnev)) $subj="$kuldnev �rtes�t�se a VPP - H�rporta honlapr�l";
		else $subj="�rtes�t�s a H�rporta honlapr�l";
	    $uzenet="Kedves $cimnev!\n\n";
		$uzenet.="Egy kedves ismer�s szeretn� felh�vni figyelmed";
	    $uzenet.="\na H�rporta honlapj�n (http://www.hirporta.hu)";
		$uzenet.="\ntal�lhat� h�rre!";
	    if($kuzenet!="")  $uzenet.="\n\n�zenet:\n----------------------\n$kuzenet\n----------------------\n";
		$uzenet.="\nL�togasd meg: http://www.hirporta.hu/$link";
	    $uzenet.="\n\nHasznos id�t�lt�st k�v�nunk oldalaink b�ng�sz�s�hez!\nVPP - H�rporta\nwww.hirporta.hu";

		mysql_db_query($db_name,"update hirek set send=send+1 where id='$id'");
		mail($to,$subj,$uzenet,"From:$from");
		echo $head1;
		echo "<p class=kiscim><br>A cikket tov�bb�tottuk!<br>K�sz�nj�k az aj�nl�st!</p>";
		echo $foot;
	}
	else {
			$uzenet="HIBA! Az emailc�m hib�s, k�rlek ellen�rizd!".$uzenet;
			index($uzenet);	
	}
}

function bezar() {
	echo "<html><body><script><!-- JavaScript k�d elrejt�se \n close(); \n // --></SCRIPT></body></html>";
}

$op=$_POST['op'];
if(empty($op)) $op=$_GET['op'];

switch($op) {
    case "sending":
        sending();
        break;

	case 'bezar':
		bezar();
		break;

    default:
        index($uzenet);
        break;
}

?>
