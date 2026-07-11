# Domain Relationships

**Mission:** PROJECT-002  
**Product:** BroDev Cashier

## Purpose

This document explains business relationships between domains and entities. It does not define database relationships, foreign keys, or API contracts.

## Sales and Cashier Operations

Cashier Operations prepares a sale while the transaction is still active. Sales owns the completed transaction after checkout is finalized.

Business relationship:

- A cashier workflow may become a completed sale.
- A completed sale must reflect the final cashier decisions.
- A completed sale should not be treated as an editable shopping session.

## Sales and Product Catalog

Sales depends on product catalog information to identify what was sold.

Business relationship:

- A sale item represents a product sold at checkout.
- Product labels and prices must be clear enough for cashier and receipt use.
- Product catalog changes should not make historical sales unclear.

## Sales and Inventory

Sales affects inventory when products are sold.

Business relationship:

- A completed sale reduces available stock.
- A sale should not complete when requested quantity exceeds available stock.
- Stock impact should be traceable as a business movement.

## Sales and Payments

A sale must have a payment outcome.

Business relationship:

- A payment belongs to the business completion of a sale.
- Payment method affects information needed for the transaction.
- Cash payment may produce change.

## Sales and Receipts

Receipts communicate sale completion to the customer.

Business relationship:

- A receipt represents a completed sale.
- A receipt should include sold items, totals, payment information, and completion time.
- A receipt should not create a new sale by itself.

## Product Catalog and Inventory

Product Catalog defines what can be sold. Inventory defines whether it is available.

Business relationship:

- A product may have stock availability.
- An inactive product should not be selected for new sales.
- A low stock condition belongs to inventory but references a product concept.

## Reporting and Operational Domains

Reporting summarizes facts from other domains.

Business relationship:

- Sales reports summarize completed sales.
- Stock reports summarize inventory conditions.
- Payment reports summarize payment methods and amounts.
- Reports should not replace the source operational record.

## Administration and All Domains

Administration controls responsibility and access.

Business relationship:

- Users perform actions in other domains according to role.
- Admin workflows manage business data that cashiers consume.
- Management users review reporting outputs.

