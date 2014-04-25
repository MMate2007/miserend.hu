<?

function url_ell() {
// URL ellen�rz�se
/////////////////////////////////
// fooldal t�bla:
// id int(2) auto_increment primary
// cim varchar(30) [A f�oldal c�me, amit pl. a fejl�cbe ki�r a b�ng�sz�]
// kod varchar(30) [k�d �kezet n�k�l, ami a f�oldalhoz kapcsol�d� k�nyvt�rakat, vagy ilyesmiket azonos�that]
// domain varchar(20) [a f�oldal domainje - ebb�l azonos�tjuk]
// design varchar(30) [az alap�rtelmez�sben be�ll�tott design]
// bekerult datetime [amikor az oldal elindult]
/////////////////////////////////

// domain: $SERVER_NAME
// a domain ut�ni URL: $REQUEST_URI

    global $_SERVER, $db_name, $hiba, $hibauzenet, $hibauzenet_prog, $_GET, $_POST;
    
	$fooldal_id=$_POST['fooldal_id'];
    if(!empty($fooldal_id)) {
        //�tugrasztjuk arra a f�oldalra
        $query="select domain,ugras from fooldal where id='$fooldal_id'";
        $lekerdez=mysql_query($query);
        list($domain,$ugras)=mysql_fetch_row($lekerdez);

        if(!empty($ugras)) $ujlink=$ugras;
        else $ujlink=$domain;
        
        foreach($_POST as $kulcs=>$ertek) {
            if($kulcs!='fooldal_id') $parameterT[]="$kulcs=$ertek";
        }
        if(is_array($parameterT)) $parameterek=implode('&',$parameterT);
        
        header("Location: http://$ujlink?$parameterek");
    }

    $fooldal_id=$_GET['fooldal_id'];
    if(!empty($fooldal_id)) {
        //�tugrasztjuk arra a f�oldalra
        $query="select domain,ugras from fooldal where id='$fooldal_id'";
        $lekerdez=mysql_query($query);
        list($domain,$ugras)=mysql_fetch_row($lekerdez);

        if(!empty($ugras)) $ujlink=$ugras;
        else $ujlink=$domain;
        
        foreach($_GET as $kulcs=>$ertek) {
            if($kulcs!='fooldal_id') $parameterT[]="$kulcs=$ertek";
        }
        if(is_array($parameterT)) $parameterek=implode('&',$parameterT);
        
        header("Location: http://$ujlink?$parameterek");
    }

    $teljes_domain=$_SERVER['SERVER_NAME'];  // pl. www.plebania.net
    if(substr_count($teljes_domain, '.')>1) {      // lebal�bb 2 pontn�l van aldomain
        $domain2=strstr($teljes_domain,'.');     //pl. .plebania.net  !!!FIGYELEM! www.aldomain.plebania.net -n�l aldomain.plebania.net lesz a domain!!!
        $karakterszam=strlen($domain2);
        $aldomain_karakterszam=strlen($teljes_domain)-$karakterszam;
        $domain=substr($domain2,1,$karakterszam);     //pl. plebania.net
        $aldomain=substr($teljes_domain,0,$aldomain_karakterszam); //pl www
        if($aldomain=='www') $aldomain='';  //A www-t nem sz�m�tjuk bele
    }
    else {
        $domain=$teljes_domain;
        $aldomain='';
    }
    //Megn�zz�k, hogy aldomainnel van-e f�oldal
    if(!empty($aldomain)) $domainell="$aldomain.$domain";
    else $domainell=$domain;
    $query="select id,cim,design,nyitomodul,ugras,domain from fooldal where domain='$domainell' and ok='i'";
    if(!$lekerdez=mysql_query($query)) {
        //Ha a lek�rdez�s nem siker�lt...
        $hiba=true;
        $hibauzenet.='Az oldal beazonos�t�s�n�l hiba t�rt�nt.';
        $hibauzenet_prog.="\n\nHIBA az adatb�zis lek�rdez�sn�l (ell.inc #114 [url_ell]):\n" . mysql_error();
    }
    else {
        $van=false;
        if(mysql_num_rows($lekerdez)==0) {
            $van=false;
            //Megn�zz�k m�g aldomain n�lk�l is
            //(ez esetben csak nyit�modul van az aldomainhez, nem �n�ll� oldal)
            if(!empty($aldomain)) {
                $domainell=$domain;
                $query="select id,cim,kod,design,nyitomodul,ugras,domain from fooldal where domain='$domainell' and ok='i'";
                $lekerdez=mysql_query($query);
                
                if(mysql_num_rows($lekerdez)>0) $van=true;
            }
        }
        else $van=true;
        
        if(!$van) {
            //HIBA, rossz helyen akarjuk megnyitni az oldalt
            //ez esetben �tir�ny�tjuk az els�k�nt be�ll�tott f�oldalunkra
            list($ujdomain)=mysql_fetch_row(mysql_query("select domain from fooldal order by sorrend"));
            header("Location: http://www.$ujdomain");
            exit;
        }
        else {
            list($fooldal_id,$fooldal_cim,$fooldal_design,$nyitomodul,$fooldal_ugras,$domain)=mysql_fetch_row($lekerdez);
            if(!empty($fooldal_ugras)) {
				//Ha m�sik oldalra kell �tugratni a domainr�l
                header("Location: $fooldal_ugras");
                exit;
            }

			//???
            if($_POST['admin']!=0 or $_GET['admin']!=0) $adminoldal=true;

            //Esetleges aldomain eset�n megn�zz�k a nyit�modult
            if(!empty($aldomain)) {
                $query="select nyitomodul,ugras from fooldal_aldomain where f_id='$fooldal_id' and aldomain='$aldomain'";
                $lekerdez=mysql_query($query);
				if($lekerdez) {
					list($nyitom,$ugras)=mysql_fetch_row($lekerdez);
					if(!empty($ugras)) {
						header("Location: $ugras");
						exit;
					}
					if(!empty($nyitom)) $nyitomodul=$nyitom;
					if(!empty($_POST['m_id']) or !empty($_GET['m_id'])) $nyitomodul='';
				}
            }
            
            $urlT = array ($fooldal_id, $fooldal_cim, $fooldal_design, $aldomain, $nyitomodul, $adminoldal);

            return $urlT;
        }
    }
}

