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

function igenaptar_index() {

	$kod="<span class=alap>V�lassz!</span>";

	return $kod;
}

function addszent($id) {
    global $linkveg,$sid,$m_id;

	$kod.=include('editscript2.php');

	$kod.="<p class=alcim>Szent / �nnep hozz�ad�sa, m�dos�t�sa</p>";

    $query="select nev,nevnap,intro,ho,nap,leiras,szin from szentek where id='$id'";
    if(!$lekerdez=mysql_query($query))
      echo '<p>HIBA!<br>'.mysql_error();
    list($nev,$nevnap,$intro,$ho,$nap,$leiras,$szin)=mysql_fetch_row($lekerdez);

    $kod.="\n<form method=post><input type=hidden name=id value=$id>";
    $kod.="\n<input type=hidden name=sid value=$sid><input type=hidden name=id value=$id>";
    $kod.="\n<input type=hidden name=m_id value=$m_id><input type=hidden name=m_op value=addingszent>";

    $kod.="\n<span class=kiscim>Szent / �nnep neve:</span>";
	$kod.="\n<br><input tpye=text name=nev value='$nev' size=40 class=urlap>";

	$kod.="\n<br><br><span class=kiscim>�nnepe:</span>";
    $kod.="<br><select name=ho class=urlap>";
    for($i=1;$i<=12;$i++) {
        $kod.= "<option value=$i";
        if($i==$ho) $kod.= ' selected';
        $kod.= '>'.alapnyelv("ho$i").'</option>';
    }
    $kod.= "</select> <input type=text name=nap value='$nap' size=2 maxlength=2 class=urlap>";

    $kod.="\n<br><br><span class=kiscim>�nnep sz�ne:</span><span class=alap><br>(v�rtan�n�l piros, egy�bk�nt �ltal�ban feh�r)</span>";
	$kod.="\n<br><select name=szinsz class=urlap>";
	$kod.="<option";
	if($szin=='feher') $kod.=' selected';
	$kod.=">feher</option><option";
	if($szin=='piros') $kod.=' selected';
	$kod.=">piros</option><option";
	if($szin=='zold') $kod.=' selected';
	$kod.=">zold</option><option";
	if($szin=='lila') $kod.=' select';
	$kod.=">lila</option></select>";
	
	$kod.="\n<br><br><span class=kiscim>N�vnap:</span><span class=alap><br>Az adott napon a szent alapj�n tartand� n�vnap (csak n�v)</span>";
	$kod.="\n<br><input tpye=text name=nevnap value='$nevnap' size=40 class=urlap>";

    $kod.="\n<br><br><span class=kiscim>R�vid le�r�s:</span>";
	$kod.="<br><textarea name=intro cols=60 rows=5 class=urlap>$intro</textarea>";

    $kod.="\n<br><br><span class=kiscim>Teljes le�r�s (elm�lked�s):</span>";
	$kod.="<br><textarea name=szoveg cols=80 rows=50 class=urlap>$leiras</textarea>";

	$kod.="\n<br><br><input type=submit value=Mehet class=urlap></form>";

	return $kod;

}

function addingszent() {
	global $_POST;

	$id=$_POST['id'];
	$nev=$_POST['nev'];
	$ho=$_POST['ho'];
	$nap=$_POST['nap'];
	$szin=$_POST['szinsz'];
	$nevnap=$_POST['nevnap'];
	$intro=$_POST['intro'];
	$leiras=$_POST['szoveg'];

	if($id>0) {
		//M�dos�t�s
		$parameter1='update';
		$parameter2="where id='$id'";
		$uj=false;
	}
	else {
		$parameter1='insert';
		$parameter2='';
		$uj=true;
	}

	$query="$parameter1 szentek set nev='$nev', ho='$ho', nap='$nap', szin='$szin', nevnap='$nevnap', intro='$intro', leiras='$leiras' $parameter2";
	if(!mysql_query($query)) echo 'HIBA!<br>'.mysql_error();
	if($uj) $id=mysql_insert_id();

	$kod.=addszent($id);

	return $kod;
}

