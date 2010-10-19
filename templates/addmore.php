<dl>

<?php switch($_GET['t']) {
case 'jam': ?>
<dt>Add an Album from <a href="http://www.jamendo.com" target="_blank">Jamendo</a>. For that just enter the jamendo id of the album.<dt>
<dd><form action="staff.php?a=addtrack" method="post"><input onClick="this.value=\'\'" type="text" size="50" name="playlistjam" value="jamendoid" /><input type="submit" value="Ok" /></form></dd>
<?php break; 
case 'm3u': ?>
<dt>Import a m3u-file from anywhere in the Internet. You have to add the duration values of each title by yourself.</dt>
<dd><form action="staff.php?a=addtrack" method="post"><input onClick="this.value=\'\'" type="text" size="50" name="playlistm3u" value="m3u" /><input type="submit" value="Ok" /></form></dd>
<?php break; 
case 'pls': ?>
<dt>Import a pls-file from anywhere in the Internet. You have to add the duration values of each title by youself.</dt>
<dd><form action="staff.php?a=addtrack" method="post"><input onClick="this.value=\'\'" type="text" size="50" name="playlistpls" value="pls" /><input type="submit" value="Ok" /></form></dd>
<?php break; 
case 'xspf': ?>
<dt>Import a xspf-file from anywhere in the Internet. You may have to add the duration values of each title by yourself.</dt>
<dd><form action="staff.php?a=addtrack" method="post"><input onClick="this.value=\'\'" type="text" size="50" name="playlistxspf" value="xspf" /><input type="submit" value="Ok" /></form></dd>
<?php break; 
case 'man':
default: ?>
<dt>Enter all data manually.</dt>
<dd><form action="staff.php?a=addtrack" method="post">Number of tracks I'd like to add: <input type="text" maxlength="2" size="2" value="" name="moretracks"> <input type="submit" value="Ok" /></form></dd>
<?php } ?>

</dl>


<p style="font-size: 7pt;"><strong>Note:</strong> This radio is meant to broadcast free music into the world. Every played song has a chance to be twittered. Also, the file host will be visible to everyone. So, please respect the law. Musicon can\'t afford the complete responsibility to it\'s in- and output.</p>
