<?php 
include_once('db.php');

/*
 * A miserend.hu misek t�bl�j�ban berakunk egy id-t, hogy azonos�thani lehessen a mis�ket
 */
$query = "ALTER TABLE misek ADD id INT NOT NULL AUTO_INCREMENT KEY FIRST";
db_query($query,2);

$query = "CREATE TABLE IF NOT EXISTS `terkep_misek_next` (
		`id` int(11) NOT NULL,
		`next` int(11) DEFAULT NULL,
		PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
db_query($query,2);

?>