function addige($id) {
    global $m_id,$ssid,$m_id;

	$kod.=include('editscript2.php');
	$kod.="<p class=alcim>Napi �nnep, gondolat (�zenet) hozz�ad�sa, m�dos�t�sa</p>";

	if($id>0) {
		$query="select szin,ev,idoszak,nap,oszov_hely,ujszov_hely,evang_hely,unnep,intro,gondolat from igenaptar where id='$id'";
		if(!$lekerdez=mysql_query($query))
			echo '<p>HIBA!<br>'.mysql_error();
		list($szing,$ev,$idoszak,$nap,$oszov_hely,$ujszov_hely,$evang_hely,$unnep,$intro,$gondolat)=mysql_fetch_row($lekerdez);
	}

    $kod.= "<form method=post><input type=hidden name=id value=$id>";
    $kod.= "<input type=hidden name=sid value=$sid>";
    $kod.= "<input type=hidden name=m_id value=$m_id>";
    $kod.= "<input type=hidden name=m_op value=addingige>";

//Id�szak    
    $kod.="\n<span class=kiscim>Id�szak:</span>";
	$kod.="\n<br><SELECT NAME=idoszak class=urlap>";
	$kod.='<option value=a';
	if($idoszak=='a') $kod.=' selected';
	$kod.='>�dventi id�</option><option value=k';
	if($idoszak=='k') $kod.=' selected';
	$kod.='>Kar�csonyi id�</option><option value=n';
	if($idoszak=='n') $kod.=' selected';
	$kod.='>Nagyb�jti id�</option><option value=h';
	if($idoszak=='h') $kod.=' selected';
	$kod.='>H�sv�ti id�</option><option value=e';
	if($idoszak=='e') $kod.=' selected';
	$kod.='>�vk�zi id�</option><option value=s';
	if($idoszak=='s') $kod.=' selected';
	$kod.='>Szent �nnepe</option></select>';

//�v
	$kod.="\n<br><br><span class=kiscim>�v:</span>";
	$kod.='<br><select name=ev class=urlap>';
	$kod.='<option value=0';
	if($ev=='0') $kod.=' selected';
	$kod.='>nincs</option><option value=A';
	if($ev=='A') $kod.=' selected';
	$kod.='>A</option><option value=B';
	if($ev=='B') $kod.=' selected';
	$kod.='>B</option><option value=C';
	if($ev=='C') $kod.=' selected';
	$kod.='>C</option></select>';

//Nap
	$kod.="\n<br><br><span class=kiscim>Nap:</span>";
	$kod.="\n<br><input type=text name=nap value='$nap' size=60 class=urlap maxlength=250>";	

//�nnep  
  	$kod.="\n<br><br><span class=kiscim>�nnep:</span><span class=alap><br>Az adott nap �nnepe, ha van</span>";
	$kod.="\n<br><input tpye=text name=unnep value='$unnep' size=60 maxlength=250 class=urlap>";

//Sz�n
    $kod.="\n<br><br><span class=kiscim>�nnep sz�ne:</span>";
	$kod.="\n<br><select name=szing class=urlap>";
	$kod.="<option";
	if($szing=='feher') $kod.=' selected';
	$kod.=">feher</option><option";
	if($szing=='piros') $kod.=' selected';
	$kod.=">piros</option><option";
	if($szing=='zold') $kod.=' selected';
	$kod.=">zold</option><option";
	if($szing=='lila') $kod.=' selected';
	$kod.=">lila</option></select>";

    $kod.="\n<br><br><span class=kiscim>Napi gondolat (r�viden -> f�oldalon jelenik meg):</span>";
	$kod.="<br><textarea name=intro cols=80 rows=6 class=urlap>$intro</textarea>";

    $kod.="\n<br><br><span class=kiscim>Elm�lked�s r�szletesebben:</span>";
	$kod.="<br><textarea name=szoveg cols=80 rows=40 class=urlap>$gondolat</textarea>";

  	$kod.="\n<br><br><span class=kiscim>Olvasm�ny hely:</span>";
	$kod.="\n<br><input tpye=text name=oszov_hely value='$oszov_hely' size=20 class=urlap>";
	if(!empty($oszov_hely)) {
		$tomb1=explode(',',$oszov_hely);
		$tomb2=explode('-',$tomb1[1]);
		$tomb3=explode(' ',$tomb1[0]);
		$konyv=$tomb3[0];
		$fej=$tomb3[1];
		$vers=$tomb2[0];
		$link="http://www.kereszteny.hu/biblia/showchapter.php?reftrans=1&abbook=$konyv&numch=$fej#$vers";
		$kod.="<a href=$link target=_blank class=link><img src=img/biblia.gif border=0 alt=Biblia align=absmiddle></a>";
	}

    $kod.="\n<br><br><span class=kiscim>Szentlecke hely:</span>";
	$kod.="\n<br><input tpye=text name=ujszov_hely value='$ujszov_hely' size=20 class=urlap>";
	if(!empty($ujszov_hely)) {
		$tomb1=explode(',',$ujszov_hely);
		$tomb2=explode('-',$tomb1[1]);
		$tomb3=explode(' ',$tomb1[0]);
		$konyv=$tomb3[0];
		$fej=$tomb3[1];
		$vers=$tomb2[0];
		$link="http://www.kereszteny.hu/biblia/showchapter.php?reftrans=1&abbook=$konyv&numch=$fej#$vers";
		$kod.="<a href=$link target=_blank class=link><img src=img/biblia.gif border=0 alt=Biblia align=absmiddle></a>";
	}

    $kod.="\n<br><br><span class=kiscim>Evang�lium hely:</span>";
	$kod.="\n<br><input tpye=text name=evang_hely value='$evang_hely' size=20 class=urlap>";
	if(!empty($evang_hely)) {
		$tomb1=explode(',',$evang_hely);
		$tomb2=explode('-',$tomb1[1]);
		$tomb3=explode(' ',$tomb1[0]);
		$konyv=$tomb3[0];
		$fej=$tomb3[1];
		$vers=$tomb2[0];
		$link="http://www.kereszteny.hu/biblia/showchapter.php?reftrans=1&abbook=$konyv&numch=$fej#$vers";
		$kod.="<a href=$link target=_blank class=link><img src=img/biblia.gif border=0 alt=Biblia align=absmiddle></a>";
	}    

	$kod.="\n<br><br><input type=submit value=Mehet></form>";

	return $kod;   
}

