<?php

function hirek_addmegse() {
	global $sessid,$linkveg,$m_id,$db_name,$_GET;

	$ki=$_GET['ki'];
	$mikor=$_GET['mikor'];
	$mikor=rawurldecode($mikor);
	$hid=$_GET['hid'];
	$query="update hirek set megnyitva='$ki', megnyitvamikor='$mikor' where id='$hid'";
	if(!mysql_query($query)) echo 'HIBA!<br>'.mysql_error();

	$kod=feltoltes_index();
	return $kod;
}

function feltoltes_index() {
	global $linkveg,$m_id,$db_name,$u_login,$sid;

	$menu.="\n<span class=alcim>Felt�lt�s oldal</span><br><br>";
	$menu.="\n<span class=alap>K�sz�nj�k, hogy seg�tesz oldalunk tartalm�nak gazdag�t�s�ban, naprak�szen tart�s�ban!</span><br>";
	
	//Kapcsol�d� templomok list�ja
	$querye="select distinct(hol_id),hol from eszrevetelek where hol='templomok' or hol='hirek'";
	if(!$lekerdeze=mysql_query($querye)) echo "HIBA!<br>$querym<br>".mysql_error();
	while(list($idk,$hol)=mysql_fetch_row($lekerdeze)) {
		$vaneszrevetelT[$hol][$idk]=true;
	}

	$query="select id,nev,varos,eszrevetel,megbizhato from templomok where letrehozta='$u_login' order by varos";
	$lekerdez=mysql_query($query);
	if(mysql_num_rows($lekerdez)>0) {
		$menu.="<br><span class=felsomenulink>M�dos�that� templomaid:</span><br>";
		while(list($tid,$tnev,$tvaros,$teszrevetel,$megbizhato)=mysql_fetch_row($lekerdez)) {
			$jelzes='';
			if($megbizhato=='i') {				
				if($teszrevetel=='i') $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomag.gif title='�j �szrev�telt �rtak hozz�!' align=absmiddle border=0></a> ";		
				elseif($teszrevetel=='f') $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomagf.gif title='�szrev�tel jav�t�sa folyamatban!' align=absmiddle border=0></a> ";		
				elseif($vaneszrevetelT['templomok'][$tid]) $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomag1.gif title='�szrev�telek!' align=absmiddle border=0></a> ";		
			}

			$menu.="$jelzes<a href=?m_id=$m_id&m_op=addtemplom&tid=$tid$linkveg class=kismenulink>- $tnev<font color=#8D317C> ($tvaros)</font></a> - <a href=?m_id=$m_id&m_op=addmise&tid=$tid$linkveg class=kismenulink><img src=img/edit.gif title='mis�k' align=absmiddle border=0>szentmise</a><br>";
		}
		$menu.="\n<br>";
	}	
	
	$menu.="<hr><br><a href=?m_id=$m_id&m_op=addhirek$linkveg class=kismenulink><img src=img/ceruza.gif border=0> <b>�j h�r felt�lt�se</b></a><br>";

	//Kapcsol�d� h�rek list�ja
	$query="select id,cim,datum,megbizhato,eszrevetel from hirek where feltette='$u_login' and (megbizhato='i' or ok='f') order by datum desc";
	$lekerdez=mysql_query($query);
	if(mysql_num_rows($lekerdez)>0) {
		$menu.="<br><img src=img/modosit.gif border=0> <span class=felsomenulink>M�dos�that� h�reid:</span><br>";
		while(list($hid,$hcim,$hdatum,$megbizhato,$heszrevetel)=mysql_fetch_row($lekerdez)) {
			$hdatum=substr($hdatum,0,10);
			$jelzes='';
			if($megbizhato=='i') {				
				if($heszrevetel=='i') $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=hirek&id=$hid&sid=$sid',550,500);\"><img src=img/csomag.gif title='�j �szrev�telt �rtak hozz�!' align=absmiddle border=0></a> ";		
				elseif($heszrevetel=='f') $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=hirek&id=$hid&sid=$sid',550,500);\"><img src=img/csomagf.gif title='�szrev�tel jav�t�sa folyamatban!' align=absmiddle border=0></a> ";		
				elseif($vaneszrevetelT['hirek'][$hid]) $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=hirek&id=$hid&sid=$sid',550,500);\"><img src=img/csomag1.gif title='�szrev�telek!' align=absmiddle border=0></a> ";		
			}

			$menu.="$jelzes<a href=?m_id=$m_id&m_op=addhirek&hid=$hid$linkveg class=kismenulink>- $hcim<font color=#8D317C> ($hdatum)</font></a><br>";
		}
		$menu.="\n<br>";
	}



	$adatT[2]=$menu;
	$tipus='doboz';
	$kod.=formazo($adatT,$tipus);	

	return $kod;
}

