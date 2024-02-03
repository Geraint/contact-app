<?php

use Psr\Container\ContainerInterface;
use Symfony\Component\Validator;

return [
    Validator\Validator\ValidatorInterface::class => function (ContainerInterface $c) {
        return Validator\Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    },
];
