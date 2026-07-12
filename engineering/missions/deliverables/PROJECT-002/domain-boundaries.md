# Domain Boundaries

**Mission:** PROJECT-002  
**Product:** BroDev Cashier

## Purpose

This document defines conceptual boundaries between business domains. These are business boundaries only and do not define database tables, API endpoints, or implementation modules.

## Cashier Operations Boundary

Cashier Operations owns the active checkout workflow before a sale is finalized.

Inside the boundary:

- Product selection during checkout.
- Quantity changes during checkout.
- Sale total preview.
- Payment entry during checkout.
- Sale completion request.
- Receipt handoff after completion.

Outside the boundary:

- Long-term product catalog management.
- Historical sales reporting.
- Manual stock adjustment.
- User administration.

## Sales Boundary

Sales owns the completed business transaction.

Inside the boundary:

- Completed sale identity.
- Sold items.
- Sale totals.
- Sale status.
- Cashier attribution.
- Transaction time.

Outside the boundary:

- Product maintenance.
- Stock availability policy ownership.
- Payment method configuration.
- Report presentation.

## Product Catalog Boundary

Product Catalog owns sellable item definitions.

Inside the boundary:

- Product identity.
- SKU.
- Product name.
- Selling price.
- Product category.
- Product unit.
- Product active or inactive status.

Outside the boundary:

- Current stock quantity.
- Completed sale records.
- Payment records.
- Receipt printing.

## Inventory Boundary

Inventory owns stock state and stock movement concepts.

Inside the boundary:

- Available stock.
- Stock movement.
- Stock adjustment.
- Low stock condition.
- Stock impact from completed sales.

Outside the boundary:

- Product naming and pricing.
- Payment settlement.
- Cashier session behavior.
- Report formatting.

## Payments Boundary

Payments owns the business record of how a sale is paid.

Inside the boundary:

- Payment method.
- Amount paid.
- Payment status.
- Change for cash payment.
- Payment reference when applicable.

Outside the boundary:

- Sale item calculation.
- Product availability.
- External bank or QRIS provider operations.
- Accounting ledger.

## Reporting Boundary

Reporting owns business summaries created from other domains.

Inside the boundary:

- Sales summaries.
- Stock summaries.
- Payment method summaries.
- Operational totals.

Outside the boundary:

- Changing original sale records.
- Changing product definitions.
- Changing stock records.
- Acting as a replacement for accounting.

## Administration Boundary

Administration owns access and operational configuration concepts.

Inside the boundary:

- User responsibility.
- Role-aware access.
- Store settings.
- Management workflows.

Outside the boundary:

- Daily cashier decisions.
- Product stock movements.
- Payment completion semantics.

