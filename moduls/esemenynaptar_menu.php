<?

function esemenynaptar_jobbmenu() {
    global $db_name,$design,$linkveg,$lang;

	if(!isset($design)) $design='alap';
	if(!isset($lang)) $lang='hu';
	
	//Tartalom l�trehoz�sa
	
	$max=20;
	$max1=10;
	$egynap=86400;
	$ma=date('Y-m-d');
	for($i=0;$i<10;$i++) {
		$newtime=time()+($i*$egynap);
		$ujdatum=date('Y-m-d',$newtime);
		$feltetelT[]="aktualis like '%$ujdatum%'";
	}
	if(is_array($feltetelT)) {
		$feltetel='and ('.implode(' or ',$feltetelT).')';
	}

	$query="select distinct(id),cim,aktualis from hirek where ok='i' $feltetel";
	$lekerdez=mysql_query($query);
	while(list($id,$cim,$aktualis)=mysql_fetch_row($lekerdez)) {
		$cimT[$id]=$cim;
		$aktualisT=explode('+',$aktualis); //t�bb d�tum is lehets�ges
		if(is_array($aktualisT)) {
			foreach($aktualisT as $datumok) {
				if($datumok>=$ma and $datumok<=$ujdatum) { //Ha ez a d�tum a kezd� �s v�gd�tum k�z� esik (teh�t nem r�gi vagy messzi esem�ny)
					$vanesemenyTT[$datumok][]=$id;					
				}
			}
		}
	}
	if(is_array($vanesemenyTT)) {
		ksort($vanesemenyTT);
		foreach($vanesemenyTT as $datumok=>$ertek) {
			foreach($ertek as $idk) {
				if($a<$max1) {
					$datumkiir=str_replace('-','.',$datumok).'.';
					$aktualis=str_replace('-','.',$aktualis).'.';
					$adatT[0]="$cimT[$idk] ($datumkiir)";
					$adatT[1]="?m_id=1&m_op=view&id=$idk$linkveg";
					$tipus='jobbhirlista';
					$kod_lista.=formazo($adatT,$tipus);
					$a++;
				}
			}
		}
	}

	if(empty($kod_lista)) {
		$kod_lista="<tr><td width=15><img src=img/space.gif width=15 height=5></td><td><span class=kicsi>A k�vetkez� n�h�ny napra nincs esem�ny az adatb�zisban</span></td></tr>";
	}


	$kod_lista.="<tr><td colspan=2><img src=img/space.gif width=15 height=15></td></tr>";
				
	$adatT[0]="Teljes esem�nynapt�r >>";
	$adatT[1]="?m_id=15$linkveg";
	$tipus='jobbhirlista';
	$kod_lista.=formazo($adatT,$tipus);	
	
	$adatT[0]='Esem�nynapt�r';
	$adatT[1]="?m_id=15$linkveg";
	$tipus='jobbmenucim';
	$kod_cim=formazo($adatT,$tipus);

	$adatT[0]=$kod_cim;	
	$adatT[2]=$kod_lista;
	$tipus='jobbmenu';
	$kod=formazo($adatT,$tipus);

    return $kod;
}


switch($op) {
    case '2':
        $hmenu=esemenynaptar_jobbmenu();
        break;

}

?>
