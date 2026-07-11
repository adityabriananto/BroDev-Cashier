# Business Workflow Specification

**Mission:** PROJECT-003  
**Product:** BroDev Cashier  
**Deliverable:** Implement core logic for business workflow definition

## Purpose

This document defines the complete business workflows of BroDev Cashier. It describes end-to-end business processes, cross-domain interactions, exception flows, and state transitions.

This is a business workflow specification only. It does not define software implementation, database tables, API endpoints, or UI screens.

## Workflow Principles

- Cashier operations must stay fast, clear, and reliable.
- Sales completion is the central business workflow.
- Product, inventory, payment, receipt, reporting, and administration concepts must support the sale workflow without weakening it.
- Completed business records should be treated as source facts for reporting.
- Exceptions such as insufficient stock, invalid payment, inactive product, and unauthorized access must stop or redirect the workflow before business records become inconsistent.

## Primary Workflow: Cashier Sale Completion

### Business Goal

A cashier completes a retail transaction for one or more products, records payment, updates inventory concepts, and provides a receipt.

### Participating Domains

- Cashier Operations
- Product Catalog
- Inventory
- Sales
- Payments
- Receipts
- Reporting
- Administration

### Main Flow

1. Cashier starts a sale.
2. Cashier selects a product from the product catalog.
3. Product Catalog confirms the product is identifiable and active for sale.
4. Cashier sets or adjusts item quantity.
5. Inventory confirms the requested quantity is available.
6. Cashier Operations calculates subtotal, tax, and grand total.
7. Cashier records payment method and payment amount or confirmation.
8. Payments validates that payment satisfies the sale total.
9. Cashier completes the sale.
10. Sales records the completed transaction as a business fact.
11. Inventory records the stock reduction caused by the completed sale.
12. Receipts issues the customer-facing proof of sale.
13. Reporting can include the completed sale, payment, and stock impact in business summaries.

### Success Result

- Sale is completed.
- Payment is recorded.
- Stock impact is recognized.
- Receipt is issued.
- Reporting source data is available.

### Business State Transition

```text
Draft Sale
  -> Item Selection
  -> Quantity Review
  -> Total Calculated
  -> Payment Pending
  -> Payment Accepted
  -> Completed Sale
  -> Receipt Issued
```

### Exception Flows

#### Product Not Available For Sale

If the selected product is inactive or not sellable:

1. Product Catalog rejects the selection.
2. Cashier Operations removes or blocks the product from the sale.
3. The sale remains in item selection state.

#### Insufficient Stock

If requested quantity exceeds available stock:

1. Inventory rejects the requested quantity.
2. Cashier Operations asks for a lower quantity or product removal.
3. The sale cannot proceed to payment until quantities are valid.

#### Missing Payment Information

If payment information is incomplete:

1. Payments rejects the payment attempt.
2. The sale remains payment pending.
3. Cashier must correct payment method, amount, or confirmation.

#### Cash Payment Below Grand Total

If cash received is less than grand total:

1. Payments rejects payment acceptance.
2. Cashier Operations keeps the sale in payment pending state.
3. Sale cannot complete until payment covers the grand total.

#### Unauthorized Cashier Action

If a user is not allowed to perform cashier workflow:

1. Administration rejects the workflow access.
2. No sale workflow is started or completed.

## Product Management Workflow

### Business Goal

An admin maintains product business information so cashiers can sell the correct items at the correct price.

### Participating Domains

- Administration
- Product Catalog
- Inventory
- Cashier Operations
- Reporting

### Main Flow

1. Admin accesses product management.
2. Administration confirms the user has management responsibility.
3. Admin creates or updates product information.
4. Product Catalog validates product identity, SKU, name, price, and active status.
5. Product becomes available or unavailable for cashier selection based on its business status.
6. Reporting can use updated product information for future summaries.

### Business State Transition

```text
Product Draft
  -> Product Validated
  -> Active Product
  -> Inactive Product
```

### Exception Flows

#### Missing Product Identity

If product identity is incomplete:

1. Product Catalog rejects the change.
2. Product remains unavailable for sale.

#### Invalid Selling Price

If selling price is not valid for sale:

1. Product Catalog rejects product activation.
2. Admin must correct the price before the product can be sold.

#### Unauthorized Product Change

If the user lacks management responsibility:

1. Administration rejects the action.
2. Product Catalog remains unchanged.

## Inventory Workflow

### Business Goal

The store maintains accurate stock visibility through sale-driven stock reduction, manual stock adjustment, and low stock awareness.

### Participating Domains

- Inventory
- Product Catalog
- Sales
- Administration
- Reporting

### Main Flow: Sale-Driven Stock Reduction

1. A sale is completed.
2. Sales identifies the sold products and quantities.
3. Inventory records stock reduction for each sold product.
4. Inventory evaluates whether any product reaches low stock condition.
5. Reporting can summarize stock changes and low stock conditions.

