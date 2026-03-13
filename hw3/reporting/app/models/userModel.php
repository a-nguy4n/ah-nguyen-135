<?php
class userModel {
    private $conn;

    public function __construct() {
        require_once ROOT . '/config.php';
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }

    public function getAll() {
        $result = $this->conn->query("SELECT id, username, role FROM users");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>