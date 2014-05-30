<?

function idoszak($i) {
    switch($i) {
        case 'a': $tmp = '�dventi id�'; break;
        case 'k': $tmp = 'Kar�csonyi id�'; break;
        case 'n': $tmp = 'Nagyb�jti id�'; break;
        case 'h': $tmp = 'H�sv�ti id�'; break;
        case 'e': $tmp = '�vk�zi id�'; break;
		case 's': $tmp = 'Szent �nnepe'; break;
    }
    return $tmp;
}

function miserend_index() {
	global $linkveg,$db_name,$m_id,$u_login,$sid,$design_url,$_GET,$u_varos,$onload,$script;

	$ma=date('Y-m-d');
	$holnap=date('Y-m-d',(time()+86400));
	$mikor='8:00-19:00';

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

	//Miserend �rlap
	$miseurlap="\n<div style='display: none'><form method=post><input type=hidden name=sid value=$sid><input type=hidden name=m_id value=$m_id><input type=hidden name=m_op value=misekeres></div>";

//Mikor
	$mainap=date('w');
	if($mainap==0) $vasarnap=$ma;
	else {
		$kulonbseg=7-$mainap;
		$vasarnap=date('Y-m-d',(time()+(86400*$kulonbseg)));
	}
	$miseurlap.="\n<img src=img/space.gif width=5 height=10><br><span class=kiscim>Mikor: </span><br><img src=img/space.gif width=10 height=5><select name=mikor class=keresourlap onChange=\"if(this.value == 'x') {document.getElementById('md').style.display='inline';} else {document.getElementById('md').style.display='none';}\">";
	$miseurlap.="<option value='$vasarnap'>vas�rnap</option><option value='$ma'>ma</option><option value='$holnap'>holnap</option><option value=x>adott napon:</option>";
	$miseurlap.="</select> <input type=text name=mikordatum id=md style='display: none' class=keresourlap maxlength=10 size=10 value='$ma'>";

	$miseurlap.="<br><img src=img/space.gif width=10 height=5><br><img src=img/space.gif width=10 height=5><select name=mikor2 class=keresourlap onChange=\"if(this.value == 'x') {document.getElementById('md2').style.display='inline'; alert('FIGYELEM! Fontos a form�tum!');} else {document.getElementById('md2').style.display='none';}\">";
	$miseurlap.="<option value=0>eg�sz nap</option><option value='de'>d�lel�tt</option><option value='du'>d�lut�n</option><option value=x>adott id�ben:</option>";
	$miseurlap.="</select> <input type=text name=mikorido id=md2 style='display: none' class=keresourlap maxlength=11 size=10 value='$mikor'>";
	$miseurlap.="<br><img src=img/space.gif width=5 height=8>";

//Hol
	$miseurlap.="\n<img src=img/space.gif width=5 height=10><br><span class=kiscim>Hol:</span><br><span class=alap>- telep�l�s: </span><br><img src=img/space.gif width=10 height=5><input type=text name=varos size=20 class=keresourlap>";	
	$miseurlap.="<br><img src=img/space.gif width=5 height=8>";

	$miseurlap.="<br><span class=alap>- egyh�zmegye: </span><br><img src=img/space.gif width=5 height=5><br><img src=img/space.gif width=10 height=5><select name=ehm class=keresourlap onChange=\"if(this.value!=0) {";
	foreach($ehmT as $id=>$nev) {
		$miseurlap.="document.getElementById('esp$id').style.display='none'; ";
	} 
	$miseurlap.="document.getElementById('esp'+this.value).style.display='inline'; document.getElementById('valassz1').style.display='none'; } else {";
	foreach($ehmT as $id=>$nev) {
		$miseurlap.="document.getElementById('esp$id').style.display='none'; ";
	} 
	$miseurlap.="document.getElementById('valassz1').style.display='inline';}\"><option value=0>mindegy</option>";	
	foreach($ehmT as $id=>$nev) {
		$miseurlap.="<option value=$id>$nev</option>";
	
		$espkerurlap.="<select id='esp$id' name=espkerT[$id] class=keresourlap style='display: none'><option value=0>mindegy</option>";	
		if(is_array($espkerT[$id])) {
    		    foreach($espkerT[$id] as $espid=>$espnev) {
			$espkerurlap.="<option value=$espid>$espnev</option>";
		    }
		}
		$espkerurlap.="</select>";
	}
	$miseurlap.="</select><br><img src=img/space.gif width=5 height=8>";
	$miseurlap.="<br><span class=alap>- esperesker�let: </span><br><img src=img/space.gif width=5 height=5><br><img src=img/space.gif width=10 height=5>";
	$miseurlap.="<div id='valassz1' style='display: inline' class=keresourlap>El�sz�r v�lassz egyh�zmegy�t.</div>";
	$miseurlap.=$espkerurlap;
	$espkerurlap='';

//Milyen
	$miseurlap.="\n<br><img src=img/space.gif width=5 height=10><br><span class=kiscim>Milyen:</span><br>";
	$miseurlap.="<table width=100% cellpadding=0 cellspacing=0><tr><td><span class=alap>- nyelv: </span><br><select name=nyelv class=keresourlap><option value=0>mindegy</option><option value=h>magyar</option><option value=va>latin</option><option value=de>n�met</option><option value=sk>szlov�k</option><option value=hr>horv�t</option><option value=pl>lengyel</option><option value=si>szlov�n</option><option value=ro>rom�n</option><option value=en>angol</option><option value=gr>g�r�g</option><option value=it>olasz</option><option value=fr>francia</option><option value=es>spanyol</option></select></td>";

	$miseurlap.="<td><span class=alap>- zene: </span><br><select name=zene class=keresourlap><option value=0>mindegy</option><option value=g>git�ros</option><option value=o>orgon�s</option><option value=cs>csendes</option>";	
	$miseurlap.="</select></td>";
	$miseurlap.="<td><span class=alap>- di�k: </span><br><select name=diak class=keresourlap><option value=0>mindegy</option><option value=d>di�k</option><option value=nd>nem di�k</option>";	
	$miseurlap.="</select></td></tr></table>";


	$miseurlap.="<br><img src=img/space.gif width=5 height=10><div align=center><input type=submit value=keres�s class=keresourlap><br><img src=img/space.gif width=5 height=8></div><div style='display: none'></form></div>";

//K�vetkez� mise a k�zelben
	$mainap=date('w');
	$mostido=date('H:i:s');
	if($mainap==0) $mainap=7;
	if(!empty($u_varos)) {
		$kovetkezomise.='<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr bgcolor="#EAEDF1"><td bgcolor="#EAEDF1" width="5"><img src="img/space.gif" width="5" height="5"></td><td bgcolor="#EAEDF1"><img src="'.$design_url.'/img/negyzet_lila.gif" width="6" height="8" align="absmiddle"><img src="img/space.gif" width="5" height="5"><span class="dobozcim_kek">K�vetkez� mis�k ('.$u_varos.'):</span></td>';
        $kovetkezomise.='<td width="5" bgcolor="#EAEDF1"><img src="img/space.gif" width="5" height="5"></td></tr><tr bgcolor="#F8F4F6">';
        $kovetkezomise.='<td width="5" bgcolor="#F8F4F6"></td><td bgcolor="#F8F4F6">';
		$vanmise=false;
		$query="select id,nev,ismertnev,nyariido,teliido from templomok where ok='i' and varos='$u_varos'";
		$lekerdez=mysql_query($query);
		while(list($tid,$tnev,$tismnev,$nyariido,$teliido)=mysql_fetch_row($lekerdez)) {
			//misekeres
			if($ma<$nyariido or $ma>$teliido) $idoszamitas='t';
			else $idoszamitas='ny';
			$querym="select ido,nyelv,milyen,megjegyzes from misek where templom='$tid' and nap='$mainap' and idoszamitas='$idoszamitas' and ido>='$mostido' and torles=0 order by ido limit 0,3";
			$lekerdezm=mysql_query($querym);
			if(mysql_num_rows($lekerdezm)>0) {
				$vanmise=true;
				$kovetkezomise.="<img src=img/space.gif width=5 height=5><br><a href=?templom=$tid$linkveg class=link title='$tismnev'><b>$tnev</b></a><br>";	
				while(list($ido,$nyelv,$milyen,$megjegyzes)=mysql_fetch_row($lekerdezm)) {
					$ido=substr($ido,0,5);
					$kovetkezomise.="<span class=alap>$ido </span>";
					if(!empty($megjegyzes)) {
						$kovetkezomise.="<img src=$design_url/img/info2.gif title='$megjegyzes' width=16 height=16 align=absmiddle>";
					}
					if(!empty($milyen)) {
						if(strstr($milyen,'g')) {
							$kovetkezomise.="<img src=$design_url/img/gitar.gif width=16 height=16 title='git�ros mise' align=absmiddle>";
						}
						if(strstr($milyen,'d')) {
							$kovetkezomise.="<img src=$design_url/img/diak.gif width=16 height=16 title='di�k mise' align=absmiddle>";
						}
						if(strstr($milyen,'cs')) {
							$kovetkezomise.="<img src=$design_url/img/csendes.gif width=16 title='csendes mise' height=16 align=absmiddle>";
						}
					}
					$kovetkezomise.='<span class=alap> | </span>';
				}
				$kovetkezomise.='<br>';
			}			
		}
		if(!$vanmise) $kovetkezomise.='<span class=alap>Adatb�zisunkban m�ra m�r nincs t�bb miseid�pont a telep�l�sen.</span>';
		$kovetkezomise.='<img src=img/space.gif width=5 height=8></td><td width="5" bgcolor="#F8F4F6"></td></tr></table>';
	}

//Templom �rlap
	$templomurlap="\n<div style='display: none'><form method=post><input type=hidden name=sid value=$sid><input type=hidden name=m_id value=$m_id><input type=hidden name=m_op value=templomkeres></div>";
	$templomurlap.="\n<img src=img/space.gif width=5 height=10><br><span class=kiscim>Telep�l�s: </span><input type=text name=varos size=20 class=keresourlap><br><img src=img/space.gif width=5 height=8>";
	$templomurlap.="<br><span class=kiscim>Kulcssz�: </span><input type=text name=kulcsszo size=20 class=keresourlap><br><img src=img/space.gif width=5 height=8>";
	
	//Egyh�zmegye
	$templomurlap.="<br><span class=kiscim>Egyh�zmegye: </span><br><img src=img/space.gif width=5 height=5><br><img src=img/space.gif width=10 height=5><select name=ehm class=keresourlap onChange=\"if(this.value!=0) {";
	foreach($ehmT as $id=>$nev) {
		$templomurlap.="document.getElementById($id).style.display='none'; ";
	} 
	$templomurlap.="document.getElementById(this.value).style.display='inline'; document.getElementById('valassz').style.display='none'; } else {";
	foreach($ehmT as $id=>$nev) {
		$templomurlap.="document.getElementById($id).style.display='none'; ";
	} 
	$templomurlap.="document.getElementById('valassz').style.display='inline';}\"><option value=0>mindegy</option>";	
	foreach($ehmT as $id=>$nev) {
		$templomurlap.="<option value=$id>$nev</option>";
	
		$espkerurlap.="<select id=$id name=espkerT[$id] class=keresourlap style='display: none'><option value=0>mindegy</option>";
		if(is_array($espkerT[$id])) {	
			foreach($espkerT[$id] as $espid=>$espnev) {
				$espkerurlap.="<option value=$espid>$espnev</option>";
			}
		}
		$espkerurlap.="</select>";
	}
	$templomurlap.="</select><br><img src=img/space.gif width=5 height=8>";

	//Esperesker�let
	$templomurlap.="<br><span class=kiscim>Esperesker�let: </span><br><img src=img/space.gif width=5 height=5><br><img src=img/space.gif width=10 height=5>";
	$templomurlap.="<div id='valassz' style='display: inline' class=keresourlap>El�sz�r v�lassz egyh�zmegy�t.</div>";
	$templomurlap.=$espkerurlap;
	$templomurlap.="<br><img src=img/space.gif width=5 height=8>";
	
	//Megye
	/*
	$templomurlap.="<br><span class=kiscim>Megye: </span><br><img src=img/space.gif width=5 height=5><br><img src=img/space.gif width=10 height=5><select name=megye class=keresourlap><option value=0>mindegy</option>";	
	foreach($megyeT as $id=>$nev) {
		$templomurlap.="<option value=$id>$nev</option>";
	}
	$templomurlap.="</select>";
	*/
	$templomurlap.="\n<br><img src=img/space.gif width=5 height=10><div align=right><input type=submit value=keres�s class=keresourlap><br><img src=img/space.gif width=5 height=10></div><div style='display: none'></form></div>";


	//AndroidRekl�m
	$androidreklam = androidreklam();
	
	//Napi gondolatok
	//Napi igehely

	$datum=$_GET['datum'];
	if(empty($datum)) $datum=$ma;

	//A liturgikus napt�rb�l kiszedj�k, hogy mi kapcsol�dik a d�tumhoz
	$query="select ige,szent,szin from lnaptar where datum='$datum'";
	list($ige,$szent,$szin)=mysql_fetch_row(mysql_query($query));

	//Az igenapt�rb�l kikeress�k a mai napot
	$query="select ev,idoszak,nap,oszov_hely,ujszov_hely,evang_hely,unnep,intro,gondolat from igenaptar where id='$ige'";
	list($ev,$idoszak,$nap,$oszov_hely,$ujszov_hely,$evang_hely,$unnep,$intro,$gondolat)=mysql_fetch_row(mysql_query($query));
	$napiuzenet=nl2br($intro);
	$elmelkedes=$gondolat;

	if((!empty($ev)) and ($ev!='0')) $igenap.="$ev �v, ";
	if(!empty($idoszak)) $igenap.=idoszak($idoszak);
	if(!empty($nap)) $igenap.=" $nap";

	if(empty($unnep)) $unnep=$igenap; 


	if($szent>0) {
		//Ha szent tartozik a napohoz
		$query="select nev,intro,leiras from szentek where id='$szent'";
		list($szentnev,$szentintro,$szentleiras)=mysql_fetch_row(mysql_query($query));
		$unnep=$szentnev;
		$napiuzenet=nl2br($szentintro);
		$elmelkedes=$szentleiras;
	}

	//Tov�bbi szentek
	$s_ho=substr($datum,5,2);
	$s_nap=substr($datum,8,2);
	if($s_ho[0]=='0') $s_ho=$s_ho[1];
	if($s_nap[0]=='0') $s_nap=$s_nap[1];
	$query="select id,nev,intro,leiras from szentek where ho='$s_ho' and nap='$s_nap' and id!='$szent'";
	$lekerdez=mysql_query($query);
	while(list($szid,$sznev,$szintro,$szleiras)=mysql_fetch_row($lekerdez)) {
		$szentidT[]=$szid;
		$szentnevT[]=$sznev;
		$introT[]=nl2br($szintro);
		$leirasT[]=$szleiras;		
	}

	if(is_array($szentidT)) {
		foreach($szentidT as $kulcs=>$ertek) {			
			if($a>0) $megszentek.='<span class=link>, </span>';
			if(!empty($introT[$kulcs]) or !empty($leirasT[$kulcs])) {
				$link="<a href=?m_id=1&m_op=szview&id=$ertek&szin=$_GET[szin]$linkveg class=link>";
			}
			else $megszentek.='<span class=link>';
			$megszentek.=$link.$szentnevT[$kulcs];
			if(!empty($link)) $megszentek.='</a>';
			else $megszentek.='</span>';
			$link='';
			$a++;
		}
	}

	if(!empty($unnep)) {
		$unnepkiir="<span class=kiscim>$unnep</span>";
	}
	if(!empty($megszentek)) {
		$unnepkiir.='<br><span class=alap>(</span>'.$megszentek.'<span class=alap>)</span>';
	}

	$uzenet="$unnepkiir<br>";
	$uzenet.="<br><div class=alapkizart>$napiuzenet</div>";
	$elmelkedes="<span class=alapkizart>$elmelkedes</span>";


	$van_o=false;
	$van_u=false;
	$van_e=false;
	if(!empty($oszov_hely)) {
		$van_o=true;
		$tomb1=explode(',',$oszov_hely);
		$tomb2=explode('-',$tomb1[1]);
		$tomb3=explode(' ',$tomb1[0]);
		$konyv=$tomb3[0];
		$fej=$tomb3[1];
		$vers=$tomb2[0];
		$link="http://www.kereszteny.hu/biblia/showchapter.php?reftrans=1&abbook=$konyv&numch=$fej#$vers";
		$oszov_biblia="<a href=$link target=_blank title='ez a r�sz �s a k�rnyezete a Bibli�ban' class=link><img src=img/biblia.gif border=0 align=absmiddle> $oszov_hely</a><br>";
	}
	if(!empty($ujszov_hely)) {
		$van_u=true;
		$tomb1=explode(',',$ujszov_hely);
		$tomb2=explode('-',$tomb1[1]);
		$tomb3=explode(' ',$tomb1[0]);
		$konyv=$tomb3[0];
		$fej=$tomb3[1];
		$vers=$tomb2[0];
		$link="http://www.kereszteny.hu/biblia/showchapter.php?reftrans=1&abbook=$konyv&numch=$fej#$vers";
		$ujszov_biblia.="<a href=$link target=_blank title='ez a r�sz �s a k�rnyezete a Bibli�ban' class=link><img src=img/biblia.gif border=0 align=absmiddle> $ujszov_hely</a><br>";
	}
	if(!empty($evang_hely)) {
		$van_e=true;
		$tomb1=explode(',',$evang_hely);
		$tomb2=explode('-',$tomb1[1]);
		$tomb3=explode(' ',$tomb1[0]);
		$konyv=$tomb3[0];
		$fej=$tomb3[1];
		$vers=$tomb2[0];
		$link="http://www.kereszteny.hu/biblia/showchapter.php?reftrans=1&abbook=$konyv&numch=$fej#$vers";
		$evang_biblia.="<a href=$link target=_blank title='ez a r�sz �s a k�rnyezete a Bibli�ban' class=link><img src=img/biblia.gif border=0 align=absmiddle> $evang_hely</a><br>";
	}
	///////////////////////////////////////////////////////////////////
	$igehelyek=$oszov_biblia.$ujszov_biblia.$evang_biblia;

	//Lit. napt�r
	$naptar="<span class=alap>napt�r</span>";

	//Programaj�nl�
	$programajanlo="<span class=alap>kapcsol�d� programok a napt�rb�l<br>Fejleszt�s alatt...</span>";

	//K�pek
	$query="select kid,katnev,fajlnev,felirat from kepek,templomok where kepek.kid=templomok.id and kepek.kat='templomok' and templomok.ok='i' and kepek.kiemelt='i'";
	if(!$lekerdez=mysql_query($query)) echo "HIBA!<br>".mysql_error();
	$mennyi=mysql_num_rows($lekerdez);
	if($mennyi>0) {
		$kepek.="\n<img src=$design_url/img/negyzet_kek.gif align=absmiddle><img src=img/space.gif width=5 height=5><span class=dobozcim_fekete>K�pek templomainkr�l</span><br><table width=100% cellpadding=0 cellspacing=0 bgcolor=#EAEDF1><tr>";
		$konyvtaralap="kepek/templomok";
		while(list($tid,$kepcim,$fajlnev)=mysql_fetch_row($lekerdez)) {
			$altT[$fajlnev]=$kepcim;
			$tidT[$fajlnev]=$tid;
			$fajlnevT[]=$fajlnev;
		}
		$kulcsokT=array_rand($fajlnevT,15);
		foreach($kulcsokT as $kulcs) {
			$fajlnev=$fajlnevT[$kulcs];
			$tid=$tidT[$fajlnev];
			$kepcim=$altT[$fajlnev];
			$konyvtar="$konyvtaralap/$tid";
			
			@$info=getimagesize("$konyvtar/kicsi/$fajlnev");
			$w1=$info[0];
			$h1=$info[1];
			if($h1>$w1 and $h1>90) {
				$arany=90/$h1;
				$ujh=90;
				$ujw=$w1*$arany;
			}
			else {
				$ujh=$h1;
				$ujw=$w1;
			}
			$title=rawurlencode($kepcim);	
			if(is_file("$konyvtar/kicsi/$fajlnev")) {
				$osszw=$osszw+$ujw;
				if($osszw<=480) {
					$kepT[]="<a href=\"?templom=$tid$linkveg\"><img src=$konyvtar/kicsi/$fajlnev title='$kepcim' border=0 width=$ujw height=$ujh></a>";
					$kepscriptT.="\nArticle[i] = new Array (\"$konyvtar/kicsi/$fajlnev\", \"?templom=$tid$linkveg\", \"$kepcim\");i++  ";
				}		
			}
		}
	
		if($osszw>480) {
			$onload="loadScroller();";
			$script.="<script type=\"text/javascript\" language=\"JavaScript\">
				<!--                                      
				Article = new Array;
				i=0;";
			$script.=$kepscriptT;
			$script.="\n--></script>";

			$script.="\n<script type=\"text/javascript\" src=\"$design_url/scroll.js\"></script>";
			$kepek.="\n<td width=460><div>";
			$kepek.="\n<script type=\"text/javascript\" language=\"JavaScript\">buildScroller();</script>";
			$kepek.="\n</div>";
			$kepek.="</td><td width=20 bgcolor=#244C8F><a href=\"#\" onmouseover=\"javascript:moveLayer();\" class=dobozcim_feher><img src=$design_url/img/fehernyil_jobb.jpg border=0 align=right></a></td>";
		}
		elseif(is_array($kepT)) {
			$kepek.='<td>'.implode("<img src=img/space.gif width=5 height=7>",$kepT).'</td>';
		}
		$kepek.="</tr></table>";

	}

    //statisztika
    $statisztika = miserend_printRegi();
    
	$tmpl_file = $design_url.'/miserend_fooldal.htm';

    $thefile = implode("", file($tmpl_file));
	$thefile = addslashes($thefile);
    $thefile = "\$r_file=\"".$thefile."\";";
	eval($thefile);
    
    return $kod = $r_file;
}

