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

    public function add($username, $password, $role, $sections) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("INSERT INTO users (username, password_hash, role, sections) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hash, $role, $sections);
        return $stmt->execute();
    }

    public function updateRole($id, $role, $sections) {
        $stmt = $this->conn->prepare("UPDATE users SET role = ?, sections = ? WHERE id = ?");
        $stmt->bind_param("ssi", $role, $sections, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>