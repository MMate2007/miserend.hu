<?

include_once("szotar/datumok.php");

function igenaptar_balmenu() {
    global $sessid,$db_name,$m_id,$dateh;
	
	//C�m l�trehoz�sa
	$adatT[0]=alapnyelv('igenapt�r');
	$tipus='balmenucim';
	$kod.=formazo($adatT,$tipus);

	include_once("igenaptar_functions_havi.php");

	//Tartalom l�trehoz�sa
	$adatT[2] = naptari(); 
	$tipus='doboz';
	$kod.=formazo($adatT,$tipus);
	
	if($m_id!=5) return $kod;
}

function igenaptar_aktivmenu() {
    global $sessid, $db_name, $dateh;

	
	//C�m l�trehoz�sa
	$adatT[0]=alapnyelv('igenapt�r');
	$tipus='balmenucim';
	$kod.=formazo($adatT,$tipus);

	include_once("igenaptar_functions_havi.php");

	//Tartalom l�trehoz�sa
	$adatT[2] = naptari(); 
	$tipus='doboz';
	$kod.=formazo($adatT,$tipus);
	

    return $kod;
}

switch($op) {
    case '1':
        $hmenu=igenaptar_balmenu();
        break;

	case 'aktiv':
		$hmenu=igenaptar_aktivmenu();
		break;
}

?>