function miserend_templomkeres() {
	global $db_name,$design_url,$linkveg,$m_id,$_POST,$_GET,$u_jogok,$u_login,$sid;

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


	$varos=$_POST['varos'];
	$kulcsszo=$_POST['kulcsszo'];
	$ehm=$_POST['ehm'];
//	$megye=$_POST['megye'];
	if(empty($_POST['espker'])) {
		$espkerpT=$_POST['espkerT'];
		$espker=$espkerpT[$ehm];
	}
	else $espker=$_POST['espker'];


	//Templom �rlap
	$templomurlap="\n<div style='display: none'><form method=post><input type=hidden name=sid value=$sid><input type=hidden name=m_id value=$m_id><input type=hidden name=m_op value=templomkeres></div>";
	$templomurlap.="\n<img src=img/space.gif width=5 height=10><br><span class=kiscim>Telep�l�s: </span><input type=text name=varos size=20 class=keresourlap value='$varos'><br><img src=img/space.gif width=5 height=8>";
	$templomurlap.="<br><span class=kiscim>Kulcssz�: </span><input type=text name=kulcsszo size=20 class=keresourlap value='$kulcsszo'><br><img src=img/space.gif width=5 height=8>";
	
	//Egyh�zmegye
	$templomurlap.="<br><span class=kiscim>Egyh�zmegye: </span><br><img src=img/space.gif width=5 height=5><br><img src=img/space.gif width=10 height=5><select name=ehm class=keresourlap onChange=\"if(this.value!=0) {";
	foreach($ehmT as $id=>$nev) {
		$templomurlap.="document.getElementById($id).style.display='none'; ";
	} 
	$templomurlap.="document.getElementById(this.value).style.display='inline'; document.getElementById('valassz').style.display='none'; } else {";
	foreach($ehmT as $id=>$nev) {
		$templomurlap.="document.getElementById($id).style.display='none'; ";
	} 
	$templomurlap.="document.getElementById('valassz').style.display='inline';}\"><option value=0>mindegy</option>";	
	foreach($ehmT as $id=>$nev) {
		$templomurlap.="<option value=$id";
		if($id==$ehm) $templomurlap.=' selected';
		$templomurlap.=">$nev</option>";
	
		if($id==$ehm) $espkerurlap.="<select id=$id name=espkerT[$id] class=keresourlap style='display: inline'><option value=0>mindegy</option>";	
		else $espkerurlap.="<select id=$id name=espkerT[$id] class=keresourlap style='display: none'><option value=0>mindegy</option>";
		if(is_array($espkerT[$id])) {	
			foreach($espkerT[$id] as $espid=>$espnev) {
				$espkerurlap.="<option value=$espid";
				if($espker==$espid) $espkerurlap.=' selected';
				$espkerurlap.=">$espnev</option>";
			}
		}
		$espkerurlap.="</select>";
	}
	$templomurlap.="</select><br><img src=img/space.gif width=5 height=8>";

	//Esperesker�let
	$templomurlap.="<br><span class=kiscim>Esperesker�let: </span><br><img src=img/space.gif width=5 height=5><br><img src=img/space.gif width=10 height=5>";
	if(empty($ehm)) $templomurlap.="<div id='valassz' style='display: inline' class=keresourlap>El�sz�r v�lassz egyh�zmegy�t.</div>";
	$templomurlap.=$espkerurlap;
	$templomurlap.="<br><img src=img/space.gif width=5 height=8>";
	
	$templomurlap.="\n<br><img src=img/space.gif width=5 height=10><div align=right><input type=submit value=keres�s class=keresourlap><br><img src=img/space.gif width=5 height=10></div><div style='display: none'></form></div>";

	if(!empty($varos)) {
		$feltetelT[]="(varos like '%$varos%' or ismertnev like '%$varos%')";
		$postdata.="<input type=hidden name=varos value='$varos'>";
	}
	if(!empty($kulcsszo)) {
		$feltetelT[]="(nev like '%$kulcsszo%' or ismertnev like '%$kulcsszo%' or cim like '%$kulcsszo%' or plebania like '%$kulcsszo%')";
		$postdata.="<input type=hidden name=kulcsszo value='$kulcsszo'>";
	}
	if(!empty($espker)) {
		$feltetelT[]="espereskerulet='$espker'";
		$postdata.="<input type=hidden name=espker value='$espker'>";
	}
	elseif(!empty($ehm)) {
		$feltetelT[]="egyhazmegye='$ehm'";
		$postdata.="<input type=hidden name=ehm value='$ehm'>";
	}

	if(is_array($feltetelT)) {
		$feltetel='and '.implode(' and ',$feltetelT);
	}

	$min=$_POST['min'];
	$leptet=$_POST['leptet'];
	if($min<0 or empty($min)) $min=0;
	if(empty($leptet)) $leptet=20;
	
	$query="select id,nev,ismertnev,varos,letrehozta from templomok where ok='i' $feltetel order by varos,nev";
	if(!$lekerdez=mysql_query($query)) echo "HIBA!<br>$query<br>".mysql_error();
	$mennyi=mysql_num_rows($lekerdez);

	$kezd=$min+1;
	$veg=$mennyi;
	if($min>0) {
		$leptetprev.="\n<form method=post><input type=hidden name=m_id value=$m_id><input type=hidden name=m_op value=templomkeres><input type=hidden name=sid value=$sid>";
		$leptetprev.=$postdata;
		$leptetprev.="<input type=hidden name=min value=$prev>";		
		$leptetprev.="\n<input type=submit value=El�z� class=urlap><input type=text size=2 value=$leptet name=leptet class=urlap></form>";
	}
	if($mennyi>$leptet) {		
		$veg=$min+$leptet;
		$prev=$min-$leptet;
		if($prev<0) $prev=0;
		$next=$min+$leptet;	

		if($mennyi>$min+$leptet) {
			$leptetnext.="\n<form method=post><input type=hidden name=m_id value=$m_id><input type=hidden name=m_op value=templomkeres><input type=hidden name=sid value=$sid><input type=hidden name=min value=$next>";
			$leptetnext.=$postdata;
			$leptetnext.="\n<input type=submit value=K�vetkez� class=urlap><input type=text size=2 value=$leptet name=leptet class=urlap></form>";
		}
	}

	$tartalom.="<br><span class=alap>�sszesen: $mennyi tal�lat<br>List�z�s: $kezd - $veg</span><br><br>";

	$query.=" limit $min,$leptet";
	$lekerdez=mysql_query($query);
	if($mennyi>0) {
		while(list($tid,$tnev,$tismertnev,$tvaros,$letrehozta)=mysql_fetch_row($lekerdez)) {
			$tartalom.="<a href=?templom=$tid$linkveg class=felsomenulink title='$tismertnev'><b>$tnev</b> <font color=#8D317C>($tvaros)</font></a>";
			if(strstr($u_jogok,'miserend')) $tartalom.=" <a href=?m_id=27&m_op=addtemplom&tid=$tid$linkveg><img src=img/edit.gif title='szerkeszt�s' align=absmiddle border=0></a> <a href=?m_id=27&m_op=addmise&tid=$tid$linkveg><img src=img/mise_edit.gif align=absmiddle border=0 title='mise m�dos�t�sa'></a>";
			elseif($letrehozta==$u_login) $tartalom.=" <a href=?m_id=29&m_op=addtemplom&tid=$tid$linkveg><img src=img/edit.gif title='szerkeszt�s' align=absmiddle border=0></a> <a href=?m_id=29&m_op=addmise&tid=$tid$linkveg><img src=img/mise_edit.gif align=absmiddle border=0 title='mise m�dos�t�sa'></a>";			
			if($tismertnev != '') $tartalom .= "<br/><span class=\"alap\" style=\"margin-left: 20px; font-style: italic;\">".$tismertnev."</span>";
			$tartalom.="<br><img src=img/space.gif width=4 height=5><br>";
		}		
		$tartalom.='<br>'.$leptetprev.$leptetnext;
	}
	else {
		$tartalom='<span class=alap>A keres�s nem hozott eredm�nyt</span>';
	}

	$focim="Keres�s a templomok k�z�tt";

	$tmpl_file = $design_url.'/miserend_talalatok.htm';

    $thefile = implode("", file($tmpl_file));
	$thefile = addslashes($thefile);
    $thefile = "\$r_file=\"".$thefile."\";";
	eval($thefile);
    
    return $kod = $r_file;
}

