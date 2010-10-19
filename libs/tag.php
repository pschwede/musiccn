<?php
require_once("database.php");

class Tag {
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
        $sql = "CREATE TABLE IF NOT EXISTS tag (
            id          INT NOT NULL AUTO_INCREMENT,
            name        VARCHAR(50),
            track       INT,
            UNIQUE KEY (name),
            PRIMARY KEY (id)
        )";
        return $db->exec($sql);
    }
    
    public function loadFromDatabase($id) {
        $db = new Database();
        $this->_data = $db->queryArray("SELECT * FROM tag WHERE id=$id");
    }
    
    public function submitToDatabase($id) {
        $db = new Database();
        $db->exec("INSERT IGNORE radio SET
            name        =   '".$this->_data['name']."',
            track       =   '".$this->_data['track']."'
            ");
    }
    
    public function __toString() {
        return $this->_data["title"];
    }
}

?>
