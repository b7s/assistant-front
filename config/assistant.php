<?php

return [
    'api_url' => env('ASSISTANT_API_URL'),
    'api_client_id' => env('ASSISTANT_API_CLIENT_ID'),
    'api_client_secret' => env('ASSISTANT_API_CLIENT_SECRET'),
    'api_timeout' => (int) env('ASSISTANT_API_TIMEOUT', 30),
    'api_prefix_version' => env('API_PREFIX_VERSION', 'v1'),
];
