<?php

return function(): array {
    $config = [
        'BASE_DIR' => __DIR__.'/../db/',
        'BASE_URI' => '/bookmark/htdocs/',
        'INFINITE_SCROLLING' => 200,
        'AUTHFILE' => __DIR__.'/../db/htusers',
    ];
    // be compatible with B's getenv
    foreach ($config as $key => $value) {
        putenv($key.'='.$value);
    }
    return $config;
};
