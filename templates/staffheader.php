<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="Author" content="Musiccn Team" />
    <meta name="robots" content="all" />
    <meta name="description" content="The DIY Web radio station for free music" />
    <meta name="keywords" content="radio, web, flash, music, stream, free, content, creative, commons, jamendo, request, dj, music" />
    <meta name="date" content="2009-03-01" />
    <link rel="stylesheet" type="text/css" href="templates/style.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="templates/neutral.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="templates/player.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="templates/superfish.css" media="screen" />
    <title>Musiccn v1.0 - The different free music discovery machine</title>
    <script type="text/javascript" src="js/jquery-1.3.1.min.js"></script>
    <script type="text/javascript" src="js/superfish.js"></script>
    <script type="text/javascript" src="js/jquery.jplayer.js"></script>
    <script type="text/javascript">
    $("document").ready(function() {
            $("#jplr1").jPlayer({
                    volume: 0
            });
    });    
    </script>
    <body>
        <div id="alert" class="alert">
            <span>
                <?php echo $alert; ?>
            </span>
        </div>
        <div id="jplr1" name="jplr1"></div>
        <div class="head stripe">
            <a href="./staff.php">
                Musiccn
            </a>
        </div>
