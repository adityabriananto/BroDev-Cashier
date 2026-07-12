# Application Behavior Design

**Mission:** PROJECT-006  
**Product:** BroDev Cashier  
**Deliverable:** Implement core logic for application behavior definition

## Purpose

This document defines the application behavior of BroDev Cashier by translating approved business workflows, architecture, and canonical data models into use cases, commands, queries, application services, interaction patterns, and state transitions.

This is a behavior model only. It remains independent from controllers, APIs, routes, Blade views, and user interfaces. It does not implement features or modify application code.

## Design Inputs

This behavior design is based on:

- PROJECT-003 Business Workflow Specification.
- PROJECT-004 Architecture Baseline.
- PROJECT-005 Canonical Data Model Design.
- Laravel best-practice guidance for single-purpose actions, dependency injection, validation, authorization, and focused testing.

## Behavior Design Principles

- Application behavior belongs in the application layer, not in controllers or views.
- Commands represent intent to change business state.
- Queries represent intent to read business state.
- Application services coordinate use cases and transaction boundaries.
- Domain rules decide business validity.
- Infrastructure provides persistence and technical adapters.
- Sale completion is the primary high-risk consistency boundary.
- Behavior must remain compatible with existing Laravel MVC while guiding future extraction into actions and services.

## Application Behavior Boundaries

### Command Behavior

Commands change business state and must validate preconditions before changing source facts.

Command examples:

- Start sale.
- Add sale item.
- Change sale item quantity.
- Complete sale.
- Record payment.
- Adjust stock.
- Activate product.
- Deactivate product.

### Query Behavior

Queries read business state and must not mutate source facts.

Query examples:

- List active products for cashier selection.
- Get current sale summary.
- Get product stock status.
- Get daily sales summary.
- Get payment method summary.
- Get low stock summary.

### Application Services

Application services coordinate workflows that span more than one domain.

Service examples:

- Cashier application service.
- Sales application service.
- Inventory application service.
- Payment application service.
- Reporting application service.

## Use Case Catalogue

### Start Sale

Purpose:

- Begin an active cashier workflow.

Command:

- `StartSaleCommand`

Inputs:

- Cashier identity.
- Optional initial context such as business date or register context.

Application behavior:

1. Confirm the user is allowed to operate cashier workflow.
2. Initialize a draft sale state.
3. Return a sale session representation to the caller.

Output:

- Draft sale representation.

State transition:

```text
None -> Draft Sale
```

Failure cases:

- User is not authorized for cashier workflow.

### Add Item To Sale

Purpose:

- Add a sellable product to the active sale.

Command:

- `AddSaleItemCommand`

Inputs:

- Draft sale identity.
- Product identity.
- Quantity.

Application behavior:

1. Confirm the sale is still editable.
2. Load product catalog information.
3. Confirm product is active and sellable.
4. Check inventory availability for requested quantity.
5. Add item or increase matching item quantity.
6. Recalculate sale totals.

Output:

- Updated sale summary.

State transition:

```text
Draft Sale -> Item Selection
Item Selection -> Total Calculated
```

Failure cases:

- Product is inactive.
- Product cannot be found.
- Quantity is invalid.
- Requested quantity exceeds available stock.

### Change Sale Item Quantity

Purpose:

- Adjust the quantity of an item in an active sale.

Command:

- `ChangeSaleItemQuantityCommand`

Inputs:

- Draft sale identity.
- Sale item identity.
- Requested quantity.

Application behavior:

1. Confirm the sale is editable.
2. Confirm quantity is greater than zero.
3. Confirm inventory can support the requested quantity.
4. Update sale item quantity.
5. Recalculate sale totals.

Output:

- Updated sale summary.

State transition:

```text
Item Selection -> Quantity Review -> Total Calculated
```

Failure cases:

- Sale is already completed.
- Quantity is invalid.
- Requested quantity exceeds available stock.

### Remove Sale Item

Purpose:

- Remove an item from an active sale.

Command:

- `RemoveSaleItemCommand`

Inputs:

- Draft sale identity.
- Sale item identity.

Application behavior:

1. Confirm the sale is editable.
2. Remove the selected item.
3. Recalculate sale totals.
4. If no items remain, return the sale to draft state.

Output:

- Updated sale summary.

State transition:

```text
Item Selection -> Total Calculated
Item Selection -> Draft Sale
```

Failure cases:

