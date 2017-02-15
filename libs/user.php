<?php
require_once("database.php");
require_once("track.php");

class User {
    private $_data;
    
    public function __construct($data = null, $id=null, $name=null) {
        if(isset($id)) {
            $this->loadFromDatabase($id);
        } elseif(isset($name)) {
            $this->loadFromDatabaseByName($name);
        } else {
            $this->_data = $data;
        }
        return $this;
    }
    
    public function install() {
        $db = new Database();
        $sql = "CREATE TABLE IF NOT EXISTS user (
            id          INT NOT NULL AUTO_INCREMENT,
            name        VARCHAR(50),
            password    VARCHAR(150),
            timejoined  INT DEFAULT 0,
            lasttimeon  INT DEFAULT 0,
            rating      INT DEFAULT 0,
            
            PRIMARY KEY (id),
            UNIQUE  KEY (name)
        )";
        return $db->exec($sql);
    }
    
    public function loadFromDatabase($id) {
        $db = new Database();
        $sql = "SELECT * FROM user WHERE id=$id";
        $this->_data = $db->queryArray($sql);
    }
    
    public function loadFromDatabaseByName($name) {
        $db = new Database();
        $sql = "SELECT * FROM user WHERE name='$name'";
        $this->_data = $db->queryArray($sql);
    }
    
    public function submitToDatabase() {
        $db = new Database();
        return $db->exec("INSERT IGNORE user SET
            name         =   '".$this->_data['name']."',
            password     =   '".$this->_data['password']."',
            timejoined   =   ".max(0,$this->_data['timejoined']).",
            rating       =   ".max(0,$this->_data['rating']).",
            lasttimeon   =   ".max(0,$this->_data['lasttimeon']));
    }
    
    public function __toString() {
        return "";//$this->_data['name'];
    }
    
    public function getValue($key) {
        return $this->_data[$key];
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
    
    public function setValue($key, $value) {
        $this->_data[$key] = $value;
        return $this->submitKey($key);
    }
    
    public function incValueBy($key, $dval) {
        $this->_data[$key] += $dval;
        return $this->submitKey($key);
    }
    
    public function seen() {
        $this->setValue('lasttimeon',time());        
    }
    
    public function timeSinceLastSeen() {
        return time()-$this->_data['lasttimeon']-60*60;
    }
    
    public function mostSuccessfullTracks() {
        $db = new Database();
				$tracks = [];
        $ids = $db->queryArrays("SELECT id FROM track WHERE ".
            "adder=".$this->_data['id']." ".
            "ORDER BY timesplayed DESC, lastplayed DESC LIMIT 10");
        foreach($ids as $id)
            $tracks[] = new Track(null,$id['id']);
        return $tracks;
    }
    
    public function addedSongs() {
        $db = new Database();
        $num = $db->queryFirst("SELECT COUNT(*) FROM track WHERE ".
            "adder=".$this->_data['id']);
        return $num;
    }
}

?>
