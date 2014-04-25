<?php

function reklam_index() {
	global $linkveg,$m_id;

	$menu.="<a href=?m_id=$m_id&m_op=add$linkveg class=kismenulink>�j rekl�m - hozz�ad�s</a><br>";
	$menu.="<a href=?m_id=$m_id&m_op=mod$linkveg class=kismenulink>Megl�v� m�dos�t�sa, t�rl�se</a><br>";

	$adatT[2]="<span class=alcim>Rekl�mok (log�k) szerkeszt�se</span><br><br>".$menu;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function reklam_add($rid) {
	global $sessid,$m_id,$db_name;

	if($rid>0) {
		$query="select cim,url,title,hol,tol,ig,szamlalo,klikk,ok,log,sorszam from reklam where id='$rid'";
		list($cim,$url,$title,$hol,$tol,$ig,$szamlalo,$klikk,$ok,$log,$sorszam)=mysql_fetch_row(mysql_db_query($db_name,$query));
	}
	else {
		$tol=date('Y-m-d');
		$egyho=60*60*24*30;
		$ig=date('Y-m-d',time()+$egyho);
	}

	$urlap="\n<form method=post ENCTYPE='multipart/form-data'>";
	$urlap.="\n<input type=hidden name=m_id value=$m_id><input type=hidden name=sessid value=$sessid>";
	$urlap.="\n<input type=hidden name=m_op value=adding><input type=hidden name=rid value=$rid>";
	
	$urlap.="\n<span class=kiscim>C�m: </span><span class=alap><br>Csak az adminban jelenik meg, a kereshet�s�g miatt.</span><br><input type=text name=cim value='$cim' class=urlap size=60 maxlength=100>";
	$urlap.="\n<br><br><span class=kiscim>K�pfelirat: </span><span class=alap><br>(nem k�telez�)</span><br><input type=text name=title value='$title' class=urlap size=60 maxlength=100>";
	$urlap.="\n<br><br><span class=kiscim>URL: </span><span class=alap><br>Klikkel�sre hova menjen. Ha m�s weboldal, akkor http:// -rel kezd�dj�n a hivatkoz�s, <br>ha oldalon bel�li link, akkor ez nem kell!</span><br><input type=text name=url value='$url' class=urlap size=60 maxlength=100>";
	$urlap.="\n<br><br><span class=kiscim>t�l:</span><span class=alap><br>Amikort�l megjelenhet</span><br><input type=text name=tol class=urlap maxlength=10 value='$tol' size=10>";
	$urlap.="\n<br><br><span class=kiscim>ig:</span><span class=alap><br>Ameddig megjelenhet (ha nulla, akkor nincs lej�rat!)</span><br><input type=text name=ig class=urlap maxlength=10 value='$ig' size=10>";

	$urlap.="\n<br><br><span class=kiscim>Sorsz�m:</span><span class=alap><br>Ha van, akkor sorrendben mindenk�pp megjelenik, ha nincs, akkor el�z�ek ut�n v�letlenszer�en maximum 5 db (csak alul �rv�nyes)</span><br><input type=text name=sorszam class=urlap maxlength=2 value='$sorszam' size=2>";
	
	$urlap.="<br><br><input type=checkbox name=ok value=i ";
	if($ok!='n') $urlap.=' checked';
	$urlap.="> <span class=alap>enged�lyez�s</span>";
	$urlap.="\n<br><br><span class=kiscim>Hol: </span><br><select name=hol class=urlap>";
	$holnev=array('','F�rekl�m (jobb fent)','Log� rekl�mok (jobb lent)');
	for($i=1;$i<=2;$i++) {
		$urlap.="<option value=$i";
		if($i==$hol) $urlap.=" selected";
		$urlap.=">$holnev[$i]</option>";
	}
	$urlap.='</select>';
	
	$urlap.="\n<br><br><span class=kiscim>K�p: </span><span class=alap><br>CSAK jpg vagy gif f�jlok!<br>Maximum 170�300-as k�pet jelen�t meg, az enn�l nagyobb k�pek NEM jelennek meg!</span><br><input type=file name=kep size=50 class=urlap>";

	//K�nyvt�r tartalm�t beolvassa
	if($rid>0) {
		$konyvtar="kepek/reklam/";
		$fajl1="$rid.jpg";
		$fajl2="$rid.gif";
		if(is_file("$konyvtar$fajl1")) {
			$valoban=$fajl1;
		}
		elseif(is_file("$konyvtar$fajl2")) {
			$valoban=$fajl2;
		}
		if(!empty($valoban))	$urlap.="<br><br><img src=$konyvtar$valoban><input type=checkbox class=urlap name=delkep value='$valoban'><span class=alap>T�r�l</span> ";
	}
	
	//Log
	if(!empty($log)) {
		$urlap.="\n<br><br><span class=kiscim>t�rt�net:</span><br><textarea cols=40 rows=8 disabled>Sz�ml�l�: $szamlalo\nKlikkel�s: $klikk\n$log</textarea>";
	}

	$urlap.="\n<br><br><input type=submit value=Mehet class=urlap>";
	$urlap.='</form>';

	$adatT[2]="<span class=alcim>Rekl�mok szerkeszt�se</span><br><br>".$urlap;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function reklam_adding() {
	global $_POST,$_FILES,$u_login,$db_name;

	$rid=$_POST['rid'];
	$cim=$_POST['cim'];
	$url=$_POST['url'];
	$title=$_POST['title'];
	$tol=$_POST['tol'];
	$ig=$_POST['ig'];
	$hol=$_POST['hol'];
	$sorszam=$_POST['sorszam'];
	$ok=$_POST['ok'];
	if($ok!='i') $ok='n';
	$most=date('Y-m-d H:i:s');

	if($rid>0) {
		$uj=false;
		$parameter1='update';
		list($log)=mysql_fetch_row(mysql_db_query($db_name,"select log from reklam where id='$rid'"));
		$ujlog=$log."\nMod: $u_login ($most)";
		$parameter2=", log='$ujlog' where id='$rid'";
	}
	else {
		$uj=true;
		$parameter1='insert';
		$parameter2=", log='Add: $u_login ($most)'";
	}

	$query="$parameter1 reklam set cim='$cim', url='$url', title='$title', tol='$tol', ig='$ig', hol='$hol', ok='$ok', sorszam='$sorszam' $parameter2";
	mysql_db_query($db_name,$query);
	if($uj) $rid=mysql_insert_id();

	$konyvtar="kepek/reklam/";

//kijel�lt k�pek t�rl�se
	$delkep=$_POST['delkep'];
	if(!empty($delkep)) unlink("$konyvtar/$delkep");

//k�pek felt�lt�se
	$kep=$_FILES['kep']['tmp_name'];
	$kepnev=$_FILES['kep']['name'];
	$kit=substr($kepnev,-3);
	if(!empty($kep)) {		
		$kimenet=$konyvtar.$rid.'.'.$kit;
		@unlink($konyvtar.$rid.".jpg");
		@unlink($konyvtar.$rid.".gif");
		if ( !copy($kep, "$kimenet") )	print("HIBA a m�sol�sn�l ($kimenet)!<br>\n");
		unlink($kep);
	}
	
	$kod=reklam_add($rid);

	return $kod;
}

function reklam_mod() {
	global $db_name,$linkveg,$m_id;

	$kiir.="<span class=kiscim>V�lassz az al�bbi rekl�mok k�z�l:</span><br><br>";

	$query="select id,cim,hol from reklam order by hol,id desc";
	$lekerdez=mysql_db_query($db_name,$query);
	while(list($rid,$cim,$hol)=mysql_fetch_row($lekerdez)) {
		if($hol!=$holell) {
			if($hol==1) $kiir.="<span class=kiscim>-----------------Fels� rekl�mok----------------------</span><br><br>";
			elseif($hol==2) $kiir.="<br><span class=kiscim>-----------------Als� rekl�mok----------------------</span><br><br>";
		}
		$holell=$hol;
		$kiir.="\n<a href=?m_id=$m_id&m_op=add&rid=$rid$linkveg class=link><b>- $cim</b></a> - <a href=?m_id=$m_id&m_op=del&rid=$rid$linkveg class=link><img src=img/del.jpg border=0 alt=T�r�l align=absmiddle> t�r�l</a><br>";
	}

	$adatT[2]="<span class=alcim>Rekl�mok szerkeszt�se - m�dos�t�s</span><br><br>".$kiir;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function reklam_del() {
	global $_GET,$db_name,$linkveg,$m_id;

	$rid=$_GET['rid'];

	$kiir="\n<span class=kiscim>Biztosan t�r�lni akarod a k�vetkez� rekl�mot?</span>";
	
	$query="select cim from reklam where id='$rid'";
	list($cim)=mysql_fetch_row(mysql_db_query($db_name,$query));

	$kiir.="\n<br><br><span class=alap>$cim</span>";

	$kiir.="<br><br><a href=?m_id=$m_id&m_op=delete&rid=$rid$linkveg class=link>Igen</a> - <a href=?m_id=$m_id&m_op=mod$linkveg class=link>NEM</a>";

	$adatT[2]="<span class=alcim>Rekl�m szerkeszt�se - t�rl�s</span><br><br>".$kiir;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function reklam_delete() {
	global $_GET,$db_name;

	$id=$_GET['rid'];
	$query="delete from reklam where id='$id'";
	mysql_db_query($db_name,$query);

	$kod=reklam_mod();

	return $kod;
}

//Jogosults�g ellen�rz�se
if(strstr($u_jogok,'reklam')) {

switch($m_op) {
    case 'index':
        $tartalom=reklam_index();
        break;

	case 'add':
		$rid=$_GET['rid'];
        $tartalom=reklam_add($rid);
        break;

    case 'mod':
        $tartalom=reklam_mod();
        break;

    case 'adding':
        $tartalom=reklam_adding();
        break;

    case 'del':
        $tartalom=reklam_del();
        break;

	case 'delete':
        $tartalom=reklam_delete();
        break;
}
}
else {
	$tartalom="\n<span class=hiba>HIBA! Nincs hozz� jogosults�god!</span>";
}
?>
