<?

function unnep_balmenu() {
    global $linkveg,$lang,$db_name,$sid,$u_id,$_SERVER,$m_id,$u_jogok,$u_login,$fooldal_id,$_GET;


}

function unnep_jobbmenu() {
    global $linkveg,$lang,$db_name,$sid,$u_id,$_SERVER,$m_id,$u_jogok,$u_login,$fooldal_id,$design_url,$_GET;

	
		$kod_cim="Mindenszentek";

		$kod_tartalom="\nAz �nnep c�lja, hogy az �sszes szentet - nemcsak azokat, akiket az Egyh�z k�l�n szentnek nyilv�n�tott - egy k�z�s napon �nnepelj�k. ";	

		$kod_tipus='piros';

	
	if(!empty($kod_tartalom)) {
		//Tartalom l�trehoz�sa
		$kodT[0]=$kod_cim;
		$kodT[1]=$kod_tartalom;
		$kodT[2]=$kod_tipus;

		return $kodT;
	}
}


switch($op) {
    case '1':
        $hmenuT=unnep_balmenu();
        break;

	case '2':
        $hmenuT=unnep_jobbmenu();
        break;

}

?>
