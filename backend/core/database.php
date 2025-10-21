<?php
class Database
{
    private static ?Database $instance = null;
    private string $host = "localhost";
    private string $db_name = "ecosolve_db";
    private string $username = "root";
    private string $password = "";
    private ?PDO $conn = null;

    // Make constructor private to enforce singleton usage
    private function __construct()
    {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $exception) {
            // Re-throw as Exception so calling code can handle it
            throw new Exception("Connection error: " . $exception->getMessage());
        }
    }

    // Return singleton instance
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Return the PDO connection
    public function getConnection(): PDO
    {
        return $this->conn;
    }
}
