<?php

$header="<html><head><title>VPP - �szrev�telek</title>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1250\">\n<style TYPE=\"text/css\">\n.alap { font-family: Arial, Verdana; font-size: 10pt; text-align: justify; }\n.urlap { font-family: Arial, Verdana;  font-size: 70%; color: #000000; background-color: #FFFFFF; }\n</style>\n</head>\n<body bgcolor=\"#FFFFFF\" text=\"#000000\">";

$footer="</body></html>";

$kidob="<Script languate=javascript> close(); </script>";


include("config.inc");
dbconnect();
$op=$_POST['op'];

function teendok($id,$kod) {
	global $header,$footer,$kidob;

	$sid=$_GET['sid'];

	$query="select login,jogok from session where sessid='$sid'";
	$lekerdez=mysql_query($query);
	list($login,$jogok)=mysql_fetch_row($lekerdez);

	if($kod=='templomok') {
		$query="select nev,ismertnev,varos,letrehozta,megbizhato from templomok where id='$id'";
		if(!$lekerdez=mysql_query($query)) echo "HIBA!<br>$query<br>".mysql_error();
		list($nev,$ismertnev,$varos,$feltolto,$megbizhato)=mysql_fetch_row($lekerdez);
		if(!strstr($jogok,'miserend') and !($login==$feltolto and $megbizhato=='i')) {
			echo $header.$kidob.$footer;
			exit();
		}
		if(!empty($ismertnev)) $ismertnev="($ismertnev)";

		$bgcolor[0]='#F3E9CD';
		$bgcolor[1]='#EFEFEF';
	
		$kiir.="\n<table width=100% bgcolor=#F5CC4C><tr><td class=alap><big><b>$nev</b> $ismertnev - <u>$varos</u></big><br><i>Jav�t�sok, v�ltoz�sok bejelent�s�nek kezel�se</i></big></td></tr></table>";
		$kiir.="\n<table width=100% bgcolor=#ECD9A4 cellpadding=0 cellspacing=1>";
		$query="select id,nev,login,email,megbizhato,datum,allapot,admin,admindatum,leiras,adminmegj from eszrevetelek where hol='$kod' and hol_id='$id' order by datum desc";
		$lekerdez=mysql_query($query);
		while(list($eid,$enev,$elogin,$eemail,$emegbizhato,$edatum,$eallapot,$eadmin,$eadatum,$eleiras,$eadminmegj)=mysql_fetch_row($lekerdez)) {
			$a++;
			if($a>1) $a=0;
			$edatum=substr($edatum,0,16);
			$eadatum=substr($eadatum,0,16);
			$eleiras=nl2br($eleiras);
		
			if($eallapot=='u') $ikon="<img src=img/Folderdownloads.gif border=0 title='�j �szrev�tel'>";
			elseif($eallapot=='f') $ikon="<img src=img/Filesedit.gif border=0 title='folyamatban van -> $eadmin ($eadatum)'>";
			else $ikon="<img src=img/Hand.gif border=0 title='Jav�tva / lez�rva -> $eadmin ($eadatum)'>";

			$szoveg="<span class=alap><b>$enev ($elogin)</b></span>";
			if($emegbizhato=='n') $szoveg.="<span class=alap><font color=red><img src=img/tilos.gif align=absmidle hspace=2><b> NEM MEGB�ZHAT�!!!</b></font></span>";
			elseif($emegbizhato=='i') $szoveg.="<img src=img/pipa.png align=absmidle hspace=2 title='megb�zhat�'>";
			if(!empty($eemail)) $szoveg.="<br><a href=mailto:$eemail class=alap><b>$eemail</b></a>";
			if($eallapot!='j') $szoveg.="<br><br><span class=alap>$eleiras</span>";	
			else {
				$szoveg.="<br><span class=alap><font color=red>Utolj�ra jav�tva / lez�rva -> $eadmin ($eadatum)</font></span>";				
			}
			if(!empty($eadminmegj)) $szoveg.='<br><br><span class=alap><u>Admin megjegyz�s:</u><br>'.nl2br($eadminmegj).'</span>';

			$urlap="<form method=post><input type=hidden name=eid value=$eid><input type=hidden name=id value=$id><input type=hidden name=kod value=$kod><input type=hidden name=sid value=$sid><input type=hidden name=op value=mod>";
			$urlapT['u']="�j";
			$urlapT['f']="folyamatban";
			$urlapT['j']="jav�tva";
			$urlap.="<span class=urlap>�llapot: </span><select name=allapot class=urlap><option value=0>-----</option>";
			foreach($urlapT as $i=>$ertek) {
				if($i!=$eallapot) {
					$urlap.="<option value=$i>$ertek</option>";
				}
			}
			$urlap.="</select>";
			$urlap1T['?']='nem tudom';
			$urlap1T['i']='bek�ld� megb�zhat�';
			$urlap1T['n']='nem megb�zhat�';
			$urlap1T['e']='egy�b �szrev�tel';
			$urlap.="<br><select name=megbizhato class=urlap>";
			foreach($urlap1T as $i=>$ertek) {
				$urlap.="<option value=$i";
				if($i==$emegbizhato) $urlap.=" selected";
				$urlap.=">$ertek</option>";
			}
			$urlap.="</select>";
			if($eallapot!='j') $urlap.="<br><span class=urlap>Megjegyz�s a jav�t�shoz:</span><br><textarea name=adminmegj class=urlap cols=17 rows=5></textarea>";
			$urlap.="<input type=submit value=ok class=urlap></form>";

			$kiir.="<tr><td valign=top width=35 bgcolor=$bgcolor[$a]>$ikon</td><td valign=top width=125 bgcolor=$bgcolor[$a]><span class=alap>$edatum</span><br>$urlap</td><td valign=top bgcolor=$bgcolor[$a]>$szoveg</td></tr>";			
		}
		$kiir.="</table>";
	}
	elseif($kod=='hirek') {
		$query="select cim,datum,feltette,megbizhato from hirek where id='$id'";
		if(!$lekerdez=mysql_query($query)) echo "HIBA!<br>$query<br>".mysql_error();
		list($cim,$datum,$feltolto,$megbizhato)=mysql_fetch_row($lekerdez);
		if(!strstr($jogok,'hirek') and !($login==$feltolto and $megbizhato=='i')) {
			echo $header.$kidob.$footer;			
			exit();
		}

		$bgcolor[0]='#F3E9CD';
		$bgcolor[1]='#EFEFEF';
	
		$datum=substr($datum,0,10);
		$kiir.="\n<table width=100% bgcolor=#F5CC4C><tr><td class=alap><big><b>$cim</b></big> - $datum<br><i>Jav�t�sok, v�ltoz�sok bejelent�s�nek kezel�se</i></big></td></tr></table>";
		$kiir.="\n<table width=100% bgcolor=#ECD9A4 cellpadding=0 cellspacing=1>";
		$query="select id,nev,login,email,megbizhato,datum,allapot,admin,admindatum,leiras,adminmegj from eszrevetelek where hol='$kod' and hol_id='$id' order by datum desc";
		$lekerdez=mysql_query($query);
		while(list($eid,$enev,$elogin,$eemail,$emegbizhato,$edatum,$eallapot,$eadmin,$eadatum,$eleiras,$eadminmegj)=mysql_fetch_row($lekerdez)) {
			$a++;
			if($a>1) $a=0;
			$edatum=substr($edatum,0,16);
			$eadatum=substr($eadatum,0,16);
			$eleiras=nl2br($eleiras);
		
			if($eallapot=='u') $ikon="<img src=img/Folderdownloads.gif border=0 title='�j �szrev�tel'>";
			elseif($eallapot=='f') $ikon="<img src=img/Filesedit.gif border=0 title='folyamatban van -> $eadmin ($eadatum)'>";
			else $ikon="<img src=img/Hand.gif border=0 title='Jav�tva / lez�rva -> $eadmin ($eadatum)'>";

			$szoveg="<span class=alap><b>$enev ($elogin)</b></span>";
			if($emegbizhato=='n') $szoveg.="<span class=alap><font color=red><img src=img/tilos.gif align=absmidle hspace=2><b> NEM MEGB�ZHAT�!!!</b></font></span>";
			elseif($emegbizhato=='i') $szoveg.="<img src=img/pipa.png align=absmidle hspace=2 title='megb�zhat�'>";
			if(!empty($eemail)) $szoveg.="<br><a href=mailto:$eemail class=alap><b>$eemail</b></a>";
			if($eallapot!='j') $szoveg.="<br><br><span class=alap>$eleiras</span>";	
			else {
				$szoveg.="<br><span class=alap><font color=red>Utolj�ra jav�tva / lez�rva -> $eadmin ($eadatum)</font></span>";				
			}
			if(!empty($eadminmegj)) $szoveg.='<br><br><span class=alap><u>Admin megjegyz�s:</u><br>'.nl2br($eadminmegj).'</span>';

			$urlap="<form method=post><input type=hidden name=eid value=$eid><input type=hidden name=id value=$id><input type=hidden name=kod value=$kod><input type=hidden name=sid value=$sid><input type=hidden name=op value=mod>";
			$urlapT['u']="�j";
			$urlapT['f']="folyamatban";
			$urlapT['j']="jav�tva";
			$urlap.="<span class=urlap>�llapot: </span><select name=allapot class=urlap><option value=0>-----</option>";
			foreach($urlapT as $i=>$ertek) {
				if($i!=$eallapot) {
					$urlap.="<option value=$i>$ertek</option>";
				}
			}
			$urlap.="</select>";
			$urlap1T['?']='nem tudom';
			$urlap1T['i']='bek�ld� megb�zhat�';
			$urlap1T['n']='nem megb�zhat�';
			$urlap1T['e']='egy�b �szrev�tel';
			$urlap.="<br><select name=megbizhato class=urlap>";
			foreach($urlap1T as $i=>$ertek) {
				$urlap.="<option value=$i";
				if($i==$emegbizhato) $urlap.=" selected";
				$urlap.=">$ertek</option>";
			}
			$urlap.="</select>";
			if($eallapot!='j') $urlap.="<br><span class=urlap>Megjegyz�s a jav�t�shoz:</span><br><textarea name=adminmegj class=urlap cols=17 rows=5></textarea>";
			$urlap.="<input type=submit value=ok class=urlap></form>";

			$kiir.="<tr><td valign=top width=35 bgcolor=$bgcolor[$a]>$ikon</td><td valign=top width=125 bgcolor=$bgcolor[$a]><span class=alap>$edatum</span><br>$urlap</td><td valign=top bgcolor=$bgcolor[$a]>$szoveg</td></tr>";			
		}
		$kiir.="</table>";
	}

	echo $header.$kiir.$footer;
}

