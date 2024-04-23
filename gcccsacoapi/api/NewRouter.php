<?php

require_once __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;


require_once __DIR__ . '/Login.php';
require_once __DIR__ . '/FormHandler.php';


$dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $r) {
    $r->addRoute('POST', '/login', ['Login', 'loginUser']);
    $r->addRoute('POST', '/register', ['RegisterUser', 'registerUser']);
    $r->addRoute('POST', '/addrecord', ['FormHandler', 'submitFormData']);
    $r->addRoute('POST', '/editrecord/{id:\d+}', ['FormHandler', 'updateFormData']);
    $r->addRoute('POST', '/deleterecord/{id:\d+}', ['FormHandler', 'deleteFormData']);
    $r->addRoute('POST', '/mail', ['Mail', 'sendEmail']);
    $r->addRoute('POST', '/schedule', ['Mail', 'scheduledSend']);
    $r->addRoute('GET', '/alumni[/{id:\d+}]', ['FormHandler', 'getFormData']);
});


$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo "Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        http_response_code(405);
        echo "Method not allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        [$controller, $method] = $handler;
        $controller = new $controller();
        $controller->$method($vars);
        break;
}
