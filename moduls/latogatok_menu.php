<?

function latogatok_balmenu() {
    global $u_id,$u_login,$u_jogok,$db_name,$fooldal_id;
/*
    $most=time();
    $limit=2400; //40 perc
    $lejart=$most-$limit;

//Lek�rdezz�k a session adatokat
    $query="select uid,login,becenev,nem,szulinap,nyilvanos,baratok,ismerosok,modul_id,host,ip from session where lastlogin>'$lejart' order by lastlogin desc";
    if(!$lekerdez=mysql_query($query)) echo 'HIBA!<br>'.mysql_error();

    while(list($uid,$ulogin,$ubecenev,$unem,$uszulinap,$unyilvanos,$ubaratok,$uismerosok,$umodul_id,$uhost,$uip)=mysql_fetch_row($lekerdez)) {
		
		//Adatok nyilv�noss�g�nak be�ll�t�si �rt�kei
		$nyilvanosT=explode('*',$unyilvanos);
		if(is_array($nyilvanosT)) {
			foreach($nyilvanosT as $ertek) {
				$ertekT=explode('-',$ertek);
				if($ertekT[1]=='0') $unyilvanosT[$ertekT[0]]='ok';
				elseif($ertekT[1]=='b' and strstr($ubaratok,"-$u_id-")) $unyilvanosT[$ertekT[0]]='ok';
				elseif($ertekT[1]=='i' and (strstr($uismerosok,"-$u_id-") or strstr($ubaratok,"-$u_id-"))) $unyilvanosT[$ertekT[0]]='ok';
				else $unyilvanosT[$ertekT[0]]='';
			}
		}

        if(empty($mcim[$umodul_id]) and $umodul_id>0) { //Ha m�g nem k�rdezt�k le
            list($mcim1,$jogkod1)=mysql_fetch_row(mysql_query("select nev,jogkod from modulok where id='$umodul_id'"));
			$mcim[$umodul_id]=$mcim1;
			$jogkod[$umodul_id]=$jogkod1;
        }
		if(!empty($jogkod[$umodul_id])) { //Ha admin modulr�l van sz�
			//�s a felhaszn�l�nak nincs ilyen jogosults�ga
			if(!strstr($u_jogok,$jogkod[$umodul_id])) {
				if($fooldal_id==2) $mcim[$umodul_id]='miserend';
				elseif($fooldal_id==1) $mcim[$umodul_id]='h�rek';
			}
			//akkor annyit l�t, hogy az illet� a f�oldalon van
		}
        if($umodul_id==0) $mcim[$umodul_id]='f�oldal';
        if($ulogin!='*vendeg*' and $ulogin!=$u_login) {
			if($unem=='f' and $unyilvanosT['nem']=='ok') $jel='<img src=img/fiu.png align=absmiddle hspace=2>';
			elseif($unem=='n' and $unyilvanosT['nem']=='ok') $jel='<img src=img/lany.png align=absmiddle hspace=2>';
			else $jel='<img src=img/user.png align=absmiddle hspace=2>';

			if($uszulinap>0 and $unyilvanosT['szuldatum']=='ok') $jel.="<img src=img/szulinap.png align=absmiddle hspace=2 title='Boldog $uszulinap. sz�linapot!'>";
            
			if(!empty($ubecenev) and $ubecenev!=$ulogin) 
				$belepettek.="$jel<a href=# title='($ulogin) $mcim[$umodul_id]' class=kismenulink>$ubecenev</a>, ";
			else 
				$belepettek.="$jel<a href=# title='$mcim[$umodul_id]' class=kismenulink>$ulogin</a>, ";
        }
		//Keres�robotok sz�r�se
		if(!strstr($uhost,'google') and !strstr($uhost,'search') and !empty($uip))  {
			$azonosipkT[$uip]++;
			//Azonos IP-r�l 10 f�l�tt 1-nek sz�m�t
			if($azonosipkT[$uip]<=10) $mennyi[$mcim[$umodul_id]]++;
			elseif($azonosipkT[$uip]==11) $mennyi[$mcim[$umodul_id]]=$mennyi[$mcim[$umodul_id]]-9;
		}
    }
    
    if(is_array($mennyi)) {
        foreach($mennyi as $kulcs=>$ertek) {
            $lista.="<br><span class=kismenulink>$kulcs: $ertek</span>";
            $ossz=$ossz+$ertek;
        }
    }
    if($ossz>0) {
        $keretmenutxt="<span class=alap>Oldalainkon jelenleg $ossz l�togat� van.</span><br>";
        $keretmenutxt.=$lista;
		if($u_id>0) {
			if(!empty($belepettek)) {
				$keretmenutxt.="<br><br><span class=alap>Az al�bbi felhaszn�l�k vannak bejelentkezve:</span><br>";
				$keretmenutxt.=$belepettek;
			}
			else {
				$keretmenutxt.="<br><br><span class=alap>Rajtad k�v�l nincs bel�pett felhaszn�l�.</span><br>";
			}
		}


    }
    else $keretmenutxt="<span class=alap>Oldalainkon rajtad k�v�l jelenleg nincs m�s l�togat�nk.</span>";

	//Tartalom l�trehoz�sa
	$kodT[0]="<span class=hasabcimlink>L�togat�k</span>";
	$kodT[1]=$keretmenutxt;

    return $kodT;
    */
}


switch($op) {
    case '1':
        $hmenuT=latogatok_balmenu();
        break;

	case '2':
        $hmenuT=latogatok_balmenu();
        break;

	case 'aktiv':
		$hmenuT=latogatok_balmenu();
		break;
}

?>
