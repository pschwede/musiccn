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
    <?php
    $style = "style";
    switch($_SESSION['speed']) {
    case 1: 
        switch($_SESSION['energy']) {
        case -1:    $style = 'angry'; break;
        case 0:     $style = 'neutral'; break;
        case 1:     $style = 'happy'; break; //partyyy
        } 
        break;
    case 0:
        switch($_SESSION['energy']) {
        case -1:    $style = 'sad'; break;
        case 0:     $style = 'neutral'; break;
        case 1:     $style = 'happy'; break;
        } 
        break;
    case -1:
        switch($_SESSION['energy']) {
        case -1:    $style = 'sad'; break;
        case 0:     $style = 'sad'; break;  // relaxing
        case 1:     $style = 'happy'; break;
        } 
        break;
    }
    echo '<link rel="stylesheet" type="text/css" href="templates/'.$style.'.css" media="screen" />';
    ?>
    <link rel="stylesheet" type="text/css" href="templates/player.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="templates/superfish.css" media="screen" />
    <title>Musiccn v1.0 - The different free music discovery machine</title>
    <script type="text/javascript" src="js/jquery-1.3.1.min.js"></script>
    <script type="text/javascript" src="js/superfish.js"></script>
    <script type="text/javascript" src="js/jquery.jplayer.js"></script>
    <script type="text/javascript" src="js/json2.js"></script>
    <script type="text/javascript">
    var playlist = [];
    var currplayer = 1;
    var currvolume = 100;
    var djsbuffer = null;
    var messagesbuffer = null;
    
    function play(trackid) {
        var track = playlist[playlist.length-1];
        if(trackid){
            for(i=0; i<playlist.length && track.id!=trackid; i++) {
                track = playlist[i];
            }
        }
        $("#jplr"+currplayer).setFile(track.url).play();
        fade(0,currvolume,20,currplayer);
        $("#song_title").fadeOut('fast').text(track.artist+" - "+track.title+" ("+(track.debug?track.debug:'')+")").fadeIn('fast');
        $("#player_interface").css('background-image','url('+track.cover+')');
    }
    
    function replay(trackid) {
        crossFade(currplayer,currplayer==1?2:1,true,trackid);
        $('#'+trackid).slideUp('slow', function() {
                        $(this).remove();
                });
    }
    
    function autorequest(preloadonly) {
        $.getJSON(
            "ajax.php?"+
            "speed=<?php echo $_SESSION['speed']; ?>"+
            "&energy=<?php echo $_SESSION['energy']; ?>"+
            "&genre=<?php echo $_SESSION['genre']; ?>",
            function(json) {
                isin = false;
                for(i=0;i<playlist.length && !isin;i++) {
                    isin |= playlist[i].id == json.id;
                }
                if(isin) {
                    autorequest(preloadonly);
                } else {
                    playlist.push(json);
                    if(!preloadonly)
                        play();
                }
            });
    }
    
    function listened() {
        track = playlist[playlist.length-1];
        if(track) {
            /*alert('a=listened&trackid='+track.id+
                    '&energy=<?php echo $_SESSION['energy']; ?>'+
                    '&speed=<?php echo $_SESSION["speed"]; ?>'+
                    '&genre=<?php echo $_SESSION["genre"]; ?>');*/
            $.ajax({
                type:       'GET',
                url:        'ajax.php',
                datatype:   'text',
                data:       'a=listened&trackid='+track.id+
                    '&energy=<?php echo $_SESSION['energy']; ?>'+
                    '&speed=<?php echo $_SESSION["speed"]; ?>'+
                    '&genre=<?php echo $_SESSION["genre"]; ?>',
                success:    function(msg) {
                    if(msg=='1') {
                        $('#alert').text("Listening behavior recorded").slideDown('slow');
                        setTimeout("$('#alert').slideUp('slow')", 3000);
                    } else {
                        $('#alert').text("Error during LBR").slideDown('slow');
                        setTimeout("$('#alert').slideUp('slow')", 3000);
                    }
                }
            });
        }
    }
    
    function fade(from, to, dvol, player) {
        dvol = dvol>0 ? dvol : -dvol; // integer abs
        if(from < to && from + dvol < to) {
            from += dvol;
            $("#jplr"+player).volume(from);
            setTimeout("fade("+from+","+to+","+dvol+","+player+");", 200);
        } else if(from > to && from - dvol > to) {
            from -= dvol;
            $("#jplr"+player).volume(from);
            setTimeout("fade("+from+","+to+","+dvol+","+player+");", 200);
        } else {
            $("#jplr"+player).volume(to);
        }
    }
    
    function crossFade(playerA,playerB,skipped,trackid) {
        // playback B, fade B up and fade down A
        setupPlayer(playerB, true);
        setupPlayer(playerA, false);
        pushTrack(skipped);
        currplayer = playerB;
        if(!trackid)
            autorequest();
        else {
            var track = playlist[0];
            for(i=0; i<playlist.length && trackid!=track.id; i++)
                track = playlist[i];
            playlist.push(track);
            play(trackid);
        }
        fade(currvolume,0,10,playerA);
        //fade(0,currvolume,10,playerB); //autorequest is doing this
    }
    
    function pushTrack(skipped) {
        if(playlist.length) {
            var track = playlist[playlist.length-1];
            $('#playlist').prepend(
                '<div style="'+(skipped?'text-decoration: line-through; ':'')+'display:none;" id="'+track.id+'" class="track stripe">'+
                '<div class="title">'+
                track.artist+" - "+track.title+
                '<a href="javascript:replay('+track.id+');">(play again)</a><a href="'+track.via+'" target="_blank">(website)</a>'+
                '</div></div>'+"\n"
                );
            $('#playlist > .track:first').css('background-image','url('+track.cover+')').slideDown('slow');
            /*if(playlist.length>15) {
                $('#playlist > .track:last').slideUp('slow', function() {
                        $(this).remove();
                });
                playlist.shift();
            }*/
        }
    }
    
    function setupPlayer(player, on) {
        if(on) {
            $("#player_play").click(function() {
                $("#jplr"+player).play();
                $("#player_play").hide();
                $("#player_pause").show();
            });
            $("#player_pause").click(function() {
                $("#jplr"+player).pause();
                $("#player_pause").hide();
                $("#player_play").show();
            });
            $("#player_stop").click(function() {
                $("#jplr"+player).stop();
                $("#player_pause").hide();
                $("#player_play").show();
            });
            
            $("#jplr"+player).onProgressChange( function(lp,ppr,ppa,pt,tt) {
                s = lp<100?" ("+"loading "+lp+"%)":'';
                
                var myTotalTime = new Date(tt-pt);
                var ttMin = (myTotalTime.getUTCMinutes() < 10) ? "0" + myTotalTime.getUTCMinutes() : myTotalTime.getUTCMinutes();
                var ttSec = (myTotalTime.getUTCSeconds() < 10) ? "0" + myTotalTime.getUTCSeconds() : myTotalTime.getUTCSeconds();
                $("#total_time").text(ttMin+":"+ttSec+s);
                if(lp>=99 && pt>=tt-3) {
									listened();
									pushTrack(false);
									autorequest();
								}
            });
            /*$("#jplr"+player).onSoundComplete( function() {
                listened();
                pushTrack(false);
                autorequest();
            });*/ // somehow inaccurate :/
        } else {            
            $("#jplr"+player).onProgressChange( function(lp,ppr,ppa,pt,tt) {});
            $("#jplr"+player).onSoundComplete( function() {});
        }
    }
    
    function updateDjList() {
        $.getJSON('ajax.php?a=upcomingdjs'+
                    '&energy=<?php echo $_SESSION['energy']; ?>'+
                    '&speed=<?php echo $_SESSION["speed"]; ?>'+
                    '&genre=<?php echo $_SESSION["genre"]; ?>',
                function(json) {
                    jsonstring = JSON.stringify(json);
                    if(djsbuffer != jsonstring) {
                        list="";
                        for(i=0; i<json.length; i++) {
                            list += '<li>'+json[i]["name"]+'</li>'
                        }
                        if(list.length)
                            $('#djlist').fadeOut().html(
                                '<h3>Upcoming DJs</h3>'+
                                '<ul>'+list+'</ul>'
                                ).fadeIn('slow');
                        else 
                            $('#djlist').slideUp();
                        djsbuffer = jsonstring;
                    }
                }
            );
        setTimeout('updateDjList();',5000);
    }
    
    function updateMessages() {
        $.getJSON('ajax.php?a=messages'+
                    '&energy=<?php echo $_SESSION['energy']; ?>'+
                    '&speed=<?php echo $_SESSION["speed"]; ?>'+
                    '&genre=<?php echo $_SESSION["genre"]; ?>',
                function(json) {
                    jsonstring = JSON.stringify(json);
                    if(messagesbuffer != jsonstring) {
                        messagesbuffer = jsonstring;
                        list="";
                        for(i=0; i<json.length; i++) {
                            list += '<li>'+
                                json[i]["timesent"]+
                                '<ul><li>'+
                                json[i]["text"]+
                                '</li></ul>'+
                                '</li>'
                        }
                        $('#djlist').hide().html(
                            '<h3>Messages</h3>'+
                            '<ul>'+list+'</ul>'
                            ).show('slow');
                    }
                }
            );
        setTimeout('updateMessages();',600);
    }
    
    $("document").ready(function() {
        
        $("ul.sf-menu").superfish({
                delay:      300,
                animation:  {height:'show', opacity:'show'},
                speed:      'fast',
                dropShadows:false
        });
        
        $("#jplr1").jPlayer({
						cssPrefix: "jplayer",
						volume: 0,
						ready:  function() {
								autorequest();
								fade(0,100,20,currplayer);
								currvolume=100;
						},
				});
				$("#jplr2").jPlayer({
						cssPrefix: "jplayer",
						volume: 0,
						ready:  function() {
								autorequest();
								fade(0,100,20,currplayer);
								currvolume=100;
						},
				}); 
        
        setupPlayer(currplayer, true);
        
        $("#player_next").click(function() {
            if(currplayer==1)
                crossFade(1,2,true);
            else
                crossFade(2,1,true);
            /*pushTrack(true); //old
            autorequest();*/
        });
        $("#player_volume_min").click(function() {
            fade(currvolume,0,50,currplayer);
            currvolume=0;
        })
        $("#player_volume_max").click(function() {
            fade(currvolume,100,10,currplayer);
            currvolume=100;
        })
        $("#player_volume_up").click(function() {
            fade(currvolume,Math.min(100,currvolume+10),5,currplayer);
            currvolume=Math.min(100,currvolume+10);
        })
        $("#player_volume_down").click(function() {
            fade(currvolume,Math.max(0,currvolume-10),5,currplayer);
            currvolume=Math.max(0,currvolume-10);
        })
        
        setTimeout('$("#alert").hide("slow");',5000);
        $("div.alert").click(function() {
            $("div.alert").slideUp("slow");
        });
        
        updateDjList();
    });
    </script>
    <body>
        <div id="alert" class="alert">
            <span>
            <?php echo $alert; ?>
            </span>
        </div>
        <div id="jplr1" name="jplr1"></div>
        <div id="jplr2" name="jplr2"></div>
        <div class="head stripe">
            <a href="./">
                Musiccn
            </a>
        </div>
