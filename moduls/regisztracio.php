<?php

function regisztracio_index() {
	global $linkveg,$m_id,$_GET,$db_name;

	$fm=$_GET['fm'];
	if($fm>0) {
		$query="select cim,leiras from fomenu where id='$fm'";
		$lekerdez=mysql_db_query($db_name,$query);
		list($cim,$leiras)=mysql_fetch_row($lekerdez);
		if(!empty($cim)) $tartalom.="<span class=alcim>$cim</span>";
		if(!empty($leiras)) $tartalom.=$leiras;
		else $tartalom.='<br><br>';
	}

	$tartalom.="<a href=?m_id=$m_id&m_op=add$linkveg class=kismenulink>Elolvastam, elfogadom, regisztr�lok - tov�bb</a><br>";
	$adatT[2]=$tartalom;
	$tipus='doboz';
	$kod.=formazo($adatT,$tipus);
	
	return $kod;
}

function regisztracio_atvett() {
	global $u_login,$linkveg,$m_id,$m_op;

	$tartalom.="\n<span class=alcim>Els� bel�p�s</span><br><br><span class=alap><b>Kedves $u_login!<br>Szeretettel k�sz�nt�nk meg�jult port�lunkon!</b><br><br>";
	$tartalom.="\nA Virtu�lis Pl�b�nia kor�bbi adatb�zis�b�l megtartottuk a felhaszn�l�neved, emailc�med �s ha be�rtad, akkor a neved. K�r�nk, hogy most az els� bel�p�s alkalm�val n�zd �t �j port�lunk regisztr�ci�s r�sz�t, ellen�rizd a megtartott adatokat, s ha j�nak l�tod, megadhatsz tov�bbi adatokat is, melyek megjelen�s�t is t�bbf�lek�ppen be�ll�thatod.<br><br><b>K�sz�nj�k, hogy id�t sz�nsz r�!</b></span><br><br>";

	$tartalom.="<a href=?m_id=$m_id&m_op=add$linkveg class=kismenulink>Tov�bb</a><br>";
	$adatT[2]=$tartalom;
	$tipus='doboz';
	$kod.=formazo($adatT,$tipus);

	return $kod;
}

