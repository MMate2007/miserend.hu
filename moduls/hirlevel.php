<?

/*
//////////////////////////
 //      alap modul      //
//////////////////////////
*/

function hirlevel_index() {
    global $design_url,$m,$db_name,$m_id,$am_id,$_GET,$_POST,$lang,$linkveg;

	$email=$_POST['email'];
	
    $tartalomkod="<p class=alcim>H�rlev�l - feliratkoz�s</p>";

	if(!empty($email)) {
		//Email ellen�rz�s
		if(!strstr($email,'@')) $emailhiba=true;

		if(!$emailhiba) {
			$tartalomkod.="<p class=alap>K�sz�ntj�k a list�n! Feliratkoz�si k�relm�t azonnal feldolgozzuk. Hogy nehogy tr�f�b�l valaki m�s akarjon az �n emailc�m�vel feliratkozni, ez�rt feliratkoz�si k�relm�t m�gegyszer j�v� kell hagyni. A j�v�hagy�s m�dj�r�l k�r�lbel�l 10-15 percen bel�l az <b>el�bb megadott</b> <font color=red>($email)</font> e-mail c�mre t�j�koztat�st k�ld�nk!</p>";

			$to='';
			//mail($to,$targy,$szoveg,"From: $email"); //mail k�ld�se a communio szerverre
		}
		else  {
			$tartalomkod.="<p class=alap><font color=red>HIBA!</font><br>�n val�sz�n�leg el�rta emailc�m�t! K�rem n�zze meg �jra!";
			$tartalomkod.='<form method=post>';
			$tartalomkod.="<input type=hidden name=m_id value=$m_id>";
			$tartalomkod.="<input type=text name=email value='$email' size=25 class='urlap'><br><input type=submit value=Feliratkoz�s class=urlap>";
			$tartalomkod.='</form>';
		}
	}
	else {
		$tartalomkod.="<p class=alap><b>Kedves L�togat�nk!</b><br><br>�nnek lehet�s�ge van feliratkozni h�rlevel�nkre...</p>";
		$tartalomkod.='<form method=post>';
		$tartalomkod.="<input type=hidden name=m_id value=$m_id>";
		$tartalomkod.="<input type=text name=email value='emailc�m' size=25 class='urlap'><br><input type=submit value=Feliratkoz�s class=urlap>";
		$tartalomkod.='</form>';
	}


	$adatT[2]=$tartalomkod;
	$tipus='doboz';
	$kod=formazo($adatT,$tipus);

    return $kod;
}


switch($m_op) {
    case 'index':
        $tartalom=hirlevel_index();
        break;

}

?>
