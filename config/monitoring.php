<?php

return [
    // If set, /healthz will require X-Health-Token header to match.
    'health_token' => env('HEALTHCHECK_TOKEN'),

    // Header used for request correlation.
    'request_id_header' => env('REQUEST_ID_HEADER', 'X-Request-Id'),
];
