<html>
<head>
<title>VPP - Súgó</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<style TYPE="text/css">

.alap { font-family: Arial, Verdana; font-size: 10pt; text-align: justify; }

</style>
</head>

<body bgcolor="#FFFFFF" text="#000000">

<?php

include("load.php");

$id=$_GET['id'];
$idT=explode('-',$id);

foreach($idT as $idk) {
	$query="select leiras from sugo where id='$idk'";
	$lekerdez=mysql_query($query);
	list($leiras)=mysql_fetch_row($lekerdez);
	$kod.="<p class=alap>$leiras</p>";
}

$kod.="<div align=center><a href=javascript:close(); class=link><img src=img/bezar.gif border=0 aling=absmiddle> Bezár</a></div>";

echo $kod;


?>
</body></html>