# Canonical Data Model Design

**Mission:** PROJECT-005  
**Product:** BroDev Cashier  
**Deliverable:** Implement core logic for canonical business data model definition

## Purpose

This document defines the canonical business data model for BroDev Cashier by translating the approved business domains, workflows, and architecture into entities, aggregates, relationships, lifecycle states, and persistence strategies.

This is a data model design document only. It does not create migrations, modify application code, or implement physical database schema.

## Design Inputs

This data model is based on:

- PROJECT-001 Product Definition.
- PROJECT-002 Business Domain Model.
- PROJECT-003 Business Workflow Specification.
- PROJECT-004 Architecture Baseline.
- Existing Laravel application baseline using Eloquent ORM and migrations.

## Data Modelling Principles

- Completed sales are source facts for reporting.
- Product catalog data describes what can be sold.
- Inventory data describes whether a product is available and why stock changed.
- Payment data records how a sale was paid.
- Receipt data is derived from completed sale and payment facts.
- Administration data controls who may perform protected workflows.
- Physical database schema must be designed later through Laravel migrations.
- Eloquent should remain the default persistence mechanism.

## Canonical Entity Groups

### Identity and Access

- User
- Role
- User Responsibility

### Product Catalog

- Product
- SKU
- Category
- Unit

### Cashier and Sales

- Sale
- Sale Item
- Sale Total
- Tax Amount
- Sale Status

### Payments

- Payment
- Payment Method
- Payment Status
- Payment Reference

### Inventory

- Stock
- Stock Movement
- Stock Adjustment
- Low Stock Condition

### Receipts

- Receipt
- Receipt Status

### Reporting

- Sales Summary
- Stock Summary
- Payment Method Summary

### Administration and Settings

- Store Setting
- Operational Configuration

## Entity Definitions

### User

Represents a person who can access the system.

Canonical data responsibilities:

- Identify the person using the system.
- Associate the person with business responsibilities.
- Attribute completed sales or administrative actions to a responsible user.

Lifecycle states:

- Active
- Inactive

Persistence strategy:

- Existing Laravel user persistence should remain the foundation.
- Role or responsibility data should be added only when authorization requirements are finalized.

### Role

Represents a business responsibility such as cashier, admin, owner, or manager.

Canonical data responsibilities:

- Express allowed workflow responsibilities.
- Support authorization for cashier, product, inventory, reporting, and administration workflows.

Lifecycle states:

- Defined
- Assigned
- Revoked

Persistence strategy:

- Keep role data simple for MVP.
- Avoid complex permission matrices until business workflows require them.

### Product

Represents a sellable item in the store catalog.

Canonical data responsibilities:

- Identify sellable goods.
- Provide cashier-facing and receipt-facing product labels.
- Preserve selling price used during checkout.
- Indicate whether the product can be selected for new sales.

Canonical attributes:

- SKU
- Name
- Selling price
- Category
- Unit
- Active status

Lifecycle states:

- Draft
- Active
- Inactive

Persistence strategy:

- Product data should be persisted as a catalog source.
- Historical sale items should preserve enough product information to keep completed sales understandable if product catalog data changes later.
- Soft deletion can be used for products when historical sale integrity must be preserved.

### Category

Represents a grouping for products.

Canonical data responsibilities:

- Organize products for admin and cashier workflows.
- Support future filtering and reporting.

Lifecycle states:

- Active
- Inactive

Persistence strategy:

- Category can remain optional until product management complexity requires it.

### Unit

Represents the measurement or packaging form of a product.

Canonical data responsibilities:

- Clarify how a product is sold.
- Support receipt and admin display.

Lifecycle states:

- Active
- Inactive

Persistence strategy:

- Unit can remain simple and stable.
- Avoid over-modelling unit conversion until the business requires it.

### Sale

Represents a completed or in-progress retail transaction.

Canonical data responsibilities:

- Preserve the business fact of a transaction.
- Attribute transaction to cashier.
- Preserve subtotal, tax, grand total, payment outcome, and completion time.
- Serve as the primary reporting source for sales activity.

Canonical attributes:

- Sale identity
- Cashier
- Sale status
- Subtotal
- Tax amount
- Grand total
- Completion time

Lifecycle states:

- Draft
- Item Selection
- Total Calculated
- Payment Pending
- Payment Accepted
- Completed
- Voided

Persistence strategy:

- Completed sales must be durable source facts.
- Draft sale persistence is optional and should be decided by future implementation needs.
- Voiding should preserve the original sale fact and represent reversal as a controlled business state.

### Sale Item

Represents one product line inside a sale.

Canonical data responsibilities:

- Identify which product was sold.
- Preserve sold quantity.
- Preserve price at time of sale.
- Contribute to sale subtotal and reporting.

Canonical attributes:

- Product reference
- Product display label at time of sale
- Quantity
- Unit price at time of sale
- Line total

Lifecycle states:

- Added
- Quantity Adjusted
- Finalized
- Voided

