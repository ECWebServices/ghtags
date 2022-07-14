<?php

// If linux or mac, root it /, otherwise it's windows and we need to root it C:\
$root = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'C:' : '/';

return [
    'default' => 'local',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => $root,
        ],
    ],
    'ghtags' => [
        'driver' => 'local',
        'root' => $_SERVER['HOME'] . '/.ghtags',
    ],
];
