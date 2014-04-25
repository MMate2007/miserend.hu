<?php

function miserend_index() {
	global $linkveg,$m_id,$db_name;

	$kod=miserend_adminmenu();

	return $kod;
}

function miserend_adminmenu() {
	global $m_id,$linkveg,$u_beosztas,$db_name;

	$menu.='<span class=alcim>Templomok �s miserend szerkeszt�se</span><br><br>';

	$menu.="<a href=?m_id=$m_id&m_op=addtemplom$linkveg class=kismenulink>�j templom felt�lt�se</a><br>";
	$menu.="<a href=?m_id=$m_id&m_op=modtemplom$linkveg class=kismenulink>Megl�v� templom m�dos�t�sa, t�rl�se, miserend hozz�ad�sa, m�dos�t�sa</a><br>";

	$adatT[2]=$menu;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	

	return $tartalom;
}

function miserend_addtemplom($tid) {
	global $sid,$linkveg,$m_id,$db_name,$onload,$u_beosztas,$u_login,$design_url;	
	
	$query="select id,nev from egyhazmegye where ok='i' order by sorrend";
	$lekerdez=mysql_db_query($db_name,$query);
	while(list($id,$nev)=mysql_fetch_row($lekerdez)) {
		$ehmT[$id]=$nev;
	}

	$query="select id,ehm,nev from espereskerulet";
	$lekerdez=mysql_db_query($db_name,$query);
	while(list($id,$ehm,$nev)=mysql_fetch_row($lekerdez)) {
		$espkerT[$ehm][$id]=$nev;
	}

	$query="select id,nev from orszagok where kiemelt='i'";
	$lekerdez=mysql_db_query($db_name,$query);
	while(list($id,$nev)=mysql_fetch_row($lekerdez)) {
		$orszagT[$id]=$nev;
	}

	$query="select id,megyenev,orszag from megye";
	$lekerdez=mysql_db_query($db_name,$query);
	while(list($id,$nev,$orszag)=mysql_fetch_row($lekerdez)) {
		$megyeT[$orszag][$id]=$nev;
	}

	$query="select megye_id,orszag,nev from varosok order by nev";
	$lekerdez=mysql_db_query($db_name,$query);
	while(list($megye,$orszag,$vnev)=mysql_fetch_row($lekerdez)) {		
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
		$varos1T[$orszag][$megye][]=$vnev1;
		$varosT[$orszag][$megye][$vnev1]=$vnev;
	}
	foreach($varos1T as $orszagid=>$m1T) {
		foreach($m1T as $megyeid=>$v1T) {
			asort($v1T, SORT_STRING);
			$varos1T[$orszagid][$megyeid]=$v1T;
		}
	}

	if($tid>0) {
		$most=date("Y-m-d H:i:s");
		$urlap.=include('editscript2.php'); //Csak, ha m�dos�t�sr�l van sz�

		$query="select nev,ismertnev,turistautak,orszag,megye,varos,cim,megkozelites,plebania,pleb_url,pleb_eml,egyhazmegye,espereskerulet,leiras,megjegyzes,szomszedos1,szomszedos2,bucsu,nyariido,teliido,frissites,kontakt,kontaktmail,adminmegj,log,ok,letrehozta,megbizhato,eszrevetel from templomok where id='$tid'";
		if(!$lekerdez=mysql_db_query($db_name,$query)) echo 'HIBA!<br>'.mysql_error();	list($nev,$ismertnev,$turistautak,$orszag,$megye,$varos,$cim,$megkozelites,$plebania,$pleb_url,$pleb_eml,$egyhazmegye,$espereskerulet,$szoveg,$megjegyzes,$szomszedos1,$szomszedos2,$bucsu,$nyariido,$teliido,$frissites,$kontakt,$kontaktmail,$adminmegj,$log,$ok,$feltolto,$megbizhato,$teszrevetel)=mysql_fetch_row($lekerdez);
	}
	else {
		$datum=date('Y-m-d H:i');
		$nyariido='2014-03-30';
		$teliido='2014-10-25';
		$urlapkieg="\n<input type=hidden name=elsofeltoltes value=i>";
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
	if(!$lekerdeze=mysql_db_query($db_name,$querye)) echo "HIBA!<br>$querym<br>".mysql_error();
	while(list($templom)=mysql_fetch_row($lekerdeze)) {
		$vaneszrevetelT[$templom]=true;
	}

	if($teszrevetel=='i') $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomag.gif title='�j �szrev�telt �rtak hozz�!' align=absmiddle border=0></a> ";		
	elseif($teszrevetel=='f') $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomagf.gif title='�szrev�tel jav�t�sa folyamatban!' align=absmiddle border=0></a> ";		
	elseif($vaneszrevetelT[$tid]) $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomag1.gif title='�szrev�telek!' align=absmiddle border=0></a> ";		
	else $jelzes='<span class=alap>Nincs</span>';

	$urlap.="\n<tr><td colspan=2><span class=kiscim>�szrev�tel: </span>$jelzes</td></tr>";

	if($tid>0) {
//Megn�z
		$urlap.="\n<tr><td colspan=2><span class=kiscim>Nyilv�nos oldal megnyit�sa:</span><span class=alap> (�j ablakban)</span> <a href=?templom=$tid class=link target=_blank><u>$nev</u></a></td></tr>";
	}

//megjegyz�s
	$urlap.="\n<tr><td bgcolor=#ECE5C8><div class=kiscim align=right>Megjegyz�s:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=1',200,300);\"><img src=img/sugo.gif border=0 title='S�g�'></a></div></td><td bgcolor=#ECE5C8><textarea name=adminmegj class=urlap cols=50 rows=3>$adminmegj</textarea><span class=alap> a szerkeszt�ssel kapcsolatosan</span></td></tr>";

//kontakt
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>Felel�s:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=2',200,300);\"><img src=img/sugo.gif border=0 title='S�g�'></a></div></td><td bgcolor=#efefef><textarea name=kontakt class=urlap cols=50 rows=2>$kontakt</textarea><span class=alap> n�v �s telefonsz�m</span><br><input type=text name=kontaktmail size=40 class=urlap value='$kontaktmail'><span class=alap> emailc�m</span></td></tr>";
//felt�lt�
	if(empty($feltolto)) $feltolto=$u_login;
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>Felt�lt� (jogosult):</div></td><td bgcolor=#efefef>";
	$urlap.="<select name=feltolto class=urlap><option value=''>Nincs</option>";
	$query="select login from user where ok='i' order by login";
	$lekerdez=mysql_db_query($db_name,$query);
	while(list($user)=mysql_fetch_row($lekerdez)) {
		$urlap.="<option value='$user'";
		if($user==$feltolto) $urlap.=" selected";
		$urlap.=">$user</option>";
	}
	$urlap.="</select> <input type=checkbox name=megbizhato class=urlap value=i";
	if($megbizhato!='n') $urlap.=" checked";
	$urlap.="><span class=alap> megb�zhat�, nem kell k�l�n enged�lyezni</span></td></tr>";

//n�v
	$urlap.="\n<tr><td bgcolor=#F5CC4C><div class=kiscim align=right>Templom neve:</div></td><td bgcolor=#F5CC4C><input type=text name=nev value=\"$nev\" class=urlap size=80 maxlength=150> <a href=\"javascript:OpenNewWindow('sugo.php?id=3',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></td></tr>";
	$urlap.="\n<tr><td bgcolor=#FAE19C><div class=kiscim align=right>k�zismert neve:</div></td><td bgcolor=#FAE19C><input type=text name=ismertnev value=\"$ismertnev\" class=urlap size=80 maxlength=150> <a href=\"javascript:OpenNewWindow('sugo.php?id=4',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a><br><span class=alap>(Helyben elfogadott (ismert) templomn�v, valamint telep�l�s, vagy telep�l�s r�szn�v, amennyiben elt�r� a telep�l�s hivatalos nev�t�l, pl. <u>izb�gi templom</u>)</span></td></tr>";

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

	//Orsz�g
	$urlap.="<img src=img/space.gif width=5 height=8><br>\n<select name=orszag class=urlap onChange=\"if(this.value!=0) {";
	foreach($orszagT as $id=>$nev) {
		$urlap.="document.getElementById('m$id').style.display='none'; ";
	} 
	$urlap.="document.getElementById('m'+this.value).style.display='inline';} else {";
	foreach($orszagT as $id=>$nev) {
		$urlap.="document.getElementById('m$id').style.display='none'; ";
	} 
	$urlap.="}\"><option value=0>Nincs / nem tudom</option>";	
	foreach($orszagT as $id=>$nev) {
		$urlap.="\n<option value=$id";
		if($orszag==$id) $urlap.=' selected';
		$urlap.=">$nev</option>";
		
		if($orszag==$id) $mdisplay='inline';
		else $mdisplay='none';
		//megye
		if(is_array($megyeT[$id])) {

			$megyeurlap.="\n<div id=m$id style='display: $mdisplay'><select name=megyeT[$id] class=keresourlap onChange=\"if(this.value!=0) {";
			foreach($megyeT[$id] as $meid=>$nev) {
				$megyeurlap.="document.getElementById('v$meid').style.display='none'; ";
			} 
			$megyeurlap.="document.getElementById('v'+this.value).style.display='inline';} else {";
			foreach($megyeT[$id] as $meid=>$nev) {
				$megyeurlap.="document.getElementById('v$meid').style.display='none'; ";
			} 
			$megyeurlap.="}\"><option value=0>Nincs / nem tudom</option>";	
			foreach($megyeT[$id] as $meid=>$mnev) {
				$megyeurlap.="\n<option value='$meid'";
				if($meid==$megye) $megyeurlap.=' selected';
				$megyeurlap.=">$mnev</option>";

				//telep�l�s
				if($megye==$meid) $vdisplay='inline';
				else $vdisplay='none';
		
				$varosurlap.="\n<div id=v$meid style='display: $vdisplay'><select name=varosT[$id][$meid] class=keresourlap><option value=0>Nincs / nem tudom</option>";	
				if(is_array($varos1T[$id][$meid])) {
					foreach($varos1T[$id][$meid] as $vnev1) {
						$varosurlap.="\n<option value='".$varosT[$id][$meid][$vnev1]."'";
						if($varosT[$id][$meid][$vnev1]==$varos) $varosurlap.=' selected';
						$varosurlap.=">".$varosT[$id][$meid][$vnev1]."</option>";
					}
				}
				else $varosurlap.="<option value=0 selected>NINCS telep�l�s felt�ltve!!!</option>";
				$varosurlap.="</select><span class=alap> (telep�l�s)</span><br></div>";

			}
			$megyeurlap.="</select><span class=alap> (megye)</span><br></div>";
		}
		else {
			//telep�l�s
		
				$varosurlap.="\n<div id=m$id style='display: $mdisplay'><select name=varosT[$id][0] class=keresourlap><option value=0>Nincs / nem tudom</option>";
				if(is_array($varos1T[$id][0])) {	
					foreach($varos1T[$id][0] as $vnev1) {
						$varosurlap.="\n<option value='".$varosT[$id][0][$vnev1]."'";
						if($varosT[$id][0][$vnev1]==$varos) $varosurlap.=' selected';
						$varosurlap.=">".$varosT[$id][0][$vnev1]."</option>";
					}
				}
				$varosurlap.="</select><span class=alap> (telep�l�s)</span><br></div>";
		}
	}
	$urlap.="</select><span class=alap> (orsz�g)</span><br>";

	//Telep�l�s
	$urlap.=$megyeurlap.$varosurlap;
	$urlap.="<input type=text name=cim value=\"$cim\" class=urlap size=60 maxlength=250><span class=alap> (utca, h�zsz�m)</span>";
	$urlap.="<br><img src=img/space.gif widt=5 height=5><br><textarea name=megkozelites class=urlap cols=50 rows=2>$megkozelites</textarea><span class=alap> (megk�zel�t�s r�vid le�r�sa)</span>";
	$urlap.="</td></tr>";

//pl�b�nia
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>Pl�b�nia adatai:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=6',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td bgcolor=#efefef><textarea name=plebania class=urlap cols=50 rows=3>$plebania</textarea><span class=alap> c�m, telefon, fax, kontakt</span>";
	$urlap.="<br><input type=text name=pleb_eml value='$pleb_eml' size=40 class=urlap maxlength=100><span class=alap> email</span>";
	$urlap.="<br><input type=text name=pleb_url value='$pleb_url' size=40 class=urlap maxlength=100><span class=alap> web http://-rel egy�tt!!!</span>";
	$urlap.="</td></tr>";


//megjegyz�s
	$urlap.="\n<tr><td bgcolor=#ffffff><div class=kiscim align=right>Megjegyz�s:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=10',200,360);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td bgcolor=#ffffff><textarea name=megjegyzes class=urlap cols=50 rows=3>$megjegyzes</textarea><br><span class=alap> ami a \"j� tudni...\" dobozban megjelenik (pl. b�cs�, v�d�szent, \"rekl�m\" stb.)</span></td></tr>";

//ny�ri-t�li id�sz�m�t�s
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>Ny�ri id�sz�m�t�s:</div></td><td bgcolor=#efefef><input type=text name=nyariido value=\"$nyariido\" class=urlap size=10 maxlength=10><span class=alap> - </span><input type=text name=teliido value=\"$teliido\" class=urlap size=10 maxlength=10> <a href=\"javascript:OpenNewWindow('sugo.php?id=8',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></td></tr>";

//Sz�veg
	$urlap.="<tr><td valign=top><div class=kiscim align=right>R�szletes le�r�s, templom t�rt�nete:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=9',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td valign=top><span class=alap><font color=red><b>FONTOS!</b></font> A sz�veghez MINDIG legyen st�lus rendelve!</span><br><textarea name=szoveg class=urlap cols=90 rows=30>$szoveg</textarea>";

	$urlap.="\n</td></tr>";

//F�jlok
	$urlap.="\n<tr><td bgcolor=#efefef valign=top><div class=kiscim align=right>Let�lthet� f�jl(ok):</td><td bgcolor=#efefef valign=top>";
	$urlap.="\n<span class=alap>Kapcsol�d� dokumentum, ha van ilyen:</span><br>";
	$urlap.="\n<span class=alap>�j f�jl: </span><input type=file size=60 name=fajl class=urlap> <a href=\"javascript:OpenNewWindow('sugo.php?id=12',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a><br>";
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
					$urlap.="<br><li><a href='$konyvtar/$filekiir' class=alap><b>$file</b></a><span class=alap> ($meret) </span><input type=checkbox class=urlap name=delfajl[] value='$file'><span class=alap>T�r�l</span></li>";
				}
			}
			closedir($handle);
		}
	}