Persistence strategy:

- Sale item facts should remain attached to the sale.
- Sale item price and label should remain understandable even if product catalog data changes later.

### Payment

Represents how a sale was paid.

Canonical data responsibilities:

- Record payment method.
- Record payment amount.
- Record whether payment was accepted.
- Represent cash change or external reference when applicable.

Canonical attributes:

- Payment method
- Payment amount
- Payment status
- Cash received
- Change amount
- Payment reference

Lifecycle states:

- Pending
- Method Selected
- Information Provided
- Accepted
- Recorded
- Failed

Persistence strategy:

- Payment may remain part of the Sale aggregate for MVP.
- Payment can become its own aggregate if external confirmations, refunds, or reconciliation become complex.

### Stock

Represents available product quantity.

Canonical data responsibilities:

- Determine whether requested sale quantity is available.
- Identify low stock condition.
- Support stock reporting.

Canonical attributes:

- Product reference
- Available quantity
- Low stock threshold
- Stock status

Lifecycle states:

- Available
- Low Stock
- Out of Stock
- Adjusted

Persistence strategy:

- Existing product stock fields may remain for MVP if the workflow stays simple.
- A separate inventory model should be introduced when stock movements, adjustments, and auditability become central.

### Stock Movement

Represents why stock increased or decreased.

Canonical data responsibilities:

- Record sale-driven stock reduction.
- Record manual adjustment impact.
- Support audit and reporting.

Canonical attributes:

- Product reference
- Movement type
- Quantity change
- Business reason
- Related sale when applicable
- Responsible user when applicable

Lifecycle states:

- Planned
- Recorded
- Reversed

Persistence strategy:

- Stock movement should become durable once inventory traceability is required.
- Sale completion and stock movement should share a consistency boundary.

### Stock Adjustment

Represents a manual correction to stock.

Canonical data responsibilities:

- Distinguish manual corrections from sale-driven stock reduction.
- Preserve business reason.
- Preserve responsible user.

Lifecycle states:

- Requested
- Validated
- Applied
- Rejected

Persistence strategy:

- Stock adjustment should be persisted separately from ordinary sale movement when audit requirements mature.

### Receipt

Represents proof of completed sale.

Canonical data responsibilities:

- Present sale identity, items, totals, payment method, and completion time.
- Support receipt reissue if printing fails.

Lifecycle states:

- Ready
- Issued
- Reissued
- Failed To Issue

Persistence strategy:

- Receipt data can be derived from Sale and Payment for MVP.
- Persist receipt issuance metadata only if reprint tracking or audit requirements require it.

### Store Setting

Represents operational configuration.

Canonical data responsibilities:

- Define tax behavior.
- Define receipt display information.
- Support business preferences.

Lifecycle states:

- Draft
- Active
- Replaced

Persistence strategy:

- Settings should be centrally managed.
- Avoid scattering hardcoded business configuration in controllers or views.

## Aggregate Design

### Sale Aggregate

Root entity:

- Sale

Included concepts:

- Sale Item
- Sale Total
- Payment outcome for MVP
- Receipt readiness
- Cashier attribution

Consistency rules:

- A completed sale must contain at least one sale item.
- A completed sale must have valid totals.
- A completed sale must have an accepted payment.
- A completed sale must be attributed to a cashier.

Persistence strategy:

- The application layer should own the transaction boundary for completing a sale.
- Sale, sale items, payment record, and stock impact must remain consistent.

### Product Aggregate

Root entity:

- Product

Included concepts:

- SKU
- Product name
- Selling price
- Category
- Unit
- Active status

Consistency rules:

- Active products must be identifiable and priced.
- Inactive products must not be selected for new sales.

Persistence strategy:

- Product catalog persistence should prioritize stable cashier lookup and historical sale clarity.

### Inventory Aggregate

Root entity:

- Stock

Included concepts:

- Stock Movement
- Stock Adjustment
- Low Stock Condition

Consistency rules:

- Stock cannot be reduced below what the business allows.
- Stock changes must have a business reason.
- Manual adjustments must be distinguishable from sale reductions.

Persistence strategy:

- Inventory may start simple but should move toward movement-based persistence as audit needs grow.
- Future implementation should consider atomic locking for stock-sensitive updates.

### Payment Aggregate

Root entity:

- Payment

Included concepts:

- Method
- Status
- Amount
- Cash change
- External reference

Consistency rules:

- Payment must satisfy the sale total before sale completion.
- Cash payment must cover the grand total.
- Non-cash payment must have sufficient business confirmation.

Persistence strategy:

- Keep payment inside sale completion for MVP.
- Extract payment as a stronger independent aggregate only when external provider workflows become complex.

### User Responsibility Aggregate

Root entity:

- User

Included concepts:

- Role
- Responsibility assignment

Consistency rules:

- Users may only perform workflows allowed by their responsibility.

Persistence strategy:

- Use Laravel authorization patterns to enforce responsibility.
- Keep role persistence simple until permission complexity grows.

## Canonical Relationships

### User and Sale

A completed sale is attributed to one cashier user.

Design rule:

- The sale should preserve cashier attribution for reporting and audit.

### Product and Sale Item

A sale item references the product sold.

Design rule:

- Sale items should preserve sale-time product name and price information so historical sales remain clear.

### Sale and Sale Item

A sale contains one or more sale items.

Design rule:

- Sale completion is invalid without at least one sale item.

### Sale and Payment

A sale has a payment outcome.

Design rule:

- Payment must be accepted before sale completion.

### Product and Stock

A product has stock availability.

Design rule:

- Product Catalog owns product identity and price.
- Inventory owns quantity and movement meaning.

### Stock and Stock Movement

Stock is explained by stock movements.

Design rule:

- Stock movement should record business reason and quantity change.

### Sale and Stock Movement

A completed sale can cause stock reduction movements.

Design rule:

- Sale-driven stock reduction should be traceable to the completed sale.

### Product and Category

A product may belong to a category.

Design rule:

- Category supports organization and filtering, not cashier sale validity by itself.

### Product and Unit

A product may have a unit.

Design rule:

- Unit clarifies how a product is sold or displayed.

## Lifecycle State Models

### Sale Lifecycle

```text
Draft
  -> Item Selection
  -> Total Calculated
  -> Payment Pending
  -> Payment Accepted
  -> Completed
  -> Receipt Issued
```

Exceptional state:

```text
Completed -> Voided
```

### Product Lifecycle

```text
Draft
  -> Active
  -> Inactive
```

### Payment Lifecycle

```text
Pending
  -> Method Selected
  -> Information Provided
  -> Accepted
  -> Recorded
```

Exceptional state:

```text
Information Provided -> Failed
```

### Stock Lifecycle

```text
Available
  -> Low Stock
  -> Out Of Stock
```

Adjustment path:

```text
Available -> Adjusted -> Available
```

### Receipt Lifecycle

```text
Ready
  -> Issued
```

Exceptional path:

```text
Ready -> Failed To Issue -> Reissued
```

## Persistence Strategy

### Laravel Persistence Baseline

- Use Laravel migrations as the source of truth for physical schema.
- Use Eloquent models as the default persistence interface.
- Use relationships with explicit return types when implemented.
- Use casts for money-like decimals, booleans, dates, enums, and structured metadata.
- Use local scopes for reusable query constraints such as active products, completed sales, and low stock products.
- Avoid raw SQL for user-driven workflows.

### Migration Strategy

Future migration work should:

- Use `php artisan make:migration`.
- Keep one concern per migration.
- Avoid modifying deployed migrations.
- Add indexes when creating schema for common filters, joins, ordering, and reporting.
- Keep destructive changes as explicit forward migrations.

### Consistency Strategy

Sale completion should be the primary transactional boundary.

The future implementation should persist these changes consistently:

- Sale fact.
- Sale items.
- Payment record.
- Stock reductions.
- Receipt readiness.

Inventory-sensitive workflows should consider:

- Transaction boundaries.
- Row-level locking or atomic locks where stock races are possible.
- Clear failure handling for insufficient stock.

### Reporting Strategy

Reporting should read from completed business facts.

Primary report sources:

- Completed sales.
- Sale items.
- Payment records.
- Product catalog.
- Stock state.
- Stock movements.

Reporting queries should:

- Avoid mutating source records.
- Use eager loading or aggregate queries to avoid N+1 behavior.
- Use indexes aligned with common report filters.

### Backward Compatibility Strategy

Existing product and transaction structures should not be rewritten immediately.

Recommended migration path:

1. Document the canonical model as the target.
2. Map existing Product and Transaction concepts to canonical Product, Sale, Sale Item, Payment, and Stock concepts.
3. Introduce missing persistence concepts only when future feature missions require them.
4. Preserve existing user-facing behavior while improving consistency boundaries.
5. Add tests before changing persistence for sale completion or inventory.

## Physical Schema Design Guidance

This document does not define physical tables, but future schema design should consider these table candidates:

- users
- roles or role assignments
- products
- categories
- units
- sales or transactions
- sale_items or transaction_items
- payments
- stock_movements
- stock_adjustments
- store_settings

Naming should follow Laravel conventions and existing repository naming unless a future migration mission approves a rename strategy.

## Data Model Risks

- Sale completion can become inconsistent if sale, payment, and stock are persisted separately without a transaction boundary.
- Product catalog changes can make historical sales unclear if sale-time product labels and prices are not preserved.
- Inventory auditability will be weak if stock quantity changes without movement records.
- Reporting can become slow if completed sales and stock movement queries are not indexed around real access patterns.
- Premature role complexity can slow MVP development.

## Readiness Statement

The canonical data model is ready to guide physical schema design, migration planning, Eloquent model design, and data access implementation in future missions.

