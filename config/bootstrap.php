<?php

require_once realpath(__DIR__ . '/../vendor/autoload.php');

use Psr\Container\ContainerInterface;

$builder = new DI\ContainerBuilder();

$builder->addDefinitions(array_merge(
    require __DIR__ . '/settings.php',
    require __DIR__ . '/entity-manager.php',
    require __DIR__ . '/validator.php',
    require __DIR__ . '/twig.php',
    require __DIR__ . '/flash.php',
));

$container = $builder->build();

return $container;
