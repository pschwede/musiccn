<?php 
require_once("libs/user.php");
require_once("libs/track.php");
require_once("libs/database.php");

$user = new User(null, $_SESSION['id']);
?>
<div class="content box">
    <h1>Hi, <?php echo $user->getValue('name'); ?></h1>
    <p>
    <?php echo date("z", $user->timeSinceLastSeen())." days, ".
                date("G", $user->timeSinceLastSeen())." hours and ".
                date("I", $user->timeSinceLastSeen())." minutes"; ?>
        gone, since I saw you the last time.
    </p>
</div>
<div class="box">
    <h3>Your statistics</h3>
    <p>Rating: <?php echo $user->getValue("rating"); ?></p>
    <p>added Tracks: <?php echo $user->addedSongs(); ?></p>
</div>
<div class="box">
    <h3>Online are..</h3>
    <?php
        $db = new Database();
        $users = $db->queryArrays("SELECT * FROM user WHERE lasttimeon>".(time() - 5*60).' ORDER BY rating');
        echo '<ol>';
        foreach($users as $usr) {
            echo '<li title="Rating: '.$usr["rating"].'">'.$usr["name"].'</li>';
        }
        echo '</ol>';
    ?>
</div>
<div class="box">
    <h3>Your most successful tracks</h3>
    <p>
        <ol style="list-style-type:decimal outside;">
        <?php
        $i = 1;
        foreach($user->mostSuccessfullTracks() as $track) {
            echo '<div class="" style="width: 100%;">'.
                '<div style="text-align:right; width: 3em; padding-right: 10px">'.$i.'</div>'.
                '<div class="title">'.
                $track->getValue('artist').' - '.$track->getValue('title').'</div>'.
                '<div style="float:right; padding-right: 10px" class="stats">'.$track->getValue('timesplayed').'x; '.($track->getValue('lastplayed')==0?'never':date('z, G:i:s',time()-$track->getValue('lastplayed')-3600).' ago').'</div>'.
                '</div>';
            $i++;
        }
        ?>
        </ol>
    </p>
</div>