function addingige() {
	global $_POST;

	$id=$_POST['id'];
	$idoszak=$_POST['idoszak'];
	$szing=$_POST['szing'];
	$ev=$_POST['ev'];
	$nap=$_POST['nap'];
	$unnep=$_POST['unnep'];
	$intro=$_POST['intro'];
	$gondolat=$_POST['szoveg'];
	$oszov_hely=$_POST['oszov_hely'];
	$ujszov_hely=$_POST['ujszov_hely'];
	$evang_hely=$_POST['evang_hely'];

	if($id>0) {
		//m�dos�t�s
		$uj=false;
		$parameter1='update';
		$parameter2=" where id='$id'";
	}
	else {
		//besz�r�s
		$uj=true;
		$parameter1='insert';
		$parameter2='';
	}

	$query="$parameter1 igenaptar set szin='$szing', ev='$ev', idoszak='$idoszak', nap='$nap', oszov_hely='$oszov_hely', ujszov_hely='$ujszov_hely',  evang_hely='$evang_hely', unnep='$unnep', intro='$intro', gondolat='$gondolat' $parameter2";
	if(!mysql_query($query)) echo 'HIBA<br>'.mysql_error();
	if($uj) $id=mysql_insert_id();

	$kod.=addige($id);

	return $kod;
}

function gondolatok() {
    global $design_url,$db_name,$linkveg,$szin,$m_id,$sessid;


//�j bejegyz�s
    $urlap.= "\n<div><a href=?m_id=$m_id&m_op=addige$linkveg class=link><b> - �j bejegyz�s hozz�ad�sa</b></a></div>";

//M�dos�t�sn�l (vagy kiv�lasztja, vagy kulcssz� alapj�n keresi
    $urlap.= "\n<form method=post><input type=hidden name=m_op value=gondolatokmod>";
	$urlap.= "<input type=hidden name=sessid value=$sessid><input type=hidden name=m_id value=$m_id>";
    $urlap.= "<br><span class=link><b>- Megl�v� bejegyz�s m�dost�sa</b> (keres�s):</span>";

//Teljes lista
	$urlap.="\n<br><br><span class=alap>Konkr�t igenap:</span><br><select name=ige class=urlap><option value=0>Keres�s a lenti mez�k seg�ts�g�vel</option>";
	$query="select id,idoszak,ev,nap from igenaptar order by idoszak asc, ev asc, nap asc";
    if(!$lekerdez=mysql_query($query)) $kod.= '<p class=hiba>HIBA a lek�rdez�sn�l!<br>'.mysql_error();
    while(list($gid,$gidoszak,$gev,$gnap)=mysql_fetch_row($lekerdez)) {
        $kiiras=idoszak($gidoszak);
        $kiiras.=',';
        if($gev!='' and $gev!='0') $kiiras.=" $gev �v,";
        $kiiras.=" $gnap";
        $urlap.= "<option value=$gid";
        //if($ige==$gid) $urlap.= ' selected';
        $urlap.= ">$kiiras</option>";
    }
    $urlap.= '</select>';

	//Id�szak (pl. �dventi id�)
    $urlap.= '<br><br><span class=alap>Id�szak: </span><br><select name=idoszak class=urlap>';
    $urlap.= '<option value=0>Nem tudom</option>';
    $urlap.= '<option value=a>�dventi id�</option><option value=k>Kar�csonyi id�</option>
         <option value=n>Nagyb�jti id�</option><option value=h>H�sv�ti id�</option>
         <option value=e>�vk�zi id�</option><option value=s>Szent �nnepe</option></select>';
    //�v (pl. A �v)
    $urlap.= '<br><br><span class=alap>�v:</span><br><select name=ev class=urlap><option value=0>Nem tudom / nincs</option>
         <option value=A>A �v</option><option value=B>B �v</option><option value=C>C �v</option>
         </select>';

    $urlap.= '<br><br><span class=alap>Kulcssz� (a le�r�sban keres)</span><br><input type=text name=kulcsszo size=25 class=urlap>';
    $urlap.= '<br><br><input type=submit value=Keres class=urlap>';
    $urlap.= '</form>';

	
	$kod="<p class=alcim>Gondolatok szerkeszt�se</p>";
	$kod.=$urlap;

	return $kod;
}

