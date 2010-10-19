<?php
function parsem3u($myFile) {
	$fh = fopen($myFile, 'r');
	$line = "";
	$i = 1;
	$array = array();
	while($line = fgets($fh)) {
		if(preg_match("/(http:|ftp:).*(mp3|ogg|aac)/",$line)) {
			$track["url"] = $line;
			preg_match("/(?<=\/)[^\/]+(?=\.(mp3|ogg|aac))/", $line, $tmp);
			$track["title"] = str_replace("_"," ",$tmp[0]);
			preg_match("/(?<=\/)[^\/]+(?=\/".$track["title"].")/", $line, $tmp);
			$track["album"] = str_replace("_"," ",$tmp[0]);
			$track["tracknum"] = $i;
			$array[] = $track;
			$i++;
		}
	}
	//print_r($array);
	return $array;
}

function parsepls($myFile) {
	$fh = fopen($myFile, 'r');
	$line = "";
	$i = 1;
	$array = array();
	while($line = fgets($fh)) {
		$track = array();
		if(preg_match("/(http:|ftp:).*(mp3|ogg|aac)/",$line)) {
			$track["url"] = $line;
		}
		if(preg_match("/(?<=Length".$i."=)[0-9]+$/",$line,$len)) {
			$track["length"] = $len[0];
		}
		$track["tracknum"] = $i;
		$array[] = $track;
		$i++;
	}
	//print_r($array);
	return $array;
}

function parsexspf($myFile) {
	$fh = fopen($myFile, 'r');
	$line = "";
	$i = 1;
	$array = array();
	$track = array();
	while($line = fgets($fh)) {
		if(preg_match("/(?<=<creator>).*(?=<\/creator>)/",$line,$regex) && !empty($regex))
			$track["artist"] = $regex[0];	
		if(preg_match("/(?<=<title>).*(?=<\/title>)/",$line,$regex) && !empty($regex))
			$track["title"] = $regex[0];
		if(preg_match("/(?<=<album>).*(?=<\/album>)/",$line,$regex) && !empty($regex))
			$track["album"] = $regex[0];
		if(preg_match("/(?<=<image>).*(?=<\/image>)/",$line,$regex) && !empty($regex))
			$track["cover"] = $regex[0];
		if(preg_match("/(?<=<location>).*(?=<\/location>)/",$line,$regex) && !empty($regex))
			$track["url"] = $regex[0];
		if(preg_match("/(?<=<info>).*(?=<\/info>)/",$line,$regex) && !empty($regex))
			$track["website"] = $regex[0];
		if(preg_match("/(?<=<duration>).*(?=<\/duration>)/",$line,$regex) && !empty($regex)) {
			$track["length"] = $regex[0];
			if($track["length"]/1000 > 0)
				$track["length"] = $track["length"]/1000;
		}
        if(preg_match("/(?<=<genre>).*(?=<\/genre>)/",$line,$regex) && !empty($regex))
			$track["genre"] = $regex[0];
        if(preg_match("/(?<=<tracknum>).*(?=<\/tracknum>)/",$line,$regex) && !empty($regex))
            if($regex[0]>0)
                $track["tracknum"] = $regex[0];
            else
                $track["tracknum"] = $i;
		if(preg_match("/<\/track>/",$line,$regex[0]) && !empty($regex)) {
			$array[] = $track;
			$track = array();
			$i++;
		}
	}
	return $array;
}

function parsepod($myFile) { //TODO!!
	$fh = fopen($myFile, 'r');
	$line = "";
	$i = 1;
	$array = array();
	$track = array();
	while($line = fgets($fh)) {
		if(preg_match("/(?<=<creator>).*(?=<\/creator>)/",$line,$regex) && strlen($regex[0]))
			$track["artist"] = $regex[0];	
		if(preg_match("/(?<=<title>).*(?=<\/title>)/",$line,$regex) && strlen($regex[0]))
			$track["title"] = $regex[0];
		if(preg_match("/(?<=<album>).*(?=<\/album>)/",$line,$regex) && strlen($regex[0]))
			$track["album"] = $regex[0];
		if(preg_match("/(?<=<image>).*(?=<\/image>)/",$line,$regex) && strlen($regex[0]))
			$track["cover"] = $regex[0];
		if(preg_match("/(?<=<location>).*(?=<\/location>)/",$line,$regex) && strlen($regex[0]))
			$track["url"] = $regex[0];
		if(preg_match("/(?<=<info>).*(?=<\/info>)/",$line,$regex) && strlen($regex[0]))
			$track["via"] = $regex[0];
		if(preg_match("/(?<=<duration>).*(?=<\/duration>)/",$line,$regex) && strlen($regex[0])) {
			$track["length"] = $regex[0];
			if($track["length"]/1000 > 0)
				$track["length"] = $track["length"]/1000;
        }
        if(preg_match("/(?<=<genre>).*(?=<\/genre>)/",$line,$regex) && strlen($regex[0]))
			$track["genre"] = $regex[0];
        if(preg_match("/(?<=<tracknum>).*(?=<\/tracknum>)/",$line,$regex) && strlen($regex[0]))
            $track["tracknum"] = $regex[0];
        else
		    $track["tracknum"] = $i;
		if(preg_match("/<\/track>/",$line,$regex[0]) && strlen($regex[0])) {
			$array[] = $track;
			$track = array();
			$i++;
		}
	}
	return $array;
}

function xspfoutput($trackarray) {
    echo "<playlist version='1' xmlns='http://xspf.org/ns/0'>\n";
    echo "<tracklist>\n";
    foreach($trackarray as $track) {
        echo "\t<track>\n";
        echo "\t\t<creator>".$track["artist"]."</creator>\n";
        echo "\t\t<title>".$track["title"]."</title>\n";
        echo "\t\t<album>".$track["album"]."</album>\n";
        echo "\t\t<image>".$track["cover"]."</image>\n";
        echo "\t\t<location>".$track["url"]."</location>\n";
        echo "\t\t<info>".$track["website"]."</info>\n";
        echo "\t\t<duration>".($track["length"]*1000)."</duration>\n";
        echo "\t\t<genre>".$track["genre"]."</genre>\n";
        echo "\t\t<tracknum>".$track["tracknum"]."</tracknum>\n";
        echo "\t</track>\n";
    }
    echo "</tracklist>\n";
    echo "</playlist>\n";
}

function parsejamendo($albumid) {
	if(preg_match("/[0-9]+$/",$albumid,$tmp)) {
		$id = $tmp[0];
		$fh = fopen("http://api.jamendo.com/get2/id+name+duration+artist_genre+album_genre+album_name+album_image+artist_name+artist_url/track/json/track_album+album_artist/?album_id=$id&n=all", 'r');
		$album = json_decode(fgets($fh),true);
		$i = 0;
		$array = array();
		while($i<count($album)) {
			$track["title"] =	$album[$i]["name"];
			$track["tracknum"] =	$i+1;
			$track["artist"] = $album[$i]["artist_name"];
			$track["album"] = $album[$i]["album_name"];
			$track["genre"] = $album[$i]["album_genre"];
			$track["url"] = "http://api.jamendo.com/get2/stream/track/redirect/?id=".$album[$i]["id"]."&streamencoding=mp31";
			$track["length"] = 	$album[$i]["duration"];
			$track["website"] = $album[$i]["artist_url"];
			$track["cover"] = $album[$i]["album_image"];
			$array[] = $track;
			$i++;
		}
	}
	return $array;
}
?>
