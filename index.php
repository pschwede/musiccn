<?php
session_start();

require_once("libs/database.php");

$_GET['radio']=1;

switch($_GET['a']) {
case 'faq':
	include("templates/header.php");
	include("templates/faq.php");
	break;
case 'setmood':
    $_GET['energy'] = max(-1,min(1,$_GET['energy']));
    $_GET['speed'] = max(-1,min(1,$_GET['speed']));
    $_SESSION['energy'] = $_GET['energy'];
    $_SESSION['speed'] = $_GET['speed'];
default:
    include("templates/header.php");
    include("templates/index.php");
    $db = new Database();
    for($i=0; $i<5; $i++) {
        $track = $db->queryArrays("SELECT * FROM track WHERE genre REGEXP '.*".
            $_SESSION["genre"].".*' LIMIT ".
            mt_rand(0,$db->queryFirst("SELECT COUNT(*) FROM track WHERE 1")-1).
            ",1");
        $tracks[] = $track[0];
    }
}

include("templates/footer.php");
?>
