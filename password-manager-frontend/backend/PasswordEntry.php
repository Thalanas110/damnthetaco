<?php
class PasswordEntry 
{
    private $conn;
    private $table_name = "passwords";

    public $id;
    public $user_id;
    public $account_type;
    public $company;
    public $email;
    public $password;
    public $is_2fa_on;

    public function __construct($db) 
    {
        $this->conn = $db;
    }

    public function create() 
    {
        $query = "INSERT INTO " . $this->table_name . " SET user_id=:user_id, account_type=:account_type, company=:company, email=:email, password=:password, is_2fa_on=:is_2fa_on";

        $stmt = $this->conn->prepare($query);

        $this->account_type = htmlspecialchars(strip_tags($this->account_type));
        $this->company = htmlspecialchars(strip_tags($this->company));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $this->is_2fa_on = htmlspecialchars(strip_tags($this->is_2fa_on));

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":account_type", $this->account_type);
        $stmt->bindParam(":company", $this->company);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $this->password);
        $stmt->bindParam(":is_2fa_on", $this->is_2fa_on);

        if ($stmt->execute()) 
        {
            return true;
        }
        return false;
    }

    public function getUserPasswords($user_id) 
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        return $stmt;
    }
}
?>
