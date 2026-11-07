<?php

return [
    'brand' => [
        'name' => env('EDI_HUB_NAME', 'EDI Integration Hub'),
        'tagline' => 'Connect ERP systems with buyer EDI networks—reliably, observably, at enterprise scale.',
        'domain' => 'edi-hub.test',
    ],

    'edi_standards' => [
        'x12' => [
            'name' => 'ANSI X12',
            'summary' => 'North American EDI standard for retail, apparel, and logistics supply chains.',
            'types' => [
                '850' => ['name' => 'Purchase Order', 'direction' => 'inbound', 'summary' => 'Buyer transmits PO lines, dates, and ship-to references.'],
                '855' => ['name' => 'PO Acknowledgment', 'direction' => 'outbound', 'summary' => 'Supplier accepts, rejects, or changes PO lines.'],
                '856' => ['name' => 'Advance Ship Notice', 'direction' => 'outbound', 'summary' => 'ASN with carton hierarchy and carrier details.'],
                '810' => ['name' => 'Invoice', 'direction' => 'outbound', 'summary' => 'Financial document matched to PO and ASN.'],
                '997' => ['name' => 'Functional Acknowledgment', 'direction' => 'both', 'summary' => 'Syntax-level acceptance of interchange envelopes.'],
                '846' => ['name' => 'Inventory Inquiry', 'direction' => 'outbound', 'summary' => 'Available-to-promise and warehouse snapshots.'],
            ],
        ],
        'edifact' => [
            'name' => 'UN/EDIFACT',
            'summary' => 'Global standard prevalent in EU apparel buying and cross-border trade.',
            'types' => [
                'ORDERS' => ['name' => 'Purchase Order', 'direction' => 'inbound', 'summary' => 'EDIFACT purchase order (message ORDERS).'],
                'ORDRSP' => ['name' => 'Order Response', 'direction' => 'outbound', 'summary' => 'Supplier response to ORDERS.'],
                'DESADV' => ['name' => 'Despatch Advice', 'direction' => 'outbound', 'summary' => 'Shipment notification equivalent to ASN.'],
                'INVOIC' => ['name' => 'Invoice', 'direction' => 'outbound', 'summary' => 'Commercial invoice message.'],
                'CONTRL' => ['name' => 'Syntax Acknowledgment', 'direction' => 'both', 'summary' => 'Envelope-level acknowledgment.'],
            ],
        ],
    ],

    'erp_integrations' => [
        'sap-business-one' => ['name' => 'SAP Business One', 'status' => 'mock', 'category' => 'ERP'],
        'sap-s4hana' => ['name' => 'SAP S/4HANA', 'status' => 'planned', 'category' => 'ERP'],
        'oracle-netsuite' => ['name' => 'Oracle NetSuite', 'status' => 'mock', 'category' => 'Cloud ERP'],
        'microsoft-dynamics-365' => ['name' => 'Microsoft Dynamics 365', 'status' => 'planned', 'category' => 'ERP'],
        'odoo' => ['name' => 'Odoo', 'status' => 'mock', 'category' => 'Open ERP'],
        'infor-fashion' => ['name' => 'Infor Fashion M3', 'status' => 'planned', 'category' => 'Apparel ERP'],
        'bluecherry' => ['name' => 'BlueCherry', 'status' => 'planned', 'category' => 'Apparel ERP'],
        'custom-rest' => ['name' => 'Custom REST / JSON', 'status' => 'mock', 'category' => 'API'],
    ],

    'solutions' => [
        'garments' => ['title' => 'Garments Manufacturing', 'headline' => 'Seasonal PO bursts without EDI firefighting'],
        'buying-houses' => ['title' => 'Buying Houses', 'headline' => 'Multi-buyer visibility on one integration layer'],
        'retail' => ['title' => 'Retail', 'headline' => 'Retail-compliant X12 with audit-ready lineage'],
        'wholesale' => ['title' => 'Wholesale', 'headline' => 'High-volume catalog and allocation EDI'],
        'distribution' => ['title' => 'Distribution', 'headline' => 'Warehouse-aware ASN and inventory feeds'],
        'logistics' => ['title' => 'Logistics', 'headline' => 'Carrier handoff and milestone events'],
        'warehousing' => ['title' => 'Warehousing', 'headline' => '3PL ASN ingestion and inventory sync'],
        'export-import' => ['title' => 'Export & Import', 'headline' => 'EDIFACT and customs reference alignment'],
    ],

    'modules_fallback' => [
        'edi-gateway' => ['name' => 'EDI Gateway', 'description' => 'Ingest, validate, route, and deliver EDI envelopes.'],
        'partner-mgmt' => ['name' => 'Trading Partner Management', 'description' => 'Onboard buyers and suppliers with ISA/GS profiles.'],
        'mapping-studio' => ['name' => 'Mapping Studio', 'description' => 'Canonical document mapping with version control.'],
        'erp-sync' => ['name' => 'ERP Sync', 'description' => 'Bidirectional connectors with health monitoring.'],
        'api-hub' => ['name' => 'API Hub', 'description' => 'REST ingress/egress with OAuth and webhooks.'],
        'compliance' => ['name' => 'Compliance & Audit', 'description' => 'Immutable audit logs and retention policies.'],
    ],

    'faq' => [
        ['q' => 'Do you support both X12 and EDIFACT?', 'a' => 'Yes. The gateway normalizes both into a canonical document model before ERP delivery.'],
        ['q' => 'Can we run a sandbox before production?', 'a' => 'Use the interactive demo environment with fictional trading partners and resettable sample traffic.'],
        ['q' => 'How are ERP connectors delivered?', 'a' => 'Core connectors ship as mock adapters for architecture validation; production ERP adapters are deployed per engagement.'],
        ['q' => 'Is multi-tenant isolation enforced?', 'a' => 'Every operational record is tenant-scoped with policy checks and optional dedicated queues.'],
    ],

    'docs_nav' => [
        'ARCHITECTURE' => 'Architecture Overview',
        'DATABASE' => 'Database Schema',
        'EDI-STANDARDS' => 'EDI Standards',
        'EDI-WORKFLOWS' => 'EDI Workflows',
        'API' => 'HTTP API',
        'INTEGRATIONS' => 'ERP Integrations',
        'MODULES' => 'Platform Modules',
        'SECURITY' => 'Security',
        'DEPLOYMENT' => 'Deployment',
        'TESTING' => 'Testing',
        'DEMO' => 'Demo Environment',
        'PACKAGE-INTEGRATION' => 'Package Integration',
        'IMPLEMENTATION-REPORT' => 'Implementation Report',
    ],

    'pricing_fallback' => [
        ['code' => 'starter', 'name' => 'Starter', 'price_monthly' => 499, 'description' => 'Single facility, 3 trading partners, X12 core set.'],
        ['code' => 'growth', 'name' => 'Growth', 'price_monthly' => 1299, 'description' => 'Multi-branch, EDIFACT add-on, API hub.'],
        ['code' => 'enterprise', 'name' => 'Enterprise', 'price_monthly' => null, 'description' => 'Dedicated queues, custom SLA, on-prem bridge.'],
    ],
];