function miserend_misekeres() {
	global $db_name,$design_url,$linkveg,$m_id,$_POST,$_GET,$u_jogok;

	$mikor=$_POST['mikor'];
	$mikordatum=$_POST['mikordatum'];
	if($mikor!='x') $mikordatum=$mikor;
	$mikor2=$_POST['mikor2'];
	$mikorido=$_POST['mikorido'];
	$varos=$_POST['varos'];
	$ehm=$_POST['ehm'];
	$espkerT=$_POST['espkerT'];
	$nyelv=$_POST['nyelv'];
	$zene=$_POST['zene'];
	$diak=$_POST['diak'];

	$min=$_POST['min'];
	if(!isset($min)) $min=0;
	$leptet=$_POST['leptet'];
	if(!isset($leptet)) $leptet=25;

	$ma=date('Y-m-d');
	$holnap=date('Y-m-d',(time()+86400));

	if($ehm>0) {
		$query="select nev from egyhazmegye where id=$ehm and ok='i'";
		$lekerdez=mysql_query($query);
		list($ehmnev)=mysql_fetch_row($lekerdez);
	}

	if($espkerT[$ehm]>0) {
		$query="select nev from espereskerulet where id='$espkerT[$ehm]'";
		$lekerdez=mysql_query($query);
		list($espkernev)=mysql_fetch_row($lekerdez); 
	}

	$zeneT=array('g'=>'git�ros', 'o'=>'orgon�s', 'cs'=>'csendes');
	$nyelvekT=array('h'=>'magyar', 'en'=>'angol', 'de'=>'n�met', 'it'=>'olasz', 'va'=>'latin', 'gr'=>'g�r�g', 'sk'=>'szlov�k', 'hr'=>'horv�t', 'pl'=>'lengyel', 'si'=>'szlov�n', 'ro'=>'rom�n', 'fr'=>'francia', 'es'=>'spanyol');

	$tartalom.="\n<span class=kiscim>Keres�si param�terek:</span><br><span class=alap>";
	$tartalom.="<img src=$design_url/img/negyzet_lila.gif align=absmidle> ";
	if($mikordatum==$ma) {
		$tartalom.='ma';
		$leptet_urlap.="<input type=hidden name=mikor value='$ma'>";
	}
	elseif($mikordatum==$holnap) {
		$tartalom.='holnap';
		$leptet_urlap.="<input type=hidden name=mikor value='$holnap'>";
	}
	else {		
		$mev=substr($mikordatum,0,4);
		$mho=substr($mikordatum,5,2);
		$mnap=substr($mikordatum,8,2);
		$mnapnev=date('w',mktime(0,0,0,$mho,$mnap,$mev));
		$napokT=array('vas�rnap','h�tf�','kedd','szerda','cs�t�rt�k','p�ntek','szombat');
		$mikornap=' '.$napokT[$mnapnev];
		$tartalom.=$mikordatum.$mikornap;

		$leptet_urlap.="<input type=hidden name=mikor value='x'>";
		$leptet_urlap.="<input type=hidden name=mikordatum value='$mikordatum'>";
	}
	$tartalom.=' ';
	if($mikor2=='de') {
		$tartalom.='d�lel�tt,';
		$leptet_urlap.="<input type=hidden name=mikor2 value='de'>";
	}
	elseif($mikor2=='du') {
		$tartalom.='d�lut�n,';
		$leptet_urlap.="<input type=hidden name=mikor2 value='du'>";
	}
	elseif($mikor2=='x') {
		$tartalom.=$mikorido;
		$leptet_urlap.="<input type=hidden name=mikor2 value='x'>";
		$leptet_urlap.="<input type=hidden name=mikorido value='$mikorido'>";
	}
	else {
		$tartalom.='eg�sz nap,';
		$leptet_urlap.="<input type=hidden name=mikor2 value='0'>";
	}
	if(!empty($varos)) {
		$varos=ucfirst($varos);
		$tartalom.="<br><img src=$design_url/img/negyzet_lila.gif align=absmidle> $varos telep�l�sen";
		$leptet_urlap.="<input type=hidden name=varos value='$varos'>";
	}
	if(!empty($ehmnev)) {
		$tartalom.="<br><img src=$design_url/img/negyzet_lila.gif align=absmidle> $ehmnev egyh�zmegy�ben,";
		$leptet_urlap.="<input type=hidden name=ehm value='$ehm'>";
	}
	if(!empty($espkernev)) {
		$tartalom.="<br><img src=$design_url/img/negyzet_lila.gif align=absmidle> $espkernev esperesker�letben,";
		$leptet_urlap.="<input type=hidden name=espkerT[$ehm] value='$espkerT[$ehm]'>";
	}
	if(!empty($nyelv) or !empty($zene) or !empty($diak)) $tartalom.="<br><img src=$design_url/img/negyzet_lila.gif align=absmidle> ";
	if(!empty($nyelv)) {
		$tartalom.="$nyelvekT[$nyelv] nyelv�, ";
		$leptet_urlap.="<input type=hidden name=nyelv value='$nyelv'>";
	}
	if(!empty($zene)) {
		$tartalom.=$zeneT[$zene].', ';
		$leptet_urlap.="<input type=hidden name=zene value='$zene'>";
	}
	if($diak=='d') {
		$tartalom.="di�k mise,";
		$leptet_urlap.="<input type=hidden name=diak value='$diak'>";
	}
	elseif($diak=='nd') {
		$tartalom.="nem di�k mise,";
		$leptet_urlap.="<input type=hidden name=diak value='$diak'>";
	}

	$tartalom.="</span><br>";

	if(!empty($_POST['leptet'])) $visszalink="?$linkveg";
	else $visszalink="javascript:history.go(-1);";
	$templomurlap="<img src=img/space.gif width=5 height=6><br><a href=$visszalink class=link><img src=img/search.gif width=16 height=16 border=0 align=absmiddle hspace=2><b>Vissza a f�oldali keres�h�z</b></a><br><img src=img/space.gif width=5 height=6>";




	if(!empty($varos)) $feltetelT[]="(t.varos like '%$varos%')";
	if(!empty($ehm)) {
		$feltetelT[]="(t.egyhazmegye='$ehm')";
		if(!empty($espkerT[$ehm])) $feltetelT[]="(t.espereskerulet='$espkerT[$ehm]')";
	}
	
	$feltetelT[]="((t.nyariido<='$mikordatum' and t.teliido>='$mikordatum' and m.idoszamitas='ny') or ((t.nyariido>'$mikordatum' or t.teliido<'$mikordatum') and m.idoszamitas='t'))";

	//milyennap
	$ev=substr($mikordatum,0,4);
	$ho=substr($mikordatum,5,2);
	$nap=substr($mikordatum,8,2);
	$time=mktime(0,0,0,$ho,$nap,$ev);
	$milyennap=date('w',$time);
	if($milyennap==0) $milyennap=7;
	//�nnep eset�n lehet vas�rnapi mise!
	$query="select unnep,mise,miseinfo from unnepnaptar where datum='$mikordatum'";
	list($unnep,$mise,$miseinfo)=mysql_fetch_row(mysql_query($query));
	if($mise=='u') $milyennap=7;
	elseif($mise=='n') $milyennap=0;

	$feltetelT[]="(m.nap='$milyennap')";

	if($mikor2=='de') {
		$mikoridotol='0:00';
		$mikoridoig='11:59';
	}
	elseif($mikor2=='du') {
		$mikoridotol='12:00';
		$mikoridoig='23:59';
	}
	elseif($mikor2=='x') {
		$mikoridoT=explode('-',$mikorido);
		$mikoridotol=$mikoridoT[0];
		$mikoridoig=$mikoridoT[1];
	}
	if($mikor2!='0') $feltetelT[]="(m.ido>='$mikoridotol' and m.ido<='$mikoridoig')";

//A d�tum hanyadik h�tnek felel meg
	$osztas=$nap/7;
	$egesz=intval($nap/7);
	if($osztas>$egesz) $hanyadik=$egesz+1;
	else $hanyadik=$egesz;

	if(!empty($nyelv)) {
		$feltetelT[]="(m.nyelv like '%".$nyelv."0%' or m.nyelv like '%$nyelv$hanyadik%')";
	}

	if(!empty($zene)) {
		if($zene=='o') $feltetelT[]="(m.milyen not like '%g0%' and m.milyen not like 'g$hanyadik%' and m.milyen not like '%cs0%' and m.milyen not like 'cs$hanyadik%')";
		else $feltetelT[]="(m.milyen like '%".$zene."0%' or m.milyen like '%$zene$hanyadik%')";
	}
	
	if($diak=='d') {
		$feltetelT[]="(m.milyen like '%d0%' or m.milyen like '%d$hanyadik%')";
	}
	elseif($diak=='nd') {
		$feltetelT[]="(m.milyen not like '%d0%' and m.milyen not like '%d$hanyadik%')";
	}

	$feltetelT[]="t.ok='i'";
	$feltetelT[]="m.torles='0000-00-00'";

	if(is_array($feltetelT)) {
		$feltetel=implode(' and ',$feltetelT);
	}

	$query="select t.id,t.nev,t.ismertnev,t.varos,t.letrehozta, m.ido,m.nyelv,m.megjegyzes from templomok t, misek m where m.templom = t.id and $feltetel order by t.varos, t.nev, m.ido";
	if(!$lekerdez=mysql_query($query)) echo "<p>HIBA #711!<br>$query<br>".mysql_error();
	$mennyi=mysql_num_rows($lekerdez);
	$query.=" limit $min,$leptet";
	if(!$lekerdez=mysql_query($query)) echo "<p>HIBA #714!<br>$query<br>".mysql_error();
	$mostido=date('H:i');
	while(list($tid,$tnev,$tismertnev,$tvaros,$letrehozta,$mido,$mnyelv,$mmegjegyzes)=mysql_fetch_row($lekerdez)) {
		$nyelvikon='';
		if(empty($templom[$tid])) {
			$templomT[$tid]="<img src=img/templom1.gif align=absmiddle width=16 height=16 hspace=2><a href=?templom=$tid$linkveg class=felsomenulink><b>$tnev</b> <font color=#8D317C>($tvaros)</font></a><br><span class=alap style=\"margin-left: 20px; font-style: italic;\">$tismertnev</span>";
			if(strstr($u_jogok,'miserend')) $templomT[$tid].=" <a href=?m_id=27&m_op=addtemplom&tid=$tid$linkveg><img src=img/edit.gif title='szerkeszt�s' align=absmiddle border=0></a>  <a href=?m_id=27&m_op=addmise&tid=$tid$linkveg><img src=img/mise_edit.gif align=absmiddle border=0 title='mise m�dos�t�sa'></a>";
			elseif($letrehozta==$u_login) $templomT[$tid].=" <a href=?m_id=29&m_op=addtemplom&tid=$tid$linkveg><img src=img/edit.gif title='szerkeszt�s' align=absmiddle border=0></a> <a href=?m_id=29&m_op=addmise&tid=$tid$linkveg><img src=img/mise_edit.gif align=absmiddle border=0 title='mise m�dos�t�sa'></a>";
		}
		if(!empty($mmegjegyzes)) $megj="<img src=$design_url/img/info2.gif border=0 title='$mmegjegyzes' align=absmiddle width=16 height=16>";
		else $megj='';

		if(strstr($mnyelv,'de')) $nyelvikon.="<img src=img/zaszloikon/de.gif width=16 height=11 vspace=2 align=absmiddle title='n�met nyelv� mise'>";
		if(strstr($mnyelv,'it')) $nyelvikon.="<img src=img/zaszloikon/it.gif width=16 height=11 vspace=2 align=absmiddle title='olasz nyelv� mise'>";
		if(strstr($mnyelv,'en')) $nyelvikon.="<img src=img/zaszloikon/en.gif width=16 height=11 vspace=2 align=absmiddle title='angol nyelv� mise'>";
		if(strstr($mnyelv,'hr')) $nyelvikon.="<img src=img/zaszloikon/hr.gif width=16 height=11 vspace=2 align=absmiddle title='horv�t nyelv� mise'>";
		if(strstr($mnyelv,'gr')) $nyelvikon.="<img src=img/zaszloikon/gr.gif width=16 height=11 vspace=2 align=absmiddle title='g�r�g nyelv� mise'>";
		if(strstr($mnyelv,'va')) $nyelvikon.="<img src=img/zaszloikon/va.gif width=16 height=11 vspace=2 align=absmiddle title='latin nyelv� mise'>";
		if(strstr($mnyelv,'si')) $nyelvikon.="<img src=img/zaszloikon/si.gif width=16 height=11 vspace=2 align=absmiddle title='szlov�n nyelv� mise'>";
		if(strstr($mnyelv,'ro')) $nyelvikon.="<img src=img/zaszloikon/ro.gif width=16 height=11 vspace=2 align=absmiddle title='rom�n nyelv� mise'>";
		if(strstr($mnyelv,'sk')) $nyelvikon.="<img src=img/zaszloikon/sk.gif width=16 height=11 vspace=2 align=absmiddle title='szlov�k nyelv� mise'>";
		if(strstr($mnyelv,'pl')) $nyelvikon.="<img src=img/zaszloikon/pl.gif width=16 height=11 vspace=2 align=absmiddle title='lengyel nyelv� mise'>";
		if(strstr($mnyelv,'fr')) $nyelvikon.="<img src=img/zaszloikon/fr.gif width=16 height=11 vspace=2 align=absmiddle title='francia nyelv� mise'>";

		if($mido<$mostido and $mikordatum==$ma) $elmult=true;
		else $elmult=false;
		if($mido=='00:00:00') $mido='?';
		if($mido[0]=='0') $mido=substr($mido,1,4);
		else $mido=substr($mido,0,5);
		if($elmult) $mido="<font color=#555555>$mido</font>";
		else $mido="<b>$mido</b>";
		$miseT[$tid][]="<img src=img/clock.gif width=16 height=16 align=absmiddle hspace=2><span class=alap>$mido</span>$nyelvikon$megj &nbsp; ";
	}
	if($mennyi==0) {
		$tartalom.="<br>";
		if(!empty($unnep)) {
			$tartalom.="<span class=alcim_lila>$unnep</span>";
			if(!empty($miseinfo)) $tartalom.="<br><span class=kiscim_kek>$miseinfo</span>";
			$tartalom.='<br><span class=kicsi><font color=red>(Az �nnep miatt a miserend elt�rhet az itt megjelen�t�l.)</font></span><br><br>';
		}
		$tartalom.='<span class=alap>Sajnos nincs tal�lat</span>';
		//$tartalom.='<span class=alap>Eln�z�st k�r�nk, a keres� technikai hiba miatt nem �zemel. Jav�t�s�n m�r dolgozunk.</span>';
	}
	else {
		$tartalom.="<span class=kiscim>�sszesen $mennyi miseid�pont</span><br><br>";
		if(!empty($unnep)) {
			$tartalom.="<span class=alcim_lila>$unnep</span>";
			if(!empty($miseinfo)) $tartalom.="<br><span class=kiscim_kek>$miseinfo</span>";
			$tartalom.='<br><span class=kicsi><font color=red>(Az �nnep miatt a miserend elt�rhet az itt megjelen�t�l.)</font></span><br><br>';
		}
		foreach($templomT as $tid=>$ertek) {
			$tartalom.=$ertek.'<br> &nbsp; &nbsp; &nbsp;';
			foreach($miseT[$tid] as $misek) {
				$tartalom.=$misek;
			}
			$tartalom.="<br><img src=img/space.gif width=4 height=8><br>";
		}
	}

	//L�ptet�s
	if($mennyi>$min+$leptet) {
		$next=$min+$leptet;
		$leptetes="<br><form method=post><input type=hidden name=m_id value=$m_id><input type=hidden name=m_op value=misekeres>";
		$leptetes.=$leptet_urlap;
		$leptetes.="<input type=submit value=K�vetkez� class=urlap><input type=text name=leptet value=$leptet class=urlap size=2><input type=hidden name=min value=$next></form>";
	}
	$tartalom.=$leptetes;


	$focim="Szentmise keres�";

	$tmpl_file = $design_url.'/miserend_talalatok.htm';

    $thefile = implode("", file($tmpl_file));
	$thefile = addslashes($thefile);
    $thefile = "\$r_file=\"".$thefile."\";";
	eval($thefile);
    
    return $kod = $r_file;
}

