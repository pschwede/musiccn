<?php
session_start();

require_once("libs/user.php");
require_once("libs/track.php");
require_once("libs/database.php");
require_once("libs/identica.lib.php");

switch($_GET['a']) {
case 'login':
    if(!$_SESSION['online']) {
        if(isset($_POST['name'])) {
            $user = new User(null, null, $_POST['name']);
            if($user->getValue('password') == md5($_POST['pwd'])) {
                // set user online
                $_SESSION['online'] = true;
                $_SESSION['id'] = $user->getValue('id');
            } else {
                // register
                //echo $user->getValue('password')." != ".md5($_POST['pwd']);
                include("templates/staffheader.php");
                include("templates/register.php");
            }
        }
    }
    break;
case 'register':
    if(!$_SESSION['online']) {
        if(strlen($_POST['name'])>2) {
            if(strlen($_POST['pwd'])>2) {
                if($_POST['pwd'] == $_POST['pwd2']) {
                    $user = new User(array(
                        'name'          =>  $_POST['name'],
                        'password'      =>  md5($_POST['pwd']),
                        'timejoined'    =>  time(),
                        'lasttimeon'    =>  0
                        ));
                    if($user->submitToDatabase()) {
                        echo '<div class="stripe"><p>Welcome! '.$_POST['name'].'</p></div>';
                    }
                } else {
                    $alert = "Sorry, password and repeatition aren't equal.";
                }
            } else {
                $alert = "Sorry, your password is too short.";
            }
        } else {
            $alert = "Sorry, your name is too short.";
            include("templates/staffheader.php");
            include("templates/register.php");
        }
    }
    break;
case 'logout':
    $_SESSION = array();
    $alert = "logged out";
}
// continue if user is online
if($_SESSION['online']) {
    $user = new User(null, $_SESSION['id']);
    $user->seen();
    switch($_GET['a']) {
    case 'addmore':
        include("templates/staffheader.php");
        include("templates/staff_menu.php");
        include("templates/addmore.php");
        break;
    case 'edit':
    case 'addtrack':
        require_once("parseplaylist.php");
        if(isset($_POST["moretracks"]) and $_POST["moretracks"]>0) {
            $tracks = array();
            for($i=0; $i<$_POST["moretracks"]; $i++)
                $tracks[] = array("tracknum" => $i+1);
        }
        if($_GET['a']=='edit') {
            $db = new Database();
            $_POST['q'] = str_replace("'",".",$_POST['q']);
            $_POST['q'] = str_replace("(",".",$_POST['q']);
            $_POST['q'] = str_replace(")",".",$_POST['q']);
            $tracks = $db->queryArrays("SELECT * FROM track WHERE album REGEXP '".$_POST["q"]."'");
        }
        elseif(isset($_POST["playlistjam"]) && $_POST["playlistjam"]!="jamendoid")
            $tracks = parsejamendo($_POST["playlistjam"]);
        elseif(isset($_POST["playlistpod"]) && $_POST["playlistpod"]!="podcast")
            $tracks = parsepod($_POST["playlistpod"]);
        elseif(isset($_POST["playlistm3u"]) && $_POST["playlistm3u"]!="m3u")
            $tracks = parsem3u($_POST["playlistm3u"]);
        elseif(isset($_POST["playlistpls"]) && $_POST["playlistpls"]!="pls")
            $tracks = parsepls($_POST["playlistpls"]);
        elseif(isset($_POST["playlistxspf"]) && $_POST["playlistxspf"]!="xspf")
            $tracks = parsexspf($_POST["playlistxspf"]);
        $question = mt_rand(0,10).(mt_rand(0,1)?'+':'-').mt_rand(0,10);
        include("templates/staffheader.php");
        include("templates/staff_menu.php");
        include("templates/addtrack.php");
        break;
    case 'add':
			eval("\$correct = ".$_POST['answer']."==".$_POST['question'].";");
			if($correct) {
        $success = true;
        $successnum = 0;
        $num = count($_POST['tracknum']);
        for($i=0; $i<$num; $i++) {
            $t = new Track(
                array(
                    'title'     =>  $_POST['title'][$i],
                    'artist'    =>  $_POST['artist'][$i],
                    'album'     =>  $_POST['album'][$i],
                    'genre'     =>  $_POST['genre'][$i],
                    'url'       =>  $_POST['url'][$i],
                    'cover'     =>  $_POST['cover'][$i],
                    'via'       =>  $_POST['website'][$i],
                    'adder'     =>  $_SESSION['id'],
                    'duration'  =>  $_POST['length'][$i],
                    'timeadded' =>  time()
                    )
                );
            $stdb = $t->submitToDatabase();
            if($stdb>0)
                $successnum += 1;
        }
        if($successnum < $num) {
            $alert = "Some tracks couldn't be added. Were they already there?";
        } elseif($successnum == $num) {
            /*try {
                $identica = new Identica('musiconradio', 'musicon123');
                $identica->updateStatus("New Album: ".$t->getValue('artist')." - ".$t->getValue('album')." http://music.on.lc #freemusic");
            } catch(exception $e) {}*/
            $alert = "You successfully added $successnum new tracks!";
        } else {
            $alert = "Unsuccessful! Only $successnum tracks added.";
        }
			} else $alert = "Unsuccessful! Wrong answer to antibot question. (".$_POST['answer']."==".$_POST['question'].")";
        include("templates/staffheader.php");
        include("templates/staff_menu.php");
        include("templates/staff.php");
        break;
    case 'explore':
        $db = new Database();
        include("templates/explorer_header.php");
        include("templates/staff_menu.php");
        include("templates/explorer.php");
        break;
    default:
        include("templates/staffheader.php");
        include("templates/staff_menu.php");
        include("templates/staff.php");
    }
} else {
    include("templates/staffheader.php");
    include("templates/login.php");
}
include("templates/footer.php");
?>
