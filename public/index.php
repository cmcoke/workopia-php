<?php 
require '../helpers.php';

require basePath('Router.php');

$router = new Router();

$routes = require basePath('routes.php');

$uri = $_SERVER['REQUEST_URI'];
// inspectAndDie($uri);

$method = $_SERVER['REQUEST_METHOD'];
// inspectAndDie($method);


$router->route($uri, $method);


?>