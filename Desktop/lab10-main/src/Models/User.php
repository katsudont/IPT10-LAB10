<?php

namespace App\Models;

use App\Models\BaseModel;
use \PDO;

class User extends BaseModel
{
    public function save($username, $email, $first_name, $last_name, $password) {

        
        $sql = "INSERT INTO users (username, email, first_name, last_name, password_hash) 
                VALUES (:username, :email, :first_name, :last_name, :password_hash)";
        
        $statement = $this->db->prepare($sql);
        
        // Hash the password before saving it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Bind parameters
        $statement->bindParam(':username', $username);
        $statement->bindParam(':email', $email);
        $statement->bindParam(':first_name', $first_name);
        $statement->bindParam(':last_name', $last_name);
        $statement->bindParam(':password_hash', $hashed_password);
        
        // Execute the statement
        try {
            $statement->execute();
            return $statement->rowCount(); // Return the number of affected rows
        } catch (\PDOException $e) {
            throw new \Exception("Error: " . $e->getMessage());
        }
    }

    public function getAllUsers()
    {
        $query = "SELECT id, first_name, last_name, email FROM users"; // Adjust according to your table structure
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return data as an associative array
    }

    // Function to retrieve password hash for login verification
    public function getPassword($username) {
        $sql = "SELECT password_hash FROM users WHERE username = :username;";
        $statement = $this->db->prepare($sql);

        $statement->execute(['username' => $username]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['password_hash'] ?? null;
    }

    // Function to get all user data
    public function getData() {
        $sql = "SELECT * FROM users;";
        $statement = $this->db->prepare($sql);

        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_CLASS, '\App\Models\User');
    }
}