function mod() {
	global $header,$footer;

	$sid=$_POST['sid'];
	$id=$_POST['id'];
	$kod=$_POST['kod'];

	$allapot=$_POST['allapot'];
	$eid=$_POST['eid'];
	$adminmegj=$_POST['adminmegj'];
	$megbizhato=$_POST['megbizhato'];

	$query="select login,jogok from session where sessid='$sid'";
	$lekerdez=mysql_query($query);
	list($login,$jogok)=mysql_fetch_row($lekerdez);


	$most=date('Y-m-d H:i:s');
	$mostkiir=date('Y-m-d H:i');

	list($elogin,$eemail,$emegbizhato,$amegj)=mysql_fetch_row(mysql_query("select login,email,megbizhato,adminmegj from eszrevetelek where id='$eid'"));

	if($adminmegj!='') {		
		if(!empty($amegj)) $amegj.="\n";
		$query="update eszrevetelek set adminmegj=\"$amegj<img src=img/edit.gif align=absmiddle title='$login ($mostkiir)'> $adminmegj \" where id='$eid'";
		if(!mysql_query($query)) echo "HIBA!<br>".mysql_error();
	}

	if($emegbizhato!=$megbizhato) {
		//A megb�zhat�s�got az �sszes bek�ld�s�n�l �t�ll�tjuk
		if(!empty($eemail) and strlen($eemail)>7) {			
			$query="select distinct(login) from eszrevetelek where email='$eemail' and login!='*vendeg*'";
			$lekerdez=mysql_query($query);
			$vanlogin=false;
			while(list($loginok)=mysql_fetch_row($lekerdez)) {
				$loginokT[]="login='$loginok'";
				if($loginok==$elogin) {
					$vanlogin=true;
				}
			}
		}
		if($elogin!='*vendeg*') {
			$query="select distinct(email) from eszrevetelek where login='$elogin' and email!=''";
			$lekerdez=mysql_query($query);
			$vanemail=false;
			while(list($emailek)=mysql_fetch_row($lekerdez)) {
				$emailekT[]="email='$emailek'";
				if($emailek==$eemail) $vanemail=true;
			}
		}
		if(!$vanemail and !empty($eemail) and strlen($eemail)>7) $emailekT[]="email='$eemail'";
		if(!$vanlogin and $elogin!='*vendeg*') $loginokT[]="login='$elogin'";
		if(is_array($emailekT)) {
			$feltetel.=' or '.implode(' or ',$emailekT);
		}
		if(is_array($loginokT)) {
			$feltetel.=' or '.implode(' or ',$loginokT);
		}
		$query="update eszrevetelek set megbizhato='$megbizhato' where id='$eid' $feltetel";
		mysql_query($query);
	}

	if($allapot!='0') {	
		$query="update eszrevetelek set admin='$login', admindatum='$most', allapot='$allapot' $adminmegjegyzes where id='$eid'";
		mysql_query($query);

		if($allapot=='u') $allapot1='i';
		elseif($allapot=='f') $allapot1='f';
		elseif($allapot=='j') $allapot1='n';

		$query="select id from eszrevetelek where hol='$kod' and hol_id='$id' and allapot='u'";
		$lekerdez=mysql_query($query);
		if(mysql_num_rows($lekerdez)==0) {
			$query="update $kod set eszrevetel='$allapot1' where id='$id'";
			mysql_query($query);
		}
		elseif($allapot=='u') {
			$query="update $kod set eszrevetel='$allapot1' where id='$id'";
			mysql_query($query);
		}
	}

	teendok($id,$kod);
}

switch($op) {
    default:
		$id=$_GET['id'];
		$kod=$_GET['kod'];
        teendok($id,$kod);
        break;

	case 'mod':
		mod();
        break;
}


?>
