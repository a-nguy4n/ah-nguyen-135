<?php
class savedReportModel {
    private $conn;

    public function __construct() {
        require_once ROOT . '/config.php';
        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $this->ensureTable();
    }

    private function ensureTable() {
        $sql = "CREATE TABLE IF NOT EXISTS saved_reports (
            id INT AUTO_INCREMENT PRIMARY KEY,
            report_type VARCHAR(32) NOT NULL,
            title VARCHAR(255) NOT NULL,
            file_name VARCHAR(255) NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            created_by VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $this->conn->query($sql);
    }

    public function create($reportType, $title, $fileName, $filePath, $createdBy) {
        $stmt = $this->conn->prepare("INSERT INTO saved_reports (report_type, title, file_name, file_path, created_by) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $reportType, $title, $fileName, $filePath, $createdBy);
        return $stmt->execute();
    }

    public function getAll() {
        $result = $this->conn->query("SELECT id, report_type, title, file_name, file_path, created_by, created_at FROM saved_reports ORDER BY created_at DESC");
        if (!$result) {
            return [];
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT id, report_type, title, file_name, file_path, created_by, created_at FROM saved_reports WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result ? $result->fetch_assoc() : null;
    }
}
?>