function gondolatokmod() {
    global $_POST,$_GET,$m_id,$linkveg,$db_name;

	$ige=$_POST['ige'];
	if(empty($ige)) $ige=$_GET['ige'];
	$ev=$_POST['ev'];
	if(empty($ev)) $ev=$_GET['ev'];
	$kulcsszo=$_POST['kulcsszo'];
	if(empty($kulcssszo)) $kulcsszo=$_GET['kulcsszo'];
	$idoszak=$_POST['idoszak'];
	if(empty($idoszak)) $idoszak=$_GET['idoszak'];

	//F�c�m
	$kod.="<p class=alcim>Gondolatok m�dos�t�sa</p>";

	$min=$_POST['min'];
	if(!isset($min)) $min=$_GET['min'];
    if(!isset($min)) $min=0;
    $leptet=30;
    $next=$min+$leptet;
    $prev=$min-$leptet;
    if($prev<0) $prev=0;
    
    $keres="select id,szin,ev,idoszak,nap,unnep from igenaptar";
    if($idoszak!='0' and $idoszak!='') $queryT[]="idoszak='$idoszak'";
    if($ev!='0' and $ev!='') $queryT[]="ev='$ev'";
    if($kulcsszo!='' and $kulcsszo!='0') $queryT[]="(intro like '%$kulcsszo%' or gondolat like '%$kulcsszo%' or oszov like '%$kulcsszo%' or ujszov like '%$kulcsszo%' or evang like '%$kulcsszo%')";
	if(is_array($queryT)) {
		$query=implode(' and ',$queryT);
		if(!empty($query)) $query=" where $query";
	}
	if($ige>0) $keres.=" where id='$ige'";
	else $keres.=$query;
    if(!$lekerdez=mysql_db_query($db_name,$keres)) echo 'HIBA<br>'.$keres.'<br>'.mysql_error();
    $mennyi=mysql_num_rows($lekerdez);
    $keres.=" limit $min,$leptet";
    $lekerdez=mysql_query($keres);
    
    $kezd=$min+1;
    if($mennyi==0) $kezd=0;
    if($mennyi>$next) $vege=$next;
    else $vege=$mennyi;
    
    $kod.= '<div class=alcim>Gondolatok m�dos�t�sa</div>';
    $kod.= "<div class=alap><b>Keres�s eredm�nye $mennyi tal�lat</b><br>";
    $kod.= "List�z�s: $kezd - $vege</div>";
    
    while(list($id,$szin,$ev1,$idoszak1,$nap1,$unnep)=mysql_fetch_row($lekerdez)) {
		$kiiras1='';
		$kiiras2='';
        if($idoszak1=='a') $kiiras1=' �dventi';
        elseif($idoszak1=='k') $kiiras1.=' Kar�csonyi';
        elseif($idoszak1=='n') $kiiras1.=' Nagyb�jti';
        elseif($idoszak1=='h') $kiiras1.=' H�sv�ti';
        elseif($idoszak1=='e') $kiiras1.=' �vk�zi';
        $kiiras1.=' id�';
        if($ev1!='0' and $ev1!='') $kiiras2=" $ev1 �v, ";
        else $kiiras2=' ';
        if($nap1!='0') $kiiras2.="$nap1";
        $kod.= "<br><a href=?m_id=$m_id&m_op=addige&id=$id$linkveg class=link>- <b>$kiiras1</b>$kiiras2 ($unnep, $szin)</a>
        <a href=?m_id=$m_id&m_op=delgondolat&id=$id$linkveg><img src=img/del.jpg width=12 height=11
        alt='Gondolat t�rl�se' border=0></a>";

    }

    //L�ptet�s ($tipus,$ido,$ev,$nap,$kulcsszo,$min)
    $kod.= '<p>';
    if($min>0) {
        $x=$leptet;
        $kod.= "<a href='?m_id=$m_id&m_op=gondolatokmod&idoszak=$idoszak&ev=$ev&nap=$nap&kulcsszo=$kulcsszo&min=$prev$linkveg' class=link1>El�z� $x tal�lat</a>";
    }
    if($mennyi>$next) {
        if($min>0) $kod.= ' - ';
        if($mennyi>$next+$leptet) $x=$leptet;
        else $x=$mennyi-$next;
        $kod.= "<a href='?m_id=$m_id&m_op=gondolatokmod&idoszak=$idoszak&ev=$ev&nap=$nap&kulcsszo=$kulcsszo&min=$next$linkveg' class=link1>K�vetkez� $x tal�lat</a>";
    }

	return $kod;
}

