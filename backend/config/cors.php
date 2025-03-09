<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'], // Allow all HTTP methods (GET, POST, etc.)
    'allowed_origins' => ['http://localhost:3000'], // Allow requests from localhost:3000
    'allowed_headers' => ['*'], // Allow all headers
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];


