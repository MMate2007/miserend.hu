<?php

function nevnaptar_index($honap,$ev) {
	global $linkveg,$db_name,$m_id,$m_op,$_GET,$_POST,$design_url,$design;

	if(!isset($desin)) $design='alap';

    define("EGYNAP", (60*60*24));
	$szoveg='';
	$imgDIR='img';

    if(!checkdate($honap,1,$ev)) {
        $mostTomb=getdate();
        $honap=$mostTomb["mon"];
        $ev=$mostTomb["year"];
    }

    $kezdet=mktime(0,0,0,$honap,1,$ev);
    $elsoNapTombje=getdate($kezdet);
    if($elsoNapTombje["wday"] == 0)
       $elsoNapTombje["wday"] = 6;
    else
       $elsoNapTombje["wday"]--;

    $szoveg.= '<div align=left><form method=post>';
    $szoveg.= "<input type=hidden name=m value=$m><input type=hidden name=fm value=$fm><input type=hidden name=op value=naptar>";
    $szoveg.= '<select name=ev class=urlap>';
	$max=date('Y')+2;
    for($x=2005;$x<=$max; $x++) {
        $szoveg.= "<option";
        $szoveg.= ($x == $ev) ? " selected":"";
        $szoveg.= ">$x\n";
    }
    $szoveg.= '</select><select name=honap class=urlap>';

      $honapok=Array ("Janu�r","Febru�r","M�rcius","�prilis","M�jus","J�nius","J�lius",
               "Augusztus","Szeptember","Okt�ber","November","December");

    for($x=1;$x<=count($honapok); $x++) {
        $szoveg.= "\t<option value=$x";
        $szoveg.= ($x == $honap)? " selected":"";
        $szoveg.= '>'.$honapok[$x-1]."\n";
    }
    $szoveg.= "</select><input type=hidden name=lang value=$lang>
         <input type=submit value=Mutat class=urlap></form></div>";

	$szoveg.="<form method=post><input type=hidden name=sessid value=$sessid><input type=hidden name=m_op value=adding><input type=hidden name=m_id value=$m_id><input type=hidden name=honap value=$honap><input type=hidden name=ev value=$ev>";

    $napok = Array ("H�tf�","Kedd","Szerda","Cs�t�rt�k","P�ntek","Szombat","Vas�rnap");

    $szoveg.= '<table width=100% border=0 cellspacing=1 cellpadding=0 align=center bgcolor=#0D4081>
          <tr bgcolor=#E5B1A3 valign=middle align=center>
          <td colspan=2 height=30 bgcolor=#FFFAE4>';
    $szoveg.= '<div align=center class=alcim>'.$ev.'. '.($honapok[$honap-1]).'</div>';
    $szoveg.= '</td></tr>';
    $kiirando=$kezdet;
    $napszamlalo=0;

	//�nnepek adatb�zisb�l
	$kezd=date('Y-m-d',$kezdet);
	if(strlen($honap)==1) $honapkiir='0'.$honap;
	else $honapkiir=$honap;
	$veg="$ev-$honapkiir-31";
	$query="select datum,unnep,szabadnap,mise,miseinfo from unnepnaptar where datum>='$kezd' and datum<='$veg'";
	if(!$lekerdez=mysql_db_query($db_name,$query)) echo "HIBA!<br>$query<br>".mysql_error();
	while(list($datum,$unnep,$szabadnap,$mise,$miseinfo)=mysql_fetch_row($lekerdez)) {
		$unnep_ho=substr($datum,5,2);
		$unnep_nap=substr($datum,8,2);
		if($unnep_nap[0]=='0') $unnep_nap=$unnep_nap[1];
		$unnepdatumT[$unnep_nap]=$unnep;
		$szabadnapT[$unnep_nap]=$szabadnap;
		$miseT[$unnep_nap]=$mise;
		$miseinfoT[$unnep_nap]=$miseinfo;
	}

	//n�vnapok adatb�zisb�l 
	$kezd=substr($kezd,5);
	$kezd=str_replace('-','',$kezd);
	$veg=substr($veg,5);
	$veg=str_replace('-','',$veg);
	$query="select datum,nevnap from nevnaptar where datum>='$kezd' and datum<='$veg'";
	if(!$lekerdez=mysql_db_query($db_name,$query)) echo "HIBA!<br>$query<br>".mysql_error();
	while(list($datum,$nevnap)=mysql_fetch_row($lekerdez)) {
		$nevnap_ho=substr($datum,0,2);
		$nevnap_nap=substr($datum,2,2);
		if($nevnap_nap[0]=='0') $nevnap_nap=$nevnap_nap[1];
		$nevnapdatumT[$nevnap_nap]=$nevnap;
	}

    for($szamlalo=0;$szamlalo<42;$szamlalo++) {
        if($napszamlalo>6) $napszamlalo=0;
        $napTomb = getdate($kiirando);

        if(($szamlalo%7)==6) $unnep=1;
        else $unnep=0;

        //Ha m�r �j h�nap j�nne, akkor megszak�tjuk!
        if($napTomb[mon]!=$honap) break;
        
        //�nnepek:
        if($napTomb[mon]==1 and $napTomb["mday"]==1) {$unnep=1;$msg='�j�v';}
        elseif($napTomb[mon]==3 and $napTomb["mday"]==15) {$unnep=1;$msg='Nemzeti �nnep';}
		elseif($napTomb[mon]==5 and $napTomb["mday"]==1) {$unnep=1;$msg='Munka �nnepe';}
        elseif($napTomb[mon]==8 and $napTomb["mday"]==20) {$unnep=1;$msg='Nemzeti �nnep';}
        elseif($napTomb[mon]==10 and $napTomb["mday"]==23) {$unnep=1;$msg='Nemzeti �nnep';}
        elseif($napTomb[mon]==11 and $napTomb["mday"]==1) {$unnep=1;$msg='Mindenszentek �nnepe';}
        elseif($napTomb[mon]==12 and $napTomb["mday"]==25) {$unnep=1;$msg='Kar�csony';}
        elseif($napTomb[mon]==12 and $napTomb["mday"]==26) {$unnep=1;$msg='Kar�csony';}
        else $msg='';

		//mozg� �nnepek (adatb�zisb�l)
		$melyiknap=$napTomb["mday"]; //El�g csak a nap, az adatb�zisban CSAK ebben a h�napban keres.
		if(!empty($unnepdatumT[$melyiknap])) { //ha ezen a napon tal�lt �nnepet
			if($szabadnapT[$melyiknap]=='i') $unnep=1; //Csak, ha szabadnap is, akkor jel�lj�k a napt�rban
			$msg=$unnepdatumT[$melyiknap];
		}


        if($szamlalo<$elsoNapTombje["wday"] || $napTomb["mon"] != $honap) {
            $napszamlalo++;
        }
        else {
            $szoveg.= "\t<tr><td width=25%";
            $datum=$ev.'-'.$honap.'-'.$napTomb['mday'];

            //Ha �nnep, akkor piros
            $szoveg.= $unnep==1 ? " bgcolor=#FFFAE4><a class=unnep title='$msg'":" bgcolor=#ECE5C8><a class=alap";
            $szoveg.= "><img src=$imgDIR/space.gif width=25% height=1 border=0>".$napTomb["mday"].'. ';
            
            $szoveg.= $napok[$napszamlalo].'</a></td><td valign=top width=75% ';
            $szoveg.= $unnep==1 ? " bgcolor=#FFFAE4>":" bgcolor=#FFFFFF>";

            //Megn�zz�k, hogy van-e hozz� esem�ny
			$napunk=$napTomb['mday'];
			if(strlen($napunk)==1) $napunk='0'.$napunk;
			$datum=date('Y-m-',$kezdet).$napunk;

            $szoveg.= "\n<span class=alap>N�vnap: </span><input type=text name=nevnap[$melyiknap] value='$nevnapdatumT[$melyiknap]' class=urlap size=40><br><span class=alap>�nnep: </span><input type=text name=unnepnap[$melyiknap] value='$unnepdatumT[$melyiknap]' class=urlap size=40><input type=checkbox name=szabadnap[$melyiknap] value='i' class=urlap";
			if($szabadnapT[$melyiknap]=='i') $szoveg.=' checked';
			$szoveg.="><span class=alap>szabadnap</span><br><span class=alap>Miseinfo: </span><input type=text name=miseinfo[$melyiknap] value='$miseinfoT[$melyiknap]' class=urlap size=40><select name=mise[$melyiknap] class=urlap><option value='v'";
			if($miseT[$melyiknap]=='v') $szoveg.=' selected';
			$szoveg.=">napi miserend</option><option value='u'";
			if($miseT[$melyiknap]=='u') $szoveg.=' selected';
			$szoveg.=">vas�rnapi miserend</option><option value='n'";
			if($miseT[$melyiknap]=='n') $szoveg.=' selected';
			$szoveg.=">nincs mise</option></select>";
			if(!empty($nevnapdatumT[$melyiknap])) $szoveg.="<input type=hidden name='vannevnap[$melyiknap]' value=1>";
			if(!empty($unnepdatumT[$melyiknap])) $szoveg.="<input type=hidden name='vanunnep[$melyiknap]' value=1>";

            $kiirando += EGYNAP;
            $napszamlalo++;
            
            //Ellen�rizz�k, hogy az �ra�ll�t�sokn�l ne legyen k�tszer ugyanaz a nap!
            $ujnaptomb=getdate($kiirando);
            if($ujnaptomb[mday]==$napTomb[mday]) $kiirando += EGYNAP;

			$szoveg.= $unnep==1 ? ' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <input type=submit value=Mehet class=urlap>':'';
            
			$szoveg.="</td></tr>\n";
        }
    }
    $szoveg.='</table><br></form>';

	$kovhonap=$honap+1;
	$kovev=$ev;
	if($kovhonap>12) {
		$kovhonap=1;
		$kovev=$ev+1;
	}

	$szoveg.="<div align=left><a href=?m_id=$m_id&ev=$kovev&honap=$kovhonap$linkveg class=link>K�vetkez� h�nap</a></div";
	
	$tartalom=$szoveg;


	$adatT[2]="<span class=alcim>Esem�nynapt�r</span><br><br>".$tartalom;
	$tipus='doboz';
	$kod.=formazo($adatT,$tipus);	

    return $kod;
}