function regisztracio_add() {
	global $sessid,$m_id,$db_name,$u_oldal,$u_beosztas,$u_id,$u_jogT,$u_id,$u_login;

	$optionT=array('0'=>'b�rki','i'=>'ismer�s','b'=>'bar�t','n'=>'senki');

	$urlap="\n<form method=post>";
	$urlap.="\n<input type=hidden name=m_id value=$m_id><input type=hidden name=sessid value=$sessid>";
	$urlap.="\n<input type=hidden name=m_op value=adding>";
	$urlap.="\n<table cellpadding=8 cellspacnig=1 bgcolor=#efefef>";

//Bejelentkez�si n�v	
	$urlap.="<tr><td valign=top bgcolor=#FFFFFF>";
	if($u_id>0) {
		$urlap.="\n<span class=kiscim>Bejelentkez�si n�v: </span><br><span class=alap>(Nem m�dos�that�!)</span><br><input type=text name=ulogin readonly value='$u_login' class=urlap size=20 maxlength=20>";

		$query="select email,becenev,nev,kontakt,szuldatum,nevnap,msn,skype,nem,csaladiallapot,foglalkozas,magamrol,vallas,orszag,varos,nyilvanos from user where uid='$u_id' and ok='i'";
		$lekerdez=mysql_db_query($db_name,$query);
		list($email,$becenev,$nev,$kontakt,$szuldatum,$nevnap,$msn,$skype,$nem,$csaladiallapot,$foglalkozas,$magamrol,$vallas,$orszag,$varos,$nyilvanos)=mysql_fetch_row($lekerdez);

		$urlap.="\n<br><br><span class=kiscim>Jelsz� (jelenlegi): </span><br><span class=alap>(FONTOS! Minden m�dos�t�shoz meg kell adni!)</span><br><input type=password name=oldjelszo class=urlap size=20 maxlength=20>";
		
		$urlap.="\n<br><br><span class=kiscim>�j jelsz�: </span><br><span class=alap>(Csak a jelenlegi m�dos�t�sa eset�n.)</span><br><input type=password name=ujjelszo1 class=urlap size=20 maxlength=20>";
		$urlap.="\n<br><br><span class=kiscim>�j jelsz� m�gegyszer: </span><br><input type=password name=ujjelszo2 class=urlap size=20 maxlength=20>";
	}
	else {
		$urlap.="\n<span class=kiscim>Bejelentkez�si n�v: </span><a href=\"javascript:OpenNewWindow('sugo.php?id=18',200,350);\"><img src=img/help.png border=0 title='S�g�' align=top></a><br><input type=text name=ulogin value='$ulogin' class=urlap size=20 maxlength=20";
		$urlap.="><br><span class=alap>(Lehet�s�g szerint �kezet �s speci�lis karakterek n�lk�l, maximum 20 bet�. Sz�k�z, id�z�jel �s aposztr�f NEM lehet benne! Ez a n�v azonos�t, ezzel tudsz majd bel�pni, de al�bb lehet�s�g van k�l�n becen�v megad�s�ra is.)</span>";
	}
	$urlap.="\n</td><td valign=top bgcolor=#FFFFFF><span class=alap>B�rki l�thatja!</td></tr>";

//Becen�v
	$urlap.="\n<tr><td valign=top bgcolor=#FFFFFF><span class=kiscim>Becen�v, megsz�l�t�s: </span><a href=\"javascript:OpenNewWindow('sugo.php?id=17',200,350);\"><img src=img/help.png border=0 title='S�g�' align=top></a><br><input type=text name=becenev class=urlap maxlength=100 size=40 value='$becenev'><br><span class=alap>(Ide keresztnevet, vagy becenevet c�lszer� �rni. Alapvet�en ezen a n�ven jelensz meg oldalunkon, az azonos�t�shoz mellette kicsiben jelezz�k a bejelentkez�si neved is.)</span>";

	$urlap.="\n</td><td valign=top bgcolor=#FFFFFF><span class=alap>B�rki l�thatja!</td></tr>";


//Email
	$urlap.="\n<tr><td valign=top bgcolor=#FFFFFF><span class=kiscim>Email c�m: </span><a href=\"javascript:OpenNewWindow('sugo.php?id=19',200,350);\"><img src=img/help.png border=0 title='S�g�' align=top></a><br><input type=text name=email class=urlap maxlength=100 size=40 value='$email'><br><span class=alap>(Erre a c�mre k�ldj�k ki a jelsz�t. A regisztr�ci�hoz sz�ks�ges egy val�s emailc�m! Elk�ld�s el�tt k�rj�k ellen�rizd!)</span>";

//Email2
	$urlap.="\n<br><br><span class=kiscim>Email c�m m�gegyszer: </span><br><input type=text name=email2 class=urlap maxlength=100 size=40 value='$email'>";

	$urlap.="\n</td><td valign=top bgcolor=#FFFFFF><span class=alap>L�thatja: </span><select name='nyilvanosT[email]' class=urlap>";
	foreach($optionT as $x=>$y) {
		$urlap.="<option value=$x";
		if(strstr($nyilvanos,"email-$x")) $urlap.=' selected';
		$urlap.=">$y</option>";
	}
	$urlap.="</select></td></tr>";

//N�v
	$urlap.="\n<tr><td valign=top bgcolor=#FFFFFF><span class=kiscim>N�v: </span><a href=\"javascript:OpenNewWindow('sugo.php?id=20',200,350);\"><img src=img/help.png border=0 title='S�g�' align=top></a><br><input type=text name=nev class=urlap maxlength=100 size=40 value='$nev'>";

	$urlap.="\n</td><td valign=top bgcolor=#FFFFFF><span class=alap>L�thatja: </span><select name='nyilvanosT[nev]' class=urlap>";
	foreach($optionT as $x=>$y) {
		$urlap.="<option value=$x";
		if(strstr($nyilvanos,"nev-$x")) $urlap.=' selected';
		$urlap.=">$y</option>";
	}
	$urlap.="</select></td></tr>";

//Nem
	$urlap.="\n<tr><td valign=top bgcolor=#FFFFFF><span class=kiscim>Nem: </span><br><input type=radio name=nem class=urlap value=f";
	if($nem=='f') $urlap.=" checked";
	$urlap.="><span class=alap>f�rfi</span> <input type=radio name=nem value=n class=urlap";
	if($nem=='n') $urlap.=" checked";
	$urlap.="><span class=alap>n�</span>";

	$urlap.="\n</td><td valign=top bgcolor=#FFFFFF><span class=alap>L�thatja: </span><select name='nyilvanosT[nem]' class=urlap>";
	foreach($optionT as $x=>$y) {
		$urlap.="<option value=$x";
		if(strstr($nyilvanos,"nev-$x")) $urlap.=' selected';
		$urlap.=">$y</option>";
	}
	$urlap.="</select></td></tr>";

//Bemutatkoz�s	
	$urlap.="\n<tr><td valign=top bgcolor=#FFFFFF><span class=kiscim>R�vid bemutatkoz�s: </span><a href=\"javascript:OpenNewWindow('sugo.php?id=21',200,350);\"><img src=img/help.png border=0 title='S�g�' align=top></a><br><span class=alap>(amennyiben sz�vesen megosztan�l valamit m�sokkal is)</span><br><textarea name=magamrol class=urlap cols=60 rows=8>$magamrol</textarea>";	

	$urlap.="\n</td><td valign=top bgcolor=#FFFFFF><span class=alap>L�thatja: </span><select name='nyilvanosT[magamrol]' class=urlap>";
	foreach($optionT as $x=>$y) {
		$urlap.="<option value=$x";
		if(strstr($nyilvanos,"magamrol-$x")) $urlap.=' selected';
		$urlap.=">$y</option>";
	}
	$urlap.="</select></td></tr>";

//Lakhely
	if(empty($orszag)) $orszag='Magyarorsz�g';
	$urlap.="\n<tr><td valign=top bgcolor=#FFFFFF><span class=kiscim>Lakhely: </span><a href=\"javascript:OpenNewWindow('sugo.php?id=26',200,430);\"><img src=img/help.png border=0 title='S�g�' align=top></a><br><span class=alap>- orsz�g: </span><select name=orszag class=urlap>";	
	$query="select nev from orszagok where ok='i' order by nev";
	$lekerdez=mysql_db_query($db_name,$query);
	while(list($onev)=mysql_fetch_row($lekerdez)) {
		$urlap.="<option value='$onev'";
		if($onev==$orszag) $urlap.=' selected';
		$urlap.=">$onev</option>";
	}
	$urlap.="</select><br><img src=img/space.gif width=5 height=8><br><span class=alap>- telep�l�s: </span><input type=text name=varos value='$varos' class=urlap size=40 maxlength=50><br><span class=alap>(K�rlek pontosan �s nagy kezd�bet�vel �rd be a telep�l�st!)</span>";

	$urlap.="\n</td><td valign=top bgcolor=#FFFFFF><span class=alap>L�thatja: </span><select name='nyilvanosT[orszag]' class=urlap>";
	foreach($optionT as $x=>$y) {
		$urlap.="<option value=$x";
		if(strstr($nyilvanos,"kontakt-$x")) $urlap.=' selected';
		$urlap.=">$y</option>";
	}
	$urlap.="</select><br><img src=img/space.gif width=5 height=8><br><select name='nyilvanosT[varos]' class=urlap>";
	foreach($optionT as $x=>$y) {
		$urlap.="<option value=$x";
		if(strstr($nyilvanos,"kontakt-$x")) $urlap.=' selected';
		$urlap.=">$y</option>";
	}
	$urlap.="</select></td></tr>";

//Kontakt
	$urlap.="\n<tr><td valign=top bgcolor=#FFFFFF><span class=kiscim>El�rhet�s�g: </span><a href=\"javascript:OpenNewWindow('sugo.php?id=22',200,350);\"><img src=img/help.png border=0 title='S�g�' align=top></a><br><span class=alap>(ha valamely el�rhet�s�ged szeretn�d megadni)</span><br><textarea name=kontakt class=urlap cols=60 rows=4>$kontakt</textarea>";	

	$urlap.="\n</td><td valign=top bgcolor=#FFFFFF><span class=alap>L�thatja: </span><select name='nyilvanosT[kontakt]' class=urlap>";
	foreach($optionT as $x=>$y) {
		$urlap.="<option value=$x";
		if(strstr($nyilvanos,"kontakt-$x")) $urlap.=' selected';
		$urlap.=">$y</option>";
	}
	$urlap.="</select></td></tr>";

//MSN, Skype
	$urlap.="\n<tr><td valign=top bgcolor=#FFFFFF><span class=kiscim>Internetes el�rhet�s�g: </span><a href=\"javascript:OpenNewWindow('sugo.php?id=28',200,350);\"><img src=img/help.png border=0 title='S�g�' align=top></a><br><span class=alap>- Skype (<a href=http://www.skype.hu/index2.php target=_blank class=link><small>www.skype.hu</small></a>) </span><input type=text name=skype class=urlap maxlength=50 size=20 value='$skype'>";
	$urlap.="<br><img src=img/space.gif width=5 height=7><br><span class=alap>- MSN Messenger (<a href=http://messenger.msn.com target=_blank class=link><small>messenger.msn.com</small></a>) </span><input type=text name=msn class=urlap maxlength=50 size=20 value='$msn'>";

	$urlap.="\n</td><td valign=top bgcolor=#FFFFFF><span class=alap>L�thatja: </span><select name='nyilvanosT[skype]' class=urlap>";
	foreach($optionT as $x=>$y) {
		$urlap.="<option value=$x";
		if(strstr($nyilvanos,"skype-$x")) $urlap.=' selected';
		$urlap.=">$y</option>";
	}
	$urlap.="</select><br><img src=img/space.gif width=5 height=7><br><select name='nyilvanosT[msn]' class=urlap>";
	foreach($optionT as $x=>$y) {
		$urlap.="<option value=$x";
		if(strstr($nyilvanos,"msn-$x")) $urlap.=' selected';
		$urlap.=">$y</option>";
	}
	$urlap.="</select></td></tr>";

//Foglalkoz�s
	$urlap.="\n<tr><td valign=top bgcolor=#FFFFFF><span class=kiscim>Foglalkoz�s: </span><a href=\"javascript:OpenNewWindow('sugo.php?id=23',200,350);\"><img src=img/help.png border=0 title='S�g�' align=top></a><br><input type=text name=foglalkozas class=urlap maxlength=100 size=40 value='$foglalkozas'>";

	$urlap.="\n</td><td valign=top bgcolor=#FFFFFF><span class=alap>L�thatja: </span><select name='nyilvanosT[foglalkozas]' class=urlap>";
	foreach($optionT as $x=>$y) {
		$urlap.="<option value=$x";
		if(strstr($nyilvanos,"foglalkozas-$x")) $urlap.=' selected';
		$urlap.=">$y</option>";
	}
	$urlap.="</select></td></tr>";

//Sz�let�snap
	if(empty($szuldatum)) $szuldatum='0000-00-00';
	$urlap.="\n<tr><td valign=top bgcolor=#FFFFFF><span class=kiscim>Sz�let�snap: </span><a href=\"javascript:OpenNewWindow('sugo.php?id=24',200,350);\"><img src=img/help.png border=0 title='S�g�' align=top></a><br><input type=text name=szuldatum class=urlap maxlength=10 size=10 value='$szuldatum'><br><span class=alap>(Fontos a form�tum: �v-h�nap-nap => 0000-00-00)</span>";

	$urlap.="\n</td><td valign=top bgcolor=#FFFFFF><span class=alap>L�thatja: </span><select name='nyilvanosT[szuldatum]' class=urlap>";
	foreach($optionT as $x=>$y) {
		$urlap.="<option value=$x";
		if(strstr($nyilvanos,"szuldatum-$x")) $urlap.=' selected';
		$urlap.=">$y</option>";
	}
	$urlap.="</select></td></tr>";

//N�vnap
	if(empty($nevnap)) $nevnap='00-00';
	$urlap.="\n<tr><td valign=top bgcolor=#FFFFFF><span class=kiscim>N�vnap: </span><a href=\"javascript:OpenNewWindow('sugo.php?id=24',200,350);\"><img src=img/help.png border=0 title='S�g�' align=top></a><br><input type=text name=nevnap class=urlap maxlength=5 size=5 value='$nevnap'><br><span class=alap>(Fontos a form�tum: h�nap-nap => 00-00)</span>";

	$urlap.="\n</td><td valign=top bgcolor=#FFFFFF><span class=alap>L�thatja: </span><select name='nyilvanosT[nevnap]' class=urlap>";
	foreach($optionT as $x=>$y) {
		$urlap.="<option value=$x";
		if(strstr($nyilvanos,"nevnap-$x")) $urlap.=' selected';
		$urlap.=">$y</option>";
	}
	$urlap.="</select></td></tr>";

//Csal�di �llapot
	$csaladiallapotT=array('titok','egyed�l�ll�', 'kapcsolatban', 'h�zas', 'elv�lt', '�zvegy', 'pap/szerzetes');
	$urlap.="\n<tr><td valign=top bgcolor=#FFFFFF><span class=kiscim>Csal�di �llapot: </span><a href=\"javascript:OpenNewWindow('sugo.php?id=25',200,350);\"><img src=img/help.png border=0 title='S�g�' align=top></a><br><select name=csaladiallapot class=urlap>";
	foreach($csaladiallapotT as $ertek) {
		$urlap.="<option value=$ertek";
		if($csaladiallapot==$ertek) $urlap.=' selected';
		$urlap.=">$ertek</option>";
	}
	$urlap.="</select>";

	$urlap.="\n</td><td valign=top bgcolor=#FFFFFF><span class=alap>L�thatja: </span><select name='nyilvanosT[csaladiallapot]' class=urlap>";
	foreach($optionT as $x=>$y) {
		$urlap.="<option value=$x";
		if(strstr($nyilvanos,"csaladiallapot-$x")) $urlap.=' selected';
		$urlap.=">$y</option>";
	}
	$urlap.="</select></td></tr>";

//Vall�s
	$csaladiallapotT=array('titok','r�mai katolikus', 'g�r�g katolikus', 'evang�likus', 'reform�tus', 'baptista', 'izraelita', 'g�r�g ortodox','p�nk�sdi','szabadkereszt�ny','Jehova tan�ja','Hit gy�lekezete','egy�b');
	$urlap.="\n<tr><td valign=top bgcolor=#FFFFFF><span class=kiscim>Vall�s: </span><a href=\"javascript:OpenNewWindow('sugo.php?id=27',200,350);\"><img src=img/help.png border=0 title='S�g�' align=top></a><br><select name=vallas class=urlap>";
	foreach($csaladiallapotT as $ertek) {
		$urlap.="<option value='$ertek'";
		if($vallas==$ertek) $urlap.=' selected';
		$urlap.=">$ertek</option>";
	}
	$urlap.="</select>";

	$urlap.="\n</td><td valign=top bgcolor=#FFFFFF><span class=alap>L�thatja: </span><select name='nyilvanosT[vallas]' class=urlap>";
	foreach($optionT as $x=>$y) {
		$urlap.="<option value=$x";
		if(strstr($nyilvanos,"vallas-$x")) $urlap.=' selected';
		$urlap.=">$y</option>";
	}
	$urlap.="</select></td></tr>";
	
	$urlap.="\n</table>";

	$urlap.='<br><br><input type=submit value=Mehet class=urlap></form>';

	if($u_id>0) $tartalom="<span class=alcim>Adatok m�dos�t�sa</span><br><br>".$urlap;
	else $tartalom="<span class=alcim>Regisztr�ci�</span><br><br>".$urlap;

	$adatT[2]=$tartalom;
	$tipus='doboz';
	$kod.=formazo($adatT,$tipus);

	return $kod;
}