function delgondolat() {
    global $m_id,$linkveg,$_GET;

	$id=$_GET['id'];

	$kod.="<p class=alcim>Gondolatok t�rl�se</p>";
    
    $kod.= '<p class=hiba>FIGYELEM! Val�ban t�r�lni akarod a k�vetkez� gondolatot?</p>';
    list($idoszak,$ev,$nap)=mysql_fetch_row(mysql_query("select idoszak,ev,nap from igenaptar where id='$id'"));
    if($idoszak=='a') $kiiras1.=' - �dventi';
    elseif($idoszak=='k') $kiiras1.=' Kar�csonyi';
    elseif($idoszak=='n') $kiiras1.=' Nagyb�jti';
    elseif($idoszak=='h') $kiiras1.=' H�sv�ti';
    elseif($idoszak=='e') $kiiras1.=' �vk�zi';
    $kiiras1.=' id�';
    if($ev!='0' and !empty($ev)) $kiiras2=" $ev �v, ";
    else $kiiras2=' ';
    if($nap!='0') $kiiras2.="$nap";

    $kod.= "<p class=kiscim><i>$kiiras1 - $kiiras2</p>";
    $kod.= '<p class=hiba>T�rl�s ut�n vissza�ll�t�sra nincs lehet�s�g!
         <br><small>T�rl�s helyett adott esetben v�laszthatod a m�dos�t�st is!</small></p>';

    $kod.= "<a href=?m_id=$m_id&m_op=deletegondolat&id=$id$linkveg class=link>T�r�l</a> -
         <a href=?m_id=$m_id&m_op=addgondolat&id=$id$linkveg class=link>M�dos�t�s</a> - <a href=?m_id=$m_id&m_op=gondolatok$linkveg class=link>M�gsem</a>";

	return $kod;
}

function deletegondolat() {
	global $_GET;

	$id=$_GET['id'];

    $query="delete from igenaptar where id='$id'";
    if(!mysql_query($query)) {
        $kod.= '<p class=hiba>HIBA a t�rl�sn�l!<br>'.mysql_error();
    }

	else $kod.=gondolatok();

	return $kod;    
}

function szentek() {
    global $m_id,$linkveg,$sessid;

	$kod.="<p class=alcim>Szentek / �nnepek hozz�ad�sa, m�dos�t�sa</p>";

//�j bejegyz�s
    $kod.= "\n<a href=?m_id=$m_id&m_op=addszent$linkveg class=link><b>- �j bejegyz�s hozz�ad�sa</b></a>";
    $kod.= "\n<br><br><div class=link><b>Megl�v� bejegyz�s m�dost�sa:</b></div>";

//M�dos�t�sn�l (vagy kiv�lasztja, vagy kulcssz� alapj�n keresi
    $kod.= "\n<form method=post><input type=hidden name=m_op value=szentekmod>";
	$kod.= "<input type=hidden name=sessid value=$sessid>";

    $query="select id,nev,ho,nap from szentek order by ho,nap";
    if(!$lekerdez=mysql_query($query)) $kod.= '<p class=hiba>HIBA!<br>'.mysql_error();
    $kod.= '<br><br><span class=alap>Szentek neve:</span> <br><select name=szid class=urlap>';
    $kod.= '<option value=0>--- ink�bb kulcssz� alapj�n keresem ---</option>';
    while(list($szid,$sznev,$ho,$nap)=mysql_fetch_row($lekerdez)) {
        $kod.= "<option value=$szid>$sznev ($ho-$nap)</option>";
    }
    $kod.= '</select>';
    $kod.= '<br><br><span class=alap>Kulcssz� (a n�vben �s a teljes le�r�sban keres)</span><br><input type=text name=kulcsszo size=25 class=urlap>';
    $kod.= '<br><br><input type=submit value=Keres class=urlap>';
    $kod.= '</form>';

	return $kod;
}

function szentekmod() {
    global $m_id,$_GET,$_POST,$linkveg;

	$szid=$_POST['szid'];
	$kulcsszo=$_POST['kulcsszo'];
	if(!isset($kulcsszo)) $kulcsszo=$_GET['kulcsszo'];

	$kod.="<p class=alcim>Szentek / �nnepek m�dos�t�sa</p>";


    if($szid!=0) {
        $query="select nev from szentek where id='$szid'";
        list($nev)=mysql_fetch_row(mysql_query($query));
       $kod.= "<div><a href=?m_id=$m_id&m_op=addszent&id=$szid$linkveg class=link><b>$nev</b> - M�dos�t�s</a> - <a href=?m_id=$m_id&m_op=delszent&id=$szid$linkveg class=link><img src=img/del.jpg border=0> T�r�l</a></div>";
    }
    else {
		$min=$_GET['min'];
        if(!isset($min)) $min=0;
        $leptet=20;
        $next=$min+$leptet;
        $prev=$min-$leptet;
        if($prev<0) $prev=0;

        $query="select id,nev,ho,nap from szentek where nev like '%$kulcsszo%' or leiras like '%$kulcsszo%'";
        $query1=$query." limit $min,$leptet";

        $lekerdez=mysql_query($query);
        $mennyi=mysql_num_rows($lekerdez);
        $kezd=$min+1;
        $vege=$min+$leptet;
        if($vege>$mennyi) $vege=$mennyi;

        $kod.= "<div class=alap><b>�sszesen $mennyi tal�lat</b>
             <br>List�z�s: $kezd - $vege</div>";

        $lekerdez=mysql_query($query1);
        while(list($id,$nev,$ho,$nap)=mysql_fetch_row($lekerdez)) {
            $kod.= "<br><a href=?m_id=$m_id&m_op=addszent&id=$id$linkveg class=link><b>$nev</b> ($ho-$nap) - M�dos�t�s</a> - <a href=?m_id=$m_id&m_op=delszent&id=$id$linkveg class=link>T�r�l</a>";
        }
        $kod.= '<p class=alap>';
        if($min>0) $kod.= " <a href=?m_id=$m_id&m_op=szentekmod&kulcsszo=$kulcsszo&min=$prev$linkveg class=link1>El�z�</a>";
        if($mennyi>$next) $kod.= " <a href=?m_id=$m_id&m_op=szentekmod&kulcsszo=$kulcsszo&min=$next$linkveg class=link1>K�vetkez�</a>";
    }

	return $kod;
}

