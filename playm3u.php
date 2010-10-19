<?php
require_once "libs/database.php";

$db = new Database();
$track = $db->queryArray("SELECT * FROM track WHERE 1 LIMIT ".
    mt_rand(1, $db->queryFirst("SELECT COUNT(*) FROM track WHERE 1")-1).
    ",1");

echo "#EXTM3U\n";
echo "#EXTINF:".$track["length"].",".$track["artist"]." - ".$track["title"]."\n";
echo $track["url"]."\n";
echo "#EXTINF:1,Please rate the tracks on music.on.lc!\n";
echo $_SERVER['SCRIPT_URI'];
?>
