<?php

use core\Database;

class Channel {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getAllChannels(){
        $this->db->query("SELECT * FROM channels WHERE ativo = 1 ORDER BY nome ASC");
        return $this->db->resultSet();
    }
}
