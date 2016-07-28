<?php

require 'app/config.php';

return [
    'db' => [
        'driver' => $config['db'][$selected]['driver'],
        'host' => $config['db'][$selected]['host'],
        'port' =>  $config['db'][$selected]['port'],
        'dbname' => $config['db'][$selected]['name'],
        'user' => $config['db'][$selected]['user'],
        'password' => $config['db'][$selected]['password']
    ],
];