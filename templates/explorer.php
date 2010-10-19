<div class="stripe">
    <div style="width:33%; float:left;">
        <input title="genres" id="genresearchbar" type="text" style="width:100%;">
    </div>
    <div style="width:33%; float:left;">
        <input title="artists" id="albumsofgenresearchbar" type="text" style="width:100%;">
    </div>
    <div style="width:33%; float:left;">
        <input title="titles" id="titlesofalbumsearchbar" type="text" style="width:100%;">
    </div>
</div>
<div class="explorer stripe" id="explorer">
    <!--<div style="overflow:hidden; width:100%; height:18px;" class="searchbar">
        <input id="generalsearchbar" type="text" style="width:100%;">
    </div>//-->
    <div>
        <ul id="genre"></ul>
    </div>
    <div>
        <ul id="albumsofgenre"></ul>
    </div>
    <div>
        <ul id="titlesofalbum"></ul>
    </div>
</div>
<div class="newest stripe" id="newest">
<?php
require_once("libs/database.php");
$tracks = $db->queryArrays("SELECT * FROM track WHERE 1 GROUP BY album ORDER BY timeadded DESC LIMIT 10");
foreach($tracks as $track) {
    $album = str_replace("'",".",$track['album']);
    $album = str_replace("(",".",$album);
    $album = str_replace(")",".",$album);
    echo '<a href="javascript:loadList(\'titlesofalbum\',\''.$album.'\')"><div class="album"><img src="'.$track['cover'].'" alt=""><span>'.$track['artist'].' - '.$track['album'].'</span></div></a>';
}
?>
</div>
