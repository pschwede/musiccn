<script type="text/javascript">
<!--
function change_same(name, value, num) {
	elements = document.getElementsByName(name);
	for (var i = num-1; i < elements.length; ++i)
		elements[i].value = value;
}

function set_cover(url) {
	$("#cover img").attr('src', url);
}

function set_album(name) {
	$("#cover span").html(name);
}

function length_assist(num) {
	$("#jplr1").jPlayer("onProgressChange", function(lp,ppr,ppa,pt,tt) {
		$("#length"+num).attr("value", (lp<100?lp+"% wait..":Math.ceil(tt/1000)));
	});
	$("#jplr1").jPlayer("setFile", $("#url"+num).attr("value")).jPlayer("play");
}

function length_assist_all(num) {
	maxnum = <?php echo count($tracks); ?>;
	$("#jplr1").jPlayer("onProgressChange", function(lp,ppr,ppa,pt,tt) {
		$("#length"+num).attr("value", (lp<100?lp+"% wait..":Math.ceil(tt/1000)));
		if(lp>=100 && maxnum >= num+1){
			length_assist_all(num+1);
		}
	});
	$("#jplr1").jPlayer("setFile", $("#url"+num).attr("value")).jPlayer("play");
} 
//-->
</script>
<form method="post" action="staff.php?a=add">
<?php if($tracks) {
    //print_r($tracks);
    $i=1;
    foreach($tracks as $track) { ?>
<div class="box" style="border:1px solid #abf;">
	<table>
        <tr><td>Title*</td><td><input type="text" value="<?php echo $track['title'];?>" name="title[]"/></td></tr></td></tr>
        <tr><td>Tracknumber</td><td><input value="<?php echo ($track['tracknum']?$track['tracknum']:$i);?>" type="text" size="3" maxlength="50" name="tracknum[]" /></td></tr>
        <tr><td>Artist*</td><td><input onchange="change_same(this.name, this.value, <?php echo $i;?>);" value="<?php echo $track['artist'];?>" type="text" maxlength="50" name="artist[]" /></td></tr>
        <tr><td>Album*</td><td><input onchange="set_album(this.value); change_same(this.name, this.value, <?php echo $i;?>);" value="<?php echo $track['album'];?>" type="text" maxlength="50" name="album[]" /></td></tr>
        <tr><td>Genre*</td><td><input onchange="change_same(this.name, this.value, <?php echo $i;?>);" value="<?php echo $track['genre'];?>" type="text" maxlength="50" name="genre[]" /></td></tr>
        <tr><td>Length</td><td><input value="<?php echo ($track['length']?$track['length']:$track['duration']);?>" type="text" size="8" maxlength="50" id="length<?php echo $i;?>" name="length[]" /> <a href="javascript:length_assist(<?php echo $i;?>);">assist</a> <a href="javascript:length_assist_all(<?php echo $i;?>);">all</a></td></tr>
        <tr><td>Cover</td><td><input onchange="set_cover(this.value); change_same(this.name, this.value, <?php echo $i;?>);" value="<?php echo $track['cover'];?>" type="text" maxlength="150" name="cover[]" /></td></tr>
        <tr><td>Website</td><td><input onchange="change_same(this.name, this.value, <?php echo $i;?>);" value="<?php echo ($track['website']?$track['website']:$track['via']);?>" type="text" maxlength="150" name="website[]" /></td></tr>
        <tr><td>url*</td><td><input value="<?php echo $track['url'];?>" type="text" maxlength="150" id="url<?php echo $i;?>" name="url[]" /></td></tr>
	</table>
</div>
<?php 
    $i++; }
}?>
<div class="stripe">
    <label for="answer">Answer this: <?php echo $question ?> = </label>
    <input type="text" name="answer" value="" size="2" />
    <input type="hidden" name="question" value="<?php echo $question ?>" />
    <div id="jquery_jplayer" name="jquery_jplayer"></div>
    <input type="submit" value="Send" />
</div>
</form>
