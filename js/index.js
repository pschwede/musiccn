var playlist = [];
var currplayer = 1;
var currvolume = 100;

function play(trackid) {
    var track = playlist[playlist.length-1];
    if(trackid){
        for(i=0; i<playlist.length && track.id!=trackid; i++) {
            track = playlist[i];
        }
    }
    $("#jplr"+currplayer).setFile(track.url).play();
    fade(0,currvolume,20,currplayer);
    $("#song_title").fadeOut('fast', function() {
        $("#song_title").text(track.artist+" - "+track.title).fadeIn('fast')
    });
    $("#player_interface").css('background-image','url('+track.cover+')');
}

function autorequest() {
    $.getJSON(
        "ajax.php?genre=<?php echo $_SESSION['genre']; ?>",
        function(json) {
            if(json) {
                playlist.push(json);
                play();
            } else {
                setTimeout("autorequest()", 300);
            }
        });
}

function listened() {
    track = playlist[playlist.length-1];
    if(track && !listened) {
        $.ajax({
                type:       'GET',
                url:        'ajax.php',
                datatype:   'text',
                data:       'a=listened&trackid='+track.id+'&energy=<?php echo $_SESSION['energy']; ?>&speed=<?php echo $_SESSION["speed"]; ?>',
                success:    function(msg) {
                    alert(msg);
                }
        });
        alert("!");
        $('#alert').text("probably sent your LB").show('slow');
        setTimeout("$('#alert').hide('slow')", 3000);
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

function crossFade(playerA,playerB,skipped) {
    // playback B, fade B up and fade down A
    setupPlayer(playerB, true);
    setupPlayer(playerA, false);
    pushTrack(skipped);
    currplayer = playerB;
    autorequest();
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
            '<a href="javascript:play('+track.id+');'+
            'hide('+track.id+');">(play again)</a><a href="'+track.via+'" target="_blank">(website)</a>'+
            '<a href="'+track.url+'" target="_blank">(file)</a></div></div>'+"\n"
            );
        $('#playlist > .track:first').slideDown('slow', function() {
                $(this).css('background-image','url('+track.cover+')');
            });
        if(playlist.length>15) {
            $('#playlist > .track:last').slideUp('slow', function() {
                    $(this).remove();
            });
            playlist.shift();
        }
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
            s = lp<100?"loading "+lp+"%"+" ":'';
            
            var myTotalTime = new Date(tt-pt);
            var ttMin = (myTotalTime.getUTCMinutes() < 10) ? "0" + myTotalTime.getUTCMinutes() : myTotalTime.getUTCMinutes();
            var ttSec = (myTotalTime.getUTCSeconds() < 10) ? "0" + myTotalTime.getUTCSeconds() : myTotalTime.getUTCSeconds();
            $("#total_time").text(s+ttMin+":"+ttSec);
        });
        $("#jplr"+player).onSoundComplete( function() {
            pushTrack(false);
            listened();
            autorequest();
        });
    } else {            
        $("#jplr"+player).onProgressChange( function(lp,ppr,ppa,pt,tt) {});
        $("#jplr"+player).onSoundComplete( function() {});
    }
}

$("document").ready(function() {
    
    $("ul.sf-menu").superfish({
            delay:      300,
            animation:  {height:'show', opacity:'show'},
            speed:      'fast',
            dropShadows:false
    });
    
    for(player=1; player<=2; player++) {
        $("#jplr"+player).jPlayer({
            cssPrefix: "jplayer",
            volume: 0,
            ready:  function() {
                pushTrack();
                autorequest();
                fade(0,100,20,currplayer);
                currvolume=100;
            },
        });            
    }
    
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
        $("div.alert").hide("slow");
    });
});
