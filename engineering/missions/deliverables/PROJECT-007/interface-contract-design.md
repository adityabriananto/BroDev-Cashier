# Interface Contract Design

**Mission:** PROJECT-007  
**Product:** BroDev Cashier  
**Deliverable:** Implement core logic for external interface contract definition

## Purpose

This document defines external interface contracts for BroDev Cashier by translating approved application behaviors into controller responsibilities, request and response contracts, validation rules, authorization rules, view models, and interaction boundaries.

This is a contract design document only. It does not implement controllers, APIs, Blade views, routes, frontend integrations, or application features.

## Design Inputs

This contract design is based on:

- PROJECT-004 Architecture Baseline.
- PROJECT-005 Canonical Data Model Design.
- PROJECT-006 Application Behavior Design.
- Current Laravel route and view baseline.
- Laravel best-practice guidance for thin controllers, Form Requests, authorization, Blade views, and route conventions.

## Existing Interface Baseline

The current application has these interface areas:

- Cashier workspace at `/`.
- Product listing endpoint at `/api/products`.
- Checkout endpoint at `/api/checkout`.
- Authentication views and actions at `/login` and `/logout`.
- Admin dashboard at `/admin/dashboard`.
- Admin product management at `/admin/products` and related product endpoints.
- Admin transaction view at `/admin/transactions` and transaction detail endpoint.

The contract below preserves these broad responsibilities while defining a cleaner target blueprint for future implementation.

## Interface Principles

- Controllers should be thin and delegate behavior to application actions or services.
- Form Requests should own request shape validation and authorization where appropriate.
- Controllers should not contain complex sale, stock, payment, or reporting logic.
- Interface contracts should map to application commands and queries from PROJECT-006.
- View models should be prepared before rendering views.
- Reporting interfaces must be read-only.
- API-like JSON endpoints should return predictable success and failure shapes.
- Browser workflow endpoints should return redirects, validation errors, or views consistently.

## Interface Areas

### Cashier Interface

Purpose:

- Support active sale workflow, product lookup, checkout, payment, and receipt handoff.

Application behaviors:

- Start sale.
- Add item to sale.
- Change sale item quantity.
- Remove sale item.
- Select payment method.
- Record payment.
- Complete sale.
- Issue receipt.

Target controller responsibility:

- Accept cashier intent.
- Validate request shape.
- Authorize cashier workflow.
- Call application behavior.
- Return cashier view model or JSON result.

### Product Management Interface

Purpose:

- Support admin product catalog maintenance.

Application behaviors:

- Create product.
- Update product.
- Activate product.
- Deactivate product.
- Restore product when supported.
- Query active products.

Target controller responsibility:

- Accept product management intent.
- Validate product input.
- Authorize product management.
- Call product application behavior.
- Return product view model or JSON result.

### Inventory Interface

Purpose:

- Support stock review and future manual adjustment workflows.

Application behaviors:

- Request stock report.
- Adjust stock.
- Detect low stock.

Target controller responsibility:

- Validate adjustment or filter input.
- Authorize inventory responsibility.
- Call inventory application behavior.
- Return stock summary or adjustment result.

### Transaction and Reporting Interface

Purpose:

- Support admin or manager review of completed sales, payments, and operational summaries.

Application behaviors:

- Request sales report.
- Request stock report.
- Request payment summary.
- View transaction detail.

Target controller responsibility:

- Validate report criteria.
- Authorize reporting access.
- Call reporting query behavior.
- Return read-only report view models.

### Authentication Interface

Purpose:

- Support login, logout, and future access responsibility boundaries.

Application behaviors:

- Authenticate user.
- End user session.
- Enforce route access.

Target controller responsibility:

- Validate credentials.
- Manage session boundary.
- Redirect users based on responsibility.

## Controller Responsibility Map

### Cashier Controller Contract

Responsibilities:

- Render cashier workspace.
- Provide active product selection data.
- Accept checkout intent.
- Delegate sale completion to application behavior.
- Return receipt-ready data after completion.

Should not:

- Calculate complex sale totals inline.
- Mutate product stock directly outside sale completion behavior.
- Own payment acceptance rules.
- Own reporting logic.

Mapped behaviors:

- `ActiveProductsQuery`
- `CurrentSaleSummaryQuery`
- `CompleteSaleCommand`
- `RecordCashPaymentCommand`
- `RecordNonCashPaymentCommand`
- `IssueReceiptCommand`

### Admin Product Controller Contract

Responsibilities:

- Render product management view.
- Accept product create, update, deactivate, and restore intent.
- Delegate product behavior to application actions.
- Return product result data.

Should not:

- Contain product business rules inline.
- Bypass Form Request validation.
- Delete or restore products without authorization.

Mapped behaviors:

- `SaveProductCommand`
- `ActivateProductCommand`
- `DeactivateProductCommand`
- `ActiveProductsQuery`

### Admin Dashboard Controller Contract

Responsibilities:

- Render operational dashboard.
- Request dashboard summary data from reporting queries.
- Return read-only view model.

