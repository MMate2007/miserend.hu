<?

function kicsinyites($forras,$kimenet,$max) {
    if(($hiba=exec("convert -geometry $max".'x'."$max $forras $kimenet")) != '') echo "HIBA!: $hiba";
    
}

function kicsinyites1($forras,$kimenet,$max) {

			if(!isset($max)) $max=120;    # maximum size of 1 side of the picture.

			$src_img=ImagecreateFromJpeg($forras);

			$oh = imagesy($src_img);  # original height
			$ow = imagesx($src_img);  # original width

			$new_h = $oh;
			$new_w = $ow;

			if($oh > $max || $ow > $max){
		       $r = $oh/$ow;
		       $new_h = ($oh > $ow) ? $max : $max*$r;
		       $new_w = $new_h/$r;
			}

			// note TrueColor does 256 and not.. 8
			$dst_img = ImageCreateTrueColor($new_w,$new_h);

			ImageCopyResized($dst_img, $src_img, 0,0,0,0, $new_w, $new_h, ImageSX($src_img), ImageSY($src_img));
			ImageJpeg($dst_img, "$kimenet");
}

function loginurlap($belephiba) {
    global $_POST,$design_url,$sid,$linkveg;

	$bal="<span class=alcim>Felhasználói oldal</span>";
	$bal.="<br><br><span class=alap>Ezen oldal megtekintéséhez kérlek lépj be!<br>Ha még nincs felhasználóneved, <a href=?m_id=28&fm=11$linkveg class=felsomenulink>itt</a> tudsz regisztrálni egyet. </span>";
	
	$adatT[2]=$bal;
	$tipus='doboz';
	$kod.=formazo($adatT,$tipus);	
	
    return $kod;
}
///////////////////////////////////////////////////////////////////////////////////////////////////

function keret($helyzet) {
	global $db_name,$m_id,$belepve,$modul_url,$lang,$elso,$szavazott,$_SERVER,$design_url,$design,$fooldal_id;
	if(!isset($design)) $design='alap';
	//$ip=$_SERVER['REMOTE_ADDR'];

	if(!$belepve) $feltetel=" and zart=0";
	$nyelv=$lang;
	if(empty($nyelv)) $nyelv='hu';
	
	if($helyzet>0) {
		$a=$helyzet;
		$query="select modul_id,fajlnev,html_tmpl from oldalkeret where helyzet='$helyzet' and fooldal_id='$fooldal_id' $feltetel order by sorrend";
		if(!$lekerdez=mysql_db_query($db_name,$query)) echo "HIBA!<br>$query<br>".mysql_error();
		while(list($mid,$fajl,$html_tmpl)=mysql_fetch_row($lekerdez)) {				
			$tmpl_file = $design_url.'/'.$html_tmpl.'.htm';

			$include=$modul_url.'/'.$fajl.'_menu.php';
			$op=$helyzet;
			include_once($include);
			if(!empty($hmenuT[0])) {
				//Csak, ha van megadva cím				
				if($a>1) $a=0;

				$hasabcim=$hmenuT[0];
				$hasabtartalom=$hmenuT[1];
				$tipus=$hmenuT[2];
	
				$thefile = implode("", file($tmpl_file));
				$thefile = addslashes($thefile);
				$thefile = "\$r_file=\"".$thefile."\";";
			    eval($thefile);
    
				$keret .= "\n".$r_file;
				$hmenuT='';
			}
			else {
				$keret .= "\n".$hmenuT[1];
				$hmenuT='';
			}
			$keret.="\n<img src=img/space.gif width=5 height=4><br>";

		}
	}
	else {
		//Ha a $helyzet=0, akkor admin menü
		$op=$helyzet;
		include_once("$modul_url/admin_menu.php");
		if(!empty($hmenuT[0])) {
			//Csak, ha van megadva cím
			$hasabcim=$hmenuT[0];
			$hasabtartalom=$hmenuT[1];
			$tablabg="#ECE5C8";
			$fejlecbg="#F5CC4C";

			$thefile = implode("", file($tmpl_file));
			$thefile = addslashes($thefile);
			$thefile = "\$r_file=\"".$thefile."\";";
		    eval($thefile);
   
			$keret .= "\n".$r_file;
			$hmenuT='';
		}
		$keret.="\n<img src=img/space.gif width=5 height=4><br>";

		include_once("$modul_url/chat_menu.php");
		if(!empty($hmenuT[0])) {
			//Csak, ha van megadva cím
			$hasabcim=$hmenuT[0];
			$hasabtartalom=$hmenuT[1];
			$tablabg="#ECE5C8";
			$fejlecbg="#F5CC4C";

			$thefile = implode("", file($tmpl_file));
			$thefile = addslashes($thefile);
			$thefile = "\$r_file=\"".$thefile."\";";
		    eval($thefile);
   
			$keret .= "\n".$r_file;
			$hmenuT='';
		}
		$keret.="\n<img src=img/space.gif width=5 height=4><br>";
	}

	Return $keret;
}

