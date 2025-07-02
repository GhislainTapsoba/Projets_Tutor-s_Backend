<?php

return [
    'web' => [
        'path' => env('FRONTEND_WEB_PATH', '/systeme-gestion-tickets-frontend'),
        'api_url' => env('FRONTEND_WEB_API_URL', 'http://localhost:8000/api'),
    ],
    'mobile' => [
        'path' => env('FRONTEND_MOBILE_PATH', '/mobile'),
        'api_url' => env('FRONTEND_MOBILE_API_URL', 'http://localhost:8000/api'),
    ],
];
