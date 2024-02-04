<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteCollectorProxy;

$container = require_once(realpath(__DIR__ . '/..') . '/config/bootstrap.php');
$app = DI\Bridge\Slim\Bridge::create($container);

$app->redirect('/', '/contacts');

// $app->get('/contacts', [App\Controller\ContactController::class, 'index']);

$app->group('/contacts', function (RouteCollectorProxy $group) {
    $group->get('', [App\Controller\ContactController::class, 'index']);
    $group->get('/new', [App\Controller\ContactController::class, 'newGet']);
    $group->post('/new', [App\Controller\ContactController::class, 'new']);
    $group->get('/{id:[0-9]+}', [App\Controller\ContactController::class, 'view']);
    $group->get('/{id:[0-9]+}/edit', [App\Controller\ContactController::class, 'editGet']);
    $group->post('/{id:[0-9]+}/edit', [App\Controller\ContactController::class, 'editPost']);
    $group->post('/{id:[0-9]+}/delete', [App\Controller\ContactController::class, 'delete']);
});

$app->add(new App\Middleware\Flash($container->get('flash')));
#$app->addErrorMiddleware(true, true, true);
$app->add(new Zeuxisoo\Whoops\Slim\WhoopsMiddleware());

$app->run();