//K�pek
	$urlap.="\n<tr><td><div class=kiscim align=right>K�pek:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=11',200,450);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td><span class=alap><font color=red>FIGYELEM!</font><br>Azonos nev� k�pek fel�l�rj�k egym�st!!! A f�jln�vben ne legyen �kezet �s sz�k�z!</span><br><input type=file name=kepT[] class=urlap size=20> <span class=alap>K�pfelirat: </span><input type=text name=kepfeliratT[] size=40 maxlength=100 class=urlap><br><input type=file name=kepT[] class=urlap size=20> <span class=alap>K�pfelirat: </span><input type=text name=kepfeliratT[] size=40 maxlength=100 class=urlap><br><input type=file name=kepT[] class=urlap size=20> <span class=alap>K�pfelirat: </span><input type=text name=kepfeliratT[] size=40 maxlength=100 class=urlap>";
	if($tid>0) {
		//Megl�v� k�pek list�ja
		$query="select fajlnev,felirat,sorszam,kiemelt from kepek where kat='templomok' and kid='$tid' order by sorszam";
		$lekerdez=mysql_db_query($db_name,$query);
		$konyvtar="kepek/templomok/$tid/kicsi";
		$urlap.="\n<table width=100% cellpadding=0 cellspacing=0><tr>";
		while(list($fajlnev,$felirat,$sorszam,$kiemelt)=mysql_fetch_row($lekerdez)) {			
			if($a%3==0 and $a>0) $urlap.="</tr><tr>";
			$a++;
			if($kiemelt=='n') $fokepchecked='';
			else $fokepchecked=' checked';
			$urlap.="\n<td valign=bottom><img src=$konyvtar/$fajlnev title='$fajlnev'><br><input type=text name=kepsorszamT[$fajlnev] value='$sorszam' maxlength=2 size=1 class=urlap><span class=alap> -f�oldal:</span><input type=checkbox name=fooldalkepT[$fajlnev] $fokepchecked value='i' class=urlap><span class=alap> -t�r�l:</span><input type=checkbox name=delkepT[] value='$fajlnev' class=urlap><br><input type=text name=kepfeliratmodT[$fajlnev] value='$felirat' maxlength=250 size=20 class=urlap></td>";
		}
		$urlap.='</tr></table>';
	}
	$urlap.='</td></tr>';


