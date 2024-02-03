<?php

declare(strict_types=1);

namespace App\Middleware;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use RuntimeException;
use Slim\Flash\Messages;

class Flash
{
    public function __construct(private Messages $flash)
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $storage = $_SESSION;
        $storageKey = 'the-key';
        $this->flash->__construct($storage, $storageKey); // doesn't work without a key
        return $handler->handle($request);
    }
}