function nevnaptar_adding() {
	global $_POST,$db_name;

	$nevnapT=$_POST['nevnap'];
	$szabadnapT=$_POST['szabadnap'];
	$unnepnapT=$_POST['unnepnap'];
	$vannevnapT=$_POST['vannevnap'];
	$vanunnepT=$_POST['vanunnep'];
	$miseT=$_POST['mise'];
	$miseinfoT=$_POST['miseinfo'];
	$honap=$_POST['honap'];
	if(strlen($honap)==1) $honap='0'.$honap;
	$ev=$_POST['ev'];

	foreach($nevnapT as $mikor=>$mi) {
		if(!empty($mi)) {
			$van=$vannevnapT[$mikor];
			if(strlen($mikor)==1) $mikor='0'.$mikor;
			if($van) {
				$parancs1="update nevnaptar set";
				$parancs2="where datum='$honap$mikor'";
			}
			else {
				$parancs1="insert nevnaptar set datum='$honap$mikor',";
				$parancs2='';
			}			
			$query="$parancs1 nevnap='$mi' $parancs2";
			if(!mysql_db_query($db_name,$query)) echo "HIBA!<br>$query<br>".mysql_error();
		}
	}
	foreach($unnepnapT as $mikor=>$mi) {
		if(!empty($mi)) {
			$van=$vanunnepT[$mikor];
			$szabadnap=$szabadnapT[$mikor];
			$mise=$miseT[$mikor];
			$miseinfo=$miseinfoT[$mikor];
			if($szabadnap!='i') $szabadnap='n';
			if(strlen($mikor)==1) $mikor='0'.$mikor;
			$datum="$ev-$honap-$mikor";
			if($van) {
				$parancs1="update unnepnaptar set";
				$parancs2="where datum='$datum'";
			}
			else {
				$parancs1="insert unnepnaptar set datum='$datum',";
				$parancs2='';
			}			
			$query="$parancs1 unnep='$mi', szabadnap='$szabadnap', mise='$mise', miseinfo='$miseinfo' $parancs2";
			if(!mysql_db_query($db_name,$query)) echo "HIBA!<br>$query<br>".mysql_error();
		}
	}

	$kod=nevnaptar_index($honap,$ev);

	return $kod;
}


if(strstr($u_jogok,'nevnaptar')) {
	//Csak, ha van jogosults�ga!

switch($m_op) {
    case 'index':
		$honap=$_POST['honap'];
		if(!isset($honap)) $honap=$_GET['honap'];
		$ev=$_POST['ev'];
		if(!isset($ev)) $ev=$_GET['ev'];
        $tartalom=nevnaptar_index($honap,$ev);
        break;

    case 'adding':
        $tartalom=nevnaptar_adding();
        break;

}
}
else {
	$tartalom='<span class=hiba>HIBA! Nincs hozz� jogosults�god!</span>';
}

?>