function regisztracio_adding() {
	global $_POST,$_FILES,$_SERVER,$u_login,$db_name,$u_id,$sid;

	$ip=$_SERVER['REMOTE_ADDR'];
    $host = gethostbyaddr($ip);
	
	$nev=$_POST['nev'];
	$becenev=$_POST['becenev'];
	$email=$_POST['email'];
	$email2=$_POST['email2'];
	$oldjelszo=$_POST['oldjelszo'];
	$ujjelszo1=$_POST['ujjelszo1'];
	$ujjelszo2=$_POST['ujjelszo2'];
	$login=$_POST['ulogin'];
	$kontakt=$_POST['kontakt'];
	$szuldatum=$_POST['szuldatum'];
	$nevnap=$_POST['nevnap'];
	$nem=$_POST['nem'];
	if(empty($nem)) $nem=0;
	$magamrol=$_POST['magamrol'];
	$foglalkozas=$_POST['foglalkozas'];
	$vallas=$_POST['vallas'];
	$orszag=$_POST['orszag'];
	$varos=$_POST['varos'];
	$skype=$_POST['skype'];
	$msn=$_POST['msn'];
	$csaladiallapot=$_POST['csaladiallapot'];
	$nyilvanosT=$_POST['nyilvanosT'];
	if(is_array($nyilvanosT)) {
		foreach($nyilvanosT as $kulcs=>$ertek) {
			$nyilvanosT2[]="$kulcs-$ertek";
		}
	}
	if(is_array($nyilvanosT2)) $nyilvanos=implode('*',$nyilvanosT2);

	$most=date('Y-m-d H:i:s');
	$hiba=false;

	if($u_id>0) {
		//m�dos�t�s
		$query="select jelszo from user where uid='$u_id'";
		$lekerdez=mysql_db_query($db_name,$query);
		list($jelszo)=mysql_fetch_row($lekerdez);

		if($ujjelszo1!=$ujjelszo2) {
			$hiba=true;
			$hibauzenet.="<span class=hiba>HIBA! A megadott k�t jelsz� nem egyezik!</span><br>";
		}
		$oldjelszo=base64_encode($oldjelszo);
		if($oldjelszo!=$jelszo) {
			$hiba=true;
			$hibauzenet.="<span class=hiba>HIBA! A megadott jelsz� hib�s!</span><br>";
		}
		if(!$hiba) {
			if(!empty($ujjelszo1)) $ujjelszo=", jelszo='".base64_encode($ujjelszo1)."'";
			$query="update user set becenev='$becenev', nev='$nev', email='$email', kontakt='$kontakt', szuldatum='$szuldatum', nevnap='$nevnap', skype='$skype', msn='$msn', nem='$nem', csaladiallapot='$csaladiallapot', foglalkozas='$foglalkozas', magamrol='$magamrol', vallas='$vallas', orszag='$orszag', varos='$varos', nyilvanos='$nyilvanos', regip='$ip ($host)', atvett='n' $ujjelszo where uid='$u_id'";
			if(!mysql_db_query($db_name,$query)) echo "HIBA!<br>$query<br>".mysql_error();

			//Sessionben is m�dos�tani kell a nemet �s a sz�linapot
			$query="update session set nem='$nem' where sessid='$sid'";
			if(!mysql_db_query($db_name,$query)) echo "HIBA!<br>$query<br>".mysql_error();

			$tartalom="<span class=alcim>Adatok m�dos�t�sa</span><br><br><span class=alap>Az adatok m�dos�t�sa sikerrel j�rt.<br>FIGYELEM! El�fordulhat, hogy bizonyos v�ltoz�sok csak a k�vetkez� bel�p�sn�l l�pnek �rv�nybe!</span>";
		}
		else {
			$tartalom="<span class=alcim>Adatok m�dos�t�sa</span><br><br>$hibauzenet<br><br><a href=javascript:history.go(-1); class=link>Vissza</a>";
		}
	}
	else {
		//�j regisztr�ci�

		if(empty($login)) {
			$hiba=true;
			$hibauzenet.="<span class=hiba>HIBA! Nem lett megadva felhaszn�l�n�v!</span><br>";
		}
		if(empty($email)) {
			$hiba=true;
			$hibauzenet.="<span class=hiba>HIBA! Nem lett megadva emailc�m!</span><br>";
		}
		if($email!=$email2) {
			$hiba=true;
			$hibauzenet.="<span class=hiba>HIBA! A be�rt emailc�mek nem egyeznek!</span><br>";
		}
		//Login ellen�rz�se
		$login=str_replace(' ','',$login); //sz�k�z t�rl�se, ha lenne benne
		$login=str_replace("&nbsp;",'',$login); //sz�k�z t�rl�se, ha lenne benne
		$login=str_replace('"','',$login); //id�z�jel t�rl�se, ha lenne benne
		$login=str_replace("'",'',$login); //aposztr�f t�rl�se, ha lenne benne
		$login=strip_tags($login); //mindenf�le html form�z�st is t�rl�nk
		$query="select uid from user where login='$login'";
		$lekerdez=mysql_db_query($db_name,$query);
		if(mysql_num_rows($lekerdez)>0) {
			$hiba=true;
			$hibauzenet.="<span class=hiba>HIBA! Ez a bejelentkez�si n�v m�r foglalt, k�rj�k v�lassz m�sikat!</span><br>";
		}

		if(!$hiba) {
			//Jelsz� gener�l�s
			$szam1=mt_rand(0,99);
			$jelszo1 = str_shuffle($login).$szam1;
			$jelszo=base64_encode($jelszo1);

			$query="insert user set nev='$nev', email='$email', kontakt='$kontakt', jelszo='$jelszo', ok='i', jogok='$jogok', login='$login', letrehozta='$u_login', regdatum='$most'";
			if(!mysql_db_query($db_name,$query)) echo "HIBA!<br>$query<br>".mysql_error();

			//email k�ld�se
			$targy='Regisztr�ci� - Virtu�lis Pl�b�nia Port�l';
			$szoveg="K�sz�nt�nk a Virtu�lis Pl�b�nia Port�l felhaszn�l�i k�z�tt!";
			$szoveg.="\n\nA bel�p�shez sz�ks�ges jelsz�: $jelszo1";
			$szoveg.="\nA bel�p�st k�vet�en a BE�LL�T�SOK men�ben lehet a jelsz�t megv�ltoztatni.";
			$szoveg.="\n\nVPP \nwww.plebania.net";
			mail($email,$targy,$szoveg,"From: info@plebania.net");
		
			$tartalom="<span class=alcim>Regisztr�ci�</span><br><br><span class=alap><b>Isten hozott!</b><br><br>A bel�p�shez sz�ks�ges k�dot elk�ldt�k a megadott emailc�mre ($email), ami a bel�p�st k�vet�en megv�ltoztathat�. <br><b>Ha p�r �r�n bel�l nem �rkezne meg, val�sz�n�leg hib�s emailc�met adt�l meg.</b>";
		}
		else {
			$tartalom="<span class=alcim>Regisztr�ci�</span><br><br>$hibauzenet<br><br><a href=javascript:history.go(-1); class=link>Vissza</a>";
		}
	}

	$adatT[2]=$tartalom;
	$tipus='doboz';
	$kod.=formazo($adatT,$tipus);


	return $kod;
}

