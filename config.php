<?php

declare(strict_types=1);

return [

    // List of allowed origins for CORS
    // If empty, Access-Control-Allow-Origin: * will be used
    // Example: ['https://example.com', 'https://app.example.com']
    'allowed_origins' => [],

    // Path to SQLite database file
    'db_path' => __DIR__ . '/logs.db',
    
];
