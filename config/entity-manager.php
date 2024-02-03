<?php

use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

return [
    EntityManager::class => function (ContainerInterface $c): EntityManager {
        /** @var array<string, array<string, mixed>> */
        $settings = $c->get('settings');

        assert(is_string($settings['doctrine']['cache_dir']));
        $cache = $settings['doctrine']['dev_mode']
            ? DoctrineProvider::wrap(new ArrayAdapter())
            : DoctrineProvider::wrap(new FilesystemAdapter(directory: $settings['doctrine']['cache_dir']))
        ;

        assert(is_array($settings['doctrine']['metadata_dirs']));
        assert(is_bool($settings['doctrine']['dev_mode']));
        $config = Setup::createAttributeMetadataConfiguration(
            $settings['doctrine']['metadata_dirs'],
            $settings['doctrine']['dev_mode'],
            null,
            $cache
        );

        assert(is_array($settings['doctrine']['connection']));
        return EntityManager::create($settings['doctrine']['connection'], $config);
    },
];
