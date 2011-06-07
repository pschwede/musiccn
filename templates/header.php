<?php include 'globalheader.php'; ?>
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
        $("#jplr"+currplayer).jPlayer("setFile", track.url).jPlayer("play");
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
            $.ajax({
                type:       'GET',
                url:        'ajax.php',
                datatype:   'text',
                data:       'a=listened&trackid='+track.id+
                    '&energy=<?php echo $_SESSION["energy"]; ?>'+
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
    
    function skipped() {
        track = playlist[playlist.length-1];
        if(track) {
            $.ajax({
                type:       'GET',
                url:        'ajax.php',
                datatype:   'text',
                data:       'a=skipped&trackid='+track.id+
                    '&energy=<?php echo $_SESSION["energy"]; ?>'+
                    '&speed=<?php echo $_SESSION["speed"]; ?>'+
                    '&genre=<?php echo $_SESSION["genre"]; ?>',
                success:    function(msg) {
                    if(msg=='1') {
                        $('#alert').text("Skipping behavior recorded").slideDown('slow');
                        setTimeout("$('#alert').slideUp('slow')", 3000);
                    } else {
                        $('#alert').text("Error during SBR").slideDown('slow');
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
            $("#jplr"+player).jPlayer("volume", from);
            setTimeout("fade("+from+","+to+","+dvol+","+player+");", 200);
        } else if(from > to && from - dvol > to) {
            from -= dvol;
            $("#jplr"+player).jPlayer("volume", from);
            setTimeout("fade("+from+","+to+","+dvol+","+player+");", 200);
        } else {
            $("#jplr"+player).jPlayer("volume", to);
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
            // find the current track
            for(i=0; i<playlist.length && trackid!=track.id; i++)
                track = playlist[i];
            playlist.push(track);
            play(trackid);
        }
        fade(currvolume,0,10,playerA);
    }
    
    function pushTrack(skipping) {
        if(skipping) {
          skipped();
        } else {
          listened();
        }
        if(playlist.length) {
            var track = playlist[playlist.length-1];
            $('#playlist').prepend(
                '<div style="'+(skipping?'text-decoration: line-through; ':'')+'display:none;" id="'+track.id+'" class="track stripe">'+
                '<div class="title">'+
                track.artist+" - "+track.title+
                '<a href="javascript:replay('+track.id+');" title="Replay">&#9851;</a><a href="'+track.via+'" target="_blank" title="Website">&#8984;</a>'+
                '</div></div>'+"\n"
                );
            $('#playlist > .track:first').css('background-image','url('+track.cover+')').slideDown('slow');
        }
    }
    
    function setupPlayer(player, on) {
        if(on) {
            $("#player_play").click(function() {
                $("#jplr"+player).jPlayer("play");
                $("#player_play").hide();
                $("#player_pause").show();
            });
            $("#player_pause").click(function() {
                $("#jplr"+player).jPlayer("pause");
                $("#player_pause").hide();
                $("#player_play").show();
            });
            $("#player_stop").click(function() {
                $("#jplr"+player).jPlayer("stop");
                $("#player_pause").hide();
                $("#player_play").show();
            });
            
            $("#jplr"+player).jPlayer("onProgressChange", function(lp,ppr,ppa,pt,tt) {
                s = lp<100?" ("+"loading "+lp+"%)":'';
                
                var myTotalTime = new Date(tt-pt);
                $("#total_time").text($.jPlayer.convertTime(myTotalTime));
                if(lp>=99 && pt>=tt-3) {
                  pushTrack(false);
                  autorequest();
                }
            });
            setTimeout("reactOnHangUp(" + $("#jplr"+player).jPlayer("getData", "diag.playedTime") + "," + player + ")", 4000);
        } else {
            $("#jplr"+player).jPlayer("onProgressChange", function(lp,ppr,ppa,pt,tt) {})
            .jPlayer("onSoundComplete", function() {});
        }
    }
    
    function reactOnHangUp(oldt, player) {
      if(oldt == $("#jplr"+player).jPlayer("getData", "diag.playedTime")) {
        if(currplayer == 1)
          crossFade(1,2, true);
        else
          crossFade(2,1, true);
      }
    }
    
    function updateDjList() {
        $.getJSON('ajax.php?a=upcomingdjs'+
                    '&energy=<?php echo $_SESSION["energy"]; ?>'+
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
    
    function setmood(energy, speed) {
      $.ajax({
        type:       'GET',
        url:        'ajax.php',
        datatype:   'text',
        data:       'a=setmood&energy='+energy+'&speed='+speed,
        success:    function(msg) {
                $('#alert').text("You are in a "+msg+" mood now.").slideDown('fast');
                setTimeout("$('#alert').slideUp('slow')", 3000);
                $("body").append("<div id=\"overlay\" />");
                $("body").css({height:'100%'});
                $('#overlay')  
                        .css({  
                            display: 'none',  
                            position: 'absolute',  
                            top:0,  
                            left: 0,  
                            width: '100%',  
                            height: '100%',  
                            zIndex: 1000,  
                            background: $("body").css('background-color')+' url(img/loading.gif) no-repeat center'  
                        })
                        .fadeIn(500, function() {
                            $("#moodstyle").attr({href: "templates/styles/"+msg+".css"});
                          })
                        .fadeOut(500, function() {
                          $(this).remove();
                        });
                /*$("html").fadeOut("slow", function() {
                  /*if(currplayer == 1) {
                    crossFade(1,2,true);
                  } else {
                    crossFade(2,1,true);
                  }
                  $("#moodstyle").attr({href: "templates/"+msg+".css"});
                  $(this).fadeIn("slow");
                  });               */
        }
      });
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
								currvolume=100;
						},
				});
				$("#jplr2").jPlayer({
						cssPrefix: "jplayer",
						volume: 0,
						ready:  function() {
								currvolume=100;
						},
				}); 
        
        setupPlayer(currplayer, true);
        
        $("#player_next").click(function() {
            if(currplayer==1)
                crossFade(1,2,true);
            else
                crossFade(2,1,true);
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
        
        $("#setGenreRock").click(function() {
            $.ajax({
                type:       'GET',
                url:        'ajax.php',
                datatype:   'text',
                data:       'a=setgenre&g=(rock|metal)',
                success:    function(msg) {
                        $('#alert').text("You are listening to Rock now! ").slideDown('slow');
                        setTimeout("$('#alert').slideUp('slow')", 3000);
                }
            });
        })
        $("#setGenreElectro").click(function() {
            $.ajax({
                type:       'GET',
                url:        'ajax.php',
                datatype:   'text',
                data:       'a=setgenre&g=(electro|idm|ance|hop)',
                success:    function(msg) {
                        $('#alert').text("You are listening to Electro now! ").slideDown('slow');
                        setTimeout("$('#alert').slideUp('slow')", 3000);
                }
            });
        })
        $("#setGenreAny").click(function() {
            $.ajax({
                type:       'GET',
                url:        'ajax.php',
                datatype:   'text',
                data:       'a=setgenre&g=.',
                success:    function(msg) {
                        $('#alert').text("You are listening to any genre now! ").slideDown('slow');
                        setTimeout("$('#alert').slideUp('slow')", 3000);
                }
            });
        })
        
        setTimeout('$("#alert").hide("slow");',5000);
        $("div.alert").click(function() {
            $("div.alert").slideUp("slow");
        });
        
        updateDjList();
    });
    </script>
    <body>
        <div id="alert" class="alert" style="top:-1;">
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