/*
	$query="select id,nev,ismertnev,varos from templomok where id!='$tid' order by varos";
	$lekerdez=mysql_db_query($db_name,$query);
	while(list($eid,$enev,$eismert,$evaros)=mysql_fetch_row($lekerdez)) {
		if(strlen($enev)>65) $enev=substr($enev,0,65).'...';
		$ismT[$eid]=" title='$eismert'>$evaros -> [$enev]";
	}

//szomsz�dos 1
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>Szomsz�dos templomok (legk�zelebbi):<br><a href=\"javascript:OpenNewWindow('sugo.php?id=13',200,500);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td bgcolor=#efefef>";
	
	if(!empty($szomszedos1)) {
		$sz1=str_replace('--','!',$szomszedos1);
		$sz1=str_replace('-','',$sz1);
		$sz1T=explode('!',$sz1);
		if(is_array($sz1T)) {
			$urlap.="\n<table width=100% bgcolor=#ECE5C8><tr><td colspan=2><span class=kiscim>Kiv�lasztott legk�zelebbi templomok:</span></td></tr>";

			foreach($sz1T as $ertek) {
				if($ertek!=$tid) {
					$urlap.="<input type=hidden name=oldsz1T[] value=$ertek>";
					$urlap.="\n<tr><td bgcolor=#FEFDFA><a href=?m_id=27&m_op=addtemplom&tid=$ertek target=_blank class=link ".$ismT[$ertek]."</a></td><td bgcolor=#FEFDFA><input type=checkbox name='delsz1T[]' value='$ertek' class=urlap><span class=alap> t�r�l</span></td></tr>";
				}
			}
			$urlap.='</table><hr>';
		}
	}
	
	$urlap.="<span class=kiscim>Hozz�ad�s:</span><br><span class=alap>CTRL-lal t�bb is kijel�lhet�, illetve visszavonhat�!<br></span><select name=szomszedos1T[] class=urlap multiple size=10>";
	foreach($ismT as $eid=>$enev) {		
		if(!strstr($szomszedos1,"-$eid-")) {
			$urlap.="\n<option value='$eid'";
			$urlap.="$enev</option>";
		}
	}
	$urlap.="</select>";
	$urlap.="</td></tr>";

//szomsz�dos 2
	$urlap.="\n<tr><td><div class=kiscim align=right>Szomsz�dos templomok (10km-en bel�li):<br><a href=\"javascript:OpenNewWindow('sugo.php?id=13',200,500);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td>";

	if(!empty($szomszedos2)) {
		$sz2=str_replace('--','!',$szomszedos2);
		$sz2=str_replace('-','',$sz2);
		$sz2T=explode('!',$sz2);
		if(is_array($sz2T)) {
			$urlap.="\n<table width=100% bgcolor=#ECE5C8><tr><td colspan=2><span class=kiscim>Kiv�lasztott 10km-en bel�li templomok:</span></td></tr>";

			foreach($sz2T as $ertek) {
				if($ertek!=$tid) {
					$urlap.="<input type=hidden name=oldsz2T[] value=$ertek>";
					$urlap.="\n<tr><td bgcolor=#FEFDFA><a href=?m_id=27&m_op=addtemplom&tid=$ertek target=_blank class=link ".$ismT[$ertek]."</a></td><td bgcolor=#FEFDFA><input type=checkbox name='delsz2T[]' value='$ertek' class=urlap><span class=alap> t�r�l</span></td></tr>";
				}
			}
			$urlap.='</table><hr>';
		}
	}

	$urlap.="<span class=kiscim>Hozz�ad�s:</span><br><span class=alap>CTRL-lal t�bb is kijel�lhet�, illetve visszavonhat�!<br></span><select name=szomszedos2T[] class=urlap multiple size=10>";
	foreach($ismT as $eid=>$enev) {		
		if(!strstr($szomszedos1,"-$eid-") and !strstr($szomszedos2,"-$eid-")) {
			$urlap.="\n<option value='$eid'";
			$urlap.="$enev</option>";
		}
	}
	$urlap.="</select>";
	$urlap.="</td></tr>";

*/
//Friss�t�s d�tuma
	if($tid>0) {
		$urlap.="\n<tr><td valign=top><div class=kiscim align=right>Friss�t�s:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=14',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td valign=top><input type=text disabled value='$frissites' size=10 class=urlap><br><input type=checkbox name=frissit value=i class=urlap><span class=alap> Friss�ts�k a d�tumot</span></td></tr>";
	}
	
//Enged�lyez�s
	$urlap.="\n<tr><td bgcolor=#efefef valign=top><div class=kiscim align=right>Megjelenhet:</div></td><td bgcolor=#efefef valign=top><input type=radio name=ok value=i";
	if($ok!='n' and $ok!='f') $urlap.=" checked";
	$urlap.="><span class=alap> igen</span>";
	$urlap.="<input type=radio name=ok value=f";
	if($ok=='f') $urlap.=" checked";
	$urlap.="><span class=alap> �ttekint�sre v�r</span>";
	$urlap.="<input type=radio name=ok value=n";
	if($ok=='n') $urlap.=" checked";
	$urlap.="><span class=alap> nem</span>";
	$urlap.=" <a href=\"javascript:OpenNewWindow('sugo.php?id=15',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></td></tr>";