function feltoltes_addtemplom($tid) {
	global $sid,$linkveg,$m_id,$db_name,$onload,$u_beosztas,$u_login,$design_url;	
	
	$query="select id,nev from egyhazmegye where ok='i' order by sorrend";
	$lekerdez=mysql_query($query);
	while(list($id,$nev)=mysql_fetch_row($lekerdez)) {
		$ehmT[$id]=$nev;
	}

	$query="select id,ehm,nev from espereskerulet";
	$lekerdez=mysql_query($query);
	while(list($id,$ehm,$nev)=mysql_fetch_row($lekerdez)) {
		$espkerT[$ehm][$id]=$nev;
	}

	if($tid>0) {
		$most=date("Y-m-d H:i:s");
		$urlap.=include('editscript2.php'); //Csak, ha m�dos�t�sr�l van sz�

		$query="select nev,ismertnev,turistautak,varos,cim,megkozelites,plebania,pleb_url,pleb_eml,egyhazmegye,espereskerulet,leiras,megjegyzes,szomszedos1,szomszedos2,bucsu,nyariido,teliido,frissites,kontakt,kontaktmail,adminmegj,log,ok,letrehozta,megbizhato,eszrevetel from templomok where id='$tid' and letrehozta='$u_login'";
		if(!$lekerdez=mysql_query($query)) echo 'HIBA!<br>'.mysql_error();	list($nev,$ismertnev,$turistautak,$varos,$cim,$megkozelites,$plebania,$pleb_url,$pleb_eml,$egyhazmegye,$espereskerulet,$szoveg,$megjegyzes,$szomszedos1,$szomszedos2,$bucsu,$nyariido,$teliido,$frissites,$kontakt,$kontaktmail,$adminmegj,$log,$ok,$feltolto,$megbizhato,$teszrevetel)=mysql_fetch_row($lekerdez);

		if(empty($nev)) $hibauzenet="<span class=hiba>HIBA! Ilyen templom nincs, vagy nem Te r�gz�tetted!</span>";
	}
	else {
		exit();
		//�jat nem lehet felvinni!!!
	
	//	$datum=date('Y-m-d H:i');
	//	$nyariido='2006-03-26';
	//	$teliido='2006-10-29';
	//	$urlapkieg="\n<input type=hidden name=elsofeltoltes value=i>";
	}

	$urlap.="\n<FORM ENCTYPE='multipart/form-data' method=post>";
	$urlap.=$urlapkieg;

	$urlap.="\n<input type=hidden name=m_id value=$m_id><input type=hidden name=sid value=$sid>";
	$urlap.="\n<input type=hidden name=m_op value=addingtemplom><input type=hidden name=tid value=$tid>";
	
	$urlap.='<table cellpadding=4>';

/*
//megnyitva
	if($hid>0 and !empty($megnyitva)) {
		$kod=rawurlencode($megnyitva);
		$urlap.="\n<tr><td>&nbsp;</td><td><img src=img/edit.gif align=absmiddle><span class=alap><font color=red>Megnyitva!</font> $megnyitva</span><br><a href=?m_id=$m_id&m_op=addmegse&hid=$hid&kod=$kod$linkveg class=link><b><font color=red>Vissza, m�gsem szerkesztem</font></b></a></td></tr>";
	}
*/
/*
//el�n�zet
	if($hid>0) $urlap.="\n<tr><td bgcolor='#efefef'>&nbsp;</td><td bgcolor='#efefef'><a href=?m_id=19&id=$hid$linkveg class=link><b>>> H�r megtekint�se (el�n�zet) <<</b></a></td></tr>";
*/

//�szrev�tel
//�szrev�telek lek�rdez�se
	$querye="select distinct(hol_id) from eszrevetelek where hol='templomok'";
	if(!$lekerdeze=mysql_query($querye)) echo "HIBA!<br>$querym<br>".mysql_error();
	while(list($templom)=mysql_fetch_row($lekerdeze)) {
		$vaneszrevetelT[$templom]=true;
	}

	if($teszrevetel=='i') $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomag.gif title='�j �szrev�telt �rtak hozz�!' align=absmiddle border=0></a> ";		
	elseif($teszrevetel=='f') $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomagf.gif title='�szrev�tel jav�t�sa folyamatban!' align=absmiddle border=0></a> ";		
	elseif($vaneszrevetelT[$tid]) $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomag1.gif title='�szrev�telek!' align=absmiddle border=0></a> ";		
	else $jelzes='<span class=alap>Nincs</span>';

	$urlap.="\n<tr><td colspan=2><span class=kiscim>�szrev�tel: </span>$jelzes</td></tr>";

//megjegyz�s
	$urlap.="\n<tr><td bgcolor=#ECE5C8><div class=kiscim align=right>Megjegyz�s:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=1',200,300);\"><img src=img/sugo.gif border=0 title='S�g�'></a></div></td><td bgcolor=#ECE5C8><textarea name=adminmegj class=urlap cols=50 rows=3>$adminmegj</textarea><span class=alap> a szerkeszt�ssel kapcsolatosan</span></td></tr>";

//kontakt
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>Felel�s: <br>(kontakt ember)<br><a href=\"javascript:OpenNewWindow('sugo.php?id=2',200,300);\"><img src=img/sugo.gif border=0 title='S�g�'></a></div></td><td bgcolor=#efefef><textarea name=kontakt class=urlap cols=50 rows=2>$kontakt</textarea><span class=alap> n�v �s telefonsz�m</span><br><input type=text name=kontaktmail size=40 class=urlap value='$kontaktmail'><span class=alap> emailc�m</span></td></tr>";

//n�v
	$urlap.="\n<tr><td bgcolor=#F5CC4C><div class=kiscim align=right>Templom neve:</div></td><td bgcolor=#F5CC4C><input type=text name=nev value=\"$nev\" class=urlap size=80 maxlength=150> <a href=\"javascript:OpenNewWindow('sugo.php?id=3',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></td></tr>";
	$urlap.="\n<tr><td bgcolor=#FAE19C><div class=kiscim align=right>k�zismert neve:</div></td><td bgcolor=#FAE19C><input type=text name=ismertnev value=\"$ismertnev\" class=urlap size=80 maxlength=150> <a href=\"javascript:OpenNewWindow('sugo.php?id=4',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a><br><span class=alap>Helyben elfogadott (ismert) templomn�v, valamint telep�l�s, vagy telep�l�s r�szn�v, amennyiben elt�r� a telep�l�s hivatalos nev�t�l</span></td></tr>";

//t�ristautak
	$urlap.="\n<tr><td bgcolor=#EFEFEF><div class=kiscim align=right>turistautak.hu ID:</div></td><td bgcolor=#EFEFEF><input type=text name=turistautak value=\"$turistautak\" class=urlap size=5 maxlength=10> <a href=\"javascript:OpenNewWindow('sugo.php?id=16',240,320);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a><br><span class=alap>(<a href=http://turistautak.hu/search.php?s=templom target=_blank class=link><u>ebb�l a list�b�l</u></a> ha bennevan)</span></td></tr>";

//c�m
	$urlap.="\n<tr><td><div class=kiscim align=right>Templom c�me:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=5',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td>";
	//Egyh�zmegye
	$urlap.="<select name=egyhazmegye class=urlap onChange=\"if(this.value!=0) {";
	foreach($ehmT as $id=>$nev) {
		$urlap.="document.getElementById($id).style.display='none'; ";
	} 
	$urlap.="document.getElementById(this.value).style.display='inline'; document.getElementById('valassz').style.display='none'; } else {";
	foreach($ehmT as $id=>$nev) {
		$urlap.="document.getElementById($id).style.display='none'; ";
	} 
	$urlap.="document.getElementById('valassz').style.display='inline';}\"><option value=0>Nincs / nem tudom</option>";	
	foreach($ehmT as $id=>$nev) {
		$urlap.="<option value=$id";
		if($egyhazmegye==$id) $urlap.=' selected';
		$urlap.=">$nev</option>";
	
		if($egyhazmegye==$id) $display='inline';
		else $display='none';
		$espkerurlap.="<div id=$id style='display: $display'><select name=espkerT[$id] class=keresourlap><option value=0>Nincs / nem tudom</option>";	
		if(is_array($espkerT[$id])) {
			foreach($espkerT[$id] as $espid=>$espnev) {
				$espkerurlap.="<option value=$espid";
				if($espid==$espereskerulet) $espkerurlap.=' selected';
				$espkerurlap.=">$espnev</option>";
			}
		}
		$espkerurlap.="</select><span class=alap> (esperesker�let)</span><br></div>";
	}
	$urlap.="</select><span class=alap> (egyh�zmegye)</span><br>";

	//Esperesker�let
	$urlap.=$espkerurlap;

	$urlap.="<select name=varos class=urlap>";
	$query="select nev from varosok order by nev";
	$lekerdez=mysql_query($query);
	while(list($vnev)=mysql_fetch_row($lekerdez)) {
		$vnev1=str_replace('�','O',$vnev);
		$vnev1=str_replace('�','O',$vnev1);
		$vnev1=str_replace('�','o',$vnev1);
		$vnev1=str_replace('�','o',$vnev1);
		$vnev1=str_replace('�','U',$vnev1);
		$vnev1=str_replace('�','U',$vnev1);
		$vnev1=str_replace('�','u',$vnev1);
		$vnev1=str_replace('�','u',$vnev1);
		$vnev1=str_replace('�','A',$vnev1);
		$vnev1=str_replace('�','a',$vnev1);
		$vnev1=str_replace('�','E',$vnev1);
		$vnev1=str_replace('�','e',$vnev1);
		$vnev1=str_replace('�','i',$vnev1);
		$vnev1=str_replace('�','U',$vnev1);
		$vnev1=str_replace('�','u',$vnev1);
		$vnev1=str_replace('�','O',$vnev1);
		$vnev1=str_replace('�','o',$vnev1);
		
		$szam=rand(0,100);
		$vnev1.=$szam;
		$varos1T[]=$vnev1;
		$varosT[$vnev1]=$vnev;
	}
	asort($varos1T, SORT_STRING);
	foreach($varos1T as $ertek) {
		$urlap.="\n<option value='$varosT[$ertek]'";
		if($varosT[$ertek]==$varos) $urlap.=" selected";
		$urlap.=">$varosT[$ertek]</option>";
	}
	$urlap.="</select><span class=alap> (telep�l�s)</span><br>";
	$urlap.="<input type=text name=cim value=\"$cim\" class=urlap size=60 maxlength=250><span class=alap> (utca, h�zsz�m)</span>";
	$urlap.="<br><img src=img/space.gif widt=5 height=5><br><textarea name=megkozelites class=urlap cols=50 rows=2>$megkozelites</textarea><span class=alap> (megk�zel�t�s r�vid le�r�sa)</span>";
	$urlap.="</td></tr>";

//pl�b�nia
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>Pl�b�nia adatai:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=6',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td bgcolor=#efefef><textarea name=plebania class=urlap cols=50 rows=3>$plebania</textarea><span class=alap> c�m, telefon, fax, kontakt</span>";
	$urlap.="<br><input type=text name=pleb_eml value='$pleb_eml' size=40 class=urlap maxlength=100><span class=alap> email</span>";
	$urlap.="<br><input type=text name=pleb_url value='$pleb_url' size=40 class=urlap maxlength=100><span class=alap> web <b>http://</b>-rel egy�tt!!!</span>";
	$urlap.="</td></tr>";

//b�csu
	$urlap.="\n<tr><td><div class=kiscim align=right>�nnep adatok:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=7',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td><textarea name=bucsu class=urlap cols=50 rows=3>$bucsu</textarea><span class=alap> Pl. b�cs�, d�tuma, le�r�s</span>";
	$urlap.="</td></tr>";

//ny�ri-t�li id�sz�m�t�s
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>Ny�ri id�sz�m�t�s:</div></td><td bgcolor=#efefef><input type=text name=nyariido value=\"$nyariido\" class=urlap size=10 maxlength=10><span class=alap> - </span><input type=text name=teliido value=\"$teliido\" class=urlap size=10 maxlength=10> <a href=\"javascript:OpenNewWindow('sugo.php?id=8',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></td></tr>";

//Sz�veg
	$urlap.="<tr><td valign=top><div class=kiscim align=right>R�szletes le�r�s, templom t�rt�nete:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=9',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td valign=top><span class=alap><font color=red><b>FONTOS!</b></font> A sz�veghez MINDIG legyen st�lus rendelve (m�dos�t�sn�l)!</span><br><textarea name=szoveg class=urlap cols=90 rows=30>$szoveg</textarea>";

	$urlap.="\n</td></tr>";

//megjegyz�s
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>Megjegyz�s:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=10',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td bgcolor=#efefef><textarea name=megjegyzes class=urlap cols=50 rows=3>$megjegyzes</textarea><span class=alap> ami megjelenik!</span></td></tr>";

//K�pek
	$urlap.="\n<tr><td><div class=kiscim align=right>K�pek:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=11',200,450);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td><span class=alap><font color=red>FIGYELEM!</font><br>Azonos nev� k�pek fel�l�rj�k egym�st!!! A f�jln�vben ne legyen �kezet �s sz�k�z!<br>T�bbsz�ri m�dos�t�ssal maximum 20 k�p t�lthet� fel!</span>";
	$urlap2='';
	if($tid>0) {		
		//Megl�v� k�pek list�ja
		$query="select fajlnev,felirat,sorszam from kepek where kat='templomok' and kid='$tid' order by sorszam";
		$lekerdez=mysql_query($query);
		$mennyi=mysql_num_rows($lekerdez);
		$konyvtar="kepek/templomok/$tid/kicsi";
		$urlap2.="\n<table width=100% cellpadding=0 cellspacing=0><tr>";
		while(list($fajlnev,$felirat,$sorszam)=mysql_fetch_row($lekerdez)) {			
			if($a%4==0 and $a>0) $urlap2.="</tr><tr>";
			$a++;
			$urlap2.="\n<td valign=bottom><img src=$konyvtar/$fajlnev><br><input type=text name=kepsorszamT[$fajlnev] value='$sorszam' maxlength=2 size=2 class=urlap><span class=alap> - t�r�l: </span><input type=checkbox name=delkepT[] value='$fajlnev' class=urlap><br><input type=text name=kepfeliratmodT[$fajlnev] value='$felirat' maxlength=250 size=20 class=urlap></td>";
		}
		$urlap2.='</tr></table>';
	}
	if($mennyi<20) {
		$urlap.="<br><input type=file name=kepT[] class=urlap size=20> <span class=alap>K�pfelirat: </span><input type=text name=kepfeliratT[] size=40 maxlength=100 class=urlap><br><input type=file name=kepT[] class=urlap size=20> <span class=alap>K�pfelirat: </span><input type=text name=kepfeliratT[] size=40 maxlength=100 class=urlap><br><input type=file name=kepT[] class=urlap size=20> <span class=alap>K�pfelirat: </span><input type=text name=kepfeliratT[] size=40 maxlength=100 class=urlap>";
	}
	$urlap.=$urlap2;
	$urlap.='</td></tr>';

//F�jlok
	$urlap.="\n<tr><td bgcolor=#efefef valign=top><div class=kiscim align=right>Let�lthet� f�jl(ok):</td><td bgcolor=#efefef valign=top>";
	$urlap.="\n<span class=alap>Kapcsol�d� dokumentum, ha van ilyen (max. 5 f�jl):</span><br>";	
	$urlap2='';
	$a=0;		
	//K�nyvt�r tartalm�t beolvassa
	if($tid>0) {
		$konyvtar="fajlok/templomok/$tid";
		if(is_dir($konyvtar)) {
			$handle=opendir($konyvtar);
			while ($file = readdir($handle)) {
				if ($file!='.' and $file!='..') {
					$meret=intval((filesize("$konyvtar/$file")/1024));
					if($meret>1000) {
						$meret=intval(($meret/1024)*10)/10;
						$meret.=' MB';
					}
					else $meret.=' kB';
					$filekiir=rawurlencode($file);
					$urlap2.="<br><li><a href='$konyvtar/$filekiir' class=alap><b>$file</b></a><span class=alap> ($meret) </span><input type=checkbox class=urlap name=delfajl[] value='$file'><span class=alap>T�r�l</span></li>";
					$a++;
				}
			}
			closedir($handle);
		}
	}
	if($a<5) $urlap.="\n<span class=alap>�j f�jl: </span><input type=file size=60 name=fajl class=urlap> <a href=\"javascript:OpenNewWindow('sugo.php?id=12',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a><br>";
	$urlap.=$urlap2;

	$query="select id,nev,ismertnev,varos from templomok where id!='$tid' order by varos";
	$lekerdez=mysql_query($query);
	while(list($eid,$enev,$eismert,$evaros)=mysql_fetch_row($lekerdez)) {
		if(strlen($enev)>65) $enev=substr($enev,0,65).'...';
		$ismT[$eid]=" title='$eimsmert'>$evaros -> [$enev]";
	}

//szomsz�dos 1
	$urlap.="\n<tr><td><div class=kiscim align=right>Szomsz�dos templomok (legk�zelebbi):<br><a href=\"javascript:OpenNewWindow('sugo.php?id=13',200,500);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td>";
	$urlap.="<span class=alap>CTRL-lal t�bb is kijel�lhet�, illetve visszavonhat�!<br></span><select name=szomszedos1T[] class=urlap multiple size=10>";
	foreach($ismT as $eid=>$enev) {		
		$urlap.="\n<option value='$eid'";
		if(strstr($szomszedos1,"-$eid-")) $urlap.=" selected";
		$urlap.="$enev</option>";
	}
	$urlap.="</select>";
	$urlap.="</td></tr>";

//szomsz�dos 2
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>Szomsz�dos templomok (10km-en bel�li):<br><a href=\"javascript:OpenNewWindow('sugo.php?id=13',200,500);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td bgcolor=#efefef>";
	$urlap.="<select name=szomszedos2T[] class=urlap multiple size=10>";
	foreach($ismT as $eid=>$enev) {		
		$urlap.="\n<option value='$eid'";
		if(strstr($szomszedos2,"-$eid-")) $urlap.=" selected";
		$urlap.="$enev</option>";
	}
	$urlap.="</select>";
	$urlap.="</td></tr>";


//Friss�t�s d�tuma
	if($tid>0) {
		$urlap.="\n<tr><td valign=top><div class=kiscim align=right>Friss�t�s:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=14',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td valign=top><input type=text disabled value='$frissites' size=10 class=urlap><br><input type=checkbox name=frissit value=i class=urlap><span class=alap> Friss�ts�k a d�umot (amennyiben nem csak kis jav�t�s volt, s az adatok megfelelnek a mai �llapotnak)</span></td></tr>";
	}

	$urlap.="\n<tr><td><div align=right><input type=submit value=Mehet class=urlap>&nbsp;</div></td><td>";

	if($tid>0) {
		$urlap.="<input type=radio name=modosit value=i class=urlap checked><span class=alap> �s �jra m�dos�t</span>";
		$urlap.="<br><input type=radio name=modosit value=m class=urlap><span class=alap> �s tov�bb a miserendre</span>";
		$urlap.="<br><input type=radio name=modosit value=n class=urlap><span class=alap> �s vissza a list�ba</span>";
	}
	else $urlap.="<input type=hidden name=modosit value=i>";

	$urlap.='</td></tr></table>';
	$urlap.="\n</form>";

	if(!empty($hibauzenet)) $urlap=$hibauzenet;
	$adatT[2]='<span class=alcim>Templom felt�lt�se / m�dos�t�sa</span><br><br>'.$urlap;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function feltoltes_addingtemplom() {
	global $_POST,$db_name,$_FILES,$u_login,$u_beosztas;

	$hiba=false;
	$tid=$_POST['tid'];
	if(!($tid>0)) exit(); //�j templomot nem enged�nk feltenni!!!
/*
	if($tid>0) {
		//Ha m�dos�t�s t�rt�nt
		$lekerdez=mysql_query("select megnyitva from hirek where id='$hid'");
		list($megnyitva)=mysql_fetch_row($lekerdez);
		if(strstr($megnyitva,$u_login)) { //�s � nyitotta meg utolj�ra,
			mysql_query("update hirek set megnyitva='' where id='$hid'"); //akkor t�r�lj�k a bejegyz�st
		}
	}
*/
	$ma=date('Y-m-d');

	$modosit=$_POST['modosit'];
	$adminmegj=$_POST['adminmegj'];
	$nev=$_POST['nev'];
	$ismertnev=$_POST['ismertnev'];
	$turistautak=$_POST['turistautak'];
	$egyhazmegye=$_POST['egyhazmegye'];
	$espkerT=$_POST['espkerT'];
	$espereskerulet=$espkerT[$egyhazmegye];
	$varos=$_POST['varos'];
	$cim=$_POST['cim'];
	$megkozelites=$_POST['megkozelites'];
	$plebania=$_POST['plebania'];
	$pleb_url=$_POST['pleb_url'];
	$pleb_eml=$_POST['pleb_eml'];
	$nyariido=$_POST['nyariido'];
	$teliido=$_POST['teliido'];
	$frissit=$_POST['frissit'];
	if($frissit=='i') $frissites=" frissites='$ma', ";
	$kontakt=$_POST['kontakt'];
	$kontaktmail=$_POST['kontaktmail'];
	$szomszedos1T=$_POST['szomszedos1T'];
	if(is_array($szomszedos1T)) $szomszedos1='-'.implode('--',$szomszedos1T).'-';
	$szomszedos2T=$_POST['szomszedos2T'];
	if(is_array($szomszedos2T)) $szomszedos2='-'.implode('--',$szomszedos2T).'-';
	$bucsu=$_POST['bucsu'];
	$megjegyzes=$_POST['megjegyzes'];
	$feltolto=$_POST['feltolto'];

	$szoveg=$_POST['szoveg'];
	$szoveg=str_replace('&eacute;','�',$szoveg);
	$szoveg=str_replace('&ouml;','�',$szoveg);
	$szoveg=str_replace('&Ouml;','�',$szoveg);
	$szoveg=str_replace('&uuml;','�',$szoveg);
	$szoveg=str_replace('&Uuml;','�',$szoveg);

	$elsofeltoltes=$_POST['elsofeltoltes'];
	if($elsofeltoltes=='i' and !empty($szoveg)) $szoveg='<p class=alap>'.nl2br($szoveg);
	
	if(empty($nev)) {
		$hiba=true;
		$hibauzenet.='<br>Nem lett kit�ltve a templom neve!';
	}

	if($hiba) {
		$txt.="<span class=hiba>HIBA a templom felt�lt�s�n�l!</span><br>";
		$txt.='<span class=alap>'.$hibauzenet.'</span>';
		$txt.="<br><br><a href=javascript:history.go(-1); class=link>Vissza</a>";
	
		$adatT[2]='<span class=alcim>Templomok felt�lt�se / m�dos�t�sa</span><br><br>'.$txt;
		$tipus='doboz';
		$kod.=formazo($adatT,$tipus);	
	}
	else {
		$most=date('Y-m-d H:i:s');
		if($tid>0) {
			$uj=false;			
			list($letrehozta,$megbizhato,$log)=mysql_fetch_row(mysql_query("select letrehozta,megbizhato,log from templomok where id='$tid'"));
			$ujlog=$log."\nMod: $u_login ($most)";
			if($letrehozta==$u_login) {
				if($megbizhato=='i') $ok=", ok='i'";
				else $ok=", ok='f'";
				$parameter1='update';
				$parameter2="$ok , modositotta='$u_login', moddatum='$most', log='$ujlog' where id='$tid' and letrehozta='$u_login'";

				//M�dos�tjuk a hozz�kapcsol�d� miseid�pontokn�l is az id�sz�m�t�si d�tumot
				$query="update misek set datumtol='$nyariido', datmig='$teliido' where tid='$tid' and torolte=''";
				mysql_query($query);
			}
			else {
				$hiba=true;
				$hibauzenet="Nincs hozz� jogosults�god!";
			}
		}
		else {
			$uj=true;
			$parameter1='insert';
			$parameter2=",ok='f', letrehozta='$u_login', regdatum='$most', log='Add: $u_login ($most)'";
			$frissites=" frissites='$ma', ";
		}

		if(!$hiba) {
			$query="$parameter1 templomok set nev='$nev', ismertnev='$ismertnev', turistautak='$turistautak', varos='$varos', cim='$cim', megkozelites='$megkozelites', plebania='$plebania', pleb_url='$pleb_url', pleb_eml='$pleb_eml', egyhazmegye='$egyhazmegye', espereskerulet='$espereskerulet', leiras='$szoveg', megjegyzes='$megjegyzes', szomszedos1='$szomszedos1', szomszedos2='$szomszedos2', bucsu='$bucsu', nyariido='$nyariido', teliido='$teliido', $frissites kontakt='$kontakt', kontaktmail='$kontaktmail', adminmegj='$adminmegj' $parameter2";
			if(!mysql_query($query)) echo 'HIBA!<br>'.mysql_error();
			if($uj) $tid=mysql_insert_id();		

		//A szomsz�dosokat �sszerendeli
			if(is_array($szomszedos1T)) {
				foreach($szomszedos1T as $id) {
					list($sz1)=mysql_fetch_row(mysql_query("select szomszedos1 from templomok where id='$id'"));
					if(!strstr($sz1,$tid)) {
						$sz1=str_replace('--','!',$sz1);
						$sz1=str_replace('-','',$sz1);
						if(!empty($sz1)) $sz1T=explode('!',$sz1);
						$sz1T[]=$tid; //felvessz�k a most felt�lt�tt templomot is hozz�
						$ujsz1='-'.implode('--',$sz1T).'-';
						$query="update templomok set szomszedos1='$ujsz1' where id='$id'";
						if(!mysql_query($query)) echo "<br>HIBA!<br>$query<br>".mysql_error();
					}
				}
			}
			if(is_array($szomszedos2T)) {
				foreach($szomszedos2T as $id) {
					list($sz2)=mysql_fetch_row(mysql_query("select szomszedos2 from templomok where id='$id'"));
					if(!strstr($sz2,$tid)) {
						$sz2=str_replace('--','!',$sz2);
						$sz2=str_replace('-','',$sz2);
						if(!empty($sz2)) $sz2T=explode('!',$sz2);
						$sz2T[]=$tid; //felvessz�k a most felt�lt�tt templomot is hozz�
						$ujsz2='-'.implode('--',$sz2T).'-';
						mysql_query("update templomok set szomszedos2='$ujsz2' where id='$id'");
					}
				}
			}

		//�s kiszedi a t�r�lt szomsz�dosokat!!!
			if(!$uj) {
				$query="select id, szomszedos1, szomszedos2 from templomok where szomszedos1 like '%-$tid-%' or szomszedos2 like '%-$tid-%'";
				$lekerdez=mysql_query($query);
				while(list($szid,$sz1,$sz2)=mysql_fetch_row($lekerdez)) {
					if(strstr($sz1,$tid) and !strstr($szomszedos1,$szid)) {
						//Ha a m�sik templomn�l szerepel a mi templomunk, de itt most nem lett be�ll�tva
						//akkor t�r�lj�k onnan is a hozz�rendel�st!
						$sz1=str_replace('--','!',$sz1);
						$sz1=str_replace('-','',$sz1);
						$sz1T=explode('!',$sz1);
						foreach($sz1T as $ertek) {
							if($ertek!=$tid) {
								$ujsz1T[]=$ertek;
							}
						}
						if(is_array($ujsz1T)) $ujsz1='-'.implode('--',$ujsz1T).'-';
						else $ujsz1='';
						mysql_query("update templomok set szomszedos1='$ujsz1' where id='$szid'");
					}
					if(strstr($sz2,$tid) and !strstr($szomszedos2,$szid)) {
						//Ha a m�sik templomn�l szerepel a mi templomunk, de itt most nem lett be�ll�tva
						//akkor t�r�lj�k onnan is a hozz�rendel�st!
						$sz2=str_replace('--','!',$sz2);
						$sz2=str_replace('-','',$sz2);
						$sz2T=explode('!',$sz2);
						foreach($sz2T as $ertek) {
							if($ertek!=$tid) {
								$ujsz2T[]=$ertek;
							}
						}
						if(is_array($ujsz2T)) $ujsz2='-'.implode('--',$ujsz2T).'-';
						else $ujsz2='';
						mysql_query("update templomok set szomszedos2='$ujsz2' where id='$szid'");
					}

				}
			}

		//f�jlkezel�s
			$fajl=$_FILES['fajl']['tmp_name'];
			$fajlnev=$_FILES['fajl']['name'];
			$delfajl=$_POST['delfajl'];

			if(is_array($delfajl)) {
				foreach($delfajl as $ertek) {
					unlink("fajlok/templomok/$tid/$ertek");
				}
			}

			if(!empty($fajl)) {
				$konyvtar="fajlok/templomok";
				//K�nyvt�r ellen�rz�se
				if(!is_dir("$konyvtar/$tid")) {
					//l�tre kell hozni
					if(!mkdir("$konyvtar/$tid",0775)) {
						echo '<p class=hiba>HIBA a k�nyvt�r l�trehoz�s�n�l!</p>';					
					}
				}

				//M�sol�s
				if(!copy($fajl,"$konyvtar/$tid/$fajlnev")) echo '<p>HIBA a m�sol�sn�l!</p>';
				unlink($fajl);
			}	

		//k�pkezel�s
			$konyvtar="kepek/templomok/$tid";
		
			$delkepT=$_POST['delkepT'];
			if(is_array($delkepT)) {		
				foreach($delkepT as $ertek) {
					@unlink("$konyvtar/$ertek");
					@unlink("$konyvtar/kicsi/$ertek");
					if(!mysql_query("delete from kepek where kat='templomok' and kid='$tid' and fajlnev='$ertek'")) echo 'HIBA!<br>'.mysql_error();
				}		
			}

			$kepfeliratT=$_POST['kepfeliratT'];		
			$kepT=$_FILES['kepT']['tmp_name'];
			$kepnevT=$_FILES['kepT']['name'];

			if(is_array($kepT)) {
				foreach($kepT as $id=>$kep) {
					if(!empty($kep)) {			
						//K�nyvt�r ellen�rz�se
						if(!is_dir("$konyvtar")) {
							//l�tre kell hozni
							if(!mkdir("$konyvtar",0775)) {
								echo '<p class=hiba>HIBA a k�nyvt�r l�trehoz�s�n�l!</p>';					
							}
							if(!mkdir("$konyvtar/kicsi",0775)) {
								echo '<p class=hiba>HIBA a k�nyvt�r l�trehoz�s�n�l!</p>';					
							}
						}

						$kimenet="$konyvtar/$kepnevT[$id]";
						$kimenet1="$konyvtar/kicsi/$kepnevT[$id]";
		
						if ( !copy($kep, "$kimenet") )
							print("HIBA a m�sol�sn�l ($kimenet)!<br>\n");
						else  {
							//Bejegyz�s az adatb�zisba
							if(!mysql_query("insert kepek set kat='templomok', kid='$tid', fajlnev='$kepnevT[$id]', felirat='$kepfeliratT[$id]'")) echo 'HIBA!<br>'.mysql_error();
						}
					
						unlink($kep);
	
						$info=getimagesize($kimenet);
						$w=$info[0];
						$h=$info[1];
      
						if($w>800 or $h>600) kicsinyites($kimenet,$kimenet,800);
				  		kicsinyites($kimenet,$kimenet1,120);
					}
				}
			}
	
			$kepfeliratmodT=$_POST['kepfeliratmodT'];
			$kepsorszamT=$_POST['kepsorszamT'];
			if(is_array($kepsorszamT)) {
				foreach($kepsorszamT as $melyikkep=>$ertek) {
					//M�dos�t�s az adatb�zisban
					if(!mysql_query("update kepek set felirat='$kepfeliratmodT[$melyikkep]', sorszam='$ertek' where kat='templomok' and kid='$tid' and fajlnev='$melyikkep'")) echo 'HIBA!<br>'.mysql_error();
				}
			}		
		} 
	
		if($modosit=='i') $kod=feltoltes_addtemplom($tid);
		elseif($modosit=='m') $kod=feltoltes_addmise($tid);
		else $kod=feltoltes_index();
	}

	return $kod;
}


function feltoltes_addmise($tid) {
	global $sid,$linkveg,$m_id,$db_name,$onload,$u_beosztas,$u_login;	
	
	$most=date("Y-m-d H:i:s");		

	list($tnev,$tismertnev,$tvaros,$datumtol,$datumig)=mysql_fetch_row(mysql_query("select nev,ismertnev,varos,nyariido,teliido from templomok where id='$tid' and letrehozta='$u_login'"));
	if(empty($tnev) and $tid>0) {
		//HIBA! Nem jogosult!!!
		$urlap.="<span class=hiba>HIBA! A templomot nem tal�lom az adatb�zisban!</span>";
	}
	else {
		$query="select nap,ido,idoszamitas,nyelv,milyen,megjegyzes from misek where templom='$tid' and torolte=''";
		if(!$lekerdez=mysql_query($query)) echo 'HIBA!<br>'.mysql_error();	
		if(mysql_num_rows($lekerdez)>0) {
			$idopontT=array('','-','-','-','-','-','-','-');
			$idoponttT=array('','-','-','-','-','-','-','-');
		}
		while(list($nap,$ido,$idoszamitas,$nyelv,$milyen,$megjegyzes)=mysql_fetch_row($lekerdez)) {
			$ido=substr($ido,0,-3);
			$ido=str_replace(':',',',$ido);
			if($idoszamitas=='ny') {
				$misenyaridb[$nap]++;
				if($idopontT[$nap]!='-') $idopontT[$nap].='+'.$ido;
				else $idopontT[$nap]=$ido;
				if(!empty($nyelvT[$nap])) $nyelvT[$nap].='+'.$nyelv;
				else $nyelvT[$nap]=$nyelv;
				if(!empty($gitarosT[$nap])) $gitarosT[$nap].=$milyen.'+';
				else $gitarosT[$nap]=$milyen.'+';
				if(!empty($megjT[$nap])) $megjT[$nap].=$megjegyzes."+";
				else $megjT[$nap]=$megjegyzes."+";		
			}
			if($idoszamitas=='t') {
				$misetelidb[$nap]++;
				if($idoponttT[$nap]!='-') $idoponttT[$nap].='+'.$ido;
				else $idoponttT[$nap]=$ido;
				if(!empty($nyelvtT[$nap])) $nyelvtT[$nap].='+'.$nyelv;
				else $nyelvtT[$nap]=$nyelv;
				if(!empty($gitarostT[$nap])) $gitarostT[$nap].=$milyen.'+';
				else $gitarostT[$nap]=$milyen.'+';
				if(!empty($megjtT[$nap])) $megjtT[$nap].=$megjegyzes."+";
				else $megjtT[$nap]=$megjegyzes."+";	
			}
		}
		list($tnev,$tvaros,$datumtol,$datumig,$misemegj)=mysql_fetch_row(mysql_query("select nev,varos,nyariido,teliido,misemegj from templomok where id='$tid'"));

		foreach($gitarosT as $kulcs=>$ertek) {
			$csakplusz='';
			$hossz=strlen($gitarosT[$kulcs]);
			$ujhossz=$hossz-1;
			$gitarosT[$kulcs]=substr($gitarosT[$kulcs],0,$ujhossz);
			for($i=0;$i<($misenyaridb[$kulcs]-1);$i++) {
				$csakplusz.='+';
			}
			if($gitarosT[$kulcs]==$csakplusz) $gitarosT[$kulcs]='';
		}
		
		foreach($gitarostT as $kulcs=>$ertek) {
			$csakplusz='';
			$hossz=strlen($gitarostT[$kulcs]);
			$ujhossz=$hossz-1;
			$gitarostT[$kulcs]=substr($gitarostT[$kulcs],0,$ujhossz);
			for($i=0;$i<($misetelidb[$kulcs]-1);$i++) {
				$csakplusz.='+';
			}
			if($gitarostT[$kulcs]==$csakplusz) $gitarostT[$kulcs]='';
		}
	
		foreach($megjT as $kulcs=>$ertek) {
			$csakplusz='';
			$hossz=strlen($megjT[$kulcs]);
			$ujhossz=$hossz-1;
			$megjT[$kulcs]=substr($megjT[$kulcs],0,$ujhossz);
			for($i=0;$i<($misenyaridb[$kulcs]-1);$i++) {
				$csakplusz.="+";
			}
			if($megjT[$kulcs]==$csakplusz) $megjT[$kulcs]='';
			else str_replace("+","\n+",$megjT[$kulcs]);
		}
		
		foreach($megjtT as $kulcs=>$ertek) {
			$csakplusz='';
			$hossz=strlen($megjtT[$kulcs]);
			$ujhossz=$hossz-1;
			$megjtT[$kulcs]=substr($megjtT[$kulcs],0,$ujhossz);
			for($i=0;$i<($misetelidb[$kulcs]-1);$i++) {
				$csakplusz.="+";
			}
			if($megjtT[$kulcs]==$csakplusz) $megjtT[$kulcs]='';
			else str_replace("+","\n+",$megjtT[$kulcs]);
		}

		$urlap.="\n<FORM ENCTYPE='multipart/form-data' method=post>";

		$urlap.="\n<input type=hidden name=m_id value=$m_id><input type=hidden name=sid value=$sid>";
		$urlap.="\n<input type=hidden name=m_op value=addingmise><input type=hidden name=tid value=$tid>";
		$urlap.="\n<input type=hidden name=datumtol value=$datumtol><input type=hidden name=datumig value=$datumig>";
	
		$urlap.='<table cellpadding=4 width=100%>';

	//n�v
		$urlap.="\n<tr><td bgcolor=#F5CC4C><div class=kiscim align=right>Templom neve:</div></td><td bgcolor=#F5CC4C><span class=kiscim>$tnev ($tvaros)</span><br><span class=alap><i>$tismertnev</i></span></td></tr>";

	//id�sz�m�t�s
		$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>Ny�ri id�sz�m�t�s:</div></td><td bgcolor=#efefef><span class=kiscim>$datumtol - $datumig</span><span class=alap> (a templom adatain�l m�dos�that�!)</span></td></tr>";
		
	//Misemegjegyz�s
	$urlap.="\n<tr><td bgcolor=#D6F8E6><span class=kiscim>Kieg�sz�t� inf�k:</span><br><a href=\"javascript:OpenNewWindow('sugo.php?id=41',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></td><td bgcolor=#D6F8E6>";
	$urlap.="<span class=alap>Rendszeres r�zsaf�z�r, szents�gim�d�s, hittan, stb.</span><br><textarea name=misemegj class=urlap cols=50 rows=10>$misemegj</textarea></td></tr>";

	//miserend
		$urlap.="\n<tr><td><span class=kiscim>Miseid�pontok:</span></td><td>";
		$urlap.="<span class=alap><font color=red><b>Kit�lt�si �tmutat� alul, a vas�rnap ut�n l�that�</b></font></span></td></tr>";

	//h�tf�
		$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>h�tf�:</div></td><td bgcolor=#efefef>";
		$urlap.="<input type=text name=idopontT[1] value=\"$idopontT[1]\" class=urlap size=30><span class=alap> ny�ri misekezd�sek</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=idoponttT[1] value=\"$idoponttT[1]\" class=urlap size=30><span class=alap> t�li misekezd�sek, ha k�l�nb�zik</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><input type=text name=nyelvT[1] value=\"$nyelvT[1]\" class=urlap size=30><span class=alap> nyelvek ny�ron</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=nyelvtT[1] value=\"$nyelvtT[1]\" class=urlap size=30><span class=alap> nyelvek t�len</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><input type=text name=gitarosT[1] value=\"$gitarosT[1]\" class=urlap size=30><span class=alap> [<b>g</b>]it�ros, [<b>cs</b>]endes, [<b>d</b>]i�k ny�ron</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=gitarostT[1] value=\"$gitarostT[1]\" class=urlap size=30><span class=alap> t�len</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><textarea name=megjT[1] class=urlap cols=60 rows=4>$megjT[1]</textarea><span class=alap> megjegyz�sek</span>";
		$urlap.="<br><textarea name=megjtT[1] class=urlap cols=60 rows=4>$megjtT[1]</textarea><span class=alap> t�li megjegyz�sek</span>";
		$urlap.="</td></tr>";

	//kedd
		$urlap.="\n<tr><td bgcolor=#D6F8E6><div class=kiscim align=right>kedd:</div></td><td bgcolor=#D6F8E6>";
		$urlap.="<input type=text name=idopontT[2] value=\"$idopontT[2]\" class=urlap size=30><span class=alap> ny�ri misekezd�sek</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=idoponttT[2] value=\"$idoponttT[2]\" class=urlap size=30><span class=alap> t�li misekezd�sek, ha k�l�nb�zik</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><input type=text name=nyelvT[2] value=\"$nyelvT[2]\" class=urlap size=30><span class=alap> nyelvek ny�ron</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=nyelvtT[2] value=\"$nyelvtT[2]\" class=urlap size=30><span class=alap> nyelvek t�len</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><input type=text name=gitarosT[2] value=\"$gitarosT[2]\" class=urlap size=30><span class=alap> [<b>g</b>]it�ros, [<b>cs</b>]endes, [<b>d</b>]i�k ny�ron</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=gitarostT[2] value=\"$gitarostT[2]\" class=urlap size=30><span class=alap> t�len</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><textarea name=megjT[2] class=urlap cols=60 rows=4>$megjT[2]</textarea><span class=alap> megjegyz�sek</span>";
		$urlap.="<br><textarea name=megjtT[2] class=urlap cols=60 rows=4>$megjtT[2]</textarea><span class=alap> t�li megjegyz�sek</span>";
		$urlap.="</td></tr>";

	//szerda
		$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>szerda:</div></td><td bgcolor=#efefef>";
		$urlap.="<input type=text name=idopontT[3] value=\"$idopontT[3]\" class=urlap size=30><span class=alap> ny�ri misekezd�sek</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=idoponttT[3] value=\"$idoponttT[3]\" class=urlap size=30><span class=alap> t�li misekezd�sek, ha k�l�nb�zik</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><input type=text name=nyelvT[3] value=\"$nyelvT[3]\" class=urlap size=30><span class=alap> nyelvek ny�ron</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=nyelvtT[3] value=\"$nyelvtT[3]\" class=urlap size=30><span class=alap> nyelvek t�len</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><input type=text name=gitarosT[3] value=\"$gitarosT[3]\" class=urlap size=30><span class=alap> [<b>g</b>]it�ros, [<b>cs</b>]endes, [<b>d</b>]i�k ny�ron</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=gitarostT[3] value=\"$gitarostT[3]\" class=urlap size=30><span class=alap> t�len</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><textarea name=megjT[3] class=urlap cols=60 rows=4>$megjT[3]</textarea><span class=alap> megjegyz�sek</span>";
		$urlap.="<br><textarea name=megjtT[3] class=urlap cols=60 rows=4>$megjtT[3]</textarea><span class=alap> t�li megjegyz�sek</span>";
		$urlap.="</td></tr>";

	//cs�t�rt�k
		$urlap.="\n<tr><td bgcolor=#D6F8E6><div class=kiscim align=right>cs�t�rt�k:</div></td><td bgcolor=#D6F8E6>";
		$urlap.="<input type=text name=idopontT[4] value=\"$idopontT[4]\" class=urlap size=30><span class=alap> ny�ri misekezd�sek</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=idoponttT[4] value=\"$idoponttT[4]\" class=urlap size=30><span class=alap> t�li misekezd�sek, ha k�l�nb�zik</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><input type=text name=nyelvT[4] value=\"$nyelvT[4]\" class=urlap size=30><span class=alap> nyelvek ny�ron</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=nyelvtT[4] value=\"$nyelvtT[4]\" class=urlap size=30><span class=alap> nyelvek t�len</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><input type=text name=gitarosT[4] value=\"$gitarosT[4]\" class=urlap size=30><span class=alap> [<b>g</b>]it�ros, [<b>cs</b>]endes, [<b>d</b>]i�k ny�ron</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=gitarostT[4] value=\"$gitarostT[4]\" class=urlap size=30><span class=alap> t�len</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><textarea name=megjT[4] class=urlap cols=60 rows=4>$megjT[4]</textarea><span class=alap> megjegyz�sek</span>";
		$urlap.="<br><textarea name=megjtT[4] class=urlap cols=60 rows=4>$megjtT[4]</textarea><span class=alap> t�li megjegyz�sek</span>";
		$urlap.="</td></tr>";

	//p�ntek
		$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>p�ntek:</div></td><td bgcolor=#efefef>";
		$urlap.="<input type=text name=idopontT[5] value=\"$idopontT[5]\" class=urlap size=30><span class=alap> ny�ri misekezd�sek</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=idoponttT[5] value=\"$idoponttT[5]\" class=urlap size=30><span class=alap> t�li misekezd�sek, ha k�l�nb�zik</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><input type=text name=nyelvT[5] value=\"$nyelvT[5]\" class=urlap size=30><span class=alap> nyelvek ny�ron</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=nyelvtT[5] value=\"$nyelvtT[5]\" class=urlap size=30><span class=alap> nyelvek t�len</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><input type=text name=gitarosT[5] value=\"$gitarosT[5]\" class=urlap size=30><span class=alap> [<b>g</b>]it�ros, [<b>cs</b>]endes, [<b>d</b>]i�k ny�ron</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=gitarostT[5] value=\"$gitarostT[5]\" class=urlap size=30><span class=alap> t�len</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><textarea name=megjT[5] class=urlap cols=60 rows=4>$megjT[5]</textarea><span class=alap> megjegyz�sek</span>";
		$urlap.="<br><textarea name=megjtT[5] class=urlap cols=60 rows=4>$megjtT[5]</textarea><span class=alap> t�li megjegyz�sek</span>";
		$urlap.="</td></tr>";

	//szombat
		$urlap.="\n<tr><td bgcolor=#F1BF8F><div class=kiscim align=right>szombat:</div></td><td bgcolor=#F1BF8F>";
		$urlap.="<input type=text name=idopontT[6] value=\"$idopontT[6]\" class=urlap size=30><span class=alap> ny�ri misekezd�sek</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=idoponttT[6] value=\"$idoponttT[6]\" class=urlap size=30><span class=alap> t�li misekezd�sek, ha k�l�nb�zik</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><input type=text name=nyelvT[6] value=\"$nyelvT[6]\" class=urlap size=30><span class=alap> nyelvek ny�ron</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=nyelvtT[6] value=\"$nyelvtT[6]\" class=urlap size=30><span class=alap> nyelvek t�len</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><input type=text name=gitarosT[6] value=\"$gitarosT[6]\" class=urlap size=30><span class=alap> [<b>g</b>]it�ros, [<b>cs</b>]endes, [<b>d</b>]i�k ny�ron</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=gitarostT[6] value=\"$gitarostT[6]\" class=urlap size=30><span class=alap> t�len</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><textarea name=megjT[6] class=urlap cols=60 rows=4>$megjT[6]</textarea><span class=alap> megjegyz�sek</span>";
		$urlap.="<br><textarea name=megjtT[6] class=urlap cols=60 rows=4>$megjtT[6]</textarea><span class=alap> t�li megjegyz�sek</span>";
		$urlap.="</td></tr>";

	//vas�rnap
		$urlap.="\n<tr><td bgcolor=#E67070><div class=kiscim align=right>vas�rnap:</div></td><td bgcolor=#E67070>";
		$urlap.="<input type=text name=idopontT[7] value=\"$idopontT[7]\" class=urlap size=30><span class=alap> ny�ri misekezd�sek</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=idoponttT[7] value=\"$idoponttT[7]\" class=urlap size=30><span class=alap> t�li misekezd�sek, ha k�l�nb�zik</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><input type=text name=nyelvT[7] value=\"$nyelvT[7]\" class=urlap size=30><span class=alap> nyelvek ny�ron</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=nyelvtT[7] value=\"$nyelvtT[7]\" class=urlap size=30><span class=alap> nyelvek t�len</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><input type=text name=gitarosT[7] value=\"$gitarosT[7]\" class=urlap size=30><span class=alap> [<b>g</b>]it�ros, [<b>cs</b>]endes, [<b>d</b>]i�k ny�ron</span>";
		$urlap.="<br>&nbsp; &nbsp;<input type=text name=gitarostT[7] value=\"$gitarostT[7]\" class=urlap size=30><span class=alap> t�len</span>";
		$urlap.="<br><img src=img/space.gif width=5 height=4>";
		$urlap.="<br><textarea name=megjT[7] class=urlap cols=60 rows=4>$megjT[7]</textarea><span class=alap> megjegyz�sek</span>";
		$urlap.="<br><textarea name=megjtT[7] class=urlap cols=60 rows=4>$megjtT[7]</textarea><span class=alap> t�li megjegyz�sek</span>";
		$urlap.="</td></tr>";

	//s�g�
		$urlap.="\n<tr><td><span class=kiscim>Kit�lt�si �tmutat�:</span></td><td>";
		$urlap.="<span class=alap>Kit�lt�sn�l a h�tf� az alapnap, a t�bbi napn�l, ha nincs kit�ltve, a h�tf�i miseadatokat m�solja be automatikusan (csak, ha nincs kit�ltve miseid�pont!). Ha valamelyik napon nincs mise, ott ki kell h�zni egy gondolatjellel (<b>-</b>), �gy akkor nem m�solja. A t�li adatokn�l mindig a ny�ri az alap�rtelmezett, ha ott nincs kit�ltve, akkor a ny�rit m�solja be automatikusan, nem a h�tf�i t�lit. Itt is �rv�nyes, ha t�len valami nincs, akkor ki kell h�zni!</span>";

		$urlap.="<br><br><span class=alap> <b>misekezd�sek</b> <input type=text value=\"9,00+18,00\" class=urlap size=10 disabled> Az id�pontn�l <b>�ra,perc (0,00)</b> a form�tum, t�bb id�pontn�l az <b>elv�laszt� a +</b> jel (p�lda az �rlapban). <br>T�li adatokat csak akkor kell megadni, ha az elt�r� a ny�rit�l.</span>";
	
		$urlap.="<br><br><span class=alap><b>nyelvek</b> (h vagy �res=magyar, e=angol, d=n�met, i=olasz, l=latin, g=g�r�g)<br>A nyelvek a be�ll�tott miseid�pontokhoz tartoznak, �gy az elv�laszt� itt is a <b>+</b> jel. El�fordulhatnak peri�dusok is, ebben az esetben a nyelv mellett a peri�dus sz�m�t kell felt�ntetni, pl d2,l3 -> minden h�nap m�sodik het�n n�met, harmadik het�n latin (A vessz� nem fontos, csak jobban tagolja). Fontos, hogy minden esetben a mejegyz�s rovatba sz�vegesen is t�ntess�k f�l!<br>";
		$urlap.="\n<u>P�lda 1:</u> a fenti 9-es mise magyar nyelv�, az esti 6-os viszont minden h�nap m�sodik vas�rnapj�n latin: <input type=text disabled class=urlap value=\"h0+,l2\" size=10> (<b>h0+l2</b>)";
		$urlap.="\n<br><u>P�lda 2:</u> a 9-es mise mindig n�met nyelv�, az esti 6-os viszont minden h�nap m�sodik vas�rnapj�n angol, egy�bk�nt latin:  <input type=text disabled class=urlap value=\"d0+l1,e2,l3,l4\" size=10> (<b>d0+l1,e2,l3,l4</b>)";
		$urlap.="\n<br><u>P�lda 3:</u> alapeset, minden mise magyar: ebben az esetben nem kell kit�lteni</span>";

		$urlap.="<br><br><span class=alap><b>git�ros, di�k, csendes</b> mis�k eset�n a nyelvekhez hasonl�an, a be�ll�tott miseid�pontokhoz tartoznak, �gy az elv�laszt� itt is a <b>+</b> jel. El�fordulhatnak peri�dusok is, ebben az esetben a h�t sz�m�t is fel kell t�ntetni, peri�dus n�lk�l 0-�t kell a bet�k�d m�g� �rni. Fontos, hogy minden esetben a mejegyz�s rovatba is t�ntess�k f�l!<br>Bet�k�dok: git�ros = g, csendes = cs, di�k = d";
		$urlap.="\n<br><u>P�lda 1:</u> a fenti 9-es mise git�ros, az esti 6-os viszont csendes: <input type=text disabled class=urlap value=\"g0+cs0\" size=10> (<b>g0+cs0</b>)";
		$urlap.="\n<br><u>P�lda 2:</u> a 9-es mise di�k mise �s a h�nap minden m�sodik vas�rnapj�n git�ros, az esti 6-os viszont rendes orgon�s:  <input type=text disabled class=urlap value=\"d0,g2+\" size=10> (<b>d0,g2+</b>)";

		$urlap.="<br><br><span class=alap><b>megjegyz�s</b> mivel nem minden param�tert tudunk pontosan be�ll�tani, illetve lehetnek egy�b elt�r�sek is, a megjegyz�s rovatba mindig t�ntess�k f�l a bizonytalan dolgokat. Pl. minden m�sodik h�ten git�ros mise, de �nnepekn�l, betegs�gekn�l cs�szhat. A megjegyz�sn�l is a <b>+</b> jel az elv�laszt� az egyes miseid�pontoknak megfelel�en. Tagolni lehet sort�r�ssel, nincs jelent�s�ge.</span>";

		$urlap.="</td></tr>";
		$urlap.='</table>';

		$urlap.="\n<br><input type=submit value=Mehet class=urlap>";
		if($tid>0) {
			$urlap.="<input type=checkbox name=modosit value=i class=urlap checked><span class=alap> �s �jra m�dos�t</span>";
			//$urlap.=" &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=?m_id=$m_id&m_op=addmegse&hid=$hid&kod=$kod$linkveg class=link><font color=red>Kil�p�s m�dos�t�s n�lk�l</font></a>";
		}
		else $urlap.="<input type=hidden name=modosit value=i>";
		$urlap.="\n</form>";
	}

	$adatT[2]='<span class=alcim>Templom felt�lt�se / m�dos�t�sa</span><br><br>'.$urlap;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function feltoltes_addingmise() {
	global $_POST,$sid,$m_id,$db_name,$u_login;

	$tid=$_POST['tid'];
	$idopontT=$_POST['idopontT'];
	$idoponttT=$_POST['idoponttT'];
	$nyelvT=$_POST['nyelvT'];
	$nyelvtT=$_POST['nyelvtT'];
	$gitarosT=$_POST['gitarosT'];
	$gitarostT=$_POST['gitarostT'];
	$misemegj=$_POST['misemegj'];
	$megjT=$_POST['megjT'];
	$megjT=str_replace("\n",'',$megjT);
	$megjtT=$_POST['megjtT'];
	$megjtT=str_replace("\n",'',$megjtT);
	$modosit=$_POST['modosit'];
	$datumtol=$_POST['datumtol'];
	$datumig=$_POST['datumig'];

	if($tid>0) {
		$ma=date('Y-m-d');
		$most=date('Y-m-d H:i:s');
		list($log)=mysql_fetch_row(mysql_query("select log from templomok where id='$tid'"));
		$log.="\nMISE_MOD: $u_login ($most)";
		$query="update templomok set misemegj='$misemegj', frissites='$ma', log='$log' where id='$tid' and letrehozta='$u_login'";
		mysql_query($query);

		$query="update misek set torles='$most', torolte='$u_login' where templom='$tid' and torolte=''";
		mysql_query($query);

	}


	for($nap=1;$nap<=7;$nap++) {
		$miseT=$idopontT[$nap];
		$misetT=$idoponttT[$nap]; //t�li
		$nyelvekT=explode('+',$nyelvT[$nap]);
		$milyenT=explode('+',$gitarosT[$nap]);
		$megjegyzesT=explode('+',$megjT[$nap]);

		if(empty($miseT)) { 
			//ha nincs kit�ltve, akkor a h�tf�it vessz�k �t
			$miseT=$idopontT[1];
			$misetT=$idoponttT[1];
			$nyelvekT=explode('+',$nyelvT[1]);
			$milyenT=explode('+',$gitarosT[1]);
			$megjegyzesT=explode('+',$megjT[1]);
		}		

		$miseT=str_replace(',',':',$miseT); // a ,-�t �talak�tjuk : pontt� a r�gz�t�shez
		$misekT=explode('+',$miseT); //ha t�bb lett megadva
		if(!empty($misetT)) {
			//Ha ki lett t�ltve (teh�t k�l�nb�zik a ny�rit�l)
			$misetT=str_replace(',',':',$misetT); // t�liben is �talak�tjuk
			$misektT=explode('+',$misetT); //ha t�bb lett megadva
		}
		else {
			//Ha nem lett kit�ltve, akkor a ny�ri �rv�nyes t�lre is
			$misektT=$misekT;
		}

		if(!empty($nyelvtT[$nap])) $nyelvektT=explode('+',$nyelvtT[$nap]); 
		else $nyelvektT=$nyelvekT; 
		if(!empty($gitarostT[$nap])) $milyentT=explode('+',$gitarostT[$nap]);
		else $milyentT=$milyenT;
		if(!empty($megjtT[$nap])) $megjegyzestT=explode('+',$megjtT[$nap]);
		else $megjegyzestT=$megjegyzesT;

		foreach($misekT as $id=>$mise) {
			if($mise!='-' and !empty($mise)) {
				if(empty($nyelvekT[$id])) $nyelvekT[$id]='h0';
				$query="insert misek set templom='$tid', nap='$nap', ido='$mise', idoszamitas='ny', datumtol='$datumtol', datumig='$datumig', nyelv='$nyelvekT[$id]', milyen='$milyenT[$id]', megjegyzes='$megjegyzesT[$id]', modositotta='$u_login', moddatum='$most'";
				mysql_query($query);
			}
		
		}
		foreach($misektT as $id=>$mise) {
			if($mise!='-' and !empty($mise)) {
				if(empty($nyelvektT[$id])) $nyelvektT[$id]='h0';
				$query="insert misek set templom='$tid', nap='$nap', ido='$mise', idoszamitas='t', datumtol='$datumtol', datumig='$datumig', nyelv='$nyelvektT[$id]', milyen='$milyentT[$id]', megjegyzes='$megjegyzestT[$id]', modositotta='$u_login', moddatum='$most'";
				mysql_query($query);
			}
		
		}

	}



	if($modosit=='i') {
		$kod=feltoltes_addmise($tid);
	}
	else {
		$kod=feltoltes_index();
	}
	
	return $kod;
}

function hirek_add($hid) {
	global $sessid,$linkveg,$m_id,$db_name,$onload,$u_login;	

	if(!is_numeric($hid) and !empty($hid)) {
		$kod.="<p class=hiba>HIBA!</p>";
	}
	else {
		if($hid>0) $tartalomkod.=include('editscript2.php');
		include_once('moduls/urlap_hirek.php');
		$kod.=hirek_urlap($hid);
	}

	return $kod;
}

function hirek_adding() {
	global $_POST,$db_name,$_FILES,$u_login,$u_beosztas,$_SERVER;

	include_once('moduls/urlap_hirek.php');
	$id=hirekadding();

	if(is_numeric($id) and $id>0) {
		$kod=hirek_add($id);
	}
	elseif($id==0) {
		$kod=feltoltes_index();
	}
	else {
		$kod=$id;
	}

	return $kod;
}

switch($m_op) {
    case 'index':
        $tartalom=feltoltes_index();
        break;

	case 'addhirek':
		$tartalom=hirek_add($hid);
		break;

	case 'addinghirek':
		$tartalom=hirek_adding();
		break;

	case 'hirek_mod':
		$tartalom=feltoltes_index();
        break;

	case 'addtemplom':
		$tid=$_GET['tid'];
        $tartalom=feltoltes_addtemplom($tid);
        break;

	case 'addmise':
		$tid=$_GET['tid'];
        $tartalom=feltoltes_addmise($tid);
        break;

    case 'addingtemplom':
        $tartalom=feltoltes_addingtemplom();
        break;

	case 'addingmise':
        $tartalom=feltoltes_addingmise();
        break;

	case 'addmegse':
		$tartalom=hirek_addmegse();
		break;

}

?>
