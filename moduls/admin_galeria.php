<?php

function galeria_index() {
	global $linkveg,$m_id;

	$menu.="<a href=?m_id=$m_id&m_op=add$linkveg class=kismenulink>�j gal�ria - hozz�ad�s</a><br>";
	$menu.="<a href=?m_id=$m_id&m_op=mod$linkveg class=kismenulink>Megl�v� m�dos�t�sa, t�rl�se</a><br>";

	$adatT[2]="<span class=alcim>Gal�ria szerkeszt�se</span><br><br>".$menu;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function galeria_add($gid) {
	global $sessid,$m_id,$db_name;

	if($gid>0) {
		$query="select cim,intro,datum,ok,szamlalo,log from galeria where id='$gid'";
		list($cim,$intro,$datum,$ok,$szamlalo,$log)=mysql_fetch_row(mysql_db_query($db_name,$query));
	}
	else $datum=date('Y-m-d');

	$urlap="\n<form method=post ENCTYPE='multipart/form-data'>";
	$urlap.="\n<input type=hidden name=m_id value=$m_id><input type=hidden name=sessid value=$sessid>";
	$urlap.="\n<input type=hidden name=m_op value=adding><input type=hidden name=gid value=$gid>";
	
	$urlap.='<table width=100%><tr><td valign=top width=50%>';

	$urlap.="\n<span class=kiscim>C�m: </span><br><input type=text name=cim value='$cim' class=urlap size=60 maxlength=100>";
	$urlap.="\n<br><br><span class=kiscim>D�tum</span><span class=alap> (egyben amikort�l megjelenik):</span><br><input type=text name=datum class=urlap maxlength=10 value='$datum' size=10> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ";
	if($ok=='n') $urlap.="<img src=img/tilos.gif title='jelenleg nem enged�lyezett'> ";
	$urlap.="<input type=checkbox name=ok value=i ";
	if($ok!='n') $urlap.=' checked';
	$urlap.="> <span class=alap>enged�lyez�s</span>";
	$urlap.="\n<br><br><span class=kiscim>Bevezet� sz�veg: </span><br><textarea name=intro class=urlap cols=60 rows=20>$intro</textarea>";
	
	$urlap.='</td><td width=50% valign=top>';

	$urlap.="\n<span class=kiscim>K�pek: </span><br><input type=file name=kepT[] size=50 class=urlap>";
	$urlap.='<br><br><input type=file name=kepT[] size=50 class=urlap><br><br><input type=file name=kepT[] size=50 class=urlap><br><br><input type=file name=kepT[] size=50 class=urlap><br><br><input type=file name=kepT[] size=50 class=urlap>';

	$urlap.="\n<br><br><input type=submit value=Mehet class=urlap>";

//Log
	if(!empty($log)) {
		$urlap.="\n<br><br><span class=kiscim>t�rt�net:</span><br><textarea cols=40 rows=8 disabled>Sz�ml�l�: $szamlalo\n$log</textarea>";
	}



	$urlap.='</td></tr></table></form><hr><span class=alcim>K�pc�mek �s sorbarendez�s be�ll�t�sa</span><hr><table width=100%><tr>';

	//K�pc�mek lek�rdez�se
	if($gid>0) {
		$query="select cim,fajlnev,sorszam from g_kepcimek where gid='$gid' order by sorszam";
		if(!$lekerdez=mysql_db_query($db_name,$query)) echo "HIBA!<br>".mysql_error();

		$konyvtar="kepek/galeria/$gid/kicsi";
		$urlap.="<form method=post><input type=hidden name=gid value=$gid><input type=hidden name=sessid value=$sessid><input type=hidden name=m_id value=$m_id><input type=hidden name=m_op value=kepcimadding>";
		while(list($kcim,$kfnev,$ksorszam)=mysql_fetch_row($lekerdez)) {
			$altT[$kfnev]=$kcim;
			$sszT[$kfnev]=$ksorszam;

			if($a%3==0 and $a>0) $urlap.='</tr><tr><td colspan=3><hr></td></tr><tr>';
			$a++;
			$urlap.="<td valign=bottom><input type=text name='sorszamT[$kfnev]' value='$sszT[$kfnev]' size=2 maxlength=2 class=urlap> <img src=$konyvtar/$kfnev><input type=checkbox class=urlap name=delkepT[] value='$kfnev'><img src=img/del.jpg title=t�r�l>";
			$urlap.="<br><input type=text name=altT[$kfnev] value='$altT[$kfnev]' class=urlap size=35>";					
			if($a%6==0) $urlap.="<input type=submit value=M�dos�t class=urlap>";
			$urlap.="</td>";

		}
		$urlap.="</tr><td colspan=3><input type=submit value=M�dos�t class=urlap></td></form>";
		
	}
	$urlap.='</tr></table>';

	$adatT[2]="<span class=alcim>Gal�ria szerkeszt�se</span><br><br>".$urlap;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function galeria_kepcimadding() {
	global $_POST,$u_login,$db_name;

	$gid=$_POST['gid'];
	$altT=$_POST['altT'];
	$sorszamT=$_POST['sorszamT'];

	if(is_array($sorszamT)) {
		foreach($sorszamT as $fajlnev=>$sorszam) {
			$vane=mysql_db_query($db_name,"select gid from g_kepcimek where gid='$gid' and fajlnev='$fajlnev'");
			if(mysql_num_rows($vane)>0) {
				$query="update g_kepcimek set cim='$altT[$fajlnev]', sorszam='$sorszam' where gid='$gid' and fajlnev='$fajlnev'";
			}
			else {
				$query="insert g_kepcimek set cim='$altT[$fajlnev]', sorszam='$sorszam', gid='$gid', fajlnev='$fajlnev'";
			}
			if(!mysql_db_query($db_name,$query)) echo 'HIBA!<br>'.mysql_error();
		}
	}

//kijel�lt k�pek t�rl�se
	$konyvtar="kepek/galeria/$gid";
	$delkepT=$_POST['delkepT'];
	if(is_array($delkepT)) {
		foreach($delkepT as $delkep) {
			unlink("$konyvtar/$delkep");
			unlink("$konyvtar/kicsi/$delkep");
			//adatb�zisb�l is!
			mysql_db_query($db_name,"delete from g_kepcimek where gid='$gid' and fajlnev='$delkep'");
		}
	}

//log bejegyz�s
	$most=date('Y-m-d H:i:s');
	list($log)=mysql_fetch_row(mysql_db_query($db_name,"select log from galeria where id='$gid'"));
	$ujlog=$log."\n->k�pMod: $u_login ($most)";
	$query="update galeria set log='$ujlog' where id='$gid'";
	mysql_db_query($db_name,$query);

	$kod=galeria_add($gid);

	return $kod;
}

function galeria_adding() {
	global $_POST,$_FILES,$u_login,$db_name;

	$gid=$_POST['gid'];
	$cim=$_POST['cim'];
	$intro=$_POST['intro'];
	$datum=$_POST['datum'];
	$ok=$_POST['ok'];
	if($ok!='i') $ok='n';
	$most=date('Y-m-d H:i:s');
	$altT=$_POST['altT'];
	$sorszamT=$_POST['sorszamT'];

	if($gid>0) {
		$uj=false;
		$parameter1='update';
		list($log)=mysql_fetch_row(mysql_db_query($db_name,"select log from galeria where id='$gid'"));
		$ujlog=$log."\nMod: $u_login ($most)";
		$parameter2=", log='$ujlog' where id='$gid'";
	}
	else {
		$uj=true;
		$parameter1='insert';
		$parameter2=",letrehozta='$u_login', regdatum='$most', log='Add: $u_login ($most)'";
	}

	$query="$parameter1 galeria set cim='$cim', intro='$intro', datum='$datum', ok='$ok' $parameter2";
	mysql_db_query($db_name,$query);
	if($uj) $gid=mysql_insert_id();

	$konyvtar="kepek/galeria/$gid";

//k�pek felt�lt�se
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
					if(!mysql_db_query($db_name,"insert g_kepcimek set gid='$gid', fajlnev='$kepnevT[$id]'")) echo 'HIBA!<br>'.mysql_error();
				}
				
				unlink($kep);
	
				$info=getimagesize($kimenet);
				$w=$info[0];
				$h=$info[1];
      
				if($w>800 or $h>600) kicsinyites($kimenet,$kimenet,600);
		  		kicsinyites($kimenet,$kimenet1,120);
			}
		}
	}

	$kod=galeria_add($gid);

	return $kod;
}