//Log
	$urlap.="\n<tr><td valign=top><div class=kiscim align=right>T�rt�net:</div></td><td valign=top><textarea cols=50 rows=6 disabled>Sz�ml�l�: $szamlalo\n$log</textarea></td></tr>";

	$urlap.="\n<tr><td><div align=right><input type=submit value=Mehet class=urlap>&nbsp;</div></td><td>";

	if($tid>0) {
		$urlap.="<input type=radio name=modosit value=i class=urlap checked><span class=alap> �s �jra m�dos�t</span>";
		$urlap.="<br><input type=radio name=modosit value=m class=urlap><span class=alap> �s tov�bb a miserendre</span>";
		$urlap.="<br><input type=radio name=modosit value=n class=urlap><span class=alap> �s vissza a list�ba</span>";
	}
	else $urlap.="<input type=hidden name=modosit value=i>";

	$urlap.='</td></tr></table>';
	$urlap.="\n</form>";

	$adatT[2]='<span class=alcim>Templom felt�lt�se / m�dos�t�sa</span><br><br>'.$urlap;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function miserend_addingtemplom() {
	global $_POST,$_SERVER,$db_name,$_FILES,$u_login,$u_beosztas;

	$ip=$_SERVER['REMOTE_ADDR'];
    $host = gethostbyaddr($ip);

	$hiba=false;
	$tid=$_POST['tid'];
/*
	if($tid>0) {
		//Ha m�dos�t�s t�rt�nt
		$lekerdez=mysql_db_query($db_name,"select megnyitva from hirek where id='$hid'");
		list($megnyitva)=mysql_fetch_row($lekerdez);
		if(strstr($megnyitva,$u_login)) { //�s � nyitotta meg utolj�ra,
			mysql_db_query($db_name,"update hirek set megnyitva='' where id='$hid'"); //akkor t�r�lj�k a bejegyz�st
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
	$orszag=$_POST['orszag'];
	$megyeT=$_POST['megyeT'];
	$megye=$megyeT[$orszag];
	if(empty($megye)) $megye=0;
	$varosT=$_POST['varosT'];
	$varos=$varosT[$orszag][$megye];
	$cim=$_POST['cim'];
	$megkozelites=$_POST['megkozelites'];
	$plebania=$_POST['plebania'];
	$pleb_url=$_POST['pleb_url'];
	$pleb_eml=$_POST['pleb_eml'];
	$nyariido=$_POST['nyariido'];
	$teliido=$_POST['teliido'];
	$megjegyzes=$_POST['megjegyzes'];
	$frissit=$_POST['frissit'];
	if($frissit=='i') $frissites=" frissites='$ma', ";
	$kontakt=$_POST['kontakt'];
	$kontaktmail=$_POST['kontaktmail'];
	$szomszedos1T=$_POST['szomszedos1T'];
	$delsz1T=$_POST['delsz1T'];
	$oldsz1T=$_POST['oldsz1T'];
	$szomszedos2T=$_POST['szomszedos2T'];
	$oldsz2T=$_POST['oldsz2T'];
	$delsz2T=$_POST['delsz2T'];
	$bucsu=$_POST['bucsu'];
	$ok=$_POST['ok'];
	$feltolto=$_POST['feltolto'];
	$megbizhato=$_POST['megbizhato'];
	if($megbizhato!='i') $megbizhato='n';

	$szoveg=$_POST['szoveg'];
	$szoveg=str_replace('&eacute;','�',$szoveg);
	$szoveg=str_replace('&ouml;','�',$szoveg);
	$szoveg=str_replace('&Ouml;','�',$szoveg);
	$szoveg=str_replace('&uuml;','�',$szoveg);
	$szoveg=str_replace('&Uuml;','�',$szoveg);
	$szoveg=str_replace("'","\'",$szoveg);

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
			$parameter1='update';
			list($log)=mysql_fetch_row(mysql_db_query($db_name,"select log from templomok where id='$tid'"));
			$ujlog=$log."\nMod: $u_login ($most)";
			$parameter2=", modositotta='$u_login', moddatum='$most', log='$ujlog' where id='$tid'";

			//M�dos�tjuk a hozz�kapcsol�d� miseid�pontokn�l is az id�sz�m�t�si d�tumot
			$query="update misek set datumtol='$nyariido', datmig='$teliido' where tid='$tid' and torolte=''";
			mysql_db_query($db_name,$query);
		}
		else {
			$uj=true;
			$parameter1='insert';
			$parameter2=", regdatum='$most', log='Add: $u_login ($most)'";
			$frissites=" frissites='$ma', ";
		}

		$query="$parameter1 templomok set nev='$nev', ismertnev='$ismertnev', turistautak='$turistautak', orszag='$orszag', megye='$megye', varos='$varos', cim='$cim', megkozelites='$megkozelites', plebania='$plebania', pleb_url='$pleb_url', pleb_eml='$pleb_eml', egyhazmegye='$egyhazmegye', espereskerulet='$espereskerulet', leiras='$szoveg', megjegyzes='$megjegyzes', szomszedos1='$szomszedos1', szomszedos2='$szomszedos2', bucsu='$bucsu', nyariido='$nyariido', teliido='$teliido', $frissites kontakt='$kontakt', kontaktmail='$kontaktmail', adminmegj='$adminmegj', letrehozta='$feltolto', megbizhato='$megbizhato', ok='$ok' $parameter2";
		if(!mysql_db_query($db_name,$query)) echo 'HIBA!<br>'.mysql_error();
		if($uj) $tid=mysql_insert_id();	
		else {
			$katnev="$nev ($varos)";
			if(!mysql_db_query($db_name,"update kepek set katnev='$katnev' where kat='templomok' and kid='$tid'")); 
		}

	//Szomsz�dos 1 (legk�zelebbi templomok)
		if(is_array($oldsz1T)) {
			if(is_array($delsz1T)) {
				foreach($oldsz1T as $ertek) {
					if(!in_array($ertek,$delsz1T)) {
						$ujsz1T[]=$ertek;
					}
				}
			}
			else {
				$ujsz1T=$oldsz1T;
			}
			if(is_array($szomszedos1T)) {
				$ujsz1T=array_merge($ujsz1T,$szomszedos1T);
			}
		}
		elseif(is_array($szomszedos1T)) {
			$ujsz1T=$szomszedos1T;
		}
		if(is_array($ujsz1T)) {
			$ujsz1='-'.implode('--',$ujsz1T).'-';
		}
	
	//Szomsz�dos 2 (10km-en bel�li templomok)
		if(is_array($oldsz2T)) {
			if(is_array($delsz2T)) {
				foreach($oldsz2T as $ertek) {
					if(!in_array($ertek,$delsz2T)) {
						$ujsz2T[]=$ertek;
					}
				}
			}
			else {
				$ujsz2T=$oldsz2T;
			}
			if(is_array($szomszedos2T)) {
				$ujsz2T=array_merge($ujsz2T,$szomszedos2T);
			}
		}
		elseif(is_array($szomszedos2T)) {
			$ujsz2T=$szomszedos2T;
		}
		if(is_array($ujsz2T)) {
			$ujsz2='-'.implode('--',$ujsz2T).'-';
		}
		
		$query="update templomok set szomszedos1='$ujsz1', szomszedos2='$ujsz2' where id='$tid'";
		if(!mysql_db_query($db_name,$query)) echo "<br>HIBA!<br>$query<br>".mysql_error();


	//�s hozz�teszi az �j szomsz�dosokat!!!
		if(is_array($szomszedos2T)) {
			foreach($szomszedos2T as $ertek) {
				$query="select szomszedos2 from templomok where id='$ertek'";
				$lekerdez=mysql_db_query($db_name,$query);
				list($masiksz2)=mysql_fetch_row($lekerdez);
				$masiksz2.="-$tid-";
				mysql_db_query($db_name,"update templomok set szomszedos2='$masiksz2' where id='$ertek'");
			}
		}

	//�s kiszedi a t�r�lt szomsz�dosokat!!!
		if(is_array($delsz2T)) {
			foreach($delsz2T as $ertek) {
				$query="select szomszedos2 from templomok where id='$ertek'";
				$lekerdez=mysql_db_query($db_name,$query);
				list($masiksz2)=mysql_fetch_row($lekerdez);
				$masiksz2=str_replace("-$tid-",'',$masiksz2);
				mysql_db_query($db_name,"update templomok set szomszedos2='$masiksz2' where id='$ertek'");
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
				if(!mysql_db_query($db_name,"delete from kepek where kat='templomok' and kid='$tid' and fajlnev='$ertek'")) echo 'HIBA!<br>'.mysql_error();
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
						$katnev="$nev ($varos)";
						if(!mysql_db_query($db_name,"insert kepek set kat='templomok', kid='$tid', katnev='$katnev', fajlnev='$kepnevT[$id]', felirat='$kepfeliratT[$id]'")) echo 'HIBA!<br>'.mysql_error();
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
		$fooldalkepT=$_POST['fooldalkepT'];
		$kepfeliratmodT=$_POST['kepfeliratmodT'];
		$kepsorszamT=$_POST['kepsorszamT'];
		if(is_array($kepsorszamT)) {
			foreach($kepsorszamT as $melyikkep=>$ertek) {
				if($fooldalkepT[$melyikkep]=='i') $kiemelt='i';
				else $kiemelt='n';
				//M�dos�t�s az adatb�zisban
				if(!mysql_db_query($db_name,"update kepek set felirat='$kepfeliratmodT[$melyikkep]', sorszam='$ertek', kiemelt='$kiemelt' where kat='templomok' and kid='$tid' and fajlnev='$melyikkep'")) echo 'HIBA!<br>'.mysql_error();
			}
		}		
	
		if($modosit=='i') $kod=miserend_addtemplom($tid);
		elseif($modosit=='m') $kod=miserend_addmise($tid);
		else $kod=miserend_modtemplom();
	}

	return $kod;
}

function miserend_modtemplom() {
	global $db_name,$linkveg,$m_id,$_POST,$u_login,$sid;

	$egyhazmegye=$_POST['egyhazmegye'];
	if($egyhazmegye=='0') $egyhazmegye='mind';
	$kulcsszo=$_POST['kkulcsszo'];	
	$allapot=$_POST['allapot'];

	$sort=$_POST['sort'];
	if(empty($sort)) $sort='moddatum desc';

	$min=$_POST['min'];
	if(!isset($min)) $min=$_GET['min'];
	if($min<0 or !isset($min)) $min=0;

	$leptet=$_POST['leptet'];
	if(!isset($leptet)) $leptet=$_GET['leptet'];
	if(!isset($leptet)) $leptet=50;

	$next=$min+$leptet;
	$prev=$min-$leptet;

	$query_kat="select id,ehm,nev from espereskerulet";
	$lekerdez_kat=mysql_db_query($db_name,$query_kat);
	while(list($esid,$eshm,$esnev)=mysql_fetch_row($lekerdez_kat)) {
		$espkerT[$eshm][$esid]=$esnev;
	}
	
	$kiir.="<span class=kiscim>A lista sz�k�thet� egyh�zmegy�k, kulcssz� �s �llapot alapj�n:</span><br>";
	$csakpriv='mind';
	$ehmmindkiir='<option value=mind>Mind</option>';
	$query_kat="select id,nev,felelos,csakez from egyhazmegye where ok='i' order by sorrend";
	$lekerdez_kat=mysql_db_query($db_name,$query_kat);
	while(list($kid,$knev,$kfelelos,$kcsakez)=mysql_fetch_row($lekerdez_kat)) {
		if($kfelelos==$u_login) {
			$ehmT['priv'][$kid]=$knev;
			if($kcsakez=='i') {
				$csakpriv='priv';
				$ehmmindkiir='';
			}
			else $csakpriv='mind';
			if(empty($egyhazmegye)) $egyhazmegye="$kid-0";
		}
		$ehmT['mind'][$kid]=$knev;
	}
	if(empty($egyhazmegye)) $egyhazmegye='mind';

	$kiir.="\n<form method=post><input type=hidden name=m_id value='$m_id'><input type=hidden name=m_op value=modtemplom>";
	$kiir.="\n<input type=hidden name=sid value=$sid>";
	$kiir.="\n<select name=egyhazmegye class=urlap>";
	$kiir.=$ehmmindkiir;
	foreach($ehmT[$csakpriv] as $kid=>$knev) {
		$kiir.="<option value=$kid-0";
		if($egyhazmegye=="$kid-0") $kiir.=" selected";
		$kiir.=">";
		$kiir.="$knev</option>";
		if(is_array($espkerT[$kid])) {
			foreach($espkerT[$kid] as $esid=>$esnev) {
				$kiir.="<option value=$kid-$esid";
				if($egyhazmegye=="$kid-$esid") $kiir.=" selected";
				$kiir.="> -> $esnev espker.</option>";
			}
		}
	}
	$kiir.="</select>";
			
	$kiir.="\n <input type=text name=kkulcsszo value='$kulcsszo' class=urlap size=20>";

//�llapot szerinti sz�r�s
	$kiir.="\n <select name=allapot class=urlap><option value=0>Mind</option><option value=i";
	if($allapot=='i') $kiir.=" selected";
	$kiir.=">csak enged�lyezett templomok</option><option value=f";
	if($allapot=='f') $kiir.=" selected";
	$kiir.=">�ttekint�sre v�r�k</option><option value=n";
	if($allapot=='n') $kiir.=" selected";
	$kiir.=">letiltott templomok</option><option value=e";
	if($allapot=='e') $kiir.=" selected";
	$kiir.=">�szrev�telezett templomok</option><option value=ef";
	if($allapot=='ef') $kiir.=" selected";
	$kiir.=">jav�t�s alatt l�v� templomok</option>";
	//$kiir.="<opton value=m";
//	if($allapot=='m') $kiir.=" selected";
//	$kiir.=">miserend n�lk�li templomok</option>";
	$kiir.="</select>";

	$kiir.="\n<br><span class=alap>rendez�s: </span><select name=sort class=urlap> ";
	$sortT['utols� m�dos�t�s']='moddatum desc';
	$sortT['telep�l�s']='varos';
	$sortT['templomn�v']='nev';
	foreach($sortT as $kulcs=>$ertek) {
		$kiir.="<option value='$ertek'";
		if($ertek==$sort) $kiir.=' selected';
		$kiir.=">$kulcs</option>";
	}
	$kiir.="\n</select><input type=submit value=Lista class=urlap></form><br>";

	if($egyhazmegye!='mind' and isset($egyhazmegye)) {
		$ehmT=explode('-',$egyhazmegye);
		if($ehmT[1]=='0')	$feltetelT[]="egyhazmegye='$ehmT[0]'";
		else $feltetelT[]="espereskerulet='$ehmT[1]'";
	}
	if(!empty($kulcsszo)) $feltetelT[]="(nev like '%$kulcsszo%' or varos like '%$kulcsszo%' or ismertnev like '%$kulcsszo%' or letrehozta like '%$kulcsszo%')";
	if(!empty($allapot)) {
		if($allapot=='e') $feltetelT[]="eszrevetel='i'";
		elseif($allapot=='ef') $feltetelT[]="eszrevetel='f'";
		else $feltetelT[]="ok='$allapot'";
	}
	if(is_array($feltetelT)) $feltetel=' where '.implode(' and ',$feltetelT);

//Mis�k lek�rdez�se
	$querym="select distinct(templom) from misek where torolte=''";
	if(!$lekerdezm=mysql_db_query($db_name,$querym)) echo "HIBA!<br>$querym<br>".mysql_error();
	while(list($templom)=mysql_fetch_row($lekerdezm)) {
		$vanmiseT[$templom]=true;
	}

//�szrev�telek lek�rdez�se
	$querye="select distinct(hol_id) from eszrevetelek where hol='templomok'";
	if(!$lekerdeze=mysql_db_query($db_name,$querye)) echo "HIBA!<br>$querym<br>".mysql_error();
	while(list($templom)=mysql_fetch_row($lekerdeze)) {
		$vaneszrevetelT[$templom]=true;
	}

	$query="select id,nev,ismertnev,varos,ok,eszrevetel from templomok $feltetel order by $sort";
	$lekerdez=mysql_db_query($db_name,$query);
	$mennyi=mysql_num_rows($lekerdez);
	if($mennyi>$leptet) {
		$query.=" limit $min,$leptet";
		$lekerdez=mysql_db_query($db_name,$query);
	}
	$kezd=$min+1;
	$veg=$min+$leptet;
	if($veg>$mennyi) $veg=$mennyi;
	if($mennyi>0) {
		$kiir.="<span class=alap>�sszesen: $mennyi tal�lat<br>List�z�s: $kezd - $veg</span><br><br>";
		if($min>0) {
			$lapozo.="\n<form method=post><input type=hidden name=m_id value=$m_id><input type=hidden name=m_op value=modtemplom><input type=hidden name=sid value=$sid><input type=hidden name=kkulcsszo value='".$_POST['kkulcsszo']."'><input type=hidden name=egyhazmegye value=$egyhazmegye><input type=hidden name=min value=$prev><input type=hidden name=sort value='$sort'>";		
			$lapozo.="\n<input type=submit value=El�z� class=urlap><input type=text size=2 value=$leptet name=leptet class=urlap></form>";
		}
		if($mennyi>$min+$leptet) {
			$lapozo.="\n<form method=post><input type=hidden name=m_id value=$m_id><input type=hidden name=m_op value=modtemplom><input type=hidden name=sid value=$sid><input type=hidden name=kkulcsszo value='".$_POST['kkulcsszo']."'><input type=hidden name=egyhazmegye value=$egyhazmegye><input type=hidden name=min value=$next><input type=hidden name=sort value='$sort'>";
			$lapozo.="\n<input type=submit value=K�vetkez� class=urlap><input type=text size=2 value=$leptet name=leptet class=urlap></form>";
		}
		$kiir.=$lapozo.'<br>';
	}
	else $kiir.="<span class=alap>Jelenleg nincs m�dos�that� templom az adatb�zisban.</span>";
	while(list($tid,$tnev,$tismert,$tvaros,$tok,$teszrevetel)=mysql_fetch_row($lekerdez)) {
		$jelzes='';
		if($teszrevetel=='i') $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomag.gif title='�j �szrev�telt �rtak hozz�!' align=absmiddle border=0></a> ";		
		elseif($teszrevetel=='f') $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomagf.gif title='�szrev�tel jav�t�sa folyamatban!' align=absmiddle border=0></a> ";		
		elseif($vaneszrevetelT[$tid]) $jelzes.="<a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomag1.gif title='�szrev�telek!' align=absmiddle border=0></a> ";		
		if(!$vanmiseT[$tid]) {
			$jelzes.="<img src=img/lampa.gif title='Nincs hozz� mise!' align=absmiddle> ";
		}		
		//Jelz�s be�ll�t�sa -> lampa = nincs kategorizalva, ora = varakozik ok=n, tilos = megjelenhet X, jegyzett�mb - szerkeszt�s alatt (megnyitva)
		//if(!empty($megnyitva)) $jelzes.="<img src=img/edit.gif title='Megnyitva: $megnyitva' align=absmiddle> ";
		//if(empty($rovatkat)) $jelzes.="<img src=img/lampa.gif title='Nincs kateg�riz�lva!' align=absmiddle> ";
		//if(!strstr($megjelenhet,'kurir')) $jelzes.="<img src=img/tilos.gif title='Megjelen�s nincs be�ll�tva!' align=absmiddle> ";
		//if($ok!='i') $jelzes.="<img src=img/ora.gif title='Felt�lt�tt h�r, �ttekint�sre v�r!' align=absmiddle> ";
		if($tok=='n') $jelzes.="<img src=img/tilos.gif title='Nem enged�lyezett!' align=absmiddle> ";
		elseif($tok=='f') $jelzes.="<img src=img/ora.gif title='Felt�lt�tt/m�dos�tott templom, �ttekint�sre v�r!' align=absmiddle> ";
		
		$kiir.="\n$jelzes <a href=?m_id=$m_id&m_op=addtemplom&tid=$tid$linkveg class=felsomenulink title='$tismert'><b>- $tnev</b><font color=#8D317C> ($tvaros)</font></a> - <a href=?m_id=$m_id&m_op=addmise&tid=$tid$linkveg class=felsomenulink><img src=img/mise_edit.png title='mis�k' align=absmiddle border=0>szentmise</a> - <a href=?m_id=$m_id&m_op=deltemplom&tid=$tid$linkveg class=link><img src=img/del.jpg border=0 alt=T�r�l align=absmiddle> t�r�l</a><br>";
	}

	$kiir.='<br>';
	$kiir.=$lapozo;

	$adatT[2]='<span class=alcim>Templomok, miserendek m�dos�t�sa</span><br><br>'.$kiir;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;


	return $kod;
}

function miserend_addmise($tid) {
	global $sid,$linkveg,$m_id,$db_name,$onload,$u_beosztas,$u_login;	
	
	$most=date("Y-m-d H:i:s");		
	
	$query="select nap,ido,idoszamitas,nyelv,milyen,megjegyzes from misek where templom='$tid' and torolte='' order by nap,ido";
	if(!$lekerdez=mysql_db_query($db_name,$query)) echo 'HIBA!<br>'.mysql_error();	
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
	list($tnev,$tvaros,$datumtol,$datumig,$misemegj,$frissites)=mysql_fetch_row(mysql_db_query($db_name,"select nev,varos,nyariido,teliido,misemegj,frissites from templomok where id='$tid'"));

	if(is_array($gitarosT)) {
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
	}
	
	if(is_array($megjT)) {
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
	}

	$urlap.="\n<FORM ENCTYPE='multipart/form-data' method=post>";

	$urlap.="\n<input type=hidden name=m_id value=$m_id><input type=hidden name=sid value=$sid>";
	$urlap.="\n<input type=hidden name=m_op value=addingmise><input type=hidden name=tid value=$tid>";
	$urlap.="\n<input type=hidden name=datumtol value=$datumtol><input type=hidden name=datumig value=$datumig>";
	
	$urlap.='<table cellpadding=4 width=100%>';

//n�v
	$urlap.="\n<tr><td bgcolor=#F5CC4C><div class=kiscim align=right>Templom neve:</div></td><td bgcolor=#F5CC4C><span class=kiscim>$tnev ($tvaros)</span></td></tr>";

//id�sz�m�t�s
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>Ny�ri id�sz�m�t�s:</div></td><td bgcolor=#efefef><span class=kiscim>$datumtol - $datumig</span><span class=alap> (a templom adatain�l m�dos�that�!)</span></td></tr>";

//Misemegjegyz�s
	$urlap.="\n<tr><td bgcolor=#D6F8E6><span class=kiscim>Kieg�sz�t� inf�k:</span><br><a href=\"javascript:OpenNewWindow('sugo.php?id=41',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></td><td bgcolor=#D6F8E6>";
	$urlap.="<span class=alap>Rendszeres r�zsaf�z�r, szents�gim�d�s, hittan, stb.</span><br><textarea name=misemegj class=urlap cols=50 rows=10>$misemegj</textarea></td></tr>";

//miserend
	$urlap.="\n<tr><td><span class=kiscim>Miseid�pontok:</span></td><td>";
	$urlap.="&nbsp;</td></tr>";

	$ma=date('Y-m-d');
	$urlap.="\n<tr><td bgcolor=#D6F8E6><span class=kiscim>Friss�t�s:</span></td><td bgcolor=#D6F8E6>";
	$urlap.="<input type=checkbox name=frissit value='i' checked class=urlap><span class=alap>D�tum friss�t�se m�dos�t�s eset�n (utols� friss�t�s: $frissites)</span></td></tr>";

//h�tf�
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>h�tf�:</div></td><td bgcolor=#efefef>";
	$urlap.="<input type=text name=idopontT[1] value=\"$idopontT[1]\" class=urlap size=30><span class=alap> ny�ri misekezd�sek</span>";
	$urlap.="<br>&nbsp; &nbsp;<input type=text name=idoponttT[1] value=\"$idoponttT[1]\" class=urlap size=30><span class=alap> t�li misekezd�sek, ha k�l�nb�zik</span>";
	$urlap.="<br><img src=img/space.gif width=5 height=4>";
	$urlap.="<br><input type=text name=nyelvT[1] value=\"$nyelvT[1]\" class=urlap size=30><span class=alap> nyelvek ny�ron -> </span><a title='latin' class=alap>va, </a><a title='n�met' class=alap>de, </a><a title='szlov�k' class=alap>sk, </a><a title='lengyel' class=alap>pl, </a><a title='szlov�n' class=alap>si, </a><a title='horv�t' class=alap>hr, </a><a title='olasz' class=alap>it, </a><a title='g�r�g' class=alap>gr, </a><a title='angol' class=alap>en, </a><a title='francia' class=alap>fr, </a>";
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
	$urlap.="<br><input type=text name=nyelvT[2] value=\"$nyelvT[2]\" class=urlap size=30><span class=alap> nyelvek ny�ron -> </span><a title='latin' class=alap>va, </a><a title='n�met' class=alap>de, </a><a title='szlov�k' class=alap>sk, </a><a title='lengyel' class=alap>pl, </a><a title='szlov�n' class=alap>si, </a><a title='horv�t' class=alap>hr, </a><a title='olasz' class=alap>it, </a><a title='g�r�g' class=alap>gr, </a><a title='angol' class=alap>en, </a><a title='francia' class=alap>fr, </a>";
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
	$urlap.="<br><input type=text name=nyelvT[3] value=\"$nyelvT[3]\" class=urlap size=30><span class=alap> nyelvek ny�ron -> </span><a title='latin' class=alap>va, </a><a title='n�met' class=alap>de, </a><a title='szlov�k' class=alap>sk, </a><a title='lengyel' class=alap>pl, </a><a title='szlov�n' class=alap>si, </a><a title='horv�t' class=alap>hr, </a><a title='olasz' class=alap>it, </a><a title='g�r�g' class=alap>gr, </a><a title='angol' class=alap>en, </a><a title='francia' class=alap>fr, </a>";
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
	$urlap.="<br><input type=text name=nyelvT[4] value=\"$nyelvT[4]\" class=urlap size=30><span class=alap> nyelvek ny�ron -> </span><a title='latin' class=alap>va, </a><a title='n�met' class=alap>de, </a><a title='szlov�k' class=alap>sk, </a><a title='lengyel' class=alap>pl, </a><a title='szlov�n' class=alap>si, </a><a title='horv�t' class=alap>hr, </a><a title='olasz' class=alap>it, </a><a title='g�r�g' class=alap>gr, </a><a title='angol' class=alap>en, </a><a title='francia' class=alap>fr, </a>";
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
	$urlap.="<br><input type=text name=nyelvT[5] value=\"$nyelvT[5]\" class=urlap size=30><span class=alap> nyelvek ny�ron -> </span><a title='latin' class=alap>va, </a><a title='n�met' class=alap>de, </a><a title='szlov�k' class=alap>sk, </a><a title='lengyel' class=alap>pl, </a><a title='szlov�n' class=alap>si, </a><a title='horv�t' class=alap>hr, </a><a title='olasz' class=alap>it, </a><a title='g�r�g' class=alap>gr, </a><a title='angol' class=alap>en, </a><a title='francia' class=alap>fr, </a>";
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
	$urlap.="<br><input type=text name=nyelvT[6] value=\"$nyelvT[6]\" class=urlap size=30><span class=alap> nyelvek ny�ron -> </span><a title='latin' class=alap>va, </a><a title='n�met' class=alap>de, </a><a title='szlov�k' class=alap>sk, </a><a title='lengyel' class=alap>pl, </a><a title='szlov�n' class=alap>si, </a><a title='horv�t' class=alap>hr, </a><a title='olasz' class=alap>it, </a><a title='g�r�g' class=alap>gr, </a><a title='angol' class=alap>en, </a><a title='francia' class=alap>fr, </a>";
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
	$urlap.="<br><input type=text name=nyelvT[7] value=\"$nyelvT[7]\" class=urlap size=30><span class=alap> nyelvek ny�ron -> </span><a title='latin' class=alap>va, </a><a title='n�met' class=alap>de, </a><a title='szlov�k' class=alap>sk, </a><a title='lengyel' class=alap>pl, </a><a title='szlov�n' class=alap>si, </a><a title='horv�t' class=alap>hr, </a><a title='olasz' class=alap>it, </a><a title='g�r�g' class=alap>gr, </a><a title='angol' class=alap>en, </a><a title='francia' class=alap>fr, </a>";
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
	
	$urlap.="<br><br><span class=alap><b>nyelvek</b> (h, hu vagy �res=magyar, en=angol, de=n�met, it=olasz, fr=francia, va=latin, gr=g�r�g, sk=szlov�k, hr=horv�t, pl=lengyel, si=szlov�n => tov�bbi nyelvek eset�n az internetes 2 bet�s v�gz�d�s az ir�nyad�!)<br>A nyelvek a be�ll�tott miseid�pontokhoz tartoznak, �gy az elv�laszt� itt is a <b>+</b> jel. El�fordulhatnak peri�dusok is, ebben az esetben a nyelv mellett a peri�dus sz�m�t kell felt�ntetni, pl de2,va3 -> minden h�nap m�sodik het�n n�met, harmadik het�n latin (A vessz� nem fontos, csak jobban tagolja). Ha minden h�ten az adott nyelven van mise, akkor nem kell megjegyz�st �rni, viszont <u>peri�dusok vagy egy�ni esetekben a mejegyz�s rovatba sz�vegesen is t�ntess�k f�l</u>!<br>";
	$urlap.="\n<u>P�lda 1:</u> a fenti 9-es mise magyar nyelv�, az esti 6-os viszont minden h�nap m�sodik vas�rnapj�n latin: <input type=text disabled class=urlap value=\"h0+,va2\" size=10> (<b>h0+va2</b>)";
	$urlap.="\n<br><u>P�lda 2:</u> a 9-es mise mindig n�met nyelv�, az esti 6-os viszont minden h�nap m�sodik vas�rnapj�n angol, egy�bk�nt latin:  <input type=text disabled class=urlap value=\"de0+va1,en2,va3,va4\" size=10> (<b>de0+va1,en2,va3,va4</b>)";
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

	$adatT[2]='<span class=alcim>Templom felt�lt�se / m�dos�t�sa</span><br><br>'.$urlap;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function miserend_addingmise() {
	global $_POST,$_SERVER,$sid,$m_id,$db_name,$u_login;

	$ip=$_SERVER['REMOTE_ADDR'];
    $host = gethostbyaddr($ip);

	$tid=$_POST['tid'];
	$frissit=$_POST['frissit'];
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
		$query="update misek set torles='$most', torolte='$u_login' where templom='$tid' and torolte=''";
		mysql_db_query($db_name,$query);
		list($log)=mysql_fetch_row(mysql_db_query($db_name,"select log from templomok where id='$tid'"));
		$log.="\nMISE_MOD: $u_login ($most - [$ip - $host])";
		if($frissit=='i') $frissites=", frissites='$ma'";
		$query="update templomok set misemegj='$misemegj', log='$log' $frissites where id='$tid'";
		mysql_db_query($db_name,$query);
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
				mysql_db_query($db_name,$query);
			}
		
		}
		foreach($misektT as $id=>$mise) {
			if($mise!='-' and !empty($mise)) {
				if(empty($nyelvektT[$id])) $nyelvektT[$id]='h0';
				$query="insert misek set templom='$tid', nap='$nap', ido='$mise', idoszamitas='t', datumtol='$datumtol', datumig='$datumig', nyelv='$nyelvektT[$id]', milyen='$milyentT[$id]', megjegyzes='$megjegyzestT[$id]', modositotta='$u_login', moddatum='$most'";
				mysql_db_query($db_name,$query);
			}
		
		}

	}



	if($modosit=='i') {
		$kod=miserend_addmise($tid);
	}
	else {
		$kod=miserend_modtemplom();
	}
	
	return $kod;
}



function miserend_deltemplom() {
	global $_GET,$db_name,$linkveg,$m_id,$u_login;

	$tid=$_GET['tid'];

	$kiir="<span class=alcim>Templom �s miserend t�rl�se</span><br><br>";
	$kiir.="\n<span class=kiscim>Biztosan t�r�lni akarod a k�vetkez� templomot?<br><font color=red>FIGYELEM! A kapcsol�d� mis�k �s k�pek is t�rl�dnek!</font></span>";
		
	$query="select nev from templomok where id='$tid'";
	list($cim)=mysql_fetch_row(mysql_db_query($db_name,$query));
	if(!empty($cim)) {
		$kiir.="\n<br><br><span class=alap><b><i>$cim</i></b></span>";

		$kiir.="<br><br><a href=?m_id=$m_id&m_op=deletetemplom&tid=$tid$linkveg class=link>Igen</a> - <a href=?m_id=$m_id&m_op=modtemplom$linkveg class=link>NEM</a>";
	}
	else {
		$kiir.="<br><br><span class=hiba>HIBA! Ilyen templom nincs!</span>";
	}

	$adatT[2]=$kiir;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function miserend_deletetemplom() {
	global $_GET,$db_name,$u_login,$u_beosztas;

	$tid=$_GET['tid'];
	$query="delete from templomok where id='$tid'";
	mysql_db_query($db_name,$query);

	//Mis�ket is t�r�lj�k
	$query="delete from misek where templom='$tid'";
	mysql_db_query($db_name,$query);

//�s kiszedi a t�r�lt szomsz�dosokat!!!
	$query="select id, szomszedos1, szomszedos2 from templomok where szomszedos1 like '%-$tid-%' or szomszedos2 like '%-$tid-%'";
	$lekerdez=mysql_db_query($db_name,$query);
	while(list($szid,$sz1,$sz2)=mysql_fetch_row($lekerdez)) {
		if(strstr($sz1,$tid)) {
			//Ha a m�sik templomn�l szerepel a mi templomunk
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
			mysql_db_query($db_name,"update templomok set szomszedos1='$ujsz1' where id='$szid'");
		}
		if(strstr($sz2,$tid)) {
			//Ha a m�sik templomn�l szerepel a mi templomunk
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
			mysql_db_query($db_name,"update templomok set szomszedos2='$ujsz2' where id='$szid'");
		}
	}

	//F�jlokat �s k�peket is t�r�lni kell!

	//K�nyvt�r tartalm�t beolvassa
	$konyvtar="fajlok/templomok/$tid";
		if(is_dir($konyvtar)) {
			$handle=opendir($konyvtar);
			while ($file = readdir($handle)) {
				if ($file!='.' and $file!='..') {
					@unlink("$konyvtar/$file");
				}
			}
			closedir($handle);
		}
	

	//K�nyvt�r tartalm�t beolvassa
	$konyvtar="kepek/templomok/$tid";
		if(is_dir($konyvtar)) {
			$handle=opendir($konyvtar);
			while ($file = readdir($handle)) {
				if ($file!='.' and $file!='..' and $file!='fokep' and $file!='kicsi') {
					unlink("$konyvtar/$file");
				}
			}
			closedir($handle);
		}
	$konyvtar="kepek/templomok/$tid/kicsi";
		if(is_dir($konyvtar)) {
			$handle=opendir($konyvtar);
			while ($file = readdir($handle)) {
				if ($file!='.' and $file!='..') {
					unlink("$konyvtar/$file");
				}
			}
			closedir($handle);
		}

	$kod=miserend_modtemplom();

	return $kod;
}

function miserend_ehmlista() {
	global $_GET,$db_name,$linkveg,$m_id,$u_login;


	$txt.="<span class=alcim>Egyh�zmegyei templomok list�ja</span><br><form method=post><input type=hidden name=m_op value=ehmlista><input type=hidden name=m_id value=$m_id><select name=ehm class=urlap>";
	$query="select id,nev from egyhazmegye";
	$lekerdez=mysql_db_query($db_name,$query);
	while(list($id,$nev)=mysql_fetch_row($lekerdez)) {
		$txt.="<option value=$id";
		if($id==$ehm) $txt.=" selected";
		$txt.=">$nev</option>";
	}
	$txt.="</select><input type=submit value=Mutat class=urlap></form>";

	$ehm=$_POST['ehm'];
	if($ehm>0) {

		list($ehmnev)=mysql_fetch_row(mysql_db_query($db_name,"select nev from egyhazmegye where id='$ehm'"));
		$txt.="<h2>$ehmnev egyh�zmegye</h2>";

		$query="select templomok.id,templomok.nev,templomok.varos,espereskerulet.nev from espereskerulet, templomok where espereskerulet.id=templomok.espereskerulet and templomok.egyhazmegye=$ehm order by templomok.espereskerulet, templomok.varos";

		if(!$lekerdez=mysql_db_query($db_name,$query)) echo "<br>HIBA!<br>$query<br>".mysql_error();
		while(list($tid,$tnev,$varos,$espker)=mysql_fetch_row($lekerdez)) {
			$a++;
			if($espker!=$espkerell) {
				$txt.= "<br><h3>$espker esperesker�let</h3>";
				$espkerell=$espker;
			}
			$txt.= "$a. [$tid] $tnev ($varos)<br>";
			$excel.="\n$tid;$tnev;$varos;$espker";
		}
		$txt.="<br><br><span class=alap>Az al�bbi sz�veget kim�solva excelbe import�lhat�.<br>Excelben: Adatok / Sz�vegb�l oszlopok -> t�bl�zatt� alak�that�</span><br><textarea class=urlap cols=60 rows=20>$excel</textarea>";
	}


	$adatT[2]=$txt;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function atir() {
	global $db_name;

	$query="select id,megjegyzes,bucsu from templomok where bucsu!=''";
	if(!$lekerdez=mysql_db_query($db_name,$query)) echo "<br>HIBA!<br>$query<br>".mysql_error();
	while(list($tid,$megj,$bucsu)=mysql_fetch_row($lekerdez)) {
		$ujmegj=$bucsu."\n\n".$megj;
		mysql_db_query($db_name,"update templomok set megjegyzes='$ujmegj' where id='$tid'");
		echo "<br>$tid -> ok";
	}
	echo '<br>k�sz';
}


//Jogosults�g ellen�rz�se
if(strstr($u_jogok,'miserend')) {

switch($m_op) {
	case 'atir':
		atir();
		break;

	case 'ehmlista':
		$tartalom=miserend_ehmlista();
		break;

    case 'index':
        $tartalom=miserend_index();
        break;

	case 'addtemplom':
		$tid=$_GET['tid'];
        $tartalom=miserend_addtemplom($tid);
        break;

	case 'addmise':
		$tid=$_GET['tid'];
        $tartalom=miserend_addmise($tid);
        break;

    case 'modtemplom':
        $tartalom=miserend_modtemplom();
        break;

    case 'addingtemplom':
        $tartalom=miserend_addingtemplom();
        break;

	case 'addingmise':
        $tartalom=miserend_addingmise();
        break;

    case 'deltemplom':
        $tartalom=miserend_deltemplom();
        break;

	case 'deletetemplom':
        $tartalom=miserend_deletetemplom();
        break;

    case 'delmise':
        $tartalom=miserend_delmise();
        break;

	case 'deletemise':
        $tartalom=miserend_deletemise();
        break;
}
}
else {
	$tartalom="\n<span class=hiba>HIBA! Nincs hozz� jogosults�god!</span>";
}

?>
