<?php

function hirek_urlap($hid) {
	global $sessid,$linkveg,$m_id,$db_name,$onload,$u_login,$u_jogok;	

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

	$query="select id,nev from orszagok";
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

	$szerkesztheti=false;
	if(strstr($u_jogok,'hirek')) $szerkesztheti=true;
	
	if($hid>0) {
		$most=date("Y-m-d H:i:s");
		$urlap.=include('editscript2.php'); //Csak, ha m�dos�t�sr�l van sz�

		$query="select kontakt,kontaktmail,cim,intro,szoveg,kerdes,orszag,megye,varos,egyhazmegye,espereskerulet,datum,aktualis,tol,hatarido,szervezotipus,szervezonev,szervezoinfo,fizetos,szamlalo,fohir,rovatkat,kulcsszo,galeria,kiemelt,kapcsolodas,ok,hirlevel,feltette,megbizhato,modositotta,moddatum,megnyitva,megnyitvamikor,adminmegj,log from hirek where id='$hid'";
		if(!$lekerdez=mysql_db_query($db_name,$query)) echo 'HIBA!<br>'.mysql_error();	list($kontakt,$kontaktmail,$cim,$intro,$szoveg,$kerdes,$orszag,$megye,$varos,$egyhazmegye,$espereskerulet,$datum,$aktualis,$tol,$hatarido,$szervezotipus,$szervezonev,$szervezoinfo,$fizetos,$szamlalo,$fohir,$rovatkat,$kulcsszo,$galeria,$kiemelt,$kapcsolodas,$ok,$hirlevel,$feltolto,$megbizhato,$modositotta,$moddatum,$megnyitva,$megnyitvamikor,$adminmegj,$log)=mysql_fetch_row($lekerdez);

		if($u_login==$feltolto and $megbizhato=='i') $szerkesztheti=true; //Ha megb�zhat�, akkor szerkesztheti
		elseif(strstr($u_jogok,'hirek')) $szerkesztheti=true; //Ha admin akkor is
		elseif($u_login!=$feltolto or ($u_login==$feltolto and $ok!='f')) { //Ha nem a saj�t h�re, vagy admin m�r beleny�lt, akkor nem szerkeszthei!
			echo "HIBA! - Te nem nyithatod meg!";
			exit();
		}

		$rovatkatT=explode('-',$rovatkat);
		
		mysql_db_query($db_name,"update hirek set megnyitva='$u_login', megnyitvamikor='$most' where id='$hid'"); //R�gz�tj�k, hogy megnyitotta

		$datum=substr($datum,0,16);
	}
	else {
		$datum=date('Y-m-d H:i');
		$aktualis='';
		$urlapkieg="\n<input type=hidden name=elsofeltoltes value=i>";
	}

	$urlap.="\n<FORM ENCTYPE='multipart/form-data' method=post name=urlap id=urlap>";
	$urlap.=$urlapkieg;

	$urlap.="\n<input type=hidden name=m_id value=$m_id><input type=hidden name=sessid value=$sessid>";
	$urlap.="\n<input type=hidden name=m_op value=addinghirek><input type=hidden name=hid value=$hid>";
	
	$urlap.='<table cellpadding=4>';

//megnyitva
	$lejarat=date('Y-m-d H:i:s',(time()-(60*60*3))); //3 �r�n�l r�gebben megnyitottn�l nem jelez
	if($hid>0 and $megnyitvamikor>=$lejarat and !empty($megnyitva)) {
		$urlap.="\n<tr><td>&nbsp;</td><td><img src=img/edit.gif align=absmiddle><span class=alap><font color=red>Megnyitva!</font> $megnyitva [$megnyitvamikor]</span><br><a href=?m_id=$m_id&m_op=addmegse&hid=$hid&ki=$megnyitva&mikor=".rawurlencode($megnyitvamikor)."$linkveg class=link><b><font color=red>Vissza, m�gsem szerkesztem</font></b></a></td></tr>";
	}
	elseif($hid>0) {
		$urlap.="\n<tr><td>&nbsp;</td><td><a href=?m_id=$m_id&m_op=addmegse&hid=$hid&kod=$kod$linkveg class=link><font color=red>Kil�p�s m�dos�t�s n�lk�l</font></a>";
		$moddatumT=explode(' ',$moddatum);
		if($moddatumT[0]==date('Y-m-d')) $moddatumkiir='ma ';
		elseif($moddatumT[0]==date('Y-m-d',(time()-86400))) $moddatumkiir='tegnap ';
		else $moddatumkiir=$moddatumT[0].' ';
		$moddatumido=substr($moddatumT[1],0,5);
		if($moddatumido[0]=='0') $moddatumkiir.=substr($moddatumido,1);
		else $moddatumkiir.=$moddatumido;
		$urlap.="<br><span class=alap>Utolj�ra szerkesztette: $modositotta [$moddatumkiir]</span>";
		$urlap.="</td></tr>";
	}

//el�n�zet
	if($szerkesztheti) {
		if($hid>0) $urlap.="\n<tr><td bgcolor='#efefef'>&nbsp;</td><td bgcolor='#efefef'><a href=?m_id=19&id=$hid$linkveg class=link target=_blank><b>>> H�r megtekint�se (el�n�zet) <<</b></a></td></tr>";
	}

//megjegyz�s	
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>Megjegyz�s:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=29',200,300);\"><img src=img/sugo.gif border=0 title='S�g�'></a></div></td><td bgcolor=#efefef><textarea name=adminmegj class=urlap cols=50 rows=3>$adminmegj</textarea><br><span class=alap> A h�rrel, szerkeszt�s�vel kapcsolatosan</span></td></tr>";

//kontakt
	$urlap.="\n<tr><td bgcolor=#ffffff><div class=kiscim align=right>Felel�s:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=30',200,300);\"><img src=img/sugo.gif border=0 title='S�g�'></a></div></td><td bgcolor=#ffffff><textarea name=kontakt class=urlap cols=50 rows=2>$kontakt</textarea><span class=alap> n�v �s telefonsz�m</span><br><input type=text name=kontaktmail size=40 class=urlap value='$kontaktmail'><span class=alap> emailc�m</span></td></tr>";

//felt�lt�
	if(strstr($u_jogok,'hirek')) {
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
	}

//c�m
	$urlap.="\n<tr><td bgcolor=#F5CC4C><div class=kiscim align=right>H�r c�me:</div></td><td bgcolor=#F5CC4C><input type=text name=cim value=\"$cim\" class=urlap size=80 maxlength=250></td></tr>";

//d�tum
	$urlap.="\n<tr><td><div class=kiscim align=right>D�tum, id�:</div></td><td><input type=text name=datum value=\"$datum\" class=urlap size=16 maxlength=16><span class=alap> (amikort�l megjelenhet �s kereshet�) <a href=\"javascript:OpenNewWindow('sugo.php?id=31',200,300);\"><img src=img/sugo.gif border=0 title='S�g�'></a></td></tr>";

//h�rlev�lben
	if($szerkesztheti) {
		$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>H�rlev�lben:</div></td><td bgcolor=#efefef><input type=radio name=hirlevel value=0 ";
		if($hirlevel=='0')  $urlap.=' checked';
		$urlap.="><span class=alap>nincs</span> <input type=radio name=hirlevel value=c ";
		if(empty($hid) or $hirlevel=='c') $urlap.=' checked';
		$urlap.="><span class=alap>csak c�m</span>";

		$urlap.=" <input type=radio name=hirlevel value=i ";
		if($hirlevel=='i') $urlap.=' checked';
		$urlap.="><span class=alap>c�m �s bevezet�</span>";

		$urlap.=" <input type=radio name=hirlevel value=t ";
		if($hirlevel=='t') $urlap.=' checked';
		$urlap.="><span class=alap>teljes sz�veg</span> <a href=\"javascript:OpenNewWindow('sugo.php?id=32',200,300);\"><img src=img/sugo.gif border=0 title='S�g�'></a></td></tr>";
	}

//aktu�lis
	if(!empty($aktualis) and !empty($tol) and $tol!='0000-00-00') {
		$aktualiskiir=$tol.'=>';
		$aktualisT=explode('+',$aktualis);
		$mennyi=count($aktualisT);
		$aktualiskiir.=$aktualisT[$mennyi-1];
	}
	else $aktualiskiir=$aktualis;

	$urlap.="\n<tr><td bgcolor=#ECE5C8><div class=kiscim align=right>Aktu�lis:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=33',200,500);\"><img src=img/sugo.gif border=0 title='S�g�'></a></div></td><td bgcolor=#ECE5C8><span class=alap>(Ha az aktualit�s ki van t�ltve, megjelenik a napt�rban! T�bb id�pont is felvihet� + jellel elv�lasztva! Pl.: 2005-05-05+2005-05-16, vagy t�l-ig form�ban Pl.: 2005-05-05=>2005-05-08 R�szletek a s�g�ban!)</span><br><input type=text name=aktualis value=\"$aktualiskiir\" class=urlap size=60 maxlength=255></td></tr>";

//T�pus
	$urlap.="\n<tr><td bgcolor=#FFFAE4><div class=kiscim align=right>Szervez�s t�pusa:</div></td><td bgcolor=#FFFAE4><span class=alap> (A napt�r napi n�zetn�l is megjelenik!)</span> <a href=\"javascript:OpenNewWindow('sugo.php?id=45',200,300);\"><img src=img/sugo.gif border=0 title='S�g�'></a>";
	$urlap.="<br><select name=szervezotipus class=urlap><option value=0>Nincs inform�ci�</option>";
	$query="select id,nev from szervezotipus";
	$lekerdez=mysql_db_query($db_name,$query);
	while(list($sztid,$sztnev)=mysql_fetch_row($lekerdez)) {
		$urlap.="<option value=$sztid";
		if($sztid==$szervezotipus) $urlap.=' selected';
		$urlap.=">$sztnev</option>";
	}
	$urlap.="</td></tr>";

//Szervez� adatai
	$urlap.="\n<tr><td bgcolor=#ECE5C8><div class=kiscim align=right>Szervez� (n�v):</div></td><td bgcolor=#ECE5C8><span class=alap> (A napt�r napi n�zet�ben is megjelenik!)</span> <a href=\"javascript:OpenNewWindow('sugo.php?id=43',200,300);\"><img src=img/sugo.gif border=0 title='S�g�'></a><br><input type=text name=szervezonev value=\"$szervezonev\" class=urlap size=60 maxlength=255></td></tr>";

	$urlap.="\n<tr><td bgcolor=#FFFAE4><div class=kiscim align=right>Szervez� kontakt:</div></td><td bgcolor=#FFFAE4><span class=alap> (A programmal kapcsolatos tov�bbi inform�ci�k el�rhet�s�ge)</span> <a href=\"javascript:OpenNewWindow('sugo.php?id=44',200,300);\"><img src=img/sugo.gif border=0 title='S�g�'></a><textarea name=szervezoinfo cols=50 rows=3 class=urlap>$szervezoinfo</textarea></td></tr>";

//Fizet�s
	$urlap.="\n<tr><td bgcolor=#ECE5C8><div class=kiscim align=right>Fizet�s:</div></td><td bgcolor=#ECE5C8>";
	$urlap.="\n<input type=radio name=fizetos value=i class=urlap";
	if($fizetos=='i') $urlap.=' checked';
	$urlap.="><span class=alap>igen, bel�p�s/k�lts�gt�r�t�ses</span> ";
	$urlap.="\n<input type=radio name=fizetos value=n class=urlap";
	if($fizetos=='n') $urlap.=' checked';
	$urlap.="><span class=alap>nem, ingyenes</span> ";
	$urlap.="\n<input type=radio name=fizetos value=0 class=urlap";
	if(empty($fizetos)) $urlap.=' checked';
	$urlap.="><span class=alap>nincs inform�ci�</span> ";
	$urlap.="</td></tr>";

//Helysz�n
	$urlap.="\n<tr><td bgcolor=#FFFAE4><div class=kiscim align=right>Esem�ny helysz�ne:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=34',200,300);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td bgcolor=#FFFAE4>";

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
		//espker
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
	$urlap.="}\">\n<option value=0>Nincs / nem tudom</option>";	
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
				foreach($varos1T[$id][$meid] as $vnev1) {
					$varosurlap.="\n<option value='".$varosT[$id][$meid][$vnev1]."'";
					if($varosT[$id][$meid][$vnev1]==$varos) $varosurlap.=' selected';
					$varosurlap.=">".$varosT[$id][$meid][$vnev1]."</option>";
				}
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

	$urlap.="</td></tr>";


//hat�rid�
	$urlap.="\n<tr><td bgcolor=#FFFFFF><div class=kiscim align=right>Hat�rid�:</div></td><td bgcolor=#FFFFFF><input type=text name=hatarido value=\"$hatarido\" class=urlap size=10 maxlength=10><span class=alap> (Pl. jelentkez�si hat�rid�, jelz�ssel megjelenik a napt�rban)</span> <a href=\"javascript:OpenNewWindow('sugo.php?id=35',200,300);\"><img src=img/sugo.gif border=0 title='S�g�'></a></td></tr>";

//intro	
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>R�vid bevezet�:</div></td><td bgcolor=#efefef><span class=alap>A f�oldalon �s a napt�r napi n�zetben jelenik meg! (l�sd: s�g�)</span> <a href=\"javascript:OpenNewWindow('sugo.php?id=42',200,400);\"><img src=img/sugo.gif border=0 title='S�g�'></a><br><textarea id=intro name=intro class=urlap cols=75 rows=3>$intro</textarea></td></tr>";

//kateg�ri�k		
	$urlap.="\n<tr><td><div align=right><span class=kiscim>Kateg�riz�l�s:</span>";
	if(strstr($u_jogok,'hirek')) $urlap.="<br><span class=kicsi>(rk = rovat kiemelt, <br>ak = aloldal kiemelt, <br>norm�l)</span>";
	$urlap.="<br><a href=\"javascript:OpenNewWindow('sugo.php?id=36',200,460);\"><img src=img/sugo.gif border=0 title='S�g�'></a></div></td><td><table width=100% cellspacing=0 cellpadding=2><tr>";

	//f�kiemelt
	if(strstr($u_jogok,'hirek')) {
		$urlap.="\n<td width=30% valign=top><span class=kiscim>F�h�r / f�esem�ny</span><br><span class=alap>f�oldali els� h�r,<br>vagy kiemelt esem�ny: </span><input type=checkbox name=fohir value=i class=urlap";
		if($fohir=='i') $urlap.=" checked";
		$urlap.="></td>";
	}
	elseif($szerkesztheti) {
		$urlap.="\n<td width=30% valign=top><input type=hidden name=fohir value=$fohir></td>";
	}
	else {
		$urlap.="\n<td width=30% valign=top>&nbsp;</td>";
	}


	//rovat
	$urlap.="\n<td width=30% valign=top><span class=kiscim>Rovatok</span><span class=alap> (rk,ak,n)</span><br><table cellpadding=0 cellspacing=0>";
	$query="select id,nev from rovatkat where ok='i' and rovat=0 order by sorszam";
	if(!$lekerdez=mysql_db_query($db_name,$query)) echo 'HIBA!<br>'.mysql_error();
	while(list($rid,$rnev)=mysql_fetch_row($lekerdez)) {
		$a++;
		if($a%2==0) $bg='bgcolor=#efefef';
		else $bg='';
		$urlap.="\n<tr><td align=right $bg><span class=kicsi>$rnev </span></td><td $bg>";
		if(strstr($u_jogok,'hirek')) {
			$urlap.="<input type=radio name=k2[] value=rr$rid";		
			if(strstr($kiemelt,"-k2*rr$rid-")) $urlap.=' checked';
			$urlap.=" onClick=\"if(document.urlap.intro.value=='') {alert('T�ltsd ki a r�vid bevezet�t is!');}\">";
			$urlap.="\n<input type=radio name=k3[] value=rr$rid";
			if(strstr($kiemelt,"-k3*rr$rid-")) $urlap.=' checked';
			$urlap.=" onClick=\"if(document.urlap.intro.value=='') {alert('T�ltsd ki a r�vid bevezet�t is!');}\">";
		}
		elseif($szerkesztheti) {
			if(strstr($kiemelt,"-k2*rr$rid-")) {
				$urlap.="<input type=hidden name=k2[] value=rr$rid>";
			}
			if(strstr($kiemelt,"-k3*rr$rid-")) {
				$urlap.="<input type=hidden name=k3[] value=rr$rid>";
			}
		}
		$urlap.="<input type=checkbox name=rovat[] value=$rid";
		if(is_array($rovatkatT)) {
			if(in_array($rid,$rovatkatT)) $urlap.=' checked';
		}
		$urlap.="></td></tr>";	
	}
	$a++;
	if($a%2==0) $bg='bgcolor=#efefef';
	else $bg='';
	if(strstr($u_jogok,'hirek')) {
		$urlap.="\n<tr><td align=right $bg><span class=kicsi>nincs </span></td><td $bg>";
		$urlap.="<input type=radio name=k2[] value=''";
		$urlap.="><input type=radio name=k3[] value=''";
		$urlap.="></td></tr>";
	}
	$urlap.='</table></td>';
	
	//f�kat
	$urlap.="\n<td width=35% valign=top><span class=kiscim>Kateg�ri�k</span><br><table cellpadding=0 cellspacing=0>";
	$query="select id,nev from rovatkat where ok='i' and rovat>0 order by rovat,sorszam";
	if(!$lekerdez=mysql_db_query($db_name,$query)) echo 'HIBA!<br>'.mysql_error();
	while(list($fkid,$fknev)=mysql_fetch_row($lekerdez)) {
		if($a%2==0) $bg='bgcolor=#efefef';
		else $bg='';
		$urlap.="<tr><td align=right $bg><span class=kicsi>$fknev </span></td><td $bg>";	
		$urlap.="<input type=checkbox name=rovat[] value=$fkid";
		if(is_array($rovatkatT)) {
			if(in_array($fkid,$rovatkatT)) $urlap.=' checked';
		}
		$urlap.="></td></tr>";
		$a++;
	}
	$urlap.='</table></td>';

	$urlap.='</tr></table></td></tr>';		

//k�rd�s	
	$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>K�rd�s (tudtad-e):</div></td><td bgcolor=#efefef><input type=text name=kerdes value=\"$kerdes\" class=urlap size=80 maxlength=250> <a href=\"javascript:OpenNewWindow('sugo.php?id=37',200,400);\"><img src=img/sugo.gif border=0 title='S�g�'></a></td></tr>";

//kulcssz�
	if(strstr($u_jogok,'hirek')) {
		$kulcsszoT=explode('--',substr($kulcsszo,1,strlen($kulcsszo)-2));
		if(is_array($kulcsszoT) and !empty($kulcsszoT[0])) {
		    $feltetel='id='.implode(' or id=',$kulcsszoT);
		    $query="select nev from kulcsszo where $feltetel";
		    $lekerdez=mysql_db_query($db_name,$query);
		    while(list($ksz_nev)=mysql_fetch_row($lekerdez)) {
			$kulcsszokiir.="$ksz_nev, ";
		    }
		}
	
		$urlap.="\n<tr><td><div class=kiscim align=right>Kulcssz�<br>(kapcsol�d� h�rek): <br><a href=\"javascript:OpenNewWindow('sugo.php?id=38',200,300);\"><img src=img/sugo.gif border=0 title='S�g�'></a></div></td><td><span class=alap>�j: </span><input type=text name=ujkulcsszo class=urlap size=40 maxlength=70><br><select name=kulcsszo[] class=urlap multiple size=8><option value='0'";
		$urlap.=">Nincs</option>";

		$query="select id,nev from kulcsszo order by nev";
		if(!$lekerdez=mysql_db_query($db_name,$query)) echo 'HIBA!<br>'.mysql_error();
		while(list($ksz_id,$ksz_nev)=mysql_fetch_row($lekerdez)) {
			$urlap.="<option value='$ksz_id'";
			if(strstr($kulcsszo,"-$ksz_id-")) $urlap.=' selected';
			$urlap.=">$ksz_nev</option>";
		}
		$urlap.="</select> <span class=alap>$kulcsszokiir</span></td></tr>";
	}
	
//Kapcsol�d� gal�ria
	if($szerkesztheti) {
		$urlap.="\n<tr><td bgcolor=#efefef><div class=kiscim align=right>Kapcsol�d� gal�ria:</div><br> <a href=\"javascript:OpenNewWindow('sugo.php?id=39',200,300);\"><img src=img/sugo.gif border=0 title='S�g�'></a></td><td bgcolor=#efefef><select name=galeria[] class=urlap multiple><option value=0";
		if(empty($galeria)) $urlap.=' selected';
		$urlap.=">Nincs</option>";
		$query="select id,cim,datum from galeria where ok='i' order by datum desc";
		if(!$lekerdez=mysql_db_query($db_name,$query)) echo 'HIBA!<br>'.mysql_error();
		while(list($gid,$gcim,$gdatum)=mysql_fetch_row($lekerdez)) {
			$urlap.="<option value='$gid'";
			if(strstr($galeria,"-$gid-")) $urlap.=' selected';
			$urlap.=">$gcim ($gdatum)</option>";
		}
		$urlap.='</select></td></tr>';
	}

//K�pek
	$urlap.="\n<tr><td><div class=kiscim align=right>K�pek:<br><a href=\"javascript:OpenNewWindow('sugo.php?id=40',200,550);\"><img src=img/sugo.gif border=0 title='S�g�' align=absmiddle></a></div></td><td><span class=alap><font color=red>FIGYELEM!</font><br>Azonos nev� k�pek fel�l�rj�k egym�st!!! A f�jln�vben ne legyen �kezet �s sz�k�z!</span><br><input type=file name=kepT[] class=urlap size=20> <span class=alap>K�pfelirat: </span><input type=text name=kepfeliratT[] size=40 maxlength=100 class=urlap><br><input type=file name=kepT[] class=urlap size=20> <span class=alap>K�pfelirat: </span><input type=text name=kepfeliratT[] size=40 maxlength=100 class=urlap><br><input type=file name=kepT[] class=urlap size=20> <span class=alap>K�pfelirat: </span><input type=text name=kepfeliratT[] size=40 maxlength=100 class=urlap>";
	if($hid>0) {
		//Megl�v� k�pek list�ja
		$query="select fajlnev,felirat,sorszam,kiemelt from kepek where kat='hirek' and kid='$hid' order by sorszam";
		$lekerdez=mysql_db_query($db_name,$query);
		$konyvtar="kepek/hirek/$hid";
		$urlap.="\n<table width=100% cellpadding=0 cellspacing=0><tr>";
		$a=0;
		while(list($fajlnev,$felirat,$sorszam,$kiemelt)=mysql_fetch_row($lekerdez)) {			
			if($a%3==0 and $a>0) $urlap.="</tr><tr>";
			$a++;
			if($kiemelt=='i') $fokepchecked=' checked';
			else $fokepchecked='';
			$info=getimagesize("$konyvtar/$fajlnev");
			$w=$info[0];
			$h=$info[1];
			$urlap.="\n<td valign=bottom><a href=javascript:OpenNewWindow('view.php?kep=$konyvtar/$fajlnev',$w,$h);><img src=$konyvtar/kicsi/$fajlnev title='$felirat' border=0></a><br><input type=text name=kepsorszamT[$fajlnev] value='$sorszam' maxlength=2 size=1 class=urlap><span class=alap> -f�oldal:</span><input type=checkbox name=fooldalkepT[$fajlnev] $fokepchecked value='i' class=urlap><span class=alap> -t�r�l:</span><input type=checkbox name=delkepT[] value='$fajlnev' class=urlap><br><input type=text name=kepfeliratmodT[$fajlnev] value='$felirat' maxlength=250 size=20 class=urlap></td>";
		}
		$urlap.='</tr></table>';
	}
	$urlap.='</td></tr>';

//F�jlok
	$urlap.="\n<tr><td bgcolor=#efefef valign=top><div class=kiscim align=right>Let�lthet� f�jl(ok):</td><td valign=top bgcolor='#efefef'>";
	$urlap.="\n<span class=alap>Kapcsol�d� dokumentum (pl. jelentkez�si lap, stb.), ha van ilyen:</span><br>";
	$urlap.="\n<span class=alap>�j f�jl: </span><input type=file size=60 name=fajl class=urlap><br>";
	//K�nyvt�r tartalm�t beolvassa
	if($hid>0) {
		$konyvtar="fajlok/hirek/$hid";
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
					$urlap.="<br><a href=\"$konyvtar/$filekiir\" class=\"link\" target=\"_blank\"><b>$file</b></a><span class=kicsi> ($meret) </span><input type=checkbox class=urlap name=delfajl[] value='$file'><span class=alap>T�r�l</span>";
				}
			}
			closedir($handle);
		}
	}

