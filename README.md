---

# Chat Service <img src="https://github.com/user-attachments/assets/6941c363-8ac8-4169-a0c9-295451ef1809" alt="Chat Logo" width="45" height="auto"/>

## Overview


This is a backend implementation of a chat application using PHP, the Slim framework, and SQLite. The application allows users to create public chat groups, join existing groups, and send messages within these groups. All groups are public, enabling any user to join any group. Additionally, users can view all messages within the groups.

## Features

- Create users
- Create chat groups
- Join chat groups
- Send messages in chat groups
- List all messages within a group
- List all chat groups

## Technologies Used

- PHP
- Slim Framework
- SQLite
- PHPUnit (for testing)
- Guzzle (for HTTP client)

## Setup

### Prerequisites

- PHP 7.4 or higher
- Composer
- PHP SQLite extension (for SQLite database support)

### Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/Abhay191/chat-service.git
    cd chat-service
    ```

2. Install dependencies:
    ```bash
    composer install
    ```

3. Set up the database:
    ```bash
    php src/database/init_db.php
    ```

### Running the Application

1. Start the Slim application:
    ```bash
    php -S localhost:8000 -t public
    ```

2. The application should now be running at `http://localhost:8000`.

### API Endpoints

#### User Endpoints

- **Create User**
  - **POST** `http://localhost:8080/api/users`
  - Request Body:
    ```json
    {
        "username": "string"
    }
    ```

#### Group Endpoints
- **Create Group**
  - **POST** `http://localhost:8080/api/groups`
  - Request Body:
    ```json
    {
        "name": "string",
        "user_id": "integer"
    }
    ```

- **Join Group**
  - **POST** `http://localhost:8080/api/groups/join`
  - Request Body:
    ```json
    {
        "group_id": "integer",
        "user_id": "integer"
    }
    ```

- **List All Groups**
  - **GET** `http://localhost:8080/api/groups/list`

#### Message Endpoints
- **Send Message**
  - **POST** `http://localhost:8080/api/messages`
  - Request Body:
    ```json
    {
        "group_id": "integer",
        "user_id": "integer",
        "content": "string"
    }
    ```
- **List All Messages within the Group**
  - **GET** `http://localhost:8080/api/messages/list`
  - Request Body:
    ```json
    {
        "group_id": "integer"
    }
    ```



## Database Schema

The project uses an **SQLite** database to manage users, groups, and messages. Below is an overview of the database schema, including the tables and their relationships.

### Tables Overview

#### `users` Table
Stores user information.

| Column     | Type    | Description                          |
|------------|---------|--------------------------------------|
| `id`       | INTEGER | Primary key, auto-incremented unique identifier for each user. |
| `username` | TEXT    | Unique, non-null username for each user. |

#### `groups` Table
Stores information about chat groups.

| Column      | Type      | Description                           |
|-------------|-----------|---------------------------------------|
| `id`        | INTEGER   | Primary key, auto-incremented unique identifier for each group. |
| `name`      | TEXT      | Unique, non-null name of the group.   |
| `created_at`| DATETIME  | Timestamp when the group was created, with a default value of the current timestamp. |

#### `messages` Table
Stores messages sent within groups.

| Column     | Type      | Description                           |
|------------|-----------|---------------------------------------|
| `id`       | INTEGER   | Primary key, auto-incremented unique identifier for each message. |
| `group_id` | INTEGER   | Foreign key referencing the `id` of the `groups` table. Represents the group where the message was sent. |
| `user_id`  | INTEGER   | Foreign key referencing the `id` of the `users` table. Represents the user who sent the message. |
| `content`  | TEXT      | The content of the message.           |
| `timestamp`| DATETIME  | Timestamp of when the message was sent, with a default value of the current timestamp. |

#### `user_group` Table
Manages the many-to-many relationship between users and groups. This table associates users with the groups they are members of.

| Column    | Type    | Description                                     |
|-----------|---------|-------------------------------------------------|
| `user_id` | INTEGER | Foreign key referencing the `id` of the `users` table. |
| `group_id`| INTEGER | Foreign key referencing the `id` of the `groups` table. |
| **Primary Key** | Composite of `user_id` and `group_id`. Ensures a user can only belong to a group once. |


### Relationships

- **Users and Groups**:  
  A **many-to-many** relationship exists between users and groups. A user can belong to multiple groups, and a group can have multiple users. This relationship is managed by the `user_group` table.

- **Users and Messages**:  
  A **one-to-many** relationship exists between users and messages. A user can send multiple messages, but each message is associated with a single user.

- **Groups and Messages**:  
  A **one-to-many** relationship exists between groups and messages. A group can contain many messages, but each message belongs to only one group.


## Testing
### Testing with `systemTest.php`

1. Ensure the server is running on `localhost:8080`:
    ```bash
    php -S localhost:8080 -t public
    ```

2. The `systemTest.php` file contains the system test cases for this project. To run this specific test file, use the following command:
    ```bash
    ./vendor/bin/phpunit tests/systemTest.php
    ```


---

