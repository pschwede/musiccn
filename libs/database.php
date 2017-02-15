<?php

class Database {
    private $_db_user	= "root";
    private $_db_pwd	= "dummy";
    private $_db_name   = "musiccn";
    private $_db_url    = "localhost";
    private $_db;
    
    /** Makes Database available
    **/
    public function __construct() {
        $this->_db = new mysqli(
            $this->_db_url, 
            $this->_db_user,
            $this->_db_pwd,
            $this->_db_name);
        if (mysqli_connect_errno())
            die("Connection refused: ". mysqli_connect_error()."<b /r>".
                "You probably need to create a ".
                "Database named '".$this->_db_name."'!");
    }
    
    public function __destruct() {
		mysqli_close($this->_db);
	}
    
    /** Executes MYSQL code
    **/
    public function exec($sql) {
        if(!$this->_db->query($sql))
            die("DB Error: ".mysqli_error($this->_db).
                " caused by '".$sql."'");
        return true;
    }
    
    /** Loads array of results of the MYSQL-Query from Database
    **/
    public function queryArray($sql, $mode=MYSQLI_ASSOC) {
			  $return = "";
        if($res = $this->_db->query($sql)) {
            try {
                $return = $res->fetch_array($mode);
            } catch(Exception $e) {
                return null;
            }
        }
        return $return;
    }
    
    /** Loads array of arrays of results of the MYSQL-Query from Database
    **/
    public function queryArrays($sql, $mode=MYSQLI_ASSOC) {
        $return = array();
        if($res = $this->_db->query($sql))
            while($rarr = $res->fetch_array($mode))
                $return[] = $rarr;
        return $return;
    }
    
    /** Loads first result of the MYSQL-Query fom Database only
    **/
    public function queryFirst($sql) {
        $res = $this->_db->query($sql);
        if($res && !$return = $res->fetch_array(MYSQLI_NUM))
            return null;
            /*die("DB Error: ".mysqli_error($this->_db).
                " caused by '".$sql."'");*/
        return $return[0];
    }
    
    /** Returns Data as MYSQLI-Result object
    **/
    public function query($sql) {
        if(!$return = $this->_db->query($sql))
            die("DB Error: ".mysqli_error($this->_db).
                " caused by '".$sql."'");
        return $return;
    }
}

?>
