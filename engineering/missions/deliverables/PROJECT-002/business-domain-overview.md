# Business Domain Overview

**Mission:** PROJECT-002  
**Product:** BroDev Cashier

## Purpose

This document defines the business domain landscape for BroDev Cashier. It focuses only on business concepts and aligns with the PROJECT-001 product definition.

## Domain Summary

BroDev Cashier is a retail point of sale domain. Its central business activity is completing sales accurately while keeping product, payment, and inventory information consistent enough for daily store operations.

The product starts with the cashier workflow and expands outward into product catalog management, inventory control, payments, receipts, reporting, and administration.

## Core Business Domains

### Cashier Operations

The cashier domain represents the checkout process. It covers selecting products, setting quantities, calculating totals, accepting payment, completing a sale, and producing a receipt.

### Sales

The sales domain represents the business record of completed transactions. It captures what was sold, in what quantity, by whom, when, and for what total amount.

### Product Catalog

The product catalog domain represents sellable items and their business identity, including SKU, name, price, category, unit, and availability status.

### Inventory

The inventory domain represents available stock and the business movements that increase, decrease, or adjust stock.

### Payments

The payments domain represents how a sale is paid. Initial payment concepts include cash, QRIS, and bank transfer.

### Receipts

The receipt domain represents the customer-facing proof of a completed sale.

### Reporting

The reporting domain represents business summaries derived from operational activity, such as sales totals, stock conditions, and payment method usage.

### Administration

The administration domain represents user responsibilities, operational settings, and access to management workflows.

## Supporting Future Domains

- Customers
- Suppliers
- Purchasing
- Promotions
- Multi-branch operations
- Advanced analytics

## Domain Priority

The first priority is cashier reliability, product correctness, inventory consistency, and payment traceability. Future domains should not weaken the speed or stability of the core checkout workflow.