- Sale is already completed.
- Item is not part of the sale.

### Select Payment Method

Purpose:

- Choose how the active sale will be paid.

Command:

- `SelectPaymentMethodCommand`

Inputs:

- Draft sale identity.
- Payment method.

Application behavior:

1. Confirm sale has at least one item.
2. Confirm totals have been calculated.
3. Confirm payment method is supported.
4. Move sale into payment pending behavior.

Output:

- Payment pending representation.

State transition:

```text
Total Calculated -> Payment Pending
Payment Pending -> Payment Method Selected
```

Failure cases:

- Sale has no items.
- Payment method is unsupported.
- Sale is not ready for payment.

### Record Cash Payment

Purpose:

- Record cash payment details and determine change.

Command:

- `RecordCashPaymentCommand`

Inputs:

- Sale identity.
- Cash received amount.

Application behavior:

1. Confirm sale is payment pending.
2. Confirm cash payment method is selected.
3. Confirm cash received covers grand total.
4. Calculate change amount.
5. Mark payment as accepted for sale completion.

Output:

- Accepted payment summary.

State transition:

```text
Payment Pending -> Payment Accepted
```

Failure cases:

- Cash received is below grand total.
- Payment method is not cash.
- Sale is not payment pending.

### Record Non-Cash Payment

Purpose:

- Record QRIS or bank transfer payment confirmation.

Command:

- `RecordNonCashPaymentCommand`

Inputs:

- Sale identity.
- Payment method.
- Payment amount.
- Optional payment reference.
- Confirmation status.

Application behavior:

1. Confirm sale is payment pending.
2. Confirm selected payment method is QRIS or bank transfer.
3. Confirm payment amount satisfies grand total.
4. Confirm business payment confirmation is sufficient.
5. Mark payment as accepted for sale completion.

Output:

- Accepted payment summary.

State transition:

```text
Payment Pending -> Payment Information Provided -> Payment Accepted
```

Failure cases:

- Payment is not confirmed.
- Payment amount is below grand total.
- Payment method is unsupported.

### Complete Sale

Purpose:

- Finalize a cashier transaction and produce durable source facts.

Command:

- `CompleteSaleCommand`

Inputs:

- Sale identity.
- Cashier identity.

Application behavior:

1. Confirm cashier is authorized.
2. Confirm sale contains at least one item.
3. Confirm all products are still sellable.
4. Confirm inventory availability.
5. Confirm accepted payment exists.
6. Persist completed sale facts.
7. Persist sale item facts.
8. Persist payment facts.
9. Persist stock reductions or stock movement facts.
10. Mark receipt as ready or issue receipt representation.
11. Return completed sale summary.

Output:

- Completed sale summary.
- Receipt-ready representation.

State transition:

```text
Payment Accepted -> Completed Sale -> Receipt Ready
```

Consistency boundary:

- Sale facts, sale items, payment, and stock reductions must succeed or fail together.

Failure cases:

- Unauthorized cashier.
- Sale has no items.
- Payment is not accepted.
- Inventory is insufficient.
- Product became inactive.
- Persistence fails.

### Issue Receipt

Purpose:

- Provide proof of completed sale.

Command:

- `IssueReceiptCommand`

Inputs:

- Completed sale identity.

Application behavior:

1. Confirm sale is completed.
2. Build receipt content from completed sale and payment facts.
3. Mark receipt issued when issuance tracking is required.
4. Return receipt representation.

Output:

- Receipt representation.

State transition:

```text
Receipt Ready -> Receipt Issued
```

Failure cases:

- Sale is not completed.
- Receipt output fails.

Rule:

- Receipt output failure must not invalidate a completed sale.

### Create Or Update Product

Purpose:

- Maintain product catalog data.

Command:

- `SaveProductCommand`

Inputs:

- Product identity when updating.
- SKU.
- Name.
- Selling price.
- Category.
- Unit.
- Active status.

Application behavior:

1. Confirm user is authorized for product management.
2. Validate product identity data.
3. Validate selling price.
4. Save product catalog facts.
5. Return product summary.

Output:

- Product summary.

State transition:

```text
Draft Product -> Active Product
Active Product -> Inactive Product
Inactive Product -> Active Product
```

Failure cases:

- Unauthorized user.
- Missing product identity.
- Invalid selling price.
- Duplicate SKU when uniqueness is required.

### Adjust Stock

Purpose:

- Apply manual stock correction with a business reason.

Command:

- `AdjustStockCommand`

Inputs:

- Product identity.
- Quantity change.
- Business reason.
- Responsible user.

Application behavior:

1. Confirm user is authorized for inventory adjustment.
2. Confirm product exists.
3. Confirm business reason is present.
4. Confirm adjustment does not violate inventory rules.
5. Record stock adjustment.
6. Update stock state.
7. Evaluate low stock condition.

Output:

- Updated stock summary.

State transition:

```text
Stock Available -> Stock Adjusted -> Stock Available
Stock Available -> Low Stock
Stock Available -> Out Of Stock
```

Failure cases:

- Unauthorized user.
- Missing business reason.
- Invalid quantity change.
- Product cannot be found.

### Request Sales Report

Purpose:

- Read sales activity from completed source facts.

Query:

- `SalesReportQuery`

Inputs:

- Date range.
- Optional cashier filter.
- Optional payment method filter.

Application behavior:

1. Confirm user is authorized for reporting.
2. Query completed sales only.
3. Summarize totals and counts.
4. Return read-only report data.

Output:

- Sales summary.

State transition:

```text
Report Requested -> Source Facts Selected -> Summary Prepared
```

Failure cases:

- Unauthorized user.
- Invalid reporting criteria.

Rule:

- Reporting behavior must not mutate source facts.

### Request Stock Report

Purpose:

- Read current stock and low stock conditions.

Query:

- `StockReportQuery`

Inputs:

- Optional product filter.
- Optional low stock filter.

Application behavior:

1. Confirm user is authorized for reporting or inventory review.
2. Query stock and product catalog data.
3. Return stock status summaries.

Output:

- Stock summary.

Failure cases:

- Unauthorized user.
- Invalid criteria.

## Application Service Map

### CashierApplicationService

Coordinates active checkout behavior.

Use cases:

- Start sale.
- Add sale item.
- Change sale item quantity.
- Remove sale item.
- Select payment method.

Collaborators:

- Product catalog behavior.
- Inventory behavior.
- Payment behavior.
- Sales behavior.

### SalesApplicationService

Coordinates completed sale behavior.

Use cases:

- Complete sale.
- Void sale when future workflow approves it.

Collaborators:

- Inventory service.
- Payment service.
- Receipt service.
- Reporting source facts.

### ProductCatalogApplicationService

Coordinates product management behavior.

Use cases:

- Save product.
- Activate product.
- Deactivate product.
- Query active products for sale.

Collaborators:

- Authorization boundary.
- Product catalog persistence.

### InventoryApplicationService

Coordinates stock behavior.

Use cases:

- Check stock availability.
- Reduce stock for completed sale.
- Adjust stock.
- Detect low stock.

Collaborators:

- Product catalog.
- Sales completion.
- Reporting.

### PaymentApplicationService

Coordinates payment behavior.

Use cases:

- Select payment method.
- Record cash payment.
- Record QRIS payment.
- Record bank transfer payment.

Collaborators:

- Sale totals.
- Future external payment boundary if needed.

### ReceiptApplicationService

Coordinates receipt behavior.

Use cases:

- Issue receipt.
- Reissue receipt.

Collaborators:

- Completed sale facts.
- Payment facts.
- Future receipt output adapter.

### ReportingApplicationService

Coordinates read-only business summaries.

Use cases:

- Sales report.
- Payment method report.
- Stock report.
- Low stock report.

Collaborators:

- Sales facts.
- Payment facts.
- Inventory facts.
- Product catalog facts.

## Command and Query Naming Guidance

Commands should be named with imperative verbs:

- `StartSaleCommand`
- `AddSaleItemCommand`
- `RecordCashPaymentCommand`
- `CompleteSaleCommand`
- `AdjustStockCommand`

Queries should be named by requested read model:

- `ActiveProductsQuery`
- `CurrentSaleSummaryQuery`
- `SalesReportQuery`
- `StockReportQuery`
- `PaymentMethodSummaryQuery`

Application actions should be single-purpose:

- `StartSaleAction`
- `AddSaleItemAction`
- `CompleteSaleAction`
- `AdjustStockAction`
- `BuildSalesReportAction`

## Interaction Patterns

### Command Handling Pattern

```text
Caller intent
  -> Command object or validated input
  -> Authorization check
  -> Application action
  -> Domain rule validation
  -> Persistence coordination
  -> Result object
```

