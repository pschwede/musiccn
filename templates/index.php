<div class="menu stripe">
    <ul class="sf-menu">
        <li>
            Mood
            <ul>
                <li>
                    You are in <i>
                    <?php switch($_SESSION['speed']) {
                    case 1: 
                        switch($_SESSION['energy']) {
                        case -1:    echo 'angry'; break;
                        case 0:     echo 'jaunty'; break;
                        case 1:     echo 'party'; break;
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
                        case 0:     echo 'relaxing'; break;
                        case 1:     echo 'mellow'; break;
                        } 
                        break;
                    }
                    ?></i> mood now.
                </li>
                <li>
                    <div style="width:22em;" id="mood_menu">
                        <div style="width:33%;">
                            <a href="?a=setmood&energy=-1&speed=1">angry</a>
                        </div>
                        <div style="width:33%;">
                            <a href="?a=setmood&energy=0&speed=1">jaunty</a>
                        </div>
                        <div style="width:33%;">
                            <a href="?a=setmood&energy=1&speed=1">party</a>
                        </div>
                        
                        <div style="width:33%;">
                            <a href="?a=setmood&energy=-1&speed=0">melancholic</a>
                        </div>
                        <div style="width:33%;">
                            <a href="?a=setmood&energy=0&speed=0">neutral</a>
                        </div>
                        <div style="width:33%;">
                            <a href="?a=setmood&energy=1&speed=0">happy</a>
                        </div>
                        
                        <div style="width:33%;">
                            <a href="?a=setmood&energy=-1&speed=-1">sad</a>
                        </div>
                        <div style="width:33%;">
                            <a href="?a=setmood&energy=0&speed=-1">relaxing</a>
                        </div>
                        <div style="width:33%;">
                            <a href="?a=setmood&energy=1&speed=-1">mellow</a>
                        </div>
                    </div>
                </li>
            </ul>
        </li>
        <li>
            Genre
            <ul>
                <li>
                    <a href="?a=setgenre&g=rock">Rock</a>
                </li>
                <li>
                    <a href="?a=setgenre&g=electro">Electronical</a>
                </li>
                <li>
                    <a href="?a=setgenre&g=.">Any</a>
                </li>
            </ul>
        </li>
        <li>
            DJs
            <ul>
                <li>
                    <a href="staff.php?a=login" target="_blank">Login</a>
                </li>
                <li>
                    <a href="staff.php?a=register" target="_blank">Register</a>
                </li>
            </ul>
        </li>
        <li>
            <a href="index.php?a=faq" target="_blank">FAQ</a>
        </li>
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
                    <a id="player_play" style="display: none;" title="Play">&#58131;</a>
                    <a id="player_pause" title="Pause">&#58138;</a>
                    <a id="player_stop" title="Stop">&#58139;</a>
                    <a id="player_next" title="Skip">&#58137;</a>
                </div>
                <div id="player_volume">
                    <a id="player_volume_min" title="Mute">&#58561;</a>
                    <a id="player_volume_down" title="Volume Down">&#58540;</a>
                    <a id="player_volume_up" title="Volume Up">&#58538;</a>
                    <a id="player_volume_max" title="Maximum Volume">&#58562;</a>
                </div>
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
