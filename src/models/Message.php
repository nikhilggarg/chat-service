<?php

class Message
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Send Message
    public function sendMessage($groupId, $userId, $content)
    {
        try {
            // Validate the group_id
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM groups WHERE id = :group_id");
            $stmt->bindParam(':group_id', $groupId);
            $stmt->execute();
            $groupExists = $stmt->fetchColumn();

            //  Validate the user_id
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

            // Check if the user is a member of the group before allowing them to send a message
            if (!$this->isUserInGroup($groupId, $userId)) {
                throw new \Exception('User is not a member of this group');
            }

            $date = date('Y-m-d H:i:s');    // Current time
            // Insert the message into the database
            $stmt = $this->db->prepare("INSERT INTO messages (group_id, user_id, content, timestamp) VALUES (:group_id, :user_id, :content, :created_at)");
            $stmt->bindParam(':group_id', $groupId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':created_at', $date); // Store the current timestamp
            $stmt->execute();

            return true;
        } catch (PDOException $e) {
            // Handle any errors
            throw new \Exception("Error: " . $e->getMessage());
        }
    }

    // Retrieve all messages in a group
    public function getMessagesByGroup($groupId)
    {
        try {
            // Validate group_id
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM groups WHERE id = :group_id");
            $stmt->bindParam(':group_id', $groupId);
            $stmt->execute();
            $groupExists = $stmt->fetchColumn();

            if ($groupExists == 0) {
                throw new \Exception("Group does not exist.");
            }

            // Retrieve all messages for a group, ordered by the creation time
            $stmt = $this->db->prepare("SELECT m.id as id, m.content as message, m.timestamp as time, u.username as username
                                        FROM messages m
                                        JOIN users u ON m.user_id = u.id
                                        WHERE m.group_id = :group_id
                                        ORDER BY m.timestamp ASC");
            $stmt->bindParam(':group_id', $groupId);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Handle any errors
            throw new \Exception("Error: " . $e->getMessage());
        }
    }

    // Check if user is present in group
    private function isUserInGroup($groupId, $userId)
    {
        try {
            // Check if the user is a member of the group
            $stmt = $this->db->prepare("SELECT 1 FROM user_group WHERE group_id = :group_id AND user_id = :user_id LIMIT 1");
            $stmt->bindParam(':group_id', $groupId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();

            return $stmt->fetchColumn() !== false;
        } catch (PDOException $e) {
            // Handle any errors
            throw new \Exception("Error: " . $e->getMessage());
        }
    }
}
