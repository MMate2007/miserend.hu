<?php

/////////////////////////////////
// F� v�ltoz�k:
// $hiba (true,false)
// $hibauzenet (hiba�zenet a l�togat�nak)
// $hibauzenet_prog (hiba�zenet a programoz�nak)
// $html_kod (html k�d, amit a v�g�n ki�runk)
/////////////////////////////////

/////////////////////////////////
//Alapadatok be�ll�t�sa (config)
/////////////////////////////////
    $hiba=false;

//Adatb�zis csatlakoz�s el�k�sz�t�se, elind�t�sa
    if(!@include_once('config.inc')) {
        $hiba=true;
        $hibauzenet_prog.='<br>HIBA! A konfigur�ci�s f�jl beh�v�sakor!';
	echo 'hiba';
    }
    dbconnect();
        
/////////////////////////////////
//Ellen�rz�sek, be�ll�t�sok
/////////////////////////////////
    if(!@include_once('ell.php')) {
        $hiba=true;
        $hibauzenet_prog.='<br>HIBA! Az ellen�rz� f�jl beh�v�sakor!';
    }

//URL ellen�rz�se
    if(!$hiba) {
        $urlT=url_ell();
		
        $fooldal_id=$urlT[0];
        $fooldal_cim=$urlT[1];
        $design=$urlT[2];
        $aldomain=$urlT[3];
        $nyitomodul=$urlT[4];
        $adminoldal=$urlT[5];

		if(!empty($_GET['design'])) $design=$_GET['design'];

        $design_url="design/$design";
	}


//Letiltott IP-r�l van-e sz�
/*
    if(!$hiba) {
        $tiltott_IP_T=ip_ell($fooldal_id);
        //$tiltott_IP �s belep_tiltott_IP true vagy false
    }
*/

		//Be�ll�tott modulok
		if($_GET['templom']>0) {
			$M_ID=26;
			$M_OP='view';
			$TID=$_GET['templom'];
		}

		//Be�ll�tott modulok
		if($_GET['hir']>0) {
			$M_ID=1;
			$M_OP='view';
			$HID=$_GET['hir'];
			if(!empty($_GET['design'])) $M_ID=33;
		}

require_once 'vendor/twig/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader); // cache?

/////////////////////////////////
//designelemek beh�v�sa
/////////////////////////////////
    if(!$hiba) {
        if(!include("$design_url/design.php")) {
            $hiba=true;
            $hibauzenet.='Eln�z�s�t k�rj�k, az oldal nem jelen�thet� meg.';
            $hibauzenet_prog.='HIBA! A desing program nem h�vhat� be!<br>'.$design_url.'/design.php';
        }
    }

/////////////////////////////////
//modul kiv�laszt�sa
/////////////////////////////////
    if(!$hiba) {
        $m_id=$_POST['m_id'];
        if(empty($m_id)) $m_id=$_GET['m_id'];
        if(empty($m_id) and $nyitomodul>0) {
			$m_id=$nyitomodul;
			//if($lang!='hu' and !empty($lang)) $m_id=17;
		}

		if(!empty($M_ID)) $m_id=$M_ID;

        if(!empty($m_id)) {
            $query="select fajlnev,sablon,zart from modulok where id='$m_id' and ok='i'";
            if(!$lekerdez=mysql_query($query)) {
                $hiba=true;
                $hibauzenet.='A v�lasztott funkci� beh�v�sa sikertelen.';
                $hibauzenet_prog.='HIBA a modul beh�v�s�n�l:<br>'.mysql_error();
            }
            list($m_fajlnev,$m_oldalsablon,$m_zart)=mysql_fetch_row($lekerdez);
        }
		
		if(!empty($m_fajlnev) and is_file("$modul_url/$m_fajlnev.php")) {
			$modul=$modul_url.'/'.$m_fajlnev.'.php';
		}
		else {
			$modul=$modul_url."/alap$lang.php";
		}
    }