Should not:

- Mutate sales, stock, payment, or product records.
- Execute heavy report logic inside the controller method.

Mapped behaviors:

- `SalesReportQuery`
- `StockReportQuery`
- `PaymentMethodSummaryQuery`

### Transaction Controller Contract

Responsibilities:

- Render transaction listing.
- Return transaction detail.
- Delegate read behavior to reporting or sales query service.

Should not:

- Change transaction state unless future void workflow is explicitly approved.

Mapped behaviors:

- `SalesReportQuery`
- `TransactionDetailQuery`

### Auth Controller Contract

Responsibilities:

- Render login form.
- Validate login request.
- Authenticate credentials.
- End session on logout.
- Redirect after login/logout.

Should not:

- Embed unrelated dashboard, cashier, product, or reporting logic.

## Request Contracts

### Login Request

Intent:

- Authenticate a user.

Fields:

- Email or username credential.
- Password.
- Remember option when supported.

Validation:

- Credential is required.
- Password is required.
- Credential format must match selected login strategy.

Authorization:

- Public route.

Success response:

- Redirect to authorized landing area.

Failure response:

- Return validation/authentication error without exposing credential existence.

### Product Save Request

Intent:

- Create or update product catalog data.

Fields:

- SKU.
- Name.
- Selling price.
- Stock-related input when current MVP requires it.
- Category when enabled.
- Unit when enabled.
- Active status.

Validation:

- SKU is required when product identity requires it.
- Name is required.
- Selling price must be numeric and not negative.
- Active status must be boolean-like.
- Category and unit must be valid when enabled.
- SKU uniqueness should be enforced when required by business rule.

Authorization:

- Product management responsibility.

Success response:

- Product summary.
- Redirect or JSON success depending on interaction style.

Failure response:

- Validation error map.
- Authorization failure when responsibility is missing.

### Product Delete or Deactivate Request

Intent:

- Remove product from active sale availability.

Fields:

- Product identity.

Validation:

- Product identity is required and must reference an existing product concept.

Authorization:

- Product management responsibility.

Success response:

- Product inactive or deleted status.

Failure response:

- Product not found.
- Unauthorized action.
- Product cannot be removed when business rules prohibit it.

### Product Restore Request

Intent:

- Restore a previously removed product when supported.

Fields:

- Product identity.

Validation:

- Product identity is required.

Authorization:

- Product management responsibility.

Success response:

- Restored product summary.

Failure response:

- Product not found.
- Product not restorable.
- Unauthorized action.

### Product Lookup Request

Intent:

- Retrieve product data for cashier selection.

Fields:

- Optional search term.
- Optional category.
- Optional active-only flag.

Validation:

- Search term length should be bounded.
- Category must be valid when provided.

Authorization:

- Cashier responsibility or public cashier access policy, depending on final auth design.

Success response:

- List of sellable product summaries.

Failure response:

- Invalid filter criteria.

### Checkout Request

Intent:

- Complete a sale.

Fields:

- Sale items.
- Product identity per item.
- Quantity per item.
- Payment method.
- Payment amount or confirmation.
- Optional payment reference.

Validation:

- At least one sale item is required.
- Each quantity must be greater than zero.
- Payment method must be supported.
- Cash received is required for cash payments.
- Payment confirmation or reference may be required for non-cash payments.

Authorization:

- Cashier responsibility.

Success response:

- Completed sale summary.
- Receipt-ready data.

Failure response:

- Product not sellable.
- Insufficient stock.
- Payment not accepted.
- Sale validation errors.
- Unauthorized action.

### Stock Adjustment Request

Intent:

- Apply manual stock correction.

Fields:

- Product identity.
- Quantity change.
- Business reason.

Validation:

- Product identity is required.
- Quantity change is required and non-zero.
- Business reason is required.

Authorization:

- Inventory management responsibility.

Success response:

- Updated stock summary.

Failure response:

- Invalid quantity.
- Missing reason.
- Product not found.
- Unauthorized action.

### Report Request

Intent:

- Retrieve read-only operational summaries.

Fields:

- Date range.
- Optional cashier filter.
- Optional payment method filter.
- Optional product filter.
- Optional low stock filter.

Validation:

- Date range must be valid.
- Filters must reference valid business concepts when provided.
- Date range length may be bounded for performance.

Authorization:

- Reporting responsibility.

Success response:

- Report view model.

Failure response:

- Invalid criteria.
- Unauthorized action.

## Response Contracts

### Standard JSON Success

Shape:

```text
status: success
message: human-readable result
data: result payload
```

Usage:

- Product mutation endpoints.
- Product lookup.
- Checkout.
- Transaction detail.

### Standard JSON Validation Failure

Shape:

```text
status: error
message: validation failed
errors: field-level error map
```

Usage:

- Invalid request shape.
- Conditional validation failures.

### Standard JSON Business Failure

Shape:

```text
status: error
message: business-readable failure
code: business failure code
details: optional context
```

Business failure codes:

