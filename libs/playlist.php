<?php
require_once("database.php");

class Playlist {
    private $_data;
    
    public function __construct($data = null) {
        $this->_data = $data;
        return $this;
    }
    
    public function install() {
        $db = new Database();
        $sql = "CREATE TABLE IF NOT EXISTS playlist (
            id          INT NOT NULL AUTO_INCREMENT,
            radio       INT,
            track       INT,
            timeadded   INT DEFAULT 0,
            playstarted INT DEFAULT 0,
            PRIMARY KEY (id)
        )";
        return $db->exec($sql);
    }
    
    public function loadFromDatabase($id) {
        $db = new Database();
        $this->_data = $db->queryArray("SELECT * FROM playlist WHERE id=$id");
    }
    
    public function submitToDatabase() {
        $db = new Database();
        $db->exec("INSERT IGNORE playlist SET
            radio   =   '".$this->_data['radio']."',
            track   =   '".$this->_data['track']."',
            timeadded   =   '".$this->_data['timeadded']."'
            ");
    }
    
    public function __toString() {
        return $this->_data["title"];
    }
}

?>
