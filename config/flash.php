<?php

use Slim\Flash\Messages;

return [
    'flash' => function () {
        $storage = [];
        return new Messages($storage);
    }
];
