<?php
function dbconnect() {
    global $config;
    $db_host = $config['connection']['host'];
    $db_uname = $config['connection']['user'];
    $db_upass = $config['connection']['password'];
    $db_name = $config['connection']['database'];

    if(!@mysql_connect($db_host, $db_uname, $db_upass)) {
        if($config['debug'] > 0)  die("Adatbázisszerverhez nem lehet csatlakozni!\n".mysql_error()."\n$idopont");
        else die('Elnézést kérünk, a szolgáltatás jelenleg nem érhető el.');
    }
    
    mysql_query("SET NAMES UTF8");
    
    if(!mysql_select_db($db_name)) {
        if($config['debug'] > 0)  die("Az '".$db_name."' adatbázis nem létezik, vagy nincs megfelelő jogosultság elérni azt!\n".mysql_error()."\n$idopont");
        else die('Elnézést kérünk, a szolgáltatás jelenleg nem érhető el.');
    }

}

function kicsinyites($forras,$kimenet,$max) {

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
            /*imageantialias($dst_img, true);*/

/*          ImageCopyResized($dst_img, $src_img, 0,0,0,0, $new_w, $new_h, ImageSX($src_img), ImageSY($src_img));*/
            ImageCopyResampled($dst_img, $src_img, 0,0,0,0, $new_w, $new_h, ImageSX($src_img), ImageSY($src_img));
            ImageJpeg($dst_img, "$kimenet");
}

function sanitize($text) {
    $text = preg_replace('/\n/i','<br/>',$text);
    $text = strip_tags($text,'<a><i><b><strong><br>');

    return $text;
}

function login($name,$password) {
    $password=base64_encode(sanitize($password));
    $name = sanitize($name);
    $query = "SELECT uid FROM user where login='$name' and jelszo='$password' and ok!='n' LIMIT 11";
    $result = mysql_query($query);
    $x = mysql_fetch_assoc($result);
    if($x == '') { 
        return false;
    }

    cookieSave($x['uid'],$name);
    
    return true;
}

function getuser() {
    $salt = 'Yzsdf';

    $uid = false;
    
    if(isset($_SESSION['auth'])) {
        $tmp = explode(':',$_SESSION['auth']);
        if(count($tmp) == 3) {
            $query = "SELECT uid,login,lejarat FROM session WHERE sessid = '".$_SESSION['auth']."' LIMIT 1 ";
            $result = mysql_query($query);
            $x = mysql_fetch_assoc($result);
            if(is_array($x)) {
                if($tmp[0] == md5($salt . md5($x['uid'] . $salt))) {
                    if($tmp[2] == md5($x['lejarat'])) {
                        $uid = $x['uid'];
                        //cookieSave($x['uid'],$x['name']);
                    }
                }
            }
        }
    }
    if($uid == false AND isset($_COOKIE['auth'])) {
        $tmp = explode(':',$_COOKIE['auth']);
        if(count($tmp) == 3) {
            $query = "SELECT uid,login,lejarat FROM session WHERE sessid = '".$_COOKIE['auth']."' LIMIT 1 ";
            $result = mysql_query($query);
            $x = mysql_fetch_assoc($result);
            if(is_array($x)) {
                if($tmp[0] == md5($salt . md5($x['uid'] . $salt))) {
                    if($tmp[2] == md5($x['lejarat'])) {
                        $uid = $x['uid'];
                        cookieSave($x['uid'],$x['name']);
                    }
                }
            }
        }
    }

    $return = new User($uid);
    return $return;
}

function cookieSave($uid,$name) {
    $salt = 'Yzsdf';
    $identifier = md5($salt . md5($uid . $salt));
    $token = md5(uniqid(rand(), TRUE));
    $timeout = time() + 60 * 60 * 24 * 7;
    setcookie('auth', "$identifier:$token:".md5($timeout), $timeout);
    $query = "DELETE FROM session WHERE uid = ".$uid." AND login = '$name' LIMIT 1;";
    mysql_query($query);
    $query = "INSERT INTO session (uid,login,sessid,lejarat) VALUES (".$uid.",'$name','$identifier:$token:".md5($timeout)."',$timeout);";
    mysql_query($query);
    $_SESSION['auth'] = "$identifier:$token:".md5($timeout);
    $query = "UPDATE user SET lastlogin = ".time()." LIMIT 1;";
    mysql_query($query);
}

