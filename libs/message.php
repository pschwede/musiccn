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
        $sql = "CREATE TABLE IF NOT EXISTS message (
            id          INT NOT NULL AUTO_INCREMENT,
            title       VARCHAR(50),
            text        VARCHAR,
            sender      INT,
            receiver    INT,
            timesent    INT DEFAULT 0,
            timeread    INT DEFAULT 0,
            PRIMARY KEY (id)
        )";
        return $db->exec($sql);
    }
    
    public function loadFromDatabase($id) {
        $db = new Database();
        $this->_data = $db->queryArray("SELECT * FROM message WHERE id=$id");
    }
    
    public function submitToDatabase() {
        $db = new Database();
        $query = "INSERT IGNORE message SET ";
        $i = 0;
        foreach($this->_data as $key => $val) {
            if($key != 'id')
                $query .= $key.' = '.$val.($i<count($this->_data)-1?', ':'');
            $i++;
        }
        $db->exec($query);
    }
    
    public function send($from, $to, $message) {
        $this->_data["sender"] = $from;
        $this->_data["receiver"] = $to;
        $this->_data["text"] = $message;
        $this->submitToDatabase();
    }
    
    public function __toString() {
        return $this->_data["title"];
    }
}

?>
