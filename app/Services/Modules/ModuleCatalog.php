<?php

namespace App\Services\Modules;

class ModuleCatalog
{
    /**
     * @return list<array{code: string, name: string, category: string, description?: string, icon?: string, is_core?: bool, default_enabled?: bool, dependencies?: list<string>, sort_order?: int}>
     */
    public static function categories(): array
    {
        return [
            ['code' => 'core', 'name' => 'Core Platform', 'icon' => 'heroicon-o-home', 'sort_order' => 10],
            ['code' => 'edi', 'name' => 'EDI & Messaging', 'icon' => 'heroicon-o-arrows-right-left', 'sort_order' => 20],
            ['code' => 'integration', 'name' => 'Integrations & APIs', 'icon' => 'heroicon-o-link', 'sort_order' => 30],
            ['code' => 'erp', 'name' => 'ERP Connectors', 'icon' => 'heroicon-o-server-stack', 'sort_order' => 40],
            ['code' => 'commerce', 'name' => 'Commerce & Partners', 'icon' => 'heroicon-o-building-storefront', 'sort_order' => 50],
            ['code' => 'documents', 'name' => 'Documents & Compliance', 'icon' => 'heroicon-o-document-text', 'sort_order' => 60],
            ['code' => 'analytics', 'name' => 'Analytics & Reporting', 'icon' => 'heroicon-o-chart-bar', 'sort_order' => 70],
            ['code' => 'automation', 'name' => 'Automation & AI', 'icon' => 'heroicon-o-cpu-chip', 'sort_order' => 80],
            ['code' => 'security', 'name' => 'Security & Governance', 'icon' => 'heroicon-o-shield-check', 'sort_order' => 90],
            ['code' => 'admin', 'name' => 'Administration', 'icon' => 'heroicon-o-cog-6-tooth', 'sort_order' => 100],
        ];
    }

