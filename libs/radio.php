<?php
require_once("database.php");

class Radio {
    private $_data;
    
    public function __construct($id = null, $data = null) {
        if($id > 0)
            loadFromDatabase($id);
        elseif(isset($data))
            $this->_data = $data;
        return $this;
    }
    
    public function install() {
        $db = new Database();
        $sql = "CREATE TABLE IF NOT EXISTS radio (
            id          INT NOT NULL AUTO_INCREMENT,
            name        VARCHAR(50),
            genre       VARCHAR(100),
            timeadded   INT DEFAULT 0,
            timesrun    INT DEFAULT 0,
            PRIMARY KEY (id)
        )";
        return $db->exec($sql);
    }
    
    public function loadFromDatabase($id) {
        $db = new Database();
        $this->_data = $db->queryArray("SELECT * FROM radio WHERE id=$id");
    }
    
    public function submitToDatabase() {
        $db = new Database();
        $query = "INSERT IGNORE radio SET ";
        foreach($this->_data as $key => $val) {
            if($key != 'id')
                $query .= $key.' = '.$val.', ';
        }
        $query .= 'PRIMARY KEY (id)';
        $db->exec($query);
    }
    
    public function __toString() {
        return $this->_data["title"];
    }
}

?>
