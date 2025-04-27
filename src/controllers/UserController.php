<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require __DIR__ . '/../models/User.php';

class UserController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Create the user
    public function createUser(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $username = $data['username'];
        try {
            $userModel = new User($this->db);
            $userId = $userModel->createUser($username);

            $response->getBody()->write(json_encode([
                'message' => 'User created successfully',
                'user_id' => $userId
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Handle error
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}