function regisztracio_jelszo() {
	global $db_name,$m_id;
	
	$cim='<span class=alcim>Jelsz� eml�keztet�</span><br><br>';
	$szoveg='<span class=alap>Az al�bbi k�t adat k�z�l legal�bb az egyik kit�lt�se alapj�n a rendszer megpr�b�l azonos�tani �s elk�ldi a megadott (regisztr�lt!) email c�mre a jelsz�t.</span><br><br>';
	$szoveg.="\n<form method=post><input type=hidden name=m_op value=jelszokuld><input type=hidden name=m_id value=$m_id><span class=alap>Felhaszn�l�n�v: </span> <input type=text name=lnev size=18 class=urlap><br><span class=alap>Emailc�m: </span> <input type=text name=mail size=25 class=urlap><br><br><input type=submit value='K�rem a jelsz�t'></form>";

	$adatT[2]=$cim.$szoveg;
	$tipus='doboz';
	$kod.=formazo($adatT,$tipus);

	return $kod;
}


function regisztracio_jelszokuld() {
	global $db_name,$_POST;

	$lnev=$_POST['lnev'];
	$mail=$_POST['mail'];

	$cim='<span class=alcim>Jelsz� eml�keztet�</span><br><br>';
	
	if(!empty($lnev)) $feltetelT[]="login='$lnev'";
	if(!empty($mail)) $feltetelT[]="email='$mail'";
	if(is_array($feltetelT)) $feltetel=implode(' and ',$feltetelT);

	if(!empty($feltetel)) {
		$query="select login,jelszo,email from user where $feltetel";
		$lekerdez=mysql_db_query($db_name,$query);
		if(mysql_num_rows($lekerdez)>0) {
			list($loginnev,$jelszo,$email)=mysql_fetch_row($lekerdez);
			$jelszokiir=base64_decode($jelszo);
			$targy="Jelsz� eml�keztet� - Virtu�lis Pl�b�nia Port�l";
			$txt="Kedves $loginnev";
			$txt.="\n\nK�r�sedre k�ldj�k a bejelentkez�shez sz�ks�ges jelsz�t:";
			$txt.="\n$jelszokiir";
			$txt.="\n\nVPP \nhttp://www.plebania.net";
			
			mail($email,$targy,$txt,"From: info@plebania.net");
			
			$szoveg="<span class=alap>A jelsz�t elk�ldt�k a regisztr�lt emailc�mre</span>";
		}
		else {
			$szoveg='<span class=alap>A megadott adatok alapj�n nem tal�ltunk felhaszn�l�t.</span><br><br><a href=javascript:history.go(-1); class=link>Vissza</a>';
		}
	}
	else {
		$szoveg='<span class=alap>Nem lett kit�ltve adat!</span><br><br><a href=javascript:history.go(-1); class=link>Vissza</a>';
	}

	$adatT[2]=$cim.$szoveg;
	$tipus='doboz';
	$kod.=formazo($adatT,$tipus);


	return $kod;
}


switch($m_op) {
    case 'index':
        $tartalom=regisztracio_index();
        break;

	case 'atvett':
		$tartalom=regisztracio_atvett();
		break;

	case 'add':
		$uid=$_GET['uid'];
        $tartalom=regisztracio_add();
        break;

    case 'adding':
        $tartalom=regisztracio_adding();
        break;

	case 'jelszo':
		$tartalom=regisztracio_jelszo();
		break;

	case 'jelszokuld':
		$tartalom=regisztracio_jelszokuld();
		break;

}

?>