function miserend_view() {
	global $TID,$linkveg,$db_name,$elso,$m_id,$m_op,$_GET,$design_url,$deisgn,$u_login,$u_jogok,$onload,$script,$sid,$titlekieg, $meta;

	$tid=$_GET['tid'];
	if(!empty($TID)) $tid=$TID;

	$query="SELECT nev,ismertnev,turistautak,varos,cim,megkozelites,plebania,pleb_url,pleb_eml,egyhazmegye,leiras,megjegyzes,misemegj,szomszedos1,szomszedos2,bucsu,nyariido,teliido,frissites,letrehozta,lat,lng,checked,eszrevetel FROM templomok 
	LEFT JOIN terkep_geocode ON terkep_geocode.tid = templomok.id 
	WHERE id='$tid' and ok='i' LIMIT 1";
	
	$lekerdez=mysql_query($query);
	$vane=mysql_num_rows($lekerdez);

	$ma=date('Y-m-d');
	list($nev,$ismertnev,$turistautak,$varos,$cim,$megkozelites,$plebania,$pleb_url,$pleb_eml,$egyhazmegye,$leiras,$megjegyzes,$misemegj,$szomszedos1,$szomszedos2,$bucsu,$nyariido,$teliido,$frissites,$letrehozta,$lat,$lng,$checked)=mysql_fetch_row($lekerdez);

	if($frissites>0) {
        $frissitve = $frissites;
		$frissites=str_replace('-','.',$frissites).'.';
		$frissites="<span class=kicsi_kek><b><u>Friss�tve:</u></b><br>$frissites</span>";
	}

	$titlekieg=" - $nev ($varos)";


	if(!empty($turistautak)) {
		$terkep="<br><a href=http://turistautak.hu/poi.php?id=$turistautak target=_blank title='Tov�bbi inf�k'><img src=http://www.geocaching.hu/images/mapcache/poi_$turistautak.gif border=0 vspace=5 hspace=5></a>";
	}

	$ev=date('Y');
	$mostido=date('H:i:s');
	$mainap=date('w');
	if($mainap==0) $mainap=7;
	$tolig=$nyariido.'!'.$teliido;
	$tolig=str_replace('-','.',$tolig);
	$tolig=str_replace("$ev.",'',$tolig);
	$tolig=str_replace('!',' - ',$tolig);
	if($ma>=$nyariido and $ma<=$teliido) {
		$nyari="<div align=center><span class=alap><b><font color=#B51A7E>ny�ri</font></b></span><br><span class=kicsi>($tolig)</span></div>";
		$teli="<div align=center><span class=alap>t�li</span></div>";
		$aktiv='ny';
	}
	else {
		$nyari="<div align=center><span class=alap>ny�ri</span><br><span class=kicsi>($tolig)</span></div>";
		$teli="<div align=center><span class=alap><b><font color=#B51A7E>t�li</font></b></span></div>";
		$aktiv='t';
	}

	//Miseid�pontok
	$query="select nap,ido,idoszamitas,nyelv,milyen,megjegyzes from misek where templom='$tid' and torles=0 order by nap,idoszamitas,ido";
	$lekerdez=mysql_query($query);
	while(list($nap,$ido,$idoszamitas,$nyelv,$milyen,$mmegjegyzes)=mysql_fetch_row($lekerdez)) {
		$idokiir=$ido;
		if($idokiir[0]=='0') $idokiir=substr($idokiir,1,4);
		else $idokiir=substr($idokiir,0,5);
		if($idokiir=='0:00') $idokiir='?';
		if($idoszamitas==$aktiv) $idokiir="<b>$idokiir</b>";
		if($nap==$mainap and $idoszamitas==$aktiv and $mostido<=$ido) $idokiir="<font color=#B51A7E>$idokiir</font>";
		if($idoszamitas=='t') $tnapokT[$nap].=$idokiir.'<br>'; //t�li
		else $napokT[$nap].=$idokiir.'<br>'; //ny�ri

		if(strstr($nyelv,'de'))  {
			if($idoszamitas=='t') $tikonT[$nap].="<img src=img/zaszloikon/de.gif width=16 height=11 vspace=2 align=absmiddle title='n�met nyelv� mise'>";
			else $ikonT[$nap].="<img src=img/zaszloikon/de.gif width=16 height=11 vspace=2 align=absmiddle title='n�met nyelv� mise'>";
		}
		if(strstr($nyelv,'it'))  {
			if($idoszamitas=='t') $tikonT[$nap].="<img src=img/zaszloikon/it.gif width=16 height=11 vspace=2 align=absmiddle title='olasz nyelv� mise'>";
			else $ikonT[$nap].="<img src=img/zaszloikon/it.gif width=16 height=11 align=absmiddle vspace=2 title='olasz nyelv� mise'>";
		}
		if(strstr($nyelv,'en')) {
			if($idoszamitas=='t') $tikonT[$nap].="<img src=img/zaszloikon/en.gif width=16 height=11 vspace=2 align=absmiddle title='angol nyelv� mise'>";
			else $ikonT[$nap].="<img src=img/zaszloikon/en.gif width=16 height=11 align=absmiddle vspace=2 title='angol nyelv� mise'>";
		}
		if(strstr($nyelv,'gr')) {
			if($idoszamitas=='t') $tikonT[$nap].="<img src=img/zaszloikon/gr.gif width=16 height=11 vspace=2 align=absmiddle title='g�r�g nyelv� mise'>";
			else $ikonT[$nap].="<img src=img/zaszloikon/gr.gif width=16 height=11 align=absmiddle vspace=2 title='g�r�g nyelv� mise'>";
		}
		if(strstr($nyelv,'va')) {
			if($idoszamitas=='t') $tikonT[$nap].="<img src=img/zaszloikon/va.gif width=16 height=11 vspace=2 align=absmiddle title='latin nyelv� mise'>";
			else $ikonT[$nap].="<img src=img/zaszloikon/va.gif width=16 height=11 align=absmiddle vspace=2 title='latin nyelv� mise'>";
		}
		if(strstr($nyelv,'ro')) {
			if($idoszamitas=='t') $tikonT[$nap].="<img src=img/zaszloikon/ro.gif width=16 height=11 vspace=2 align=absmiddle title='rom�n nyelv� mise'>";
			else $ikonT[$nap].="<img src=img/zaszloikon/ro.gif width=16 height=11 align=absmiddle vspace=2 title='rom�n nyelv� mise'>";
		}
		if(strstr($nyelv,'sk')) {
			if($idoszamitas=='t') $tikonT[$nap].="<img src=img/zaszloikon/sk.gif width=16 height=11 vspace=2 align=absmiddle title='szlov�k nyelv� mise'>";
			else $ikonT[$nap].="<img src=img/zaszloikon/sk.gif width=16 height=11 align=absmiddle vspace=2 title='szlov�k nyelv� mise'>";
		}
		if(strstr($nyelv,'si')) {
			if($idoszamitas=='t') $tikonT[$nap].="<img src=img/zaszloikon/si.gif width=16 height=11 vspace=2 align=absmiddle title='szlov�n nyelv� mise'>";
			else $ikonT[$nap].="<img src=img/zaszloikon/si.gif width=16 height=11 align=absmiddle vspace=2 title='szlov�n nyelv� mise'>";
		}
		if(strstr($nyelv,'hr')) {
			if($idoszamitas=='t') $tikonT[$nap].="<img src=img/zaszloikon/hr.gif width=16 height=11 vspace=2 align=absmiddle title='horv�t nyelv� mise'>";
			else $ikonT[$nap].="<img src=img/zaszloikon/hr.gif width=16 height=11 align=absmiddle vspace=2 title='horv�t nyelv� mise'>";
		}
		if(strstr($nyelv,'pl')) {
			if($idoszamitas=='t') $tikonT[$nap].="<img src=img/zaszloikon/pl.gif width=16 height=11 vspace=2 align=absmiddle title='lengyel nyelv� mise'>";
			else $ikonT[$nap].="<img src=img/zaszloikon/pl.gif width=16 height=11 align=absmiddle vspace=2 title='lengyel nyelv� mise'>";
		}
		if(strstr($nyelv,'fr')) {
			if($idoszamitas=='t') $tikonT[$nap].="<img src=img/zaszloikon/fr.gif width=16 height=11 vspace=2 align=absmiddle title='francia nyelv� mise'>";
			else $ikonT[$nap].="<img src=img/zaszloikon/fr.gif width=16 height=11 align=absmiddle vspace=2 title='francia nyelv� mise'>";
		}

		if(!empty($mmegjegyzes)) {
			if($idoszamitas=='t') $tikonT[$nap].="<img src=$design_url/img/info2.gif title='$mmegjegyzes' width=16 height=16 align=absmiddle>";
			else $ikonT[$nap].="<img src=$design_url/img/info2.gif title='$mmegjegyzes' width=16 height=16 align=absmiddle>";
		}

		if(!empty($milyen)) {
			if(strstr($milyen,'g')) {
				if($idoszamitas=='t') $tikonT[$nap].="<img src=$design_url/img/gitar.gif width=16 height=16 title='git�ros mise' align=absmiddle>";
				else $ikonT[$nap].="<img src=$design_url/img/gitar.gif width=16 height=16 title='git�ros mise' align=absmiddle>";
			}
			if(strstr($milyen,'d')) {
				if($idoszamitas=='t') $tikonT[$nap].="<img src=$design_url/img/diak.gif width=16 height=16 title='di�k mise' align=absmiddle>";
				else $ikonT[$nap].="<img src=$design_url/img/diak.gif width=16 height=16 title='di�k mise' align=absmiddle>";
			}
			if(strstr($milyen,'cs')) {
				if($idoszamitas=='t') $tikonT[$nap].="<img src=$design_url/img/csendes.gif width=16 title='csendes mise' height=16 align=absmiddle>";
				else $ikonT[$nap].="<img src=$design_url/img/csendes.gif width=16 height=16 title='csendes mise' align=absmiddle>";
			}
		}
		if($idoszamitas=='ny') $ikonT[$nap].='<br>';
		else $tikonT[$nap].='<br>';
	}

	if(strstr($u_jogok,'miserend')) {
		$nev.=" <a href=?m_id=27&m_op=addtemplom&tid=$tid$linkveg><img src=img/edit.gif align=absmiddle border=0 title='Szerkeszt�s/m�dos�t�s'></a> <a href=?m_id=27&m_op=addmise&tid=$tid$linkveg><img src=img/mise_edit.gif align=absmiddle border=0 title='mise m�dos�t�sa'></a>";
	
		$query="select allapot from eszrevetelek where hol = 'templomok' AND hol_id = '".$tid."' GROUP BY allapot ORDER BY allapot limit 5;";
		$result=mysql_query($query);
		$allapotok = array();
		while ($row = mysql_fetch_assoc($result)) { if($row['allapot']) $allapotok[] = $row['allapot'];}
		if(in_array('u',$allapotok)) $nev.=" <a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomag.gif title='�j �szrev�telt �rtak hozz�!' align=absmiddle border=0></a> ";		
		elseif(in_array('f',$allapotok)) $nev.=" <a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomagf.gif title='�szrev�tel jav�t�sa folyamatban!' align=absmiddle border=0></a> ";	
		elseif(count($allapotok)>0) $nev.=" <a href=\"javascript:OpenScrollWindow('naplo.php?kod=templomok&id=$tid&sid=$sid',550,500);\"><img src=img/csomag1.gif title='�szrev�telek!' align=absmiddle border=0></a> ";		
	
	
	}
	elseif($u_login==$letrehozta) {
		$nev.=" <a href=?m_id=29&m_op=addtemplom&tid=$tid$linkveg><img src=img/edit.gif align=absmiddle border=0 title='Szerkeszt�s/m�dos�t�s'></a> <a href=?m_id=29&m_op=addmise&tid=$tid$linkveg><img src=img/mise_edit.gif align=absmiddle border=0 title='mise m�dos�t�sa'></a>";
	}

	if(!empty($ismertnev)) $ismertnev="<span class=alap><i><b>K�zismert nev�n:</b></i><br></span><span class=dobozfocim_fekete><b><font color=#AC007A>$ismertnev</font></b></span><br><img src=img/space.gif width=5 height=7><br>";
	$cim="<span class=alap><i>C�m:</i> <u>$varos, $cim</u></span>";
	
	if($checked > 0) 
		$cim .= "<br/><span class=alap><i>T�rk�pen:</i> <u><a href=\"http://terkep.miserend.hu/?templom=$tid\">$lat; $lng</a></u></span>";
	else
		$cim .= "<br/><span class=alap><u><a href=\"http://terkep.miserend.hu/?templom=$tid\">Seg�ts megtal�lni a t�rk�pen!</a></u></span>";
	
	$kapcsolat=nl2br($plebania);
	if(!empty($pleb_url)) $kapcsolat.="<br/><div style=\"width: 230px;white-space: nowrap;overflow: hidden;o-text-overflow: ellipsis;text-overflow: ellipsis;\">Weboldal: <a href=$pleb_url target=_blank class=link title='$pleb_url'  onclick=\"ga('send','event','Outgoing Links','click','".$pleb_url."');\">".preg_replace("/http:\/\//","",$pleb_url)."</a></div>";
	if(!empty($pleb_eml)) $kapcsolat.="<div style=\"width: 230px;white-space: nowrap;overflow: hidden;o-text-overflow: ellipsis;text-overflow: ellipsis;\">Email: <a href='mailto:$pleb_eml' class=link>$pleb_eml</a></div>";

	if(!empty($megkozelites)) {
		$megkozelit='<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr bgcolor="#EAEDF1">'; 
		$megkozelit.='<td bgcolor="#EAEDF1" width="5"><img src="img/space.gif" width="5" height="5"></td>';
		$megkozelit.='<td><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td><img src="'.$design_url.'/img/negyzet_lila.gif" width="6" height="8" align="absmiddle"><img src="img/space.gif" width="5" height="5"><span class="dobozcim_kek">Megk�zel�t�s</span></td><td>';
		$megkozelit.='<div align="right"><img src="'.$design_url.'/img/lilapontok_kek.jpg" width="43" height="6"></div></td></tr></table>';			
		$megkozelit.='</td><td width="5"><img src="img/space.gif" width="5" height="5"></td></tr><tr bgcolor="#F8F4F6"><td width="5"></td><td class="alap">';
		$megkozelit.=nl2br($megkozelites);
		$megkozelit.='</td><td width="5"></td></tr></table><img src=img/space.gif width=5 height=10>';
	}
	$eszrevetel='<img src=img/space.gif width=5 height=10><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr bgcolor="#EAEDF1">'; 
	$eszrevetel.='<td bgcolor="#EAEDF1" width="5"><img src="img/space.gif" width="5" height="5"></td>';
	$eszrevetel.='<td><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td><!--<img src="'.$design_url.'/img/negyzet_lila.gif" width="6" height="8" align="absmiddle">--><img src=img/alert.gif align=top width=16 height=15><img src="img/space.gif" width="5" height="5"><span class="dobozcim_kek">�szrev�telek, kieg�sz�t�s</span></td><td>';
	$eszrevetel.='<div align="right"><img src="'.$design_url.'/img/lilapontok_kek.jpg" width="43" height="6"></div></td></tr></table>';			
	$eszrevetel.='</td><td width="5"><img src="img/space.gif" width="5" height="5"></td></tr><tr bgcolor="#F8F4F6"><td width="5"></td><td>';
	$eszrevetel.="<p class=alapkizart>Amennyiben a templommal, adataival, vagy a miserenddel kapcsolatosan �szrev�teled van, k�r�nk �rd meg nek�nk! <b><i>H�l�san k�sz�nj�k a seg�ts�ged!</i></b><br><div align=center><a href=\"javascript:OpenNewWindow('eszrevetel.php?sid=$sid&id=$tid&kod=templomok',450,530);\" class=link><font color=#8D317C><b>�szrev�telek bek�ld�se</b></font></a></div>";
	$eszrevetel.='</td><td width="5"></td></tr></table>';

	//AndroidReklam
	$androidreklam = androidreklam();
	
	if(!empty($szomszedos1)) {
		/*$szomszedos1=str_replace('--','!',$szomszedos1);
		$szomszedos1=str_replace('-','',$szomszedos1);*/
		$szomszedos1T=explode(',',$szomszedos1);
		foreach($szomszedos1T as $idk) {			
			$feltetelT[]="id='$idk'";
		}
		if(is_array($feltetelT)) {
			$feltetel=implode(' or ',$feltetelT);
			$query="select id,nev,ismertnev,varos from templomok where ($feltetel) and ok='i' order by varos";
			$lekerdez=mysql_query($query);
			while(list($szid,$sznev,$szismertnev,$szvaros)=mysql_fetch_row($lekerdez)) {
				$sz1.="<li class=link><a href=?templom=$szid$linkveg class=link title='$szismertnev' onclick=\"ga('send','event','Inbound Links','Szomszedsag','?templom=".$szid.$linkveg."')\">$sznev ($szvaros)</a></li>";
			}
		}
	}
	else $sz1="<span class=link>-</span><br>";
	
	if(!empty($szomszedos2)) {
		/*$szomszedos2=str_replace('--','!',$szomszedos2);
		$szomszedos2=str_replace('-','',$szomszedos2);*/
		$szomszedos2T=explode(',',$szomszedos2);
		foreach($szomszedos2T as $idk2) {
			if(is_array($szomszedos1T)) {
				if(!in_array($idk2,$szomszedos1T)) $feltetel2T[]="id='$idk2'";
			}
		}
		if(is_array($feltetel2T)) {
			$sz2 = "<ul style='-webkit-padding-start: 20px;-webkit-margin-before: 0em;'>";
			$feltetel2=implode(' or ',$feltetel2T);
			$query="select id,nev,ismertnev,varos from templomok where ($feltetel2) and ok='i' order by varos";
			$lekerdez=mysql_query($query);
			$c=0;
			while(list($szid,$sznev,$szismertnev,$szvaros)=mysql_fetch_row($lekerdez)) {
				$sz2.="<li class=link><a href=?templom=$szid$linkveg class=link title='$szismertnev'  onclick=\"ga('send','event','Inbound Links','Szomszedsag','?templom=".$szid.$linkveg."')\">$sznev ($szvaros)</a></li>";
				if($c>4) { $sz2.= "<li style='display:inline'>...</li>" ; break; } $c++; 
			}
			$sz2 .= "</ul>";
		}
	}
	else $sz2="<span class=link>-</span><br>";

	////////////////////////
	//$sz1='<span class=kicsi>a szolg�ltat�s �tmenetileg sz�netel</span>';
	//$sz2='<span class=kicsi>a szolg�ltat�s �tmenetileg sz�netel</span>';
	
	$marcsak = (int) ((strtotime('2014-03-20') - time())/  ( 60 * 60 * 24 ));
	//$sz1="<span class=\"kicsi\"><a href=\"http://terkep.miserend.hu\" target=\"_blank\">M�r csak ".$marcsak." nap �s itt a t�rk�p.</a></span>";
	//$sz2= $sz1;
	////////////////////////

	$bucsu=nl2br($bucsu);

	if(!empty($misemegj)) {
		$dobozcim='Kapcsol�d� inform�ci�k';
		$dobozszoveg=nl2br($misemegj);
		$align='';
		$width='';

		$tmpl_file = $design_url.'/liladoboz.htm';

	    $thefile = implode("", file($tmpl_file));
		$thefile = addslashes($thefile);
	    $thefile = "\$r_file=\"".$thefile."\";";
		eval($thefile);
    
		$misemegjegyzes = $r_file;	
	}

	if(!empty($megjegyzes)) {
		$dobozcim='J� tudni...';
		$dobozszoveg=nl2br($megjegyzes);
		$align="align=right";
		$width="width=50%";

		$tmpl_file = $design_url.'/liladoboz.htm';

	    $thefile = implode("", file($tmpl_file));
		$thefile = addslashes($thefile);
	    $thefile = "\$r_file=\"".$thefile."\";";		
		eval($thefile);
    
		$jotudni = $r_file;	
	}

	//k�pek	
	$query="select fajlnev,felirat from kepek where kat='templomok' and kid='$tid' order by sorszam";
	$lekerdez=mysql_query($query);
	$mennyi=mysql_num_rows($lekerdez);
	if($mennyi>0) {		
		$kepek.="\n<img src=$design_url/img/negyzet_kek.gif align=absmiddle><img src=img/space.gif width=5 height=5><span class=dobozcim_fekete>K�pek a templomr�l</span><br><table width=100% cellpadding=0 cellspacing=0 bgcolor=#EAEDF1><tr>";
		$konyvtar="kepek/templomok/$tid";
		while(list($fajlnev,$kepcim)=mysql_fetch_row($lekerdez)) {
			$altT[$fajlnev]=$kepcim;
			if(!isset($ogimage)) $ogimage = '<meta property="og:image" content="'.$konyvtar."/".$fajlnev.'">';
			@$info=getimagesize("$konyvtar/$fajlnev");
			$w=$info[0];
			$h=$info[1];
			if($w>800 or $h>600) {
				$w=800; 
				$h=600;
				$window='Scroll';
			}
			else $window='New';
			@$info=getimagesize("$konyvtar/kicsi/$fajlnev");
			$w1=$info[0];
			$h1=$info[1];
			if($h1>$w1 and $h1>90) {
				$arany=90/$h1;
				$ujh=90;
				$ujw=$w1*$arany;
			}
			else {
				$ujh=$h1;
				$ujw=$w1;
			}
			$osszw=$osszw+$ujw;
			$title=rawurlencode($kepcim);			
			$kepT[]="<a href=\"javascript:Open".$window."Window('view.php?kep=$konyvtar/$fajlnev&title=$title',$w,$h);\"><img src=$konyvtar/kicsi/$fajlnev title='$kepcim' border=0 width=$ujw height=$ujh></a>";
			$kepscriptT.="\nArticle[i] = new Array (\"$konyvtar/kicsi/$fajlnev\", \"javascript:Open".$window."Window('view.php?kep=$konyvtar/$fajlnev&title=$title',$w,$h);\", \"$kepcim\");i++  ";
		}
	
		if($osszw>480) {
			$onload="loadScroller();";
			$script.="<script type=\"text/javascript\" language=\"JavaScript\">
				<!--                                      
				Article = new Array;
				i=0;";
			$script.=$kepscriptT;
			$script.="\n--></script>";

			$script.="\n<script type=\"text/javascript\" src=\"$design_url/scroll.js\"></script>";
			$kepek.="\n<td width=460><div>";
			$kepek.="\n<script type=\"text/javascript\" language=\"JavaScript\">buildScroller();</script>";
			$kepek.="\n</div>";
			$kepek.="</td><td width=20 bgcolor=#244C8F><a href=\"#\" onmouseover=\"javascript:moveLayer();\" class=dobozcim_feher><img src=$design_url/img/fehernyil_jobb.jpg border=0 align=right></a></td>";
		}
		else {
			$kepek.='<td>'.implode("<img src=img/space.gif width=5 height=7>",$kepT).'</td>';
		}
		$kepek.="</tr></table>";

		if(isset($ogimage)) $meta .= $ogimage."\n";
		
	}

    
    //Seg�ts a friss�t�sben!
    if(strtotime($frissitve) < strtotime("-3 year")) { 
        session_start();
        if(!isset($_SESSION['help_'.$tid])) {
            $new = true;
            $_SESSION['help_'.$tid] = time();            
        } else $new = false;
        $help = '
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="jscripts2/colorbox-master/jquery.colorbox.js"></script>
        <link rel="stylesheet" href="'.$design_url.'/colorbox.css" />
        <script>
			$(document).ready(function(){
                $.colorbox.settings.close = \'Bez�r\';
                ';
        if($new == true) $help .= '$.colorbox({inline:true, href:"#inline_content"});';
        $help .= '
				//Examples of how to assign the Colorbox event to elements
				$(".ajax").colorbox();
				$(".inline").colorbox({inline:true, width:"50%"});
			});
		</script>';
        
     
     
     $help .= '<!-- This contains the hidden content for inline calls -->
		<div style=\'display:none\'>
			<div id=\'inline_content\' style=\'padding:10px; background:#fff;\'>
                <div class="focim_fekete block" style="background-color: #D0D6E4;width:100%;margin-bottom:5px">
                    <img src="'.$design_url.'/img/negyzet_lila.gif" width="6" height="8" align="absmiddle" style="margin-right:5px;margin-left:10px;">                 
                    <span class="dobozfocim_fekete">Elavult adat!</span>
                    <div class="focim_fekete" style="float:right;margin-right:10px;height:7px;">&nbsp;
                        <img src="'.$design_url.'/img/lilacsik.jpg" width="170" height="7" align="absmiddle">
                    </div>
                </div>	
			<p class="alap">Sajnos a <strong>'.$nev.' ('.$varos.')</strong> adatai m�r r�gen voltak friss�tve ('.date('Y.m.d.',strtotime($frissitve)).'). Ez�rt k�nnyen lehet, hogy hib�s m�r a miserend.</p>';
      
      $results2 = mysql_query("SELECT * FROM eszrevetelek WHERE hol = 'templomok' AND hol_id = ".$tid." AND ( allapot = 'u' OR allapot = 'f' ) ORDER BY datum DESC, allapot DESC LIMIT 1 ;");       
      if(mysql_num_rows($results2)>0) {
           $eszre = mysql_fetch_assoc($results2);
           $help .= '<p class="alap"><strong>Nagy �r�m�nkre m�r volt olyan l�togat�nk, aki ut�na n�zett az adatoknak. �ppen most dolgozzuk fel a bek�ld�tt �szrev�telt.</strong></p>';
       } else {
            $help .= '<p class="alap" align="center"><strong>K�r�nk, seg�ts a t�bbieknek azzal, hogy megk�ld�d nek�nk a friss miserendet, ha siker�lt ut�naj�rni!</strong></p>			
            <div style="background-color:#F8F4F6;margin-bottom:5px;width:100%">'.$kapcsolat.'</div>';
           }
      $help .= ' '.$eszrevetel.'			
			</div>
		</div>';
        
        $eszrevetel .= '<p><a class=\'inline\' href="#inline_content">Seg�ts friss�teni!</a></p>';
       }
       else $help = '';
    
	if($vane>0) {
		$tmpl_file = $design_url.'/templom.htm';

	    $thefile = implode("", file($tmpl_file));
		$thefile = addslashes($thefile);
	    $thefile = "\$r_file=\"".$thefile."\";";
		eval($thefile);
    
	    return $kod = $help.$r_file;
	}
	else {

		$kod="<span class=hiba>A keresett templom nem tal�lhat�.</span>";
	
		return $kod;
	}
}

function androidreklam() {

	$dobozcim='M�r androidra is';
	//$dobozszoveg=nl2br($misemegj);
	$dobozszoveg = "<a href=\"https://play.google.com/store/apps/details?id=com.frama.miserend.hu\" onclick=\"ga('send','event','Advertisment','play.store','liladoboz-kep')\"><img src=\"http://terkep.miserend.hu/images/device-2014-03-24-230146_framed.png\" height=\"180\" style=\"float:right\"></a>Megjelent a <a href=\"https://play.google.com/store/apps/details?id=com.frama.miserend.hu\" onclick=\"ga('send','event','Advertisment','play.store','liladoboz')\">miserend androidos mobiltelefonokra</a> k�sz�lt v�ltozata is. �m m�g meg kell tal�lni n�h�ny templomnak a pontos hely�t a t�rk�pen. K�rem seg�tsen nek�nk!<br/><center><a href=\"http://terkep.miserend.hu\" onclick=\"ga('send','event','Advertisment','terkep.miserend.hu','liladoboz')\">terkep.miserend.hu</a></center>";
	
	$dobozszoveg = "<a href=\"https://play.google.com/store/apps/details?id=com.frama.miserend.hu\">
  <img alt=\"T�ltd le a Google Play-r�l\" src=\"img/hu_generic_rgb_wo_60.png\" /></a>";
	$align='';
	$width='';

	global $design_url;
	$tmpl_file = $design_url.'/liladoboz.htm';

	$thefile = implode("", file($tmpl_file));
	$thefile = addslashes($thefile);
	$thefile = "\$r_file=\"".$thefile."\";";
	eval($thefile);
    
	return $dobozszoveg; //$r_file;	
}

