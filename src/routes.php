<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/controllers/UserController.php';
require __DIR__ . '/controllers/GroupController.php';
require __DIR__ . '/controllers/MessageController.php';
require __DIR__ . '/database/Database.php';

// Initialize the database
$db = new Database();
$userController = new UserController($db->getConnection());
$groupController = new GroupController($db->getConnection());
$messageController = new MessageController($db->getConnection());


$app->group('/api', function (RouteCollectorProxy $group) use ($userController, $groupController, $messageController) {
    $group->post('/users', [$userController, 'createUser']);
    $group->post('/groups', [$groupController, 'createGroup']);
    $group->post('/groups/join', [$groupController, 'joinGroup']);
    $group->post('/messages', [$messageController, 'sendMessage']);
    $group->get('/groups/list', [$groupController, 'getAllGroups']);
    $group->get('/messages/list', [$messageController, 'listMessages']);
});