function delszent() {
    global $m_id,$linkveg,$_GET;

	$id=$_GET['id'];

	$kod.="<p class=alcim>Szentek / �nnepek t�rl�se</p>";

    $kod.= '<p class=hiba>FIGYELEM! Biztosan t�r�lni akarod a k�vetkez� szentet?</p>';

    list($szent)=mysql_fetch_row(mysql_query("select nev from szentek where id='$id'"));
    $kod.= "<p class=kiscim><i>$szent</p>";
    
    $kod.= '<p class=hiba>T�rl�s ut�n vissza�ll�t�sra nincs lehet�s�g!
         <br><small>T�rl�s helyett adott esetben v�laszthatod a m�dos�t�st is!</small></p>';

    $kod.= "<a href=?m_id=$m_id&m_op=deleteszent&id=$id$linkveg class=link>T�r�l</a> -
         <a href=?m_id=$m_id&m_op=addszent&id=$id$linkveg class=link>M�dos�t�s</a> - <a href=?m_id=$m_id&m_op=szentek$linkveg class=link>M�gsem</a>";

	return $kod;
}

function deleteszent() {
	global $_GET;

	$id=$_GET['id'];

    mysql_query("delete from szentek where id='$id'");
    $kod.=szentek();

	return $kod;
}


function naptar($honap,$ev) {
    global $_POST,$_GET,$linkveg,$m_id;

    $kod.= '<span class=alcim>Liturgikus napt�r</span><br><span class=alap><i>Itt kell be�ll�tani az
    aktu�lis liturgikus napt�rnak megfelel�en, hogy az adott naphoz mely szent, illetve gondolat
    tartozik.</i></span><br><br>';
    
    define("EGYNAP", (60*60*24));
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

    $kod.= "<form method=post><input type=hidden name=sessid value=$sessid><input type=hidden name=m_id value=$m_id><input type=hidden name=m_op value=naptar>";
    $kod.= '<select name=ev>';
    for($x=2004;$x<=2010; $x++) {
        $kod.= "<option";
        $kod.= ($x == $ev) ? " selected":"";
        $kod.= ">$x\n";
    }


    $kod.= '</select><select name=honap>';

    $honapok=Array ("Janu�r","Febru�r","M�rcius","�prilis","M�jus","J�nius","J�lius",
               "Augusztus","Szeptember","Okt�ber","November","December");

    for($x=1;$x<=count($honapok); $x++) {
        $kod.= "\t<option value=$x";
        $kod.= ($x == $honap)? " selected":"";
        $kod.= '>'.$honapok[$x-1]."\n";
    }


    $kod.= '</select><input type=submit value=Mutat></form>';

    $napok = Array ("H�tf�","Kedd","Szerda","Cs�t�rt�k","P�ntek","Szombat","Vas�rnap");


    $kod.= '<div align=center class=alcim>'.$ev.'. '.$honapok[$honap-1].'</div><br>';
    $kod.= '<table border=1 cellpadding=2 cellspacing=0>';

    foreach ($napok as $nap) {
      if($nap=='Vas�rnap') $tulajdonsag='bgcolor=#FFF9F9 class=unnep';
      else $tulajdonsag='class=link';
      $kod.= "\t<td width=14% $tulajdonsag align=center><b>$nap</b></td>\n";
    }
    $kiirando=$kezdet;

    for($szamlalo=0;$szamlalo<(6*7);$szamlalo++) {
        $napTomb = getdate($kiirando);
        $moddatum=date('Y-m-d',$kiirando);
        //$katunnep = katolikus �nnep, amikor pirossal �rjuk ki az �nnepet (vas�rnap �s fontosabb �nnepeken)
        if(($szamlalo%7)==6) {
            $unnep=1;
            $katunnep=1;
        }
        else {
            $unnep=0;
            $katunnep=0;
        }

        if((($szamlalo)%7)==0){
            if($napTomb[mon]!=$honap)
              break;
            $kod.= '</tr><tr>';
        }
        //�nnepek:
        if($napTomb[mon]==1 and $napTomb["mday"]==1) {$unnep=1;$katunnep=1;$msg='�j�v';}
        elseif($napTomb[mon]==3 and $napTomb["mday"]==15) {$unnep=1;$msg='Nemzeti �nnep';}
        elseif($napTomb[mon]==8 and $napTomb["mday"]==20) {$unnep=1;$msg='Nemzeti �nnep';}
        elseif($napTomb[mon]==10 and $napTomb["mday"]==23) {$unnep=1;$msg='Nemzeti �nnep';}
        elseif($napTomb[mon]==11 and $napTomb["mday"]==1) {$unnep=1;$katunnep=1;$msg='Mindenszentek �nnepe';}
        elseif($napTomb[mon]==12 and $napTomb["mday"]==25) {$unnep=1;$katunnep=1;$msg='Kar�csony';}
        elseif($napTomb[mon]==12 and $napTomb["mday"]==26) {$unnep=1;$katunnep=1;$msg='Kar�csony';}
        else $msg='';
        if($katunnep==1) $class='unnep1';
        else $class='linkkicsi';

        if($szamlalo<$elsoNapTombje["wday"] || $napTomb["mon"] != $honap) {
            $kod.= '<td><br></td>';
        }
        else {
            $kod.= "\t<td align=center valign=top";
            //Ha �nnep, akkor piros
            $kod.= $unnep==1 ? " bgcolor=#FFF9F9><a class=unnep title='$msg'":" class=link";
            $kod.= '>'.$napTomb["mday"]."</a><a href=?m_id=$m_id&m_op=modnaptar&datum=$moddatum$linkveg class=$class><br>";

            //Megn�zz�k, hogy van-e hozz� esem�ny
			$szent=0;
			$ige=0;
            $nap=$napTomb[mday];
            $datum="$ev-$honap-$nap";
            $query="select ige,szent from lnaptar where datum='$datum'";
            if(!$eredmeny=mysql_query($query))
              $kod.= '<p class=hiba>HIBA a lek�rdez�sn�l!<br>'.mysql_error();
            if(mysql_num_rows($eredmeny)>0) {
                list($ige,$szent)=mysql_fetch_row($eredmeny);
                if($szent>0) {
                    $query_sz="select nev from szentek where id='$szent'";
                    list($sznev)=mysql_fetch_row(mysql_query($query_sz));
                    $kod.= "$sznev";
                }
                elseif($ige>0) {
                    $queryg="select idoszak,ev,nap,unnep from igenaptar where id='$ige'";
                    list($gidoszak,$gev,$gnap,$gunnep)=mysql_fetch_row(mysql_query($queryg));
                    $kiiras=idoszak($gidoszak);
                    $kiiras.=',';
                    if($gev!='' and $gev!='0') $kiiras.=" $gev �v,";
                    $kiiras.=" $gnap";
					if(!empty($gunnep)) $kiiras.="$gunnep unnepe";
                    $kod.= $kiiras;
                }
            }
            if(($szent==0) and ($ige==0)) $kod.= "HOZZ�AD";
            $kod.= "</a>";
            $kiirando += EGYNAP;
            $ujnaptomb=getdate($kiirando);
            if($ujnaptomb[mday]==$napTomb[mday]) $kiirando += EGYNAP;
            $kod.= "</td>\n";
        }
    }
    $kod.= '</tr></table>';

	return $kod;
}