function miserend_getRegi() {
    $return = array();
    $results = mysql_query('SELECT templomok.id, templomok.varos, templomok.nev, templomok.ismertnev, frissites, egyhazmegye, egyhazmegye.nev as egyhazmegyenev FROM templomok LEFT JOIN egyhazmegye ON egyhazmegye.id = egyhazmegye WHERE templomok.ok = "i" AND templomok.nev LIKE \'%templom%\' ORDER BY frissites ASC LIMIT 100');
    while ($templom = mysql_fetch_assoc($results)) {
        $results2 = mysql_query("SELECT * FROM eszrevetelek WHERE hol = 'templomok' AND hol_id = ".$templom['id']." AND ( allapot = 'u' OR allapot = 'f' ) ORDER BY datum DESC, allapot DESC LIMIT 1 ;");       
        //while ($eszrevetel = mysql_fetch_assoc($results2)) { print_R($eszrevetel); }
        if(mysql_num_rows($results2)>0) {
            $eszrevetel = mysql_fetch_assoc($results2);
            $templom['eszrevetel'] = $eszrevetel;
        }
        $return[] = $templom;
    }
    return $return;
}
function miserend_printRegi() {
    $templomok = miserend_getRegi();

    $return = '<img src="design/miserend/img/negyzet_kek.gif" align="absmiddle" style="margin-right:5px"><span class="dobozcim_fekete">Legr�gebben friss�tett templomaink</span><br/>';
    $return .= "<span class=\"alap\">Seg�ts nek�nk az adatok frissen tart�s�ban! H�vj fel egy r�gen friss�lt templomot!</span><br/><br/>";
    $c = 0;
    foreach($templomok as $templom) {
        if(isset($templom['eszrevetel'])) {
            $return .= "<span class=\"alap\"><i>folyamatban: ".$templom['nev']." (".$templom['varos'].")</i></span><br/>\n";
        } else {
            $return .= "<span class=\"alap\">".date('Y.m.d.',strtotime($templom['frissites']))."</span> <a class=\"felsomenulink\" href=\"?templom=".$templom['id']."\">".$templom['nev']." (".$templom['varos'].")</a><br/>\n";
        }
        //echo print_R($templom,1)."<br>";
	
        if($c>10) break;    
        $c++;
    }


    return $return;
    
}
	
switch($m_op) {
    case 'index':
        $tartalom=miserend_index();
        break;

	case 'templomkeres':
		$tartalom=miserend_templomkeres();
		break;

	case 'misekeres':
		$tartalom=miserend_misekeres();
		break;

	case 'view':
		$tartalom=miserend_view();
		break;

}

?>
