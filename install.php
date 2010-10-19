<?php

require_once("libs/track.php");
require_once("libs/playlist.php");
require_once("libs/radio.php");
require_once("libs/user.php");

/** setup the database
**/

$items[] = new Track(array());
$items[] = new Playlist();
$items[] = new Radio();
$items[] = new User();

$success = true;
foreach($items as $item) {
    $success &= $item->install();
    echo ".";
}
echo "<strong>Installation ".
    ($success?'successful. You might delete this file now.':'failed.').
    "</strong>";
?>