    /**
     * @return list<array{code: string, name: string, category: string, description?: string, icon?: string, is_core?: bool, default_enabled?: bool, dependencies?: list<string>, sort_order?: int}>
     */
    public static function modules(): array
    {
        return [
            ['code' => 'dashboard', 'name' => 'Dashboard', 'category' => 'core', 'is_core' => true, 'default_enabled' => true, 'sort_order' => 10],
            ['code' => 'company_profile', 'name' => 'Company Profile', 'category' => 'core', 'is_core' => true, 'default_enabled' => true, 'sort_order' => 20],
            ['code' => 'user_management', 'name' => 'User Management', 'category' => 'core', 'default_enabled' => true, 'sort_order' => 30],
            ['code' => 'branch_management', 'name' => 'Branch Management', 'category' => 'core', 'default_enabled' => true, 'sort_order' => 40],
            ['code' => 'notifications', 'name' => 'Notifications', 'category' => 'core', 'default_enabled' => true, 'sort_order' => 50],
            ['code' => 'edi_gateway', 'name' => 'EDI Gateway', 'category' => 'edi', 'default_enabled' => true, 'dependencies' => ['dashboard'], 'sort_order' => 10],
            ['code' => 'edi_inbound', 'name' => 'EDI Inbound Processing', 'category' => 'edi', 'dependencies' => ['edi_gateway'], 'sort_order' => 20],
            ['code' => 'edi_outbound', 'name' => 'EDI Outbound Processing', 'category' => 'edi', 'dependencies' => ['edi_gateway'], 'sort_order' => 30],
            ['code' => 'edi_translation', 'name' => 'EDI Translation Engine', 'category' => 'edi', 'dependencies' => ['edi_gateway'], 'sort_order' => 40],
            ['code' => 'edi_validation', 'name' => 'EDI Validation & Rules', 'category' => 'edi', 'dependencies' => ['edi_gateway'], 'sort_order' => 50],
            ['code' => 'edi_acknowledgments', 'name' => 'EDI Acknowledgments (997/999)', 'category' => 'edi', 'dependencies' => ['edi_inbound'], 'sort_order' => 60],
            ['code' => 'as2_transport', 'name' => 'AS2 Transport', 'category' => 'edi', 'dependencies' => ['edi_gateway'], 'sort_order' => 70],
            ['code' => 'sftp_transport', 'name' => 'SFTP / FTP Transport', 'category' => 'edi', 'dependencies' => ['edi_gateway'], 'sort_order' => 80],
            ['code' => 'van_connectivity', 'name' => 'VAN Connectivity', 'category' => 'edi', 'dependencies' => ['edi_gateway'], 'sort_order' => 90],
            ['code' => 'api_hub', 'name' => 'API Integration Hub', 'category' => 'integration', 'default_enabled' => true, 'sort_order' => 10],
            ['code' => 'webhooks', 'name' => 'Webhooks', 'category' => 'integration', 'dependencies' => ['api_hub'], 'sort_order' => 20],
            ['code' => 'rest_connectors', 'name' => 'REST Connectors', 'category' => 'integration', 'dependencies' => ['api_hub'], 'sort_order' => 30],
            ['code' => 'odata_connectors', 'name' => 'OData Connectors', 'category' => 'integration', 'dependencies' => ['api_hub'], 'sort_order' => 40],
            ['code' => 'graphql_gateway', 'name' => 'GraphQL Gateway', 'category' => 'integration', 'dependencies' => ['api_hub'], 'sort_order' => 50],
            ['code' => 'message_queue_bridge', 'name' => 'Message Queue Bridge', 'category' => 'integration', 'dependencies' => ['api_hub'], 'sort_order' => 60],
            ['code' => 'erp_sap', 'name' => 'SAP ERP Connector', 'category' => 'erp', 'sort_order' => 10],
            ['code' => 'erp_oracle', 'name' => 'Oracle ERP Connector', 'category' => 'erp', 'sort_order' => 20],
            ['code' => 'erp_dynamics', 'name' => 'Microsoft Dynamics Connector', 'category' => 'erp', 'sort_order' => 30],
            ['code' => 'erp_netsuite', 'name' => 'NetSuite Connector', 'category' => 'erp', 'sort_order' => 40],
            ['code' => 'erp_quickbooks', 'name' => 'QuickBooks Connector', 'category' => 'erp', 'sort_order' => 50],
            ['code' => 'erp_custom', 'name' => 'Custom ERP Adapter', 'category' => 'erp', 'dependencies' => ['api_hub'], 'sort_order' => 60],
            ['code' => 'trading_partners', 'name' => 'Trading Partner Management', 'category' => 'commerce', 'default_enabled' => true, 'sort_order' => 10],
            ['code' => 'partner_onboarding', 'name' => 'Partner Onboarding', 'category' => 'commerce', 'dependencies' => ['trading_partners'], 'sort_order' => 20],
            ['code' => 'partner_portal', 'name' => 'Partner Portal', 'category' => 'commerce', 'dependencies' => ['trading_partners'], 'sort_order' => 30],
            ['code' => 'purchase_orders', 'name' => 'Purchase Orders (850)', 'category' => 'commerce', 'dependencies' => ['edi_gateway'], 'sort_order' => 40],
            ['code' => 'invoices', 'name' => 'Invoices (810)', 'category' => 'commerce', 'dependencies' => ['edi_gateway'], 'sort_order' => 50],
            ['code' => 'ship_notices', 'name' => 'Advance Ship Notices (856)', 'category' => 'commerce', 'dependencies' => ['edi_gateway'], 'sort_order' => 60],
            ['code' => 'inventory_sync', 'name' => 'Inventory Sync (846)', 'category' => 'commerce', 'dependencies' => ['edi_gateway'], 'sort_order' => 70],
            ['code' => 'document_archive', 'name' => 'Document Archive', 'category' => 'documents', 'sort_order' => 10],
            ['code' => 'document_search', 'name' => 'Document Search', 'category' => 'documents', 'dependencies' => ['document_archive'], 'sort_order' => 20],
            ['code' => 'compliance_audit', 'name' => 'Compliance Audit Trail', 'category' => 'documents', 'default_enabled' => true, 'sort_order' => 30],
            ['code' => 'digital_signatures', 'name' => 'Digital Signatures', 'category' => 'documents', 'sort_order' => 40],
            ['code' => 'analytics_overview', 'name' => 'Analytics Overview', 'category' => 'analytics', 'default_enabled' => true, 'sort_order' => 10],
            ['code' => 'transaction_reports', 'name' => 'Transaction Reports', 'category' => 'analytics', 'dependencies' => ['analytics_overview'], 'sort_order' => 20],
            ['code' => 'sla_monitoring', 'name' => 'SLA Monitoring', 'category' => 'analytics', 'dependencies' => ['analytics_overview'], 'sort_order' => 30],
            ['code' => 'usage_billing_reports', 'name' => 'Usage & Billing Reports', 'category' => 'analytics', 'dependencies' => ['analytics_overview'], 'sort_order' => 40],
            ['code' => 'workflow_builder', 'name' => 'Workflow Builder', 'category' => 'automation', 'sort_order' => 10],
            ['code' => 'scheduled_jobs', 'name' => 'Scheduled Jobs', 'category' => 'automation', 'dependencies' => ['workflow_builder'], 'sort_order' => 20],
            ['code' => 'ai_mapping_assistant', 'name' => 'AI Mapping Assistant', 'category' => 'automation', 'sort_order' => 30],
            ['code' => 'anomaly_detection', 'name' => 'Anomaly Detection', 'category' => 'automation', 'dependencies' => ['analytics_overview'], 'sort_order' => 40],
            ['code' => 'rbac', 'name' => 'Role-Based Access Control', 'category' => 'security', 'is_core' => true, 'default_enabled' => true, 'sort_order' => 10],
            ['code' => 'audit_logs', 'name' => 'Audit Logs', 'category' => 'security', 'default_enabled' => true, 'sort_order' => 20],
            ['code' => 'sso', 'name' => 'Single Sign-On (SSO)', 'category' => 'security', 'sort_order' => 30],
            ['code' => 'ip_allowlist', 'name' => 'IP Allowlist', 'category' => 'security', 'sort_order' => 40],
            ['code' => 'module_settings', 'name' => 'Module Settings', 'category' => 'admin', 'default_enabled' => true, 'sort_order' => 10],
            ['code' => 'billing_subscription', 'name' => 'Billing & Subscription', 'category' => 'admin', 'default_enabled' => true, 'sort_order' => 20],
            ['code' => 'developer_tools', 'name' => 'Developer Tools', 'category' => 'admin', 'sort_order' => 30],
            ['code' => 'sandbox_environment', 'name' => 'Sandbox Environment', 'category' => 'admin', 'sort_order' => 40],
        ];
    }

    public static function moduleCount(): int
    {
        return count(self::modules());
    }
}
