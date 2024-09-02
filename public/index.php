<?php 
// Include the helpers file for utility functions.
require '../helpers.php';

// Include the Router class for handling routes.
require basePath('Router.php');

// Include the Database class for handling database connections.
require basePath('Database.php');

// Create a new instance of the Router class.
$router = new Router();

// Load the routes configuration from the routes.php file.
$routes = require basePath('routes.php');

// Parse the current URI from the server's REQUEST_URI variable, extracting only the path component.
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Uncomment the following line to debug the URI by inspecting and stopping the script.
// inspectAndDie($uri);

// Get the current HTTP request method from the server's REQUEST_METHOD variable.
$method = $_SERVER['REQUEST_METHOD'];
// Uncomment the following line to debug the HTTP method by inspecting and stopping the script.
// inspectAndDie($method);

// Route the request based on the URI and method.
$router->route($uri, $method);

?>