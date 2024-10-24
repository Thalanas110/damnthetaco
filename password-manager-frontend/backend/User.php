<?php

class User 
{
    private $conn;
    private $table_name = "users";

    public $id;
    public $username;
    public $email;
    public $password;
    public $is_admin;

    public function __construct($db) 
    {
        $this->conn = $db;
    }

    public function isEmailRegistered() 
    {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->email);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function register() 
    {
        if ($this->isEmailRegistered()) 
        {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " SET username=:username, email=:email, password=:password, is_admin=:is_admin";
        $stmt = $this->conn->prepare($query);

        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
        $this->is_admin = htmlspecialchars(strip_tags($this->is_admin));

        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":is_admin", $this->is_admin);

        if ($stmt->execute()) 
        {
            return true;
        }

        return false;
    }
}
?>