function nyelvmeghatarozas() {
    global $modul_url,$linkveg;
//Nyelv meghatározása
    $lang=$_POST['lang'];
    if(!isset($lang)) $lang=$_GET['lang'];
    if($lang=='hu') $lang='';
        
    if(!@include_once("$modul_url/szotar/alapszotar$lang.inc")) {
        $hiba=true;
        $hibauzenet_prog.='<br>Sorry, not translated this language!';
    }

    if(!empty($lang)) {
        $linkveg.="&lang=$lang";
    }
}

function neighboursUpdate($tid = false) {
    global $config;

    $query = 'SELECT szomszedos1, szomszedos2, templomok.id, lng, lat,  templomok.varos, templomok.nev 
            FROM templomok LEFT JOIN terkep_geocode ON id = tid 
            WHERE templomok.ok = "i" ORDER BY frissites DESC ';
    $result = mysql_query($query);
    
    while(($row = mysql_fetch_array($result))) $templomok[$row['id']] = $row;    

    $i = 0;
    foreach($templomok as $templom) { if($tid == false OR $templom['id'] == $tid) { 

        set_time_limit('600');
        $ds10 = $ds = array();
        $c = 0;
        $szomszedsag = array();
        $szomszedsag10 = array();
        foreach($templomok as $szomszed) {
            
            $lat1 = $templom['lat'] * M_PI / 180;
            $lat2 = $szomszed['lat'] * M_PI / 180;
            $long1 = $templom['lng'] * M_PI / 180;
            $long2 = $szomszed['lng'] * M_PI / 180;
            $R = 6371; // km
            $d = $R * acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($long2 - $long1)) * 1000;
            
            if($d < 10000 AND $szomszed['id'] <> $templom['id']) {
                $szomszedsag10[$d] = array('id'=>$szomszed['id'],'d'=>$d,'nev'=>$szomszed['nev'],'varos'=>$szomszed['varos']);
                $ds10[$d] = $d;
                }
            if($szomszed['id'] <> $templom['id']) {
                $szomszedsag[$d] = array('id'=>$szomszed['id'],'d'=>$d,'nev'=>$szomszed['nev'],'varos'=>$szomszed['varos']);            
                $ds[$d] = $d;
            }
            //if($c>10) break; $c++;
        }
        array_multisort($ds10, SORT_ASC, $szomszedsag10);
        array_multisort($ds, SORT_ASC, $szomszedsag);
        
        $szomszedsag = array_slice($szomszedsag, 0, 1); 
        //ksort($szomszedsag10);
        //reset($szomszedsag10);
        $szomszedsag10 = array_slice($szomszedsag10, 0, 11); 
        
        $nyers = '';
        if($config['debug'] > 0) echo " ".$templom['frissites']." <a href=\"http://miserend.hu/?templom=".$templom['id']."\">".$templom['nev']." (".$templom['varos'].")</a><br/>";
        foreach($szomszedsag10 as $szomszed) {
            $nyers .= $szomszed['id'].",";      
            //echo "<div style='margin-left:40px;'>".print_r($szomszed,1)."</div>";
        }
        
        $elso = array_shift(array_values($szomszedsag));
        $elso = "".$elso['id']."";
        if($nyers == '') $nyers = '';
        if($templom['szomszedos1'] == "") {}
        
            $query = "UPDATE templomok SET szomszedos1 = '".$elso."' WHERE id = ".$templom['id']." LIMIT 1";
            if($config['debug'] > 1) echo $query."<br/>";
            mysql_query($query);
            $query = "UPDATE templomok SET szomszedos2 = '".$nyers."' WHERE id = ".$templom['id']." LIMIT 1";
            if($config['debug'] > 1) echo $query."<br/>";
            mysql_query($query);
    } }

}
?>