function modnaptar() {
    global $_GET,$linkveg,$m_id,$sid;

	$datum=$_GET['datum'];

	$query="select ige,szent from lnaptar where datum='$datum'";
	list($ige,$szent)=mysql_fetch_row(mysql_query($query));

    $kod.= '<p class=alcim>Liturgikus napt�r - hozz�ad�s, m�dos�t�s</p>';
    $kod.= '<form method=post><input type=hidden name=m_op value=modingnaptar>';
	$kod.= "\n<input type=hidden name=sid value=$sid><input type=hidden name=m_id value=$m_id>";
    $kod.= "<input type=hidden name=id value=$id>";
    $kod.= "<div class=alap><b>D�tum:</b> <input type=text name=datum value='$datum' size=10 class=urlap> <small><font color=red>(Form�tum fontos!)</font></small>";
    $kod.= '<br><br><b>Igenapot mindig k�telez� v�lasztani</b>, <small>ha szent �nnepe van, akkor v�lassz szentet is
   - ekkor a nap �nnep�t ("f�c�m") �s a gondolatot a szent le�r�s�b�l vessz�k. Ha nem v�lasztasz szentet, az igenap megnevez�se lesz a "f�c�m",
    az aznapi szent ilyenkor - ha van, de nem �nnep, csak eml�knap - ez alatt jelenik meg z�r�jelben. Szent �nnep�n az igenapot aszerint kell
    kiv�lasztani, hogy van-e saj�t olvasm�nya vagy a liturgikus �v id�pontj�hoz tartoz� olvasm�nyokat olvass�k a mis�n (ld. direkt�rium ! )</small><br> </div>';

//Gondolat kiv�laszt�sa
	$kod.="\n<br><span class=alap>igenapok:</span><br><select name=ige class=urlap><option value=0>M�g nincs</option>";
	$query="select id,idoszak,ev,nap from igenaptar order by idoszak asc, ev asc, nap asc";
    if(!$lekerdez=mysql_query($query)) $kod.= '<p class=hiba>HIBA a lek�rdez�sn�l!<br>'.mysql_error();
    while(list($gid,$gidoszak,$gev,$gnap)=mysql_fetch_row($lekerdez)) {
        $kiiras=idoszak($gidoszak);
        $kiiras.=',';
        if($gev!='' and $gev!='0') $kiiras.=" $gev �v,";
        $kiiras.=" $gnap";
        $kod.= "<option value=$gid";
        if($ige==$gid) $kod.= ' selected';
        $kod.= ">$kiiras</option>";
    }
    $kod.= '</select>';

//Szentek kiv�laszt�sa
	$ev=date('Y');
	$kod.= "\n<br><br><span class=alap>szentek: </span><br><select name=szent class=urlap><option value=0>Nincs</option>";
    $query="select id,nev,ho,nap from szentek order by ho asc, nap asc";
    if(!$lekerdez=mysql_query($query)) $kod.= '<p class=hiba>HIBA a lek�rdez�sn�l!<br>'.mysql_error();
    while(list($szid,$sznev,$szho,$sznap)=mysql_fetch_row($lekerdez)) {
        $kod.= "<option value=$szid";
	    if($szent==$szid) $kod.= ' selected';
        $kod.= ">($szho-$sznap) $sznev</option>";
    }
    $kod.= '</select>';
	
	$kod.= '<br><br><input type=submit value=Mehet class=urlap></form>';

	return $kod;
}

