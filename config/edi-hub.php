<?php

return [
    'name' => env('EDI_HUB_NAME', 'EDI and API Integration Hub'),
    'demo_mode' => (bool) env('APP_DEMO_MODE', false),
    'demo_password' => env('DEMO_PASSWORD'),
    'delivery_driver' => env('EDI_HUB_DELIVERY_DRIVER', 'mock'),
    'demo' => [
        'enabled' => (bool) env('APP_DEMO_MODE', false),
        'password' => env('DEMO_PASSWORD', 'DemoPass123!'),
        'emails' => [
            'platform' => env('DEMO_EMAIL_PLATFORM', 'demo.platform@edi-hub.test'),
            'owner' => env('DEMO_EMAIL_OWNER', 'demo.owner@edi-hub.test'),
            'edi' => env('DEMO_EMAIL_EDI', 'demo.edi@edi-hub.test'),
            'integrator' => env('DEMO_EMAIL_INTEGRATOR', 'demo.integrator@edi-hub.test'),
            'ops' => env('DEMO_EMAIL_OPS', 'demo.ops@edi-hub.test'),
            'buyer' => env('DEMO_EMAIL_BUYER', 'demo.buyer@edi-hub.test'),
            'supplier' => env('DEMO_EMAIL_SUPPLIER', 'demo.supplier@edi-hub.test'),
            'auditor' => env('DEMO_EMAIL_AUDITOR', 'demo.auditor@edi-hub.test'),
        ],
    ],
    'queues' => [
        'edi_inbound' => 'edi-inbound',
        'edi_outbound' => 'edi-outbound',
        'edi_validation' => 'edi-validation',
        'edi_mapping' => 'edi-mapping',
        'edi_delivery' => 'edi-delivery',
        'api_sync' => 'api-sync',
        'webhook_delivery' => 'webhook-delivery',
        'notification' => 'notification',
        'reporting' => 'reporting',
        'ai_processing' => 'ai-processing',
    ],
    'retention_days' => (int) env('EDI_RETENTION_DAYS', 365),
    'ai' => [
        'enabled' => (bool) env('EDI_AI_ENABLED', false),
        'require_tenant_opt_in' => true,
        'allow_auto_publish_mappings' => false,
        'redact_sensitive_data' => true,
    ],
    'security' => [
        'encrypt_credentials_at_rest' => true,
        'never_log_secrets' => true,
        'webhook_signature_header' => 'X-EDI-Hub-Signature',
    ],
    'packages' => [
        'drutogoti' => true,
        'ai_hub' => true,
        'ai_catalog' => true,
        'unicode_pdf' => true,
        'universal_slug' => true,
        'attendance_hub' => (bool) env('ATTENDANCE_HUB_ENABLED', false),
        // laravel-admin-panel intentionally NOT integrated — conflicts with Filament as primary admin.
        'laravel_admin_panel' => false,
    ],
];