function galeria_mod() {
	global $db_name,$linkveg,$m_id;

	$kiir.="<span class=kiscim>V�lassz az al�bbi gal�ri�k k�z�l:</span><br><br>";

	$query="select id,cim,datum,ok from galeria order by datum desc";
	$lekerdez=mysql_db_query($db_name,$query);
	while(list($gid,$cim,$datum,$ok)=mysql_fetch_row($lekerdez)) {
		if($ok!='i') $kiir.="<img src=img/tilos.gif align=absmiddle title='nem enged�lyezett'>";
		$kiir.="\n<a href=?m_id=$m_id&m_op=add&gid=$gid$linkveg class=link><b> - $cim</b> ($datum)</a> - <a href=?m_id=$m_id&m_op=del&gid=$gid$linkveg class=link><img src=img/del.jpg border=0 alt=T�r�l align=absmiddle> t�r�l</a><br>";
	}

	$adatT[2]="<span class=alcim>Gal�ria szerkeszt�se - m�dos�t�s</span><br><br>".$kiir;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function galeria_del() {
	global $_GET,$db_name,$linkveg,$m_id;

	$gid=$_GET['gid'];

	$kiir="\n<span class=kiscim>Biztosan t�r�lni akarod a k�vetkez� gal�ri�t?</span>";
	
	$query="select cim,datum from galeria where id='$gid'";
	list($cim,$datum)=mysql_fetch_row(mysql_db_query($db_name,$query));

	$kiir.="\n<br><br><span class=alap>$cim ($datum)</span>";

	$kiir.="<br><br><a href=?m_id=$m_id&m_op=delete&gid=$gid$linkveg class=link>Igen</a> - <a href=?m_id=$m_id&m_op=mod$linkveg class=link>NEM</a>";

	$adatT[2]="<span class=alcim>Gal�ria szerkeszt�se - t�rl�s</span><br><br>".$kiir;
	$tipus='doboz';
	$tartalom.=formazo($adatT,$tipus);	
	
	$kod=$tartalom;

	return $kod;
}

function galeria_delete() {
	global $_GET,$db_name;

	$id=$_GET['gid'];
	$query="delete from galeria where id='$id'";
	mysql_db_query($db_name,$query);

//k�pc�mek t�rl�se
	$query="delete from g_kepcimek where gid='$id'";
	mysql_db_query($db_name,$query);

//k�pek t�rl�se
		$konyvtar="kepek/galeria/$id";
		if(is_dir($konyvtar)) {
			$handle=opendir($konyvtar);
			while ($file = readdir($handle)) {
				if ($file!='.' and $file!='..' and $file!='kicsi') {
					unlink("$konyvtar/$file");
					unlink("$konyvtar/kicsi/$file");
				}
			}
			closedir($handle);
		}

	$kod=galeria_mod();

	return $kod;
}

//Jogosults�g ellen�rz�se
if(strstr($u_jogok,'galeria')) {

switch($m_op) {
    case 'index':
        $tartalom=galeria_index();
        break;

	case 'add':
		$gid=$_GET['gid'];
        $tartalom=galeria_add($gid);
        break;

    case 'mod':
        $tartalom=galeria_mod();
        break;

    case 'adding':
        $tartalom=galeria_adding();
        break;

	case 'kepcimadding':
		$tartalom=galeria_kepcimadding();
		break;

    case 'del':
        $tartalom=galeria_del();
        break;

	case 'delete':
        $tartalom=galeria_delete();
        break;
}
}
else {
	$tartalom="\n<span class=hiba>HIBA! Nincs hozz� jogosults�god!</span>";
}
?>