- `product_not_sellable`
- `insufficient_stock`
- `payment_not_accepted`
- `sale_not_editable`
- `unauthorized_action`
- `report_criteria_invalid`

### Browser View Success

Shape:

```text
view: target view
view_model: prepared data for rendering
flash: optional success message
```

Usage:

- Cashier workspace.
- Admin dashboard.
- Product management.
- Transaction list.
- Login form.

### Browser Redirect Success

Shape:

```text
redirect: named target
flash: success message
```

Usage:

- Login.
- Logout.
- Product form submission when using non-AJAX flow.

## View Model Contracts

### Cashier Workspace View Model

Purpose:

- Render cashier sale workspace.

Data:

- Cashier identity summary.
- Active product summaries.
- Current sale summary.
- Supported payment methods.
- Tax display configuration.
- Receipt configuration.

Rules:

- Product summaries should include only fields needed for selection.
- No database queries should be executed from Blade.

### Product Summary View Model

Purpose:

- Present product catalog information.

Data:

- Product identity.
- SKU.
- Name.
- Selling price.
- Stock status summary.
- Active or inactive status.
- Category label when enabled.
- Unit label when enabled.

Rules:

- Product availability should be precomputed before rendering.

### Sale Summary View Model

Purpose:

- Present active or completed sale information.

Data:

- Sale identity.
- Item summaries.
- Subtotal.
- Tax amount.
- Grand total.
- Payment status.
- Cashier summary.
- Sale state.

Rules:

- Totals should come from application behavior, not Blade calculations.

### Receipt View Model

Purpose:

- Present proof of completed sale.

Data:

- Sale identity.
- Completion time.
- Cashier label.
- Item lines.
- Subtotal.
- Tax.
- Grand total.
- Payment method.
- Cash received and change when applicable.
- Store receipt display settings.

Rules:

- Receipt can be re-rendered from completed sale facts.
- Receipt rendering failure must not invalidate sale completion.

### Dashboard View Model

Purpose:

- Present operational summary.

Data:

- Sales totals.
- Payment method totals.
- Low stock summary.
- Recent transactions.
- Reporting period.

Rules:

- Dashboard data must be read-only.

### Transaction Detail View Model

Purpose:

- Present completed transaction facts.

Data:

- Sale identity.
- Completion time.
- Cashier.
- Item lines.
- Payment details.
- Totals.
- Stock impact summary when available.

Rules:

- Transaction detail is read-only unless a future void workflow is approved.

## Authorization Contract

### Public Access

- Login form.
- Login submission.

### Cashier Responsibility

- Cashier workspace.
- Product lookup for sale.
- Checkout.
- Receipt issue or reissue for completed cashier sale.

### Product Management Responsibility

- Product management view.
- Product create.
- Product update.
- Product deactivate.
- Product restore.

### Inventory Responsibility

- Stock adjustment.
- Low stock review when separated from reporting.

### Reporting Responsibility

- Dashboard.
- Transaction list.
- Transaction detail.
- Sales report.
- Stock report.
- Payment summary.

### Administration Responsibility

- User and settings workflows when introduced.

## Validation Contract

### Validation Location

- Form Requests should validate request shape and authorization where possible.
- Application actions should validate workflow state.
- Domain rules should validate business truth.

### Conditional Validation Examples

- Cash received is required when payment method is cash.
- Payment reference may be required when payment method is QRIS or bank transfer.
- Business reason is required when stock is manually adjusted.
- Date range is required when report type needs a period.

## Interaction Boundaries

### Controller to Application Boundary

Controllers may pass validated input to commands or actions.

Controllers must not:

- Build complex business decisions.
- Directly coordinate stock reduction and payment persistence.
- Query inside Blade by passing incomplete view data.

### Application to View Boundary

Application behavior returns result data.

Controllers transform result data into:

- View models.
- JSON response payloads.
- Redirects with flash messages.

### Browser to JSON Boundary

JSON endpoints should return stable success and error shapes.

Rules:

- Business failure codes should be predictable.
- Validation errors should be field-addressable.
- Sensitive internal exception details should not be exposed.

### Browser to Blade Boundary

Blade views should receive complete view models.

Rules:

- No direct database queries in views.
- Keep UI calculations minimal.
- Prefer components for repeated interface elements when implementation begins.

## Backward Compatibility

This contract preserves existing interface areas while defining cleaner future boundaries.

Existing areas to preserve:

- Cashier root workspace.
- Product lookup endpoint.
- Checkout endpoint.
- Login and logout routes.
- Admin dashboard.
- Admin product management.
- Admin transactions.

Recommended adoption path:

1. Keep existing route names stable where practical.
2. Introduce Form Requests around existing POST, PUT, DELETE, and checkout endpoints.
3. Extract application behavior from controllers into actions.
4. Standardize JSON response shapes.
5. Introduce view model preparation before Blade rendering.
6. Add policies or gates for protected workflows.

## Readiness Statement

This interface contract design is ready to guide controller, request, response, Blade, API, and frontend integration implementation. Future implementation missions should use this contract as the boundary between user-facing interfaces and application behavior.