function formazo($adatT,$tipus) {
	global $design_url,$szin,$design;

	if(!isset($design)) $design='alap';

	$cim=$adatT[0];
	$cimlink=$adatT[1];
	$tartalom=$adatT[2];
	$tartalom2=$adatT[3]; //híreknél 2. hasáb
	$tovabb=$adatT[4]; //híreknél "cikk bõvebben"
	$tovabblink=$adatT[5]; //általában a $cimlink

	$rovatcim=$adatT[6];
	$datum=$adatT[7];

		
    $tmpl_file = $design_url.'/'.$tipus.'.htm';

    $thefile = implode("", file($tmpl_file));
    $thefile = addslashes($thefile);
    $thefile = "\$r_file=\"".$thefile."\";";
    eval($thefile);
    
    return $kod = $r_file;
}

function langmenu() {
	global $lang,$sessid;

	if($lang=='en') {
		$enlink='<span class=szurkelink>english</span>';
	}
	elseif($lang=='de') {
		$delink='<span class=szurkelink>deutsch</span>';
	}
	else {
		$hulink='<span class=szurkelink>magyar</span>';
	}

	if(empty($enlink)) $enlink="<a href='?lang=en&sessid=$sessid' class='kismenulink'>english</a>";
	if(empty($delink)) $delink="<a href='?lang=de&sessid=$sessid' class='kismenulink'>deutsch</a>";
	if(empty($hulink)) $hulink="<a href='?lang=hu&sessid=$sessid' class='kismenulink'>magyar</a>";

	$nyelvlinkT=array($enlink,$delink,$hulink);

	Return $nyelvlinkT;	
}

function fomenu($hol) {
	global $db_name,$fooldal_id,$m_id,$linkveg,$lang,$design_url,$sid;

/*
	$query="select id,menucim,domain from fooldal where ok='i' and menucim!='' order by menusorrend";
	$lekerdez=mysql_db_query($db_name,$query);
	while(list($id,$menucim,$domain)=mysql_fetch_row($lekerdez)) {
*/
	$fomenuT=array(1=>"hírporta", 2=>"miserend", 4=>"táborhely", 5=>"médiatár", 6=>"emberhalász", 7=>"fórum", 3=>"plébánia");
	foreach($fomenuT as $id=>$menucim) {
		$link="?fooldal_id=$id&amp;sid=$sid";
		if($id==$fooldal_id) $aktiv='aktiv';
		else $aktiv='sima';
		$kod.="\n<div class=\"".$hol."menu_$aktiv\"><a href=\"$link\" title=\"$domain\">$menucim</a></div>";
	}
 
 
    return $kod;
}

