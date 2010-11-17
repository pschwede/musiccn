<?php
require_once("libs/database.php");
require_once("libs/track.php");
require_once("libs/user.php");
require_once("libs/identica.lib.php");

$db = new Database();

switch($_GET['a']) {
case 'listened':
    if($_GET['trackid']) {
        $track = new Track(null, $_GET["trackid"]);
        /*if($track->getValue('adder')) {
            $user = new User(null, $track->getValue('adder'));
            $user->incValueBy('rating',1) or die(-1);
        }*/
        if($track->getValue('forcedbydj')) {
            $user = new User(null, $track->getValue('forcedbydj'));
            $user->incValueBy('rating',1) or die(-1);
        }
        if($_GET['energy']!=0 || $_GET['speed']!=0) {
            // make sure neutral players don't mess up the data
            $sql = "UPDATE track SET ".
                "energy=(energy * 10+(".intval($_GET['energy']).")) / (11), ".
                "speed=(speed * 10+(".intval($_GET['speed']).")) / (11), ".
                "timesplayed=timesplayed+1, ".
                "forcedbydj=0, ".
                "lastplayed=".time()." WHERE id=".$_GET['trackid'];
        } else {
            $sql = "UPDATE track SET ".
                "timesplayed=timesplayed+1, ".
                "forcedbydj=0, ".
                "lastplayed=".time()." WHERE id=".$_GET['trackid'];
        }
        echo $db->exec($sql);
        if(!mt_rand(0,5)) {
			try {
                $identica = new Identica('musiconradio', 'musicon123');
                $identica->updateStatus("!listen: ".$track->getValue('artist')." - ".$track->getValue('album')." ".$track->getValue('via')." #freemusic");
            } catch(exception $e) {}
		}
    } else echo -1;
    break;
case 'bestofall':
    switch($_GET['t']) {
    default:
        if(!$_GET['num'])
            $_GET['num'] = 5;
        $tracks = $db->queryArrays("SELECT * FROM track WHERE 1 ".
            "ORDER BY timesplayed DESC, lastplayed DESC LIMIT ".intval($_GET['from']).','.intval($_GET['from']+$_GET['num']));
        echo json_encode($tracks);
    }
    break;
case 'explore':
    $_GET['q'] = str_replace("'",".",$_GET['q']);
    $_GET['q'] = str_replace("(",".",$_GET['q']);
    $_GET['q'] = str_replace(")",".",$_GET['q']);
    switch($_GET['f']) {
    case 'title':
        $rows = $db->queryArrays("SELECT * FROM track WHERE title REGEXP '.*".$_GET["q"].".*' GROUP BY title");
        echo json_encode($rows);
        break;
    case 'artist':
        $rows = $db->queryArrays("SELECT * FROM track WHERE artist REGEXP '.*".$_GET["q"].".*' GROUP BY album");
        echo json_encode($rows);
        break;
    case 'albumsofartist':
        $rows = $db->queryArrays("SELECT * FROM track WHERE artist REGEXP '.*".$_GET["q"].".*' GROUP BY album");
        echo json_encode($rows);
        break;
    case 'albumsofgenre':
        $rows = $db->queryArrays("SELECT * FROM track WHERE genre REGEXP '.*".$_GET["q"].".*' GROUP BY genre");
        echo json_encode($rows);
        break;
    case 'titlesofalbum':
        $rows = $db->queryArrays("SELECT * FROM track WHERE album REGEXP '.*".$_GET["q"].".*' ORDER BY id");
        echo json_encode($rows);
        break;
    case 'genre':
    default:
        $genres = array();
        $rows = $db->queryArrays("SELECT genre FROM track WHERE genre REGEXP '.*".$_GET["q"].".*' GROUP BY genre");
        foreach($rows as $row) {
            foreach(split(' ',$row['genre']) as $val1)
                foreach(split(',',$val1) as $val)
                    if(strlen($val)>2 && !in_array(ucfirst($val),$genres))
                        $genres[] = ucfirst($val);
        }
        sort($genres);
        echo json_encode($genres);
    }
    break;
case 'upcomingdjs':
    $djids = $db->queryArray("SELECT forcedbydj FROM track WHERE timeforced+duration>".time());
    if($djids) {
        foreach($djids as $djid) {
            $users[] = $db->queryArray("SELECT * FROM user WHERE id=".$djid);
        }
        echo json_encode($users);
    } else echo "[]";
    break;
case 'messages':
    $messages = $db->queryArray("SELECT * FROM messages WHERE timeforced>".time());
    if($messages) {
        echo json_encode($messages);
    } else echo "[]";
    break;
case 'force':
    $time = max(time(),
                $db->queryFirst(
                "SELECT timeforced+duration FROM track WHERE timeforced+duration>".time()." and forcedbydj>0 ORDER BY timeforced+duration DESC LIMIT 1")
                );
    $query = "UPDATE track SET ".
            "forcedbydj = ".$_GET['dj'].", ".
            "timeforced = ".$time.
            " WHERE timeforced+duration<$time and id = ".$_GET["trackid"];
    if($_GET['trackid'] && $_GET['dj']) {
        if($db->exec($query))
            echo ($time-time()==0?1:$time-time());
        else
            echo $query;
    } else echo $query;
    break;
case 'setgenre':
    $_SESSION["genre"] = $_GET["g"];
    echo 1;
    break;
case 'setmood':
    $_SESSION["speed"] = $_GET["speed"];
    $_SESSION["energy"] = $_GET["energy"];
    switch($_SESSION['speed']) {
    case 1: 
        switch($_SESSION['energy']) {
        case -1:    echo 'angry'; break;
        case 0:     echo 'jaunty'; break;
        case 1:     echo 'party'; break; //partyyy
        } 
        break;
    case 0:
        switch($_SESSION['energy']) {
        case -1:    echo 'melancholic'; break;
        case 0:     echo 'neutral'; break;
        case 1:     echo 'happy'; break;
        } 
        break;
    case -1:
        switch($_SESSION['energy']) {
        case -1:    echo 'sad'; break;
        case 0:     echo 'relaxing'; break;  // relaxing
        case 1:     echo 'mellow'; break;
        } 
        break;
    default:
        echo 'neutral'+$_SESSION["speed"]+""+$_SESSION["energy"];
    }
    break;
default:
    if(mt_rand(0,2)) { //50% // 80%
        if(empty($track)) {
            // Get conform music which is forced by dj and is in time
            $query = "genre REGEXP '.*".$_GET["genre"].".*'";
            if($_GET["energy"]!=0)
                $query .= " and energy".(intval($_GET['energy'])>0?'>':'<')."0 ";
            if($_GET["speed"]!=0)
                $query .= " and speed".(intval($_GET['speed'])>0?'>':'<')."0 ";
            $query .= " and forcedbydj>0 and".
                " timeforced+duration>".time()." and".
                " ".time().">timeforced ORDER BY timeforced ASC LIMIT 1";
            $track = $db->queryArray("SELECT * FROM track WHERE ".$query);
            if(isset($track))
                $track["debug"] = "forced";
        }
    }
    if(!mt_rand(0,2) && empty($track)) {
        // Get conform music
        $query = "genre REGEXP '.*".$_GET["genre"].".*'";
        if($_GET["energy"]!=0)
            $query .= " and energy".(intval($_GET['energy'])>0?'>':'<')."0 ";
        if($_GET["speed"]!=0)
            $query .= " and speed".(intval($_GET['speed'])>0?'>':'<')."0 ";
        $query .= " and lastplayed<".(time()-$db->queryFirst("SELECT SUM(duration) FROM track WHERE 1"));
        $query .= " LIMIT ".
            mt_rand(0, 
                $db->queryFirst(
                  "SELECT COUNT(*) FROM track WHERE ".$query
                  )-1
                ).
            ",1";
        $track = $db->queryArray("SELECT * FROM track WHERE ".$query);
        if(isset($track))
            $track["debug"] = "conform";
    }
    if(mt_rand(0,3)==0) {
        if(empty($track)) {
            // Get forced tracks in time which haven't been assigned to any mood
            $query = "(timesplayed=0 or (speed=0 and energy=0)) and forcedbydj>0".
                " and timeforced+duration>".time().
                " and ".time().">timeforced ORDER BY timeforced DESC";
            $query .= " LIMIT ".mt_rand(0,$db->queryFirst("SELECT COUNT(*) FROM track WHERE ".$query)-1).",1";
            $track = $db->queryArray("SELECT * FROM track WHERE ".$query);
            if(isset($track))
                $track["debug"] = "forced w/o no mood";
        }
    }
    if(mt_rand(0,2)==0) {
        if(empty($track)) {
            // Get old forced tracks with genre which haven't been played yet but may fit to the mood
            $query = "genre REGEXP '.*".$_GET["genre"].".*' ";
            $query .= " and timesplayed=0 and forcedbydj>0";
            $query .= " LIMIT ".mt_rand(0,$db->queryFirst("SELECT COUNT(*) FROM track WHERE ".$query)-1).",1";
            $track = $db->queryArray("SELECT * FROM track WHERE ".$query);
            if(isset($track))
                $track["debug"] = "old forced never played";
        }
    }
    if(mt_rand(0,2)==0) {
        if(empty($track)) {
            // Get old forced tracks
            $query = "forcedbydj>0";
            $query .= " LIMIT ".mt_rand(0,$db->queryFirst("SELECT COUNT(*) FROM track WHERE ".$query)-1).",1";
            $track = $db->queryArray("SELECT * FROM track WHERE ".$query);
            if(isset($track))
                $track["debug"] = "old forced never played";
        }
    }
    if(empty($track)) {   
        // Get random unplayed track with genre
        $track = $db->queryArray("SELECT * FROM track WHERE genre REGEXP '.*".$_GET["genre"].".*' and timesplayed=0 and forcedbydj>0 LIMIT ".
            mt_rand(1, $db->queryFirst("SELECT COUNT(*) FROM track WHERE genre REGEXP '.*".$_GET["genre"].".*'")-1).
            ",1");
        if(isset($track))
            $track["debug"] = "random forced w/ genre";
    }
    if(empty($track)) {   
        // Get random unplayed track
        $track = $db->queryArray("SELECT * FROM track WHERE genre REGEXP '.*".$_GET["genre"].".*' and timesplayed = 0 LIMIT ".
            mt_rand(1, $db->queryFirst("SELECT COUNT(*) FROM track WHERE genre REGEXP '.*".$_GET["genre"].".*' and timesplayed = 0")-1).
            ",1");
        if(isset($track))
            $track["debug"] = "random unplayed w/ genre";
    }
    if(empty($track)) {
        // Get some totally random track
        $track = $db->queryArray("SELECT * FROM track WHERE 1 LIMIT ".
            mt_rand(1, $db->queryFirst("SELECT COUNT(*) FROM track WHERE 1")-1).
            ",1");
        if(isset($track))
            $track["debug"] = "pure randomness!";
    }
    echo json_encode($track);
}
?>