function modingnaptar() {
    global $_POST,$linkveg,$m_id;

	$datum=$_POST['datum'];
	$ige=$_POST['ige'];
	$szent=$_POST['szent'];


//D�tum ellen�rz�s
    $ev = substr ("$datum", 0, 4);
    $honap = substr ("$datum", 5, 2);
    $nap = substr("$datum", 8, 2);
    if(!checkdate($honap,$nap,$ev)) {
        echo '<p class=hiba>HIBA! Nem l�tez� d�tum.</p>';
        echo '<a href=javascript:history.go(-1); class=link>Vissza</a>';
        exit;
    }

	//Ha van szent, akkor a szent sz�ne a m�rvad�, egy�bk�nt pedig a gondolat sz�ne
	if($szent>0) {
		$query="select szin from szentek where id='$szent'";
		list($szin)=mysql_fetch_row(mysql_query($query));
	}
	else {
		$query="select szin from igenaptar where id='$ige'";
		list($szin)=mysql_fetch_row(mysql_query($query));
	}

	//Van-e m�r ilyen d�tum:
	$lekerdez=mysql_query("select datum from lnaptar where datum='$datum'");
	if(mysql_num_rows($lekerdez)>0) $uj=false;
	else $uj=true;

    //Ha m�dos�t�sr�l van sz�
	if(!$uj) {
		if($szent==0 and $ige==0) {
			//T�r�lj�k
			if(!mysql_query("delete from lnaptar where datum='$datum'"))
				$kod.= '<p class=hiba>HIBA a t�rl�sn�l!<br>'.mysql_error();
		}
		else {
			if(!mysql_query("update lnaptar set ige='$ige', szent='$szent', szin='$szin' where datum='$datum'"))
				$kod.= '<p class=hiba>HIBA a m�dos�t�sn�l!<br>'.mysql_error();
		}
    }	
    else {
        if(!mysql_query("insert lnaptar set ige='$ige', szent='$szent', szin='$szin', datum='$datum'"))
          $kod.= '<p class=hiba>HIBA a r�gz�t�sn�l!<br>'.mysql_error();

    }
    $kod.=naptar($honap,$ev);

	return $kod;
}

if(strstr($u_jogok,'igenaptar')) {

	switch($m_op) {
    
	case 'index':
        $tartalom=igenaptar_index();
        break;

    case "naptar":
		$honap=$_POST['honap'];
		$ev=$_POST['ev'];
        $tartalom=naptar($honap,$ev);
        break;
        
    case "modnaptar":
        $tartalom=modnaptar();
        break;
        
    case "modingnaptar":
        $tartalom=modingnaptar();
        break;
       
    case "szentek":
        $tartalom=szentek();
        break;

    case "szentekmod":
        $tartalom=szentekmod();
        break;
        
    case "delszent":
        $tartalom=delszent();
        break;
        
    case "deleteszent":
        $tartalom=deleteszent();
        break;
        
    case "gondolatok":
        $tartalom=gondolatok();
        break;
        
    case "gondolatokmod":
        $tartalom=gondolatokmod();
        break;
        
    case "delgondolat":
        $tartalom=delgondolat();
        break;
        
    case "deletegondolat":
        $tartalom=deletegondolat();
        break;
	
	case 'addszent':
		$id=$_GET['id'];
		$tartalom=addszent($id);
		break;

	case 'addingszent':
		$tartalom=addingszent();
		break;

	case 'addige':
		$id=$_GET['id'];
		$tartalom=addige($id);
		break;

	case 'addingige':
		$tartalom=addingige();
		break;

	}
}
else {
	$tartalom.="<p class=hiba>HIBA! A v�lasztott modul nem �rhet� el!</p>";
}

?>