//Megjelenhet (jogosults�g!)
	if(strstr($u_jogok,'hirek')) {
		$urlap.="\n<tr><td><div class=kiscim align=right>Megjelenhet:</div></td><td>";
		$urlap.="\n<input type=checkbox name=ok value=i class=urlap";
		if($ok!='n' and $ok!='f') $urlap.=' checked';
		$urlap.="><span class=alap>igen</span> ";
		$urlap.="</td></tr>";
	}

//Sz�veg
	$urlap.="<tr><td bgcolor=#EFEFEF valign=top><div class=kiscim align=right>H�r r�szletes sz�vege:</div></td><td bgcolor=#EFEFEF valign=top><span class=alap><font color=red><b>FONTOS!</b></font> A sz�veghez MINDIG legyen st�lus rendelve! <br>(Els� felt�lt�s ut�n a szerkeszt�ablakkal form�zhat�.)</span><br><textarea name=szoveg class=urlap cols=90 rows=40>$szoveg</textarea>";

	$urlap.="\n</td></tr>";
	
//Log
	if($hid>0 and strstr($u_jogok,'hirek')) {
		$urlap.="\n<tr><td valign=top><div class=kiscim align=right>t�rt�net:</div></td><td valign=top><textarea cols=50 rows=6 disabled>Sz�ml�l�: $szamlalo\n$log</textarea></td></tr>";
	}

	$urlap.='</table>';

	$urlap.="\n<br><input type=submit value=Mehet class=urlap>";
	if($hid>0) {
		$urlap.="<input type=checkbox name=modosit value=i class=urlap checked><span class=alap> �s �jra m�dos�t</span>";
		$urlap.=" &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <a href=?m_id=$m_id&m_op=addmegse&hid=$hid&kod=$kod$linkveg class=link><font color=red>Kil�p�s m�dos�t�s n�lk�l</font></a>";
	}
	else $urlap.="<input type=hidden name=modosit value=i>";
	$urlap.="\n</form>";

	$adatT[2]='<span class=alcim>H�r felt�lt�se</span><br><br>'.$urlap;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function hirekadding() {
	global $db_name,$u_login,$u_jogok;

	$ip=$_SERVER['REMOTE_ADDR'];
    $host = gethostbyaddr($ip);

	$hiba=false;
	$hid=$_POST['hid'];
	if(!is_numeric($hid) and !empty($hid)) {
		echo 'HIBA! - nem j� a hid';
		exit();
	}
	if($hid>0) {
		//Ha m�dos�t�s t�rt�nt
		$lekerdez=mysql_db_query($db_name,"select megnyitva,feltette,megbizhato,ok from hirek where id='$hid'");
		list($megnyitva,$feltolto,$megbizhato,$ok)=mysql_fetch_row($lekerdez);
		if(strstr($megnyitva,$u_login)) { //�s � nyitotta meg utolj�ra,
			mysql_db_query($db_name,"update hirek set megnyitva='' where id='$hid'"); //akkor t�r�lj�k a bejegyz�st
		}

		$szerkesztheti=false;
		if(strstr($u_jogok,'hirek')) {
			$szerkesztheti=true;
			$ok=$_POST['ok'];
			if($ok!='i') $ok='n';
			$feltolto=$_POST['feltolto'];
			if(empty($feltolto)) $feltolto=$u_login;
			$megbizhato=$_POST['megbizhato'];
			if($megbizhato!='i') $megbizhato='n';
		}
		elseif($u_login==$feltolto and $megbizhato=='i') {
			$szerkesztheti=true;
		}
		elseif($ok=='f') {
			$szerkesztheti=true;
		}
	}
	else {
		$szerkesztheti=true;
		$ok='f';

		if(strstr($u_jogok,'hirek')) {
			//HA admin t�lt fel �j h�rt, akkor az enged�lyez�s adatait is r�gz�tj�k!
			$ok=$_POST['ok'];
			if($ok!='i') $ok='n';
			$feltolto=$_POST['feltolto'];
			if(empty($feltolto)) $feltolto=$u_login;
			$megbizhato=$_POST['megbizhato'];
			if($megbizhato!='i') $megbizhato='n';
		}
		else {
			//Ha sima �jfelt�lt�s, akkor a saj�t nev�hez rendelj�k
			//�s megkeress�k, hogy m�s h�rekn�l hogy lett be�ll�tva a megb�zhat�s�ga
			$feltolto=$u_login;
			$query="select megbizhato from hirek where feltette='$u_login' limit 0,1";
			list($megbizhato)=mysql_fetch_row(mysql_db_query($db_name,$query));
			if($megbizhato!='i') $megbizhato='n';
		}

	}

	if(!$szerkesztheti) {
		echo 'HIBA! Te nem szerkesztheted!';
		exit();
	}

	$modosit=$_POST['modosit'];
	$adminmegj=$_POST['adminmegj'];
	$kontakt=$_POST['kontakt'];
	$kontaktmail=$_POST['kontaktmail'];	
	$cim=$_POST['cim'];
	$datum=$_POST['datum'];
	$hirlevel=$_POST['hirlevel'];
	$szervezotipus=$_POST['szervezotipus'];
	$szervezonev=$_POST['szervezonev'];
	$szervezoinfo=$_POST['szervezoinfo'];
	$fizetos=$_POST['fizetos'];
	$intro=$_POST['intro'];
	$aktualis=$_POST['aktualis'];
	if(strstr($aktualis,'=>')) { //t�l-ig lett megadva
		$aktualisT=explode('=>',$aktualis);
		$tol=$aktualisT[0];
		$ig=$aktualisT[1];
		$tolev=substr($tol,0,4);
		$igev=substr($ig,0,4);
		$tolho=substr($tol,5,2);
		$igho=substr($ig,5,2);
		$tolnap=substr($tol,8,2);
		$ignap=substr($ig,8,2);
		$tolido=mktime(0,0,0,$tolho,$tolnap,$tolev);
		$igido=mktime(0,0,0,$igho,$ignap,$igev);
		$egynap=86400;
		for($i=$tolido;$i<=$igido;$i=$i+$egynap) {
			$ujaktualisT[]=date('Y-m-d',$i);
		}
		if(is_array($ujaktualisT)) $aktualis=implode('+',$ujaktualisT);
	}
	$egyhazmegye=$_POST['egyhazmegye'];
	$espkerT=$_POST['espkerT'];
	if($egyhazmegye>0) {
		$espker=$espkerT[$egyhazmegye];
	}
	$orszag=$_POST['orszag'];
	$megyeT=$_POST['megyeT'];
	if($orszag>0) {
		$megye=$megyeT[$orszag];
	}
	$varosT=$_POST['varosT'];
	if($orszag>0 and $megye>0) {
		$varos=$varosT[$orszag][$megye];
	}
	elseif($orszag>0) {
		$varos=$varosT[$orszag][0];
	}
	$hatarido=$_POST['hatarido'];
	$fohir=$_POST['fohir'];
	if($fohir!='i') $fohir='n';
		
	$rovatT=$_POST['rovat'];
	if(is_array($rovatT)) $rovatkat='-'.implode('--',$rovatT).'-';
	
	$k2T=$_POST['k2'];
	if(is_array($k2T)) $kiemeltT[]='-k2*'.implode('--k2*',$k2T).'-';
	$k3T=$_POST['k3'];
	if(is_array($k3T)) $kiemeltT[]='-k3*'.implode('--k3*',$k3T).'-';

	if(is_array($kiemeltT)) $kiemelt=implode('',$kiemeltT);


	$kerdes=$_POST['kerdes'];	

	$szoveg=$_POST['szoveg'];
	$szoveg=str_replace('&eacute;','�',$szoveg);
	$szoveg=str_replace('&aacute;','�',$szoveg);
	$szoveg=str_replace('&Eacute;','�',$szoveg);
	$szoveg=str_replace('&Aacute;','�',$szoveg);
	$szoveg=str_replace('&ouml;','�',$szoveg);
	$szoveg=str_replace('&Ouml;','�',$szoveg);
	$szoveg=str_replace('&uuml;','�',$szoveg);
	$szoveg=str_replace('&Uuml;','�',$szoveg);
	$szoveg=str_replace("'","\'",$szoveg);

	$elsofeltoltes=$_POST['elsofeltoltes'];
	if($elsofeltoltes=='i') $szoveg='<p class=alap>'.nl2br($szoveg);


	$galeriaT=$_POST['galeria'];
	if(is_array($galeriaT)) $galeria='-'.implode('--',$galeriaT).'-';

///////////////////////////
	$kep=$_FILES['kep']['tmp_name'];
	$kepnev=$_FILES['kep']['name'];
	$kicsinyit=$_POST['kicsinyit'];
	if(empty($kicsinyit)) $kicsinyit=120;
	$align=$_POST['align'];
	if($align!='0') $align="align=$align";

	$fajl=$_FILES['fajl']['tmp_name'];
	$fajlnev=$_FILES['fajl']['name'];
	$delfajl=$_POST['delfajl'];

	if(is_array($delfajl)) {
		foreach($delfajl as $ertek) {
			unlink("fajlok/hirek/$hid/$ertek");
		}
	}

//Kulcssz�kezel�s
	$kulcsszoT=$_POST['kulcsszo'];
	$ujkulcsszo=$_POST['ujkulcsszo'];

	if(is_array($kulcsszoT)) {
		$kulcsszo='-'.implode('--',$kulcsszoT).'-';
	}

	if(!empty($ujkulcsszo)) {
		$query="select id from kulcsszo where nev='$ujkulcsszo'";
		$lekerdez=mysql_db_query($db_name,$query);
		list($ksz_id)=mysql_fetch_row($lekerdez);
		if($ksz_id>0) $kulcsszo.="-$ksz_id-"; //ha olyat �rtak be �jnak, ami m�r volt
		else {
			mysql_db_query($db_name,"insert kulcsszo set nev='$ujkulcsszo'");
			$ksz_id=mysql_insert_id();
			$kulcsszo.="-$ksz_id-";
		}
	}

	if(empty($cim)) {
		$hiba=true;
		$hibauzenet.='<br>Nem lett kit�ltve a c�m mez�!';
	}
	if(empty($intro) and empty($szoveg)) {
		$hiba=true;
		$hibauzenet.='<br>Nem lett kit�ltve sem bevezet�, sem sz�vegmez�!';
	}

	if($hiba) {
		$txt.="<span class=hiba>HIBA a h�rek felt�lt�s�n�l!</span><br>";
		$txt.='<span class=alap>'.$hibauzenet.'</span>';
		$txt.="<br><br><a href=javascript:history.go(-1); class=link>Vissza</a>";
	
		$adatT[2]='<span class=alcim>H�rek admin</span><br><br>'.$txt;
		$tipus='doboz';
		$kod.=formazo($adatT,$tipus);	
		
		echo $txt;
	}
	else {
		$most=date('Y-m-d H:i:s');
		if($hid>0) {
			$uj=false;
			$parameter1='update';
			list($log)=mysql_fetch_row(mysql_db_query($db_name,"select log from hirek where id='$hid'"));
			$ujlog=$log."\nMod: $u_login ($most)";
			$parameter2=", modositotta='$u_login', moddatum='$most', log='$ujlog' where id='$hid'";
		}
		else {
			$uj=true;
			$parameter1='insert';
			$parameter2=", fdatum='$most', log='Add: $u_login ($most)'";
		}

		$query="$parameter1 hirek set kontakt='$kontakt', kontaktmail='$kontaktmail', cim='$cim', intro='$intro', szoveg='$szoveg', kerdes='$kerdes', orszag='$orszag', megye='$megye', varos='$varos', egyhazmegye='$egyhazmegye', espereskerulet='$espker', datum='$datum', aktualis='$aktualis', tol='$tol', hatarido='$hatarido', szervezotipus='$szervezotipus', szervezonev='$szervezonev', szervezoinfo='$szervezoinfo', fizetos='$fizetos', fohir='$fohir', rovatkat='$rovatkat', kulcsszo='$kulcsszo', galeria='$galeria', kiemelt='$kiemelt', ok='$ok', hirlevel='$hirlevel', adminmegj='$adminmegj', feltette='$feltolto', megbizhato='$megbizhato' $parameter2";
		if(!mysql_db_query($db_name,$query)) echo 'HIBA!<br>'.mysql_error();
		if($uj) $hid=mysql_insert_id();

		if(!empty($fajl)) {
		$konyvtar="fajlok/hirek";
		//K�nyvt�r ellen�rz�se
		if(!is_dir("$konyvtar/$hid")) {
			//l�tre kell hozni
			if(!mkdir("$konyvtar/$hid",0775)) {
				echo '<p class=hiba>HIBA a k�nyvt�r l�trehoz�s�n�l!</p>';					
			}
		}
		//M�sol�s
		if(!copy($fajl,"$konyvtar/$hid/$fajlnev")) echo '<p>HIBA a m�sol�sn�l!</p>';
		unlink($fajl);
	}

	//k�pkezel�s
		$konyvtar="kepek/hirek/$hid";		

		$delkepT=$_POST['delkepT'];
		if(is_array($delkepT)) {		
			foreach($delkepT as $ertek) {
				@unlink("$konyvtar/$ertek");
				@unlink("$konyvtar/kicsi/$ertek");
				if(!mysql_db_query($db_name,"delete from kepek where kat='hirek' and kid='$hid' and fajlnev='$ertek'")) echo 'HIBA!<br>'.mysql_error();
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
						if(!mkdir("$konyvtar/fooldal",0775)) {
							echo '<p class=hiba>HIBA a k�nyvt�r l�trehoz�s�n�l!</p>';					
						}
					}

					$kimenet="$konyvtar/$kepnevT[$id]";
					$kimenet1="$konyvtar/kicsi/$kepnevT[$id]";
					$kimenet2="$konyvtar/fooldal/$kepnevT[$id]";
	
					if ( !copy($kep, "$kimenet") )
						print("HIBA a m�sol�sn�l ($kimenet)!<br>\n");
					else  {
						//Bejegyz�s az adatb�zisba
						if(!mysql_db_query($db_name,"insert kepek set kat='hirek', kid='$hid', fajlnev='$kepnevT[$id]', felirat='$kepfeliratT[$id]'")) echo 'HIBA!<br>'.mysql_error();
					}
					
					unlink($kep);
	
					$info=getimagesize($kimenet);
					$w=$info[0];
					$h=$info[1];
      
					if($w>800 or $h>600) kicsinyites($kimenet,$kimenet,800);
			  		kicsinyites($kimenet,$kimenet1,120);
					kicsinyites($kimenet,$kimenet2,90);
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
				if(!mysql_db_query($db_name,"update kepek set felirat='$kepfeliratmodT[$melyikkep]', sorszam='$ertek', kiemelt='$kiemelt' where kat='hirek' and kid='$hid' and fajlnev='$melyikkep'")) echo 'HIBA!<br>'.mysql_error();
			}
		}	

		if($modosit=='i') $kod=$hid;
		else $kod=0;
	}

	return $kod;
}

?>
