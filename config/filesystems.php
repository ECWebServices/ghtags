<?php

return [
    'default' => 'local',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => getcwd(),
        ],
    ],
    'ghtags' => [
        'driver' => 'local',
        'root' => $_SERVER['HOME'] . '/.ghtags',
    ],
];
