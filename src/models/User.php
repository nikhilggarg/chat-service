<?php

class User
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Create user
    public function createUser($username)
    {
        try {
            // Check the username criteria
            /*
                Username criteria:
                1. The name should be longer than 3 characters and shorter than 20 characters.
                2. The username must not contain any whitespace characters.
                3. The username can only contain letters, numbers, underscores, and hyphens.
            */
            // Validate the username criteria
            if (strpos($username, ' ') !== false) {
                throw new \Exception("Usernames cannot contain spaces.");
            }
            if (strlen($username) < 3 || strlen($username) > 20) {
                throw new \Exception("Username must be between 3 and 20 characters.");
            }
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
                throw new \Exception("Username can only contain letters, numbers, underscores, and hyphens.");
            }

            // Check if the user already exists
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                throw new \Exception("User already exists. Please use a different username.");
            }

            // Insert the new user
            $stmt = $this->db->prepare("INSERT INTO users (username) VALUES (:username)");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // Handle any errors
            throw new \Exception("Error: " . $e->getMessage());
        }
    }
}
