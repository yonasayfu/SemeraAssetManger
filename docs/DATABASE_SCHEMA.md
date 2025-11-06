# Database Schema Overview

This document summarizes the core entities and relationships introduced by the Freshservice‑style upgrade.

## Core Tables

### vendors
- id (PK)
- name (string, required)
- contact_name, email, phone, website (nullable)
- notes (text, nullable)
- soft deletes

### products
- id (PK)
- vendor_id (FK → vendors.id, nullable, indexed)
- name (string, required)
- sku (string, nullable)
- warranty_months (int, nullable)
- unit_cost_minor (int, nullable) — store money in minor units
- currency (string, nullable)
- notes (text, nullable)
- soft deletes
- index: vendor_id, sku

### contracts
- id (PK)
- type (string enum: lease|maintenance|license|warranty)
- status (string, nullable)
- vendor_id, product_id (FK, nullable, indexed)
- asset_id (FK → assets.id, nullable)
- start_at, end_at (date, nullable)
- amount_minor (int, nullable), currency (string, nullable)
- notes (text, nullable)
- soft deletes
- index: type, end_at, vendor_id

### purchase_orders
- id (PK)
- number (string, unique-ish by convention)
- name (string)
- vendor_id (FK → vendors.id, nullable)
- expected_delivery_at (date, nullable)
- status (string: open|received|cancelled)
- total_minor (int, nullable), currency (string, nullable)
- notes (text, nullable)
- soft deletes
- index: vendor_id, expected_delivery_at

### purchase_order_items
- id (PK)
- purchase_order_id (FK → purchase_orders.id, indexed)
- product_id (FK → products.id, nullable)
- qty (int)
- unit_cost_minor (int, nullable), currency (string, nullable)
- received_qty (int, nullable)
- soft deletes
- index: purchase_order_id

### software
- id (PK)
- vendor_id (FK → vendors.id, nullable)
- name (string)
- type (string enum: saas|on-prem)
- seats_total, seats_used (int, nullable)
- status (string, default active)
- notes (text, nullable)
- soft deletes
- index: vendor_id

## assets (updated)
- vendor_id (FK → vendors.id, nullable, indexed)
- product_id (FK → products.id, nullable, indexed)
- purchase_order_item_id (FK → purchase_order_items.id, nullable, indexed)
- custom_fields (json, nullable)

## Relationships
- Vendor hasMany Product, PurchaseOrder, Software
- Product belongsTo Vendor; hasMany PurchaseOrderItem
- PurchaseOrder belongsTo Vendor; hasMany PurchaseOrderItem
- PurchaseOrderItem belongsTo PurchaseOrder, Product
- Contract belongsTo Vendor, Product, Asset
- Software belongsTo Vendor
- Asset belongsTo Vendor, Product, PurchaseOrderItem

## Reports & Indexes
- Contracts: index on (type, end_at, vendor_id)
- POs: index on (vendor_id, expected_delivery_at)
- PO Items: index on (purchase_order_id)
- Assets: foreign key indices for vendor_id/product_id/purchase_order_item_id

## Alerts & Notifications
- Alerts table records generated events (type, asset_id, source_id, source_type, message, due_date, sent, sent_at)
- Daily jobs build alerts for maintenance/warranty, contracts expiring, and POs due/overdue

## Migrations (high‑level)
- 2025_11_05_000120_add_vendor_product_to_assets.php
- 2025_11_05_000140_create_purchase_orders_tables.php
- 2025_11_05_000150_create_software_table.php
- 2025_11_05_000160_add_po_item_to_assets.php
- 2025_11_05_000170_add_custom_fields_to_assets.php

