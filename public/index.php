<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;

$container = require_once(realpath(__DIR__ . '/..') . '/config/bootstrap.php');
$app = DI\Bridge\Slim\Bridge::create($container);

$app->redirect('/', '/contacts');

$app->group('/contacts', function (RouteCollectorProxy $group) {
    $controller = App\Controller\ContactController::class;
    $group->get('', [$controller, 'list']);
    $group->get('/new', [$controller, 'new']);
    $group->post('/new', [$controller, 'create']);
    $group->get('/{id:[0-9]+}', [$controller, 'show']);
    $group->get('/{id:[0-9]+}/edit', [$controller, 'edit']);
    $group->post('/{id:[0-9]+}/edit', [$controller, 'update']);
    $group->post('/{id:[0-9]+}/delete', [$controller, 'delete']);
});

$app->add(new App\Middleware\Flash($container->get('flash')));
$app->add(new Zeuxisoo\Whoops\Slim\WhoopsMiddleware());

$app->run();
