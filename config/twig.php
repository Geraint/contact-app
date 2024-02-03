<?php

use Psr\Container\ContainerInterface;

return [
    Twig\Environment::class => function (ContainerInterface $c) {
        $loader = new Twig\Loader\FilesystemLoader(realpath(__DIR__ . '/..') . '/template/');
        $twig = new Twig\Environment($loader, [
            'strict_variables' => true,
            'debug'            => true,
        ]);
        $twig->addExtension(new Twig\Extension\DebugExtension());
        return $twig;
    },
];
