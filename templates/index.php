<div class="menu stripe">
    <ul class="sf-menu">
        <li>
            Mood
            <ul>
                <li>
                    <div style="width:22em;" id="mood_menu">
                        <div style="width:33%;">
                            <a href="javascript:setmood(-1,1);">angry</a>
                        </div>
                        <div style="width:33%;">
                            <a href="javascript:setmood(0,1);">jaunty</a>
                        </div>
                        <div style="width:33%;">
                            <a href="javascript:setmood(1,1);">party</a>
                        </div>
                        
                        <div style="width:33%;">
                            <a  href="javascript:setmood(-1,0);">melancholic</a>
                        </div>
                        <div style="width:33%;">
                            <a  href="javascript:setmood(0,0);">neutral</a>
                        </div>
                        <div style="width:33%;">
                            <a  href="javascript:setmood(1,0);">happy</a>
                        </div>
                        
                        <div style="width:33%;">
                            <a href="javascript:setmood(-1,-1);">sad</a>
                        </div>
                        <div style="width:33%;">
                            <a href="javascript:setmood(0,-1);">relaxing</a>
                        </div>
                        <div style="width:33%;">
                            <a href="javascript:setmood(1,-1);">mellow</a>
                        </div>
                    </div>
                </li>
            </ul>
        </li>
        <li>
            Genre
            <ul>
                <li>
                    <a id="setGenreRock">Rock</a>
                </li>
                <li>
                    <a id="setGenreElectro">Electro</a>
                </li>
                <li>
                    <a id="setGenreAny">Any</a>
                </li>
            </ul>
        </li>
        <li>
            Backstage
            <ul>
                <li>
                    <a href="staff.php?a=login" target="_blank">Login</a>
                </li>
                <li>
                    <a href="staff.php?a=register" target="_blank">Register</a>
                </li>
            </ul>
        </li>
        <!--<li>
            <a href="index.php?a=faq" target="_blank">FAQ</a>
        </li>//-->
    </ul>
</div>
<div class="jquery_jplayer"></div>
<div class="stripe">
    <div class="left">
        <div class="player stripe" id="player_interface">
            <div id="album_cover_box">
                <img src="" alt="" />
            </div>
            <div id="player_control">
                <div id="song_title">Silence</div>
                <div id="player_pps">
                    <a id="player_play" style="display: none;" title="Play">Play</a>
                    <a id="player_pause" title="Pause">Pause</a>
                    <!--<a id="player_stop" title="Stop">&#11035;</a>//-->
                    <a id="player_next" title="Skip">Next</a>
                </div>
                <!--<div id="player_volume">
                    <a id="player_volume_min" title="Mute">&#8857;</a>
                    <a id="player_volume_down" title="Volume Down">&#8854;</a>
                    <a id="player_volume_up" title="Volume Up">&#8853;</a>
                    <a id="player_volume_max" title="Maximum Volume">&#8859;</a>
                </div>//-->
                <div id="play_time"></div>
                <div id="player_progress">
                    <div id="player_progress_load_bar">
                        <div id="player_progress_play_bar"></div>
                    </div>
                </div>
                <div id="total_time"></div>
            </div>
        </div>
        <div id="playlist" class="playlist"></div>
    </div>
    <div class="right">
        <div class="djlist stripe" id="djlist"></div>
        <div class="messages stripe" id="messages"></div>
    </div>
</div>
