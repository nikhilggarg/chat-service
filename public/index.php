<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate app
$app = AppFactory::create();

// Parse json data
$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

$app->addErrorMiddleware(true, true, true);

// Add route callbacks
$app->get('/', function (Request $request, Response $response, $args) {
    $html = "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Chat Service</title>
        <style>
            /* Reset some basic elements */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f9f9f9;
                color: #333;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                padding: 20px;
            }
            .container {
                text-align: center;
                background-color: #ffffff;
                padding: 30px 40px;
                border-radius: 8px;
                box-shadow: 0 12px 20px rgba(0, 0, 0, 0.1);
                width: 100%;
                max-width: 500px;
            }
            h1 {
                font-size: 2.5em;
                color: #4CAF50;
                margin-bottom: 20px;
                letter-spacing: 1px;
                font-weight: 600;
            }
            p {
                font-size: 1.2em;
                color: #555;
                line-height: 1.6;
                margin-top: 15px;
            }
            .button {
                display: inline-block;
                background-color: #4CAF50;
                color: #ffffff;
                padding: 12px 24px;
                border-radius: 4px;
                font-size: 1.1em;
                text-decoration: none;
                margin-top: 30px;
                transition: background-color 0.3s ease, transform 0.2s ease;
            }
            .button:hover {
                background-color: #45a049;
                transform: translateY(-2px);
            }
            .button:active {
                transform: translateY(1px);
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>Hello!</h1>
            <p>Welcome to the Chat Service. We're happy to have you here.</p>
            <a href='#' class='button'>Get Started</a>
        </div>
    </body>
    </html>";

    $response->getBody()->write($html);
    return $response;
});

// Include the API route definitions
require __DIR__ . '/../src/routes.php';

// Run application
$app->run();