function design() {
    global $design_url,$db_name,$m_id,$_GET,$tartalom,$m_oldalsablon,$balkeret,$jobbkeret,$onload,$sid,$linkveg,$u_id,$u_login,$u_jogok,$belepve,$loginhiba,$script;

	if(!isset($design)) $design='alap';
    $title=alapnyelv('title');	
	$top=alapnyelv('top');
	
	$nyelvlinkT=langmenu();
	$enlink=$nyelvlinkT[0];
	$delink=$nyelvlinkT[1];
	$hulink=$nyelvlinkT[2];

	if(!$belepve) $onload='onload=fokusz();';

//Scriptek////////////////////////////////////
	$script.="\n".'<script language="JavaScript" type="text/javascript">
	<!--
	function fokusz() {
      document.loginurlap.login.focus();
	}
  
	function OpenPrintWindow(url, x, y) {
      var options = "toolbar=no,menubar=yes,scrollbars=yes,resizable=yes,width=" + x + ",height=" + y;
      msgWindow=window.open(url,"", options);
	}
  
	function OpenNewWindow(url, x, y) {
      var options = "toolbar=no,menubar=no,scrollbars=no,resizable=yes,width=" + x + ",height=" + y;
      msgWindow=window.open(url,"", options);
	}

	function OpenScrollWindow(url, x, y) {
      var options = "toolbar=no,menubar=no,scrollbars=yes,resizable=yes,width=" + x + ",height=" + y;
      msgWindow=window.open(url,"", options);
	}

	function UnCryptMailto(s) {
		var n=0;
		var r="";
		for(var i=0;i<s.length;i++) { 
			n=s.charCodeAt(i); 
			if (n>=8364) {n = 128;}
			r += String.fromCharCode(n-(2)); 
		}
		return r;
	}

	function EnCryptMailto(s) {
		var n=0;
		var r="";
		for(var i=0;i<s.length;i++) { 
			n=s.charCodeAt(i); 
			if (n>=8364) {n = 128;}
			r += String.fromCharCode(n+(2)); 
		}
		return r;
	}
	
	function linkTo_UnCryptMailto(s)	{
		location.href=UnCryptMailto(s);
	}

	// -->
	</script>';
////////////////////////////////////////////

	$emaillink_lablec="<A HREF=\"javascript:linkTo_UnCryptMailto('ocknvq%3CkphqBjktrqtvc0jw');\" class=emllink>info<img src=img/kukaclent.gif align=absmiddle alt='kukac' border=0>hirporta.hu</a>";
	

//Impresszum link
	$impkiir=alapnyelv('Impresszum');
	$impfm=alapnyelv('impfm');
	$impresszumlink="<a href=?m_id=17&fm=12$linkveg class=implink>$impkiir</a>";

	$keszitok="<a href=http://www.b-gs.hu class=implink title='BGS artPart' target=_blank>design</a><br /><a href=http://www.florka.hu class=implink title='Florka Kft.' target=_blank>programozás</a>";

//Névnap
	$ho=date('n');
	$honap=alapnyelv("ho$ho");
	$nap=date('j');
	$napszam=date('w');
	$napnev=alapnyelv("nap$napszam");
	$datumkiir="$honap $nap. $napnev";					  
	
	//Névnapok
	$datumn=date('md');
	$query="select nevnap from nevnaptar where datum='$datumn'";
	$lekerdez=mysql_db_query($db_name,$query);
	list($nevnapok)=mysql_fetch_row($lekerdez);

	//Ünnepnapok
	$datumu=date('Y-m-d');
	$query="select unnep from unnepnaptar where datum='$datumu'";
	$lekerdez=mysql_db_query($db_name,$query);
	list($unnepnapok)=mysql_fetch_row($lekerdez);

	$nevnap="<span class=\"datum\">$datumkiir</span><br /><span class=\"unnep\">";
	if(!empty($unnepnapok)) $nevnap.="$unnepnapok, ";
	$nevnap.=$nevnapok;
	$nevnap.='</span>';

//META adatok
	$meta.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
	$meta.="\n";
	$meta.='<meta name="description" content="VPP-Hírporta: hírek, programajánlatok, publickációk" />';
	$meta.="\n";
	$meta.='<meta name="keywords" content="katolikus, tábor, elõadás, lelkigyakorlat, koncert, család, ifjúság, szabadidõ, hitélet, egyház" />';
	$meta.="\n";
	$meta.='<meta name="author" content="VPP" />';
	$meta.="\n";
	$meta.='<meta http-equiv="content-language" content="hu" />';
	$meta.="\n";
	$meta.='<meta name="robots" content="all" />';
	$meta.="\n";
	$meta.='<meta name="revisit-after" content="1 days" />';
	$meta.="\n";

	$meta.='<link rel="stylesheet" href="'.$design_url.'/img/style.css" type="text/css" media="all" />';
	$meta.="\n";
	$meta.='<link rel="stylesheet" href="'.$design_url.'/img/style_jobbhasab.css" type="text/css" media="all" />';
	if(($m_id==1 or $m_id==33) and empty($_GET['hir'])) {
		$meta.="\n";
		$meta.='<link rel="stylesheet" href="'.$design_url.'/img/style_fooldal.css" type="text/css" media="all" />';
		$meta.="\n";
	}
	elseif(($m_id==1 or $m_id==33) and $_GET['hir']>0) {
		$meta.="\n";
		$meta.='<link rel="stylesheet" href="'.$design_url.'/img/style_hirview.css" type="text/css" media="all" />';
		$meta.="\n";
	}
	$meta.='<link rel="stylesheet" href="'.$design_url.'/img/style_print.css" type="text/css" media="print" />';
	$meta.="\n";		
	

//Loginûrlap
	if($belepve) {
		//Ha bent van
		$loginurlap="\n<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" height=\"44\"><tr>";
		$loginurlap.="<td><div align=\"left\" class=\"kicsi\"><img src=\"img/space.gif\" width=\"4\" height=\"4\">Belépve:</div><div align=\"center\" class=\"alcim\">$u_login</div></td>";
		$loginurlap.="<td><img src=\"img/space.gif\" width=\"10\" height=\"2\"><br><a href=?m_id=28&m_op=add$linkveg class=loginlink><img src=$design_url/img/negyzet.jpg border=0 align=absmiddle> Beállítások</a><br><a href=?kilep=1$linkveg class=loginlink><img src=$design_url/img/negyzet.jpg border=0 align=absmiddle> Kilépés</a></td></tr></table>";
	}
	else {
		//Belépés									
		$loginurlap="\n<form method=\"post\" name=\"loginurlap\"><input type=\"hidden\" name=\"kilep\" value=\"0\" /><table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" width=\"100%\" height=\"44\"><tr>";
		$loginurlap.="\n<td>Felhasználónév:<br><input type=\"text\" name=\"login\" value=\"".$_POST['login']."\" size=\"16\" class=\"loginurlap\" /></td>";
		$loginurlap.="\n<td>Jelszó:<br /><input type=\"password\" name=\"passw\" size=\"16\" class=\"loginurlap\" /></td>";
		        
		$loginurlap.="<td rowspan=\"2\" width=\"65\" align=\"center\">";
		if(!empty($_POST['login'])) {
			$loginurlap.="<font color=\"red\">$loginhiba</font><br >";
		}
		$loginurlap.="<input type=\"image\" border=\"0\" src=\"$design_url/img/belepesgomb.jpg\" align=\"absmiddle\" /></td>";
		
		$loginurlap.="\n</tr><tr>";
		$loginurlap.="\n<td><a href=\"?m_id=28&amp;fm=11$linkveg\">Regisztráció <img src=\"$design_url/img/negyzet.jpg\" align=\"absmiddle\" border=\"0\"></a></td>";
		$loginurlap.="\n<td><a href=\"?m_id=28&amp;m_op=jelszo$linkveg\" title=\"Jelszó emlékeztetõ\">Jelszó emlékeztetõ <img src=\"$design_url/img/negyzet.jpg\" border=\"0\" align=\"absmiddle\"></a></td>";
		
		$loginurlap.="</tr><input type=\"hidden\" name=\"sid\" value=\"$sid\"></table></form>";
	}


//Fõmenü
	$felsomenu=fomenu('felso');
	$alsomenu=fomenu('also');


//Fõhasáb összeállítása
	$fohasab=$tartalom;

//Adminbal
	if($belepve and !empty($u_jogok)) {
		$adminbal=keret(0);
	}

//jobbkeret összeállítása
	$jobbhasab=keret(2);
	$tartalom=str_replace("!%jobbhasab%!",$jobbhasab,$tartalom);
	$tartalom=str_replace("!%adminjobb%!",$adminbal,$tartalom);

//Balkeret összeállítása
	$balhasab=$adminbal.keret(1);


    if(empty($m_oldalsablon)) {
		$m_oldalsablon='alap';
    }
	$sablon="sablon_$m_oldalsablon.htm";

	$tmpl_file = $design_url.'/'.$sablon;

    $thefile = implode("", file($tmpl_file));
    $thefile = addslashes($thefile);
    $thefile = "\$r_file=\"".$thefile."\";";
    eval($thefile);
    
    return $html_kod = $r_file;
}



?>