### Main Flow: Manual Stock Adjustment

1. Authorized admin starts a stock adjustment.
2. Administration confirms permission.
3. Inventory receives the product, quantity change, and business reason.
4. Inventory records the adjustment as distinct from sale-driven stock reduction.
5. Inventory evaluates low stock condition.
6. Reporting can include adjustment activity in stock summaries.

### Business State Transition

```text
Stock Available
  -> Stock Reserved For Sale Review
  -> Stock Reduced
  -> Low Stock Detected
  -> Stock Adjusted
```

### Exception Flows

#### Adjustment Without Business Reason

If a manual stock correction lacks a reason:

1. Inventory rejects the adjustment.
2. Stock remains unchanged.

#### Adjustment By Unauthorized User

If the user lacks responsibility:

1. Administration rejects access.
2. Inventory records no change.

## Payment Workflow

### Business Goal

The store records how a sale was paid and ensures payment satisfies the sale total.

### Participating Domains

- Cashier Operations
- Payments
- Sales
- Reporting

### Main Flow: Cash Payment

1. Cashier selects cash payment.
2. Cashier enters cash received.
3. Payments compares cash received with grand total.
4. If cash covers the total, Payments calculates change.
5. Cashier completes the sale.

### Main Flow: QRIS Payment

1. Cashier selects QRIS payment.
2. Cashier records payment confirmation when available.
3. Payments accepts the payment when business confirmation is sufficient.
4. Cashier completes the sale.

### Main Flow: Bank Transfer Payment

1. Cashier selects bank transfer payment.
2. Cashier records payment reference or confirmation when available.
3. Payments accepts the payment when business confirmation is sufficient.
4. Cashier completes the sale.

### Business State Transition

```text
Payment Pending
  -> Payment Method Selected
  -> Payment Information Provided
  -> Payment Accepted
  -> Payment Recorded
```

### Exception Flows

#### Unsupported Payment Method

If the payment method is outside supported business methods:

1. Payments rejects the method.
2. Cashier must choose cash, QRIS, or bank transfer.

#### Payment Not Confirmed

If non-cash payment cannot be confirmed:

1. Payments keeps the sale payment pending.
2. Sale cannot complete until confirmation is acceptable.

## Receipt Workflow

### Business Goal

The customer receives proof of a completed sale.

### Participating Domains

- Sales
- Payments
- Receipts
- Cashier Operations

### Main Flow

1. Sale is completed.
2. Sales provides sale identity, items, totals, cashier, and completion time.
3. Payments provides payment method and payment result.
4. Receipts produces customer-facing sale proof.
5. Cashier provides the receipt to the customer.

### Business State Transition

```text
Completed Sale
  -> Receipt Ready
  -> Receipt Issued
```

### Exception Flows

#### Receipt Cannot Be Issued

If receipt output fails:

1. Sale remains completed.
2. Cashier may retry issuing the receipt.
3. Reporting still treats the sale as completed.

## Reporting Workflow

### Business Goal

Owners, managers, and admins review business activity using completed operational records.

### Participating Domains

- Reporting
- Sales
- Payments
- Inventory
- Product Catalog
- Administration

### Main Flow

1. Authorized user requests a report.
2. Administration confirms reporting access.
3. Reporting gathers completed business facts from sales, payments, inventory, and product catalog concepts.
4. Reporting presents summaries such as sales totals, payment method totals, stock conditions, and low stock information.
5. User reviews operational summary.

### Business State Transition

```text
Report Requested
  -> Source Facts Selected
  -> Summary Prepared
  -> Summary Reviewed
```

### Exception Flows

#### Unauthorized Report Access

If the user lacks reporting responsibility:

1. Administration rejects access.
2. No business summary is shown.

#### No Matching Business Records

If no records match the report criteria:

1. Reporting presents an empty business result.
2. Source business records remain unchanged.

## Cross-Domain Interaction Summary

| Trigger | Primary Domain | Supporting Domains | Business Result |
| --- | --- | --- | --- |
| Sale started | Cashier Operations | Product Catalog, Inventory | Active checkout workflow |
| Product selected | Product Catalog | Cashier Operations, Inventory | Sellable item added or rejected |
| Quantity changed | Cashier Operations | Inventory | Quantity accepted or rejected |
| Payment recorded | Payments | Cashier Operations, Sales | Payment accepted or pending |
| Sale completed | Sales | Inventory, Payments, Receipts, Reporting | Completed transaction |
| Stock adjusted | Inventory | Administration, Reporting | Stock correction recorded |
| Report requested | Reporting | Sales, Payments, Inventory, Product Catalog | Operational summary |

## Workflow Readiness

These workflows are ready to inform later architecture, database design, API contract design, and feature implementation. Future technical missions should preserve the business states, exception flows, and domain boundaries defined here.

