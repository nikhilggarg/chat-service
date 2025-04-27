<?php

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class systemTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => 'http://localhost:8000/api/', // Base URL
            'http_errors' => false
        ]);
    }

    public function testCompleteFlow()
    {
        // This test simulates the following flow:
        /*
            1. Two users are registered, with usernames stored in the variables $username1 and $username2.
            2. The user $username1 creates a group with the name stored in the variable $groupname.
            3. Since $username1 created the group, they are automatically added as a member of the group $groupname.
            4. The user $username2 joins the group $groupname.
            5. The user $username1 sends a message in the group $groupname.
            6. The user $username2 sends a message in the same group $groupname.
            7. The API is called to list all messages in the group $groupname.
            8. Finally, the list of all groups is fetched.

            Note: If you want to run this test multiple times, make sure to modify the variables $username1, $username2, and $groupname to unique values to avoid conflicts
        */

        $username1 = "User1";
        $username2 = "User2";
        $groupname = "Group1";

        // Create User1
        $response = $this->client->post('users', [
            'json' => ['username' => $username1]
        ]);

        $responseData = (string) $response->getBody();

        $data = json_decode($responseData, true);

        if (isset($data['user_id'])) {
            $userId1 = (int)$data['user_id'];
            $message = $data['message'];
            echo "User ID: " . $userId1 . "\n";
            echo "Message: " . $message . "\n";
        } else {
            $error = $data['error'];
            echo $error . "\n";
        }

        $this->assertEquals(200, $response->getStatusCode());

        echo "\n";

        // Create User2
        $response = $this->client->post('users', [
            'json' => ['username' => $username2]
        ]);

        $responseData = (string) $response->getBody();
        $data = json_decode($responseData, true);

        if (isset($data['user_id'])) {
            $userId2 = (int)$data['user_id'];
            $message = $data['message'];
            echo "User ID: " . $userId2 . "\n";
            echo "Message: " . $message . "\n";
        } else {
            $error = $data['error'];
            echo $error . "\n";
        }

        $this->assertEquals(200, $response->getStatusCode());

        echo "\n";

        // Create Group 
        $response = $this->client->post('groups', [
            'json' => [
                'name' => $groupname,
                'user_id' => $userId1
            ]
        ]);

        $responseData = (string) $response->getBody();
        $data = json_decode($responseData, true);

        if (isset($data['group_id'])) {
            $groupId = (int)$data['group_id'];
            $message = $data['message'];
            echo "Group ID: " . $groupId . "\n";
            echo "Message: " . $message . "\n";
        } else {
            $error = $data['error'];
            echo $error . "\n";
        }

        $this->assertEquals(200, $response->getStatusCode());

        echo "\n";

        // User2 join Group1 
        $response = $this->client->post('groups/join', [
            'json' => [
                'id' => $groupId,
                'user_id' => $userId2
            ]
        ]);

        $responseData = (string) $response->getBody();
        $data = json_decode($responseData, true);

        if (isset($data['message'])) {
            $message = $data['message'];
            echo "Message: " . $message . "\n";
        } else {
            $error = $data['error'];
            echo $error . "\n";
        }

        $this->assertEquals(200, $response->getStatusCode());

        echo "\n";

        // User1 send message in Group1
        $response = $this->client->post('messages', [
            'json' => [
                'group_id' => $groupId,
                'user_id' => $userId1,
                'content' => 'Hello, from User1!'
            ]
        ]);

        $responseData = (string) $response->getBody();
        $data = json_decode($responseData, true);

        if (isset($data['message'])) {
            $message = $data['message'];
            echo "Message: " . $message . "\n";
        } else {
            $error = $data['error'];
            echo $error . "\n";
        }

        $this->assertEquals(200, $response->getStatusCode());

        // User2 send message in Group1
        $response = $this->client->post('messages', [
            'json' => [
                'group_id' => $groupId,
                'user_id' => $userId2,
                'content' => 'Hello, from User2!'
            ]
        ]);

        $responseData = (string) $response->getBody();
        $data = json_decode($responseData, true);

        if (isset($data['message'])) {
            $message = $data['message'];
            echo "Message: " . $message . "\n";
        } else {
            $error = $data['error'];
            echo $error . "\n";
        }

        $this->assertEquals(200, $response->getStatusCode());

        echo "\n";

        // List messages in Group1
        $response = $this->client->get('messages/list', [
            'json' => [
                'group_id' => $groupId,
            ]
        ]);

        $responseData = (string) $response->getBody();

        $data = json_decode($responseData, true);

        if (isset($data['messages']) && is_array($data['messages'])) {
            echo "Messages in the group: \n";
            foreach ($data['messages'] as $message) {
                echo "------------------------\n";
                echo "ID: " . $message['id'] . "\n";
                echo "Message: " . $message['message'] . "\n";
                echo "Time: " . $message['time'] . "\n";
                echo "Username: " . $message['username'] . "\n";
                echo "------------------------\n";
            }
        } else {
            $error = $data['error'];
            echo $error . "\n";
        }

        $this->assertEquals(200, $response->getStatusCode());

        echo "\n";
        echo "\n";

        // List of all groups
        $response = $this->client->get('groups/list', []);

        $responseData = (string) $response->getBody();
        $data = json_decode($responseData, true);

        if (isset($data['groups']) && is_array($data['groups'])) {
            echo "Groups: \n";
            foreach ($data['groups'] as $group) {
                echo "------------------------\n";
                echo "ID: " . $group['id'] . "\n";
                echo "Name: " . $group['name'] . "\n";
                echo "Created At: " . $group['created_at'] . "\n";
                echo "------------------------\n";
            }
        } else {
            $error = $data['error'];
            echo $error . "\n";
        }

        $this->assertEquals(200, $response->getStatusCode());
    }
}