function extra_ell($fooldal_id) {
//Extra alkalom ellen�rz�se
/////////////////////////////////
// id int(3) auto_increment primary
// tol datetime [amikort�l �rv�nyes]
// ig datetime [ameddig �rv�nyes]
// uzenet text [�zenet sz�vege, ami ki�r�sra ker�l]
// tipus enum(s,bn) [esem�ny t�pusa: semmi nem j�n be, csak az �zenet, vagy bel�p�s nincs, csak �zenet + egy�b oldalak]
// fooldalak varchar(50) [mely f�oldalakat �rinti az esem�ny]
//
// FIGYELEM! Egy id�szakban egy oldalhoz CSAK EGY esem�ny lehets�ges
/////////////////////////////////

    global $hiba, $hibauzenet, $hibauzenet_prog, $db_name;

    $most=date('Y-m-d H:i:s');
    $fooldal="and fooldalak like '%-".$fooldal_id."-%'";

    $query="select uzenet,tipus from extra_alkalom where tol<'$most' and ig>'$most' $fooldal";
    if(!$lekerdez=mysql_query($query)) {
        //Ha a lek�rdez�s nem siker�lt...
        $hiba=true;
        $hibauzenet.='HIBA az adatb�zis lek�rdez�sn�l. A szolg�ltat�s jelenleg nem �rhet� el.';
        $hibauzenet_prog.="\n\nHIBA az adatb�zis lek�rdez�sn�l (ell.inc #109 [extra_ell]):\n" . mysql_error();
    }
    else {
        list($extra_uzenet,$extra_tipus)=mysql_fetch_row($lekerdez);
        //Egy id�szakban egy oldalhoz CSAK EGY esem�ny lehets�ges
        $extraT = array ($extra_uzenet, $extra_tipus);
        return $extraT;
    }
}

function modul_ell($fooldal_id,$aldomain) {
//Beh�vand� modulok ellen�rz�se
/////////////////////////////////
// fooldal_modulok t�bla:
// id int(3) auto_increment primary
// fooldal_id int(2) [Melyik f�oldalhoz tartozik]
// hova enum(f,b,t,j,l) [Hova ker�l - fejl�c, balmen�, tartalom, jobbmen�, l�bl�c]
// tipus enum(a,d) [T�pusa - �lland� (a) vagy alap�rtelmezett (d), melyet az egyes oldalak �t�rhatnak.
// sorrend int(4) [Ha t�bb modul is beh�v�sra ker�l, itt lehet be�ll�tani a sorrendet]
// modul int(3) [modul k�dja]
/////////////////////////////////
// A fent beh�vott alap�rtelmezett modulokat az url param�terei m�dos�thatj�k!

    global $hiba, $hibauzenet, $hibauzenet_prog, $html_kod;
    
    $query="select hova,tipus,sorrend,modul from fooldal_modulok where fooldal_id='$fooldal_id' order by hova, sorrend";
    if(!$lekerdez=mysql_query($query)) {
        //Ha a lek�rdez�s nem siker�lt...
        $hiba=true;
        $hibauzenet.='';
        $hibauzenet_prog.="\n\nHIBA az adatb�zis lek�rdez�sn�l (ell.inc #125 [modul_ell]):\n" . mysql_error();
    }
    else {
        while(list($fm_hova,$fm_tipus,$fm_sorrend,$fm_modul)=mysql_fetch_row($lekerdez)) {
            if($fm_hova=='f') $modulList['f'][]=$fm_modul;
            elseif($fm_hova=='b') $modulList['b'][]=$fm_modul;
            elseif($fm_hova=='t') $modulList['b'][]=$fm_modul;
            elseif($fm_hova=='j') $modulList['b'][]=$fm_modul;
            elseif($fm_hova=='l') $modulList['b'][]=$fm_modul;
        }
        //Itt m�g ellen�rizni kell, hogy az URL adatai alapj�n v�ltozik-e valami!
        return $modulList;
    }
}

?>