/////////////////////////////////
//bel�p�s ellen�rz�se
/////////////////////////////////
    $belepve=false;
	$sid=$_COOKIE['sid'];
	if(!empty($sid)) {
		$vancookie=true;
		if(!empty($_GET['sid'])) $sid=$_GET['sid'];
	}
	else {
		$vancookie=false;
		$sid=$_POST['sid'];
	    if(empty($sid)) $sid=$_GET['sid'];
	}

	$kilep=$_GET['kilep'];
	if(!empty($_POST['login'])) $kilep='';

	include('login.php');
    //Ha kil�p
	if($kilep>0) {
		kilepes();
        $belepve=false;
	    $u_id=0;
		$u_login='';
        $u_jogok='';
		$u_oldal='';
		$u_beosztas='';
		setcookie('sid','',time()-3600,'/','miserend.hu');
		setcookie('sid','',time()-3600,'/','hirporta.hu');
		setcookie('sid','',time()-3600,'/','plebania.net');
		setcookie('sid','',time()-3600,'/','taborhely.info');
		setcookie('sid','',time()-3600,'/','emberhalasz.net');
    }
	else {
		//Ha m�r van sid-je, akkor friss�tj�k
        if (!empty($sid)) {
			setcookie('sid',$sid,time()+86400,'/','miserend.hu');
			setcookie('sid',$sid,time()+86400,'/','hirporta.hu');
			setcookie('sid',$sid,time()+86400,'/','plebania.net');
			setcookie('sid',$sid,time()+86400,'/','taborhely.info');
			setcookie('sid',$sid,time()+86400,'/','emberhalasz.net');
	        $belepesT=belepell();
			$belepve=$belepesT[0];
		}
        //Ha m�g nincs, akkor adunk neki
	    else {
		    $belepesT=ujvendeg();
			$sid=$belepesT[10];
			$belepve=$belepesT[0];
			setcookie('sid',$sid,time()+86400,'/','miserend.hu');
			setcookie('sid',$sid,time()+86400,'/','hirporta.hu');
			setcookie('sid',$sid,time()+86400,'/','plebania.net');
			setcookie('sid',$sid,time()+86400,'/','taborhely.info');
			setcookie('sid',$sid,time()+86400,'/','emberhalasz.net');
        }
	    //Ha z�rt oldalra akarna bel�pni �s m�g nincs bel�pve
		if(($m_zart and !$belepve) or !empty($_POST['login'])) {
			$belepesT=beleptet();
			$belepve=$belepesT[0];
			
			$atvett=$belepesT[20];
			if($atvett=='i') {
				$modul="$modul_url/regisztracio.php";
				$m_id=28;
			}			
		}		
		
		$u_id=$belepesT[1];
        $u_login=$belepesT[2];
        $u_jogok=$belepesT[3];
	    $belephiba=$belepesT[4];
		$szavazott=$belepesT[5];
		$u_oldal=$belepesT[6];
		$u_beosztas=$belepesT[7];
		$tiltott_IP=$belepesT[8];
		$belep_tiltott_IP=$belepesT[9];
		$u_varos=$belepesT[12];
		$u_becenev=$belepesT[13];
		$u_baratok=$belepesT[14];
		$u_ismerosok=$belepesT[15];

		if(strstr($belep_tiltott_IP,$fooldal_id)) {
			$belepve=false;
			$loginhiba='Felhaszn�l� kiz�rva!';
		}
		if(strstr($tiltott_IP,$fooldal_id)) {
			header("location: http://www.plebania.net");
			exit;
		}

	}

	if($vancookie) {
		//Ha van a cookieban sid, akkor nem kell a linkek v�g�re kitenni
		$linkveg_sid='';
	}
	else {
		$linkveg_sid="&sid=$sid";
	}
	$linkveg=$linkveg_sid;

/////////////////////////////////
//jogosults�g ellen�rz�se
/////////////////////////////////
    $jogosult=false;
    $mjogell='-'.$m_id.'-';
    if(strstr($u_jogok,$mjogell) or $m_jogok==0) $jogosult=true;
    
    $mehet=false;
    if($m_zart and $belepve and $jogosult) $mehet=true;
    elseif(!$m_zart) $mehet=true;

/////////////////////////////////
//nyelv be�ll�t�sa
/////////////////////////////////
	nyelvmeghatarozas();

/////////////////////////////////
//tartalmi r�sz �ssze�ll�t�sa
/////////////////////////////////
    if(!$hiba and !$tiltott_IP_T[0] and $mehet) {
        $m_op=$_POST['m_op'];
        if(empty($m_op)) $m_op=$_GET['m_op'];
        if(empty($m_op)) $m_op='index';

		if(!empty($M_OP)) $m_op=$M_OP;

		if($atvett=='i') $m_op='atvett';

        //Beh�vjuk a modulhoz a sz�t�r�t is
        $szotarfajl="$modul_url/szotar/$m_id$lang.inc";
        if($m_id>0 and is_file($szotarfajl)) {
            if(!@include_once($szotarfajl)) {
                $hiba=true;
                $hibauzenet_prog.='<br>HIBA a modul nyelvi f�jl beh�v�s�n�l!';
            }
        }

		//Beh�vjuk a modulhoz az egy�ni designf�jlt is, ha van
        $designfajl="$design_url/d_$m_id.php";
        if($m_id>0 and is_file($designfajl)) {
            if(!@include_once($designfajl)) {
                $hiba=true;
                $hibauzenet_prog.='<br>HIBA a modul design f�jl beh�v�s�n�l!';
            }
        }
        //Mivel jogosult, beh�vjuk a modult
        if(!include_once($modul)) {
            $hiba=true;
            $hibauzenet.='';
            $hibauzenet_prog.='HIBA! A v�lasztott modul nem h�vhat� be!<br>'.$modul;
        }
    }
    //Nincs hozz� jogosults�ga!
    elseif(!$jogosult and $m_zart and $belepve) {
        $tartalom='<big><br><span class=hiba>HIBA a modul megnyit�s�ban!</span></big>';
    }
    //Nincs bel�pve, kirakjuk az �rlapot
    elseif($m_zart and !$belepve and !$tiltott_IP_T[1]) {
        $tartalom=loginurlap($belephiba);
        $onload='onload="fokusz();"';
    }
	//Le van tiltva, nem l�phet be
    elseif($m_zart and $tiltott_IP_T[1]) {
        $tartalom='<big><br><span class=hiba>Nem l�phetsz be!</span></big>';
    }

/////////////////////////////////
//hiba�zenetek kezel�se
/////////////////////////////////
    if($hiba) {
        $html_kod=$hibauzenet_prog;
    }
	elseif($tiltott_IP_T[0]) {
        $html_kod='Ki vagy tiltva!';
    }


/////////////////////////////////
//tartalom form�z�sa a sablonba
/////////////////////////////////
    else {
        $html_kod=design();
    }

/////////////////////////////////
//html k�d kik�ld�se a b�ng�sz�nek
/////////////////////////////////
    print $html_kod;

?>
