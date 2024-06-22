<?php

return [
    'groups' => [
        'admin' => ['admin.*', 'frontend.users.*', 'frontend.auth.logout'],
        'users' => ['frontend.user.*', 'frontend.auth.logout'],
        'auth' => ['frontend.auth.*'],
        'public' => [],
    ],
];
