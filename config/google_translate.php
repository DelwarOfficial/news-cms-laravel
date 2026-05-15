<?php

return [
    /*
    | Google Cloud project ID
    */
    'project_id' => env('GOOGLE_TRANSLATE_PROJECT_ID', ''),

    /*
    | Path to the service account JSON key file (relative to storage_path())
    | Example: 'app/google-translate-key.json'
    */
    'key_file_path' => env('GOOGLE_TRANSLATE_KEY_PATH', ''),

    /*
    | Absolute path override (takes precedence if set)
    | Useful in production where the file may live outside storage/
    */
    'key_file_absolute' => env('GOOGLE_TRANSLATE_KEY_ABSOLUTE', ''),

    /*
    | Monthly character limit for cost control
    | Google Translate v3 costs ~$20 per 1M characters (pay-as-you-go)
    | Set to 0 to disable (unlimited)
    */
    'monthly_limit' => env('GOOGLE_TRANSLATE_MONTHLY_LIMIT', 1_500_000),

    /*
    | Source and target locales for the admin translate button
    */
    'source_locale' => 'bn',
    'target_locale' => 'en',

    /*
    | Fields to translate on the Post model
    */
    'fields' => [
        'title',        // maps to title_bn → title_en
        'summary',      // maps to summary_bn → summary_en (rich text)
        'body',         // maps to body_bn → body_en (rich text)
        'meta_title',   // maps to meta_title_bn → meta_title_en
        'meta_description', // maps to meta_description_bn → meta_description_en
    ],
];
