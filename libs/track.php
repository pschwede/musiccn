<?php
require_once("database.php");

class Track {
	private $_data;
	
	/** Delivers a Track object or creates one
	**/
	public function __construct($data=null, $id=null) {
        if(isset($id)) {
            $this->loadFromDatabase($id);
        } else {
            $this->_data = $data;
        }
        return $this;
	}
    
    public function install() {
        $db = new Database();
        $sql = "CREATE TABLE IF NOT EXISTS track (
            id          INT NOT NULL AUTO_INCREMENT,
            title       VARCHAR(80) DEFAULT NULL,
            artist      VARCHAR(80) DEFAULT NULL,
            album       VARCHAR(80) DEFAULT NULL,
            url         VARCHAR(150) DEFAULT NULL,
            cover       VARCHAR(150) DEFAULT NULL,
            via         VARCHAR(150) DEFAULT NULL,
            adder       INT,
            duration    SMALLINT DEFAULT NULL,
            replaygain  TINYINT DEFAULT 0,
            genre       VARCHAR(80) DEFAULT NULL,
            energy      FLOAT DEFAULT 0,
            speed       FLOAT DEFAULT 0,
            
            timeadded   INT DEFAULT 0,
            lastplayed  INT DEFAULT 0,
            timesplayed INT DEFAULT 0,
            forcedbydj  INT DEFAULT 0,
            timeforced  INT DEFAULT 0,
            
            PRIMARY KEY(id),
            UNIQUE  KEY(url)
        )";
        return $db->exec($sql);
    }
	
    public function loadFromDatabase($id) {
        $db = new Database();
        $sql = "SELECT * FROM track WHERE id=$id";
        $this->_data = $db->queryArray($sql);
    }
    
    private function cleanup($s) {
        return $s;
    }
    
    public function submitToDatabase() {
        $db = new Database();
        if($this->_data['duration']>120 && $this->_data['duration']<600) {
            $alreadyexists = $db->queryFirst("SELECT COUNT(*) FROM track WHERE url = '".$this->_data['url']."' LIMIT 1");
            //echo '('.$alreadyexists.' @ '.$this->_data['url'].') ';
            if($alreadyexists) {
                $db->exec("UPDATE track SET
                    title       =   \"".$this->cleanup($this->_data['title'])."\",
                    artist      =   \"".$this->cleanup($this->_data['artist'])."\",
                    album       =   \"".$this->cleanup($this->_data['album'])."\",
                    genre       =   \"".$this->cleanup($this->_data['genre'])."\",
                    cover       =   \"".$this->cleanup($this->_data['cover'])."\",
                    via         =   \"".$this->cleanup($this->_data['via'])."\",
                    duration    =   ".$this->cleanup($this->_data['duration'])."
                    WHERE url = '".$this->_data['url']."'");
                return 2;
            } else {
                $query = "INSERT IGNORE track SET 
										title       =   \"".$this->cleanup($this->_data['title'])."\",
                    artist      =   \"".$this->cleanup($this->_data['artist'])."\",
                    album       =   \"".$this->cleanup($this->_data['album'])."\",
                    url 				= 	\"".$this->_data['url']."\",
                    genre       =   \"".$this->cleanup($this->_data['genre'])."\",
                    cover       =   \"".$this->cleanup($this->_data['cover'])."\",
                    via         =   \"".$this->cleanup($this->_data['via'])."\",
                    duration    =   ".$this->cleanup($this->_data['duration']).",
                    timeadded   =   ".time().",
                    adder			  =   ".$_SESSION["id"]."";
                return $db->exec($query);
            }
        } else return -1;
        return 0;
    }
    
	public function __toString() {
         return '<div class="track stripe">'.
            $this->_data["artist"]." - ".$this->_data["title"].
            //'<span class="stats">'.$this->_data["timesplayed"].'x'.$this->_data["energy"].'e'.$this->_data["speed"].'s</span>'.
            '</div>';
	}
    
    public function toArray() {
        return $this->_data;
    }
    
    private function submitKey($key) {
        $db = new Database();
        if(is_string($this->_data[$key]))
            return $db->exec("UPDATE user SET
                $key         =   '".$this->_data[$key]."'
                WHERE id=".$this->_data['id']);
        else
            return $db->exec("UPDATE user SET 
                $key         =   ".$this->_data[$key]."
                WHERE id=".$this->_data['id']);
    }
    
    public function getValue($key) {
        return $this->_data[$key];
    }
    
    public function setValue($key, $value) {
        $this->data[$key] = $value;
        return $this->submitKey($key);
    }
    
    public function incValueBy($key, $dval) {
        $this->data[$key] += $dval;
        return $this->submitKey($key);
    }
    
    public function totalCount() {
        $db = new Database();
        return $db->queryCount("SELECT COUNT(*) FROM track WHERE 1");
    }
}
?>
