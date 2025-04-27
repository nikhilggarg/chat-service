<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../models/Group.php';

class GroupController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Create Group
    public function createGroup(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $name = $data['name'];
        $userId = $data['user_id'];
        try {
            // Create a new group
            $groupModel = new Group($this->db);
            $groupId = $groupModel->createGroup($name, $userId);

            // Add the user to the group
            $groupModel->joinGroup($groupId, $userId);
            $response->getBody()->write(json_encode([
                'group_id' => $groupId,
                'message' => 'Group created successfully'
            ]));

            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Handle error
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    // Allows a user to join a group
    public function joinGroup(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $groupId = $data['id'];
        $userId = $data['user_id'];
        try {
            $groupModel = new Group($this->db);
            $result = $groupModel->joinGroup($groupId, $userId);

            $response->getBody()->write(json_encode(['message' => 'User joined the group successfully']));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Handle error
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    // Get All Groups
    public function getAllGroups(Request $request, Response $response, array $args)
    {
        try {
            $groupModel = new Group($this->db);

            // Fetch all groups from the database
            $groups = $groupModel->getAllGroups();

            // Check if groups were found
            if (empty($groups)) {
                $response->getBody()->write(json_encode(['message' => 'No groups found.']));
            } else {
                $response->getBody()->write(json_encode(['groups' => $groups]));
            }

            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Handle error
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}
