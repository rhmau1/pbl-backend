<?php
require_once __DIR__ . '/../../config/Database.php';

class User
{
    private $conn;
    private $table_name = "users";

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function createUser($name, $email, $password)
    {
        $query = "INSERT INTO {$this->table_name} (name, email, password_hash, role)
                  VALUES (?, ?, ?, ?)";

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $role = 'user';
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $email, $hashed, $role]);
    }

    public function findByEmail($email)
    {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table_name} WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsers()
    {
        $stmt = $this->conn->prepare(
            "SELECT id, name, email, role FROM {$this->table_name}"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUser($id)
    {
        $stmt = $this->conn->prepare(
            "SELECT id, name, email, role FROM {$this->table_name} WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $name, $email)
    {
        $stmt = $this->conn->prepare(
            "UPDATE {$this->table_name} SET name = ?, email = ? WHERE id = ?"
        );
        return $stmt->execute([$name, $email, $id]);
    }

    public function deleteUser($id)
    {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table_name} WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }
}
