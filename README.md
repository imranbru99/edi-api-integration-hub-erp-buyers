<p align="center">
  <img src="docs/screenshots/dashboard.svg" alt="EDI &amp; API Integration Hub preview" width="100%"/>
</p>

<h1 align="center">EDI & API Integration Hub</h1>
<p align="center"><strong>Connect ERP with buyer systems, trading partners, ASN, invoices and B2B document flows</strong></p>

<p align="center">
  <a href="https://github.com/imranbru99/edi-api-integration-hub-erp-buyers"><img alt="GitHub" src="https://img.shields.io/badge/GitHub-imranbru99%2Fedi-api-integration-hub-erp-buyers-0F172A?style=for-the-badge&logo=github"/></a>
  <img alt="Laravel" src="https://img.shields.io/badge/Laravel-PHP-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img alt="Author" src="https://img.shields.io/badge/Author-Imran%20Ahmed-14B8A6?style=for-the-badge"/>
</p>

---

## README location

This file is the project homepage README:

**https://github.com/imranbru99/edi-api-integration-hub-erp-buyers/blob/main/README.md**

## Overview

**EDI & API Integration Hub** is a Laravel SaaS platform that connects ERP systems with buyer/retailer networks using EDI-style document exchange and modern APIs. It helps buying houses, suppliers, 3PLs and brands exchange orders, ASN, invoices and inventory messages with full monitoring and auditability.

## Product preview

| Dashboard | Operations |
| :---: | :---: |
| ![Dashboard](docs/screenshots/dashboard.svg) | ![Operations](docs/screenshots/operations.svg) |

## Complete features

### Core integration platform

- Multi-tenant EDI & API integration hub for ERP ↔ buyer / retailer systems
- Trading partner onboarding, profiles, certificates and connection settings
- Inbound and outbound document pipelines with status tracking
- Message mapping, transformation and validation rules
- Retry, replay, quarantine and error-handling queues
- Audit trail for every transmission and transformation step

### EDI / B2B document types

- Purchase orders and order acknowledgements
- Advanced shipping notices (ASN)
- Invoices and credit notes
- Inventory advice / stock updates
- Shipment and logistics status documents
- Custom document types per trading partner

### API & connectivity

- REST API endpoints for ERP and middleware systems
- Webhook subscriptions for document events
- AS2 / SFTP / API connector patterns for partner exchange
- API keys, scopes and rate limiting
- Sandbox vs production connection profiles

### Operations & monitoring

- Live transmission dashboard and failure alerts
- Partner health / connectivity monitoring
- Document search, filters and payload inspection
- Scheduled jobs for polling, cleanup and retries
- Notification center for ops and partner contacts

### Business & admin

- Multi-branch / multi-company ready tenant model
- Roles & permissions for integrators, ops and auditors
- Filament admin resources for partners, documents and settings
- Subscription / plan hooks for SaaS packaging
- Multilingual and Unicode-ready document metadata

### Act as a complete enterprise software development team consisting of

- PRODUCT VISION
- TECHNOLOGY STACK

### Create the following modules

- CORE EDI GATEWAY

### When an invoice is approved

- BUYER AND SUPPLIER PORTALS

### Display all available modules with

- Industry Solutions

### Admin panels (Filament resources)

- Api Client
- Branch
- Certificate
- Edi Transaction
- Integration Connection
- Invoice
- Mapping Profile
- Module
- Plan
- Platform User
- Product
- Purchase Order
- Shipment
- Tenant
- Trading Partner
- Transaction Error
- Webhook Endpoint
- Workflow

### Domain models implemented

- Ai Approval Record
- Ai Conversation
- Ai Provider Setting
- Ai Usage Log
- Api Client
- Api Key
- Api Request Log
- Asn Document
- Audit Log
- Blog Post
- Branch
- Business Rule
- Certificate
- Contact Request
- Demo Request
- Department
- Document
- Edi Acknowledgment
- Edi Control Number
- Edi Document Type
- Edi Envelope
- Edi Standard
- Edi Transaction
- Edi Transaction Event
- Edi Validation Rule
- Faq
- Feature Flag
- Integration Connection
- Integration Sync Log
- Inventory Record
- Invoice
- Invoice Item
- Login History
- Mapping Approval
- Mapping Field
- Mapping Profile
- Mapping Rule
- Mapping Test Case
- Mapping Version
- Module
- Module Activity Log
- Module Category
- Order Acknowledgment
- Page
- Partner Address
- Partner Connection
- Partner Contact
- Partner Document Type
- Partner Identifier
- Plan

## Tech stack
- PHP 8.2+ / Laravel
- Filament admin
- Queues, migrations, seeders
- Vite frontend assets
- MySQL/SQLite ready

## Quick start
```bash
git clone https://github.com/imranbru99/edi-api-integration-hub-erp-buyers.git
cd edi-api-integration-hub-erp-buyers
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install && npm run build
php artisan serve
```

## Author
**Imran Ahmed** · https://imrandev.bd · me@imrandev.bd

---
## 🤝 Let's Connect & Collaborate

I am open to **Senior Remote Full-Stack Roles**, AI Platform Architecture Contracts, and Enterprise Technical Advisory.

**Timezone:** UTC+6 (Dhaka / Rangpur, Bangladesh)  
**Website:** https://imrandev.bd · **Email:** me@imrandev.bd · **GitHub:** https://github.com/imranbru99