### Query Handling Pattern

```text
Caller intent
  -> Query object or criteria
  -> Authorization check
  -> Read model query
  -> Summary/result object
```

### Sale Completion Pattern

```text
CompleteSaleCommand
  -> authorize cashier
  -> validate sale readiness
  -> validate payment acceptance
  -> validate stock availability
  -> open consistency boundary
  -> persist sale facts
  -> persist payment facts
  -> persist stock impact
  -> prepare receipt
  -> return completed sale result
```

### Reporting Pattern

```text
ReportQuery
  -> authorize report access
  -> validate criteria
  -> read completed source facts
  -> aggregate summary
  -> return read-only report result
```

## State Transition Summary

### Sale State

```text
Draft
  -> Item Selection
  -> Total Calculated
  -> Payment Pending
  -> Payment Accepted
  -> Completed
  -> Receipt Ready
  -> Receipt Issued
```

Invalid transitions:

- Draft directly to Completed.
- Payment Pending to Completed without Payment Accepted.
- Completed back to Item Selection.

### Product State

```text
Draft
  -> Active
  -> Inactive
```

Invalid transitions:

- Draft to Active without identity and price validation.
- Inactive product into sale item selection.

### Payment State

```text
Pending
  -> Method Selected
  -> Information Provided
  -> Accepted
  -> Recorded
```

Invalid transitions:

- Pending directly to Recorded.
- Information Provided to Accepted when amount is insufficient.

### Stock State

```text
Available
  -> Low Stock
  -> Out Of Stock
  -> Adjusted
```

Invalid transitions:

- Available to reduced stock when requested quantity exceeds availability.
- Adjusted without a business reason.

### Receipt State

```text
Ready
  -> Issued
  -> Reissued
```

Invalid transitions:

- Ready before sale completion.

## Validation Boundaries

### Request-Level Validation

Future Form Requests should validate input shape:

- Required fields.
- Numeric quantity and amount shape.
- Supported payment method values.
- Date range format.

### Application-Level Validation

Application actions should validate workflow preconditions:

- Sale is editable.
- Sale is ready for payment.
- Payment is accepted.
- User can perform workflow.

### Domain-Level Validation

Domain rules should validate business truth:

- Stock availability.
- Product sellability.
- Sale total correctness.
- Payment acceptance.

## Authorization Boundaries

Authorization should occur before protected use cases execute.

Protected use cases:

- Product management.
- Stock adjustment.
- Report access.
- User administration.
- Sale voiding in future workflows.

Cashier use cases require cashier responsibility.

## Error and Result Model

Application behavior should return clear results rather than leaking controller-specific concerns.

Success result examples:

- Sale started.
- Sale summary updated.
- Payment accepted.
- Sale completed.
- Report prepared.

Business failure examples:

- ProductNotSellable.
- InsufficientStock.
- PaymentNotAccepted.
- UnauthorizedAction.
- SaleNotEditable.
- ReportCriteriaInvalid.

System failure examples:

- PersistenceFailure.
- ReceiptOutputFailure.

Rule:

- ReceiptOutputFailure should not reverse a completed sale.

## Testing Behavior Guidance

Future tests should focus on application behavior, not only UI paths.

Priority behavior tests:

- Start sale requires cashier responsibility.
- Add item rejects inactive product.
- Add item rejects insufficient stock.
- Change quantity recalculates sale totals.
- Cash payment rejects amount below grand total.
- Complete sale persists sale, payment, and stock impact together.
- Complete sale fails without accepted payment.
- Stock adjustment requires business reason.
- Reports read completed source facts only.

Testing style:

- Use feature tests for user-facing workflow paths.
- Use application action tests when actions are introduced.
- Use unit tests for isolated domain rules and value objects.
- Use factories and states for setup when models are available.

## Backward Compatibility

This behavior model does not require immediate refactoring.

Recommended adoption path:

1. Keep existing controllers and views operational.
2. Introduce action classes when modifying complex workflows.
3. Move sale completion behavior out of controllers first.
4. Add Form Requests for complex input.
5. Add policies or gates for protected workflows.
6. Add behavior tests before changing persistence behavior.

## Readiness Statement

This application behavior design is ready to guide future feature implementation while remaining independent from controllers, APIs, and user interfaces. Future implementation missions should use these use cases, commands, queries, services, interaction patterns, and state transitions as the application-layer contract.

