<?php

class Group
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Create the group
    public function createGroup($name, $userId)
    {
        try {
            // Validate user_id
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $userExists = $stmt->fetchColumn();

            if ($userExists == 0) {
                throw new \Exception("User is not registered.");
            }

            // Check the group name criteria
            /*
                Group name criteria:
                1. The name should be longer than 3 characters.
                2. The group name must not contain any leading or trailing whitespaces.
            */
            if (strlen($name) < 3) {
                throw new \Exception("Group name must be longer than 3 characters.");
            }
            if (trim($name) !== $name) {
                throw new \Exception("Group name must not contain leading or trailing whitespace.");
            }

            // Check if the group already exists
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM groups WHERE name = :name");
            $stmt->bindParam(':name', $name);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                throw new \Exception("Group with this name already exists");
            }

            // Insert the new group
            $stmt = $this->db->prepare("INSERT INTO groups (name) VALUES (:name)");
            $stmt->bindParam(':name', $name);
            $stmt->execute();
            $groupId = $this->db->lastInsertId();

            return $groupId;
        } catch (PDOException $e) {
            // Error
            return "Error: " . $e->getMessage();
        }
    }

    // Allows a user to join a group
    public function joinGroup($groupId, $userId)
    {
        try {
            // Validate the group_id
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM groups WHERE id = :group_id");
            $stmt->bindParam(':group_id', $groupId);
            $stmt->execute();
            $groupExists = $stmt->fetchColumn();

            // Validate the user_id
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE id = :user_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $userExists = $stmt->fetchColumn();

            if ($groupExists == 0 && $userExists == 0) {
                throw new \Exception("Group and user do not exist.");
            }
            if ($groupExists == 0) {
                throw new \Exception("Group does not exist.");
            }

            if ($userExists == 0) {
                throw new \Exception("User is not registered.");
            }

            // Check if the user is already a member of the group
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM user_group WHERE group_id = :group_id AND user_id = :user_id");
            $stmt->bindParam(':group_id', $groupId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            $isMember = $stmt->fetchColumn();

            if ($isMember > 0) {
                throw new \Exception("The user is already a member of the group.");
            }

            // Add the user to the group
            $stmt = $this->db->prepare("INSERT INTO user_group (group_id, user_id) VALUES (:group_id, :user_id)");
            $stmt->bindParam(':group_id', $groupId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();

            return "User joined the group successfully.";
        } catch (PDOException $e) {
            // Handle any errors
            throw new \Exception("Error: " . $e->getMessage());
        }
    }

    // Get All Groups
    public function getAllGroups()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM groups");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle any errors
            throw new \Exception("Error: " . $e->getMessage());
        }
    }
}
