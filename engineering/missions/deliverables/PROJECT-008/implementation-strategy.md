# Implementation Strategy

**Mission:** PROJECT-008  
**Product:** BroDev Cashier  
**Deliverable:** Implement core logic for implementation strategy definition

## Purpose

This document defines the implementation strategy for BroDev Cashier by translating the approved architecture, canonical data model, application behavior, and interface contracts into an executable development roadmap.

This is an implementation planning document only. It does not modify application code, create migrations, implement controllers, or change runtime behavior.

## Planning Inputs

This strategy is based on:

- PROJECT-004 Architecture Baseline.
- PROJECT-005 Canonical Data Model Design.
- PROJECT-006 Application Behavior Design.
- PROJECT-007 Interface Contract Design.
- Current Laravel MVC repository structure.
- Laravel best-practice guidance for actions, Form Requests, migrations, tests, and thin controllers.

## Implementation Principles

- Maintain backward compatibility with existing cashier and admin flows.
- Introduce structure gradually, beginning with high-risk workflows.
- Keep controllers thin and move business behavior into actions or services.
- Use Form Requests for non-trivial request validation.
- Use Laravel migrations for schema changes and never edit deployed migrations.
- Add focused tests before changing sale, payment, stock, and product behavior.
- Preserve existing routes and route names where practical.
- Avoid repository abstractions unless they solve real complexity.

## Current Implementation Baseline

Existing code areas:

- `CashierController`
- `AdminController`
- `AuthController`
- `Product`
- `Transaction`
- `User`
- Product migrations.
- Transaction item and payment method migrations.
- Cashier, admin, auth, and layout Blade views.
- Basic feature and unit tests.

Current architecture:

```text
Routes
  -> Controllers
  -> Models
  -> Database
  -> Blade / JSON
```

Target evolution:

```text
Routes
  -> Controllers / Form Requests
  -> Application Actions
  -> Domain Rules
  -> Eloquent / Infrastructure
  -> View Models / JSON Responses
```

## Module Sequencing

### Sequence 1: Safety and Baseline Tests

Goal:

- Capture current behavior before refactoring.

Scope:

- Add focused tests for existing login, cashier index, product lookup, checkout, product management, and transaction detail behavior.
- Document current route behavior and response expectations.
- Ensure existing tests pass.

Dependencies:

- Current route and controller behavior.
- Existing factories and seeders.

Engineering checkpoints:

- `php artisan test --compact` passes for touched tests.
- No controller behavior changed.
- Existing routes remain stable.

### Sequence 2: Request Validation Boundary

Goal:

- Move request shape validation out of controllers.

Scope:

- Introduce Form Requests for product save, product update, product restore or delete, checkout, login, report filters, and future stock adjustment.
- Keep controller methods compatible with existing route inputs.
- Use validated input only.

Dependencies:

- Baseline tests from Sequence 1.

Engineering checkpoints:

- Validation failures remain user-readable.
- JSON validation errors remain predictable.
- No feature behavior changes except stricter invalid input handling.

### Sequence 3: Response and View Model Boundary

Goal:

- Standardize controller outputs without changing business behavior.

Scope:

- Define response conventions for JSON success, validation failure, and business failure.
- Prepare view models for cashier workspace, product list, dashboard, transaction detail, and receipt data.
- Ensure Blade views receive complete data and do not query directly.

Dependencies:

- Interface contracts from PROJECT-007.
- Existing Blade views.

Engineering checkpoints:

- Cashier workspace renders with the same visible behavior.
- Product and transaction endpoints retain compatible response shapes or introduce documented additions only.
- No database queries added to Blade views.

### Sequence 4: Product Catalog Foundation

Goal:

- Stabilize product management behavior.

Scope:

- Extract product save, update, deactivate, restore, and active product query into application actions.
- Preserve existing `Product` model behavior.
- Keep soft delete behavior compatible with current product workflows.

Dependencies:

- Product Form Requests.
- Product management baseline tests.

Engineering checkpoints:

- Product create, update, delete, restore, and listing tests pass.
- Controller methods are thin.
- Product business rules are testable outside controller methods.

### Sequence 5: Cashier Checkout Foundation

Goal:

- Extract checkout behavior from controller logic into a dedicated application action.

Scope:

- Introduce `CompleteSaleAction` or equivalent.
- Validate sale item input.
- Validate product sellability.
- Validate stock availability.
- Validate payment method and amount.
- Preserve current checkout response compatibility.

Dependencies:

- Checkout Form Request.
- Product catalog action or query.
- Baseline checkout tests.

Engineering checkpoints:

- Checkout happy path passes.
- Insufficient stock path passes.
- Invalid payment path passes.
- Controller delegates to application action.

### Sequence 6: Sale, Payment, and Stock Consistency Boundary

Goal:

- Ensure completed sales, payments, and stock changes remain consistent.

Scope:

- Wrap sale completion, transaction item persistence, payment persistence, and stock reduction in an explicit transaction boundary.
- Introduce stock availability check before completion.
- Preserve historical sale item clarity.
- Prepare migration plan for stock movement records if not already available.

Dependencies:

- Complete sale action.
- Data model decisions from PROJECT-005.
- Current transaction-related migrations.

Engineering checkpoints:

- Partial sale completion cannot leave inconsistent payment or stock state.
- Tests cover transaction rollback on business failure.
- Existing completed transaction records remain readable.

### Sequence 7: Inventory Movement Foundation

Goal:

- Introduce explicit inventory movement behavior when schema is approved.

Scope:

- Add stock movement and stock adjustment persistence through future migrations.
- Add inventory adjustment action.
- Add low stock detection query.
- Preserve existing product stock field until transition is complete.

Dependencies:

- Physical schema design mission or migration approval.
- Product catalog foundation.
- Sale completion consistency boundary.

Engineering checkpoints:

- Stock reductions are traceable.
- Manual adjustments require business reason.
- Low stock reporting is queryable.
- Backward compatibility with existing product stock display is preserved.

### Sequence 8: Receipt Output Boundary

Goal:

- Separate receipt data preparation from checkout controller behavior.

Scope:

- Introduce receipt view model builder or action.
- Build receipt data from completed sale and payment facts.
- Ensure receipt failure does not reverse completed sale.

Dependencies:

- Completed sale behavior.
- Payment data availability.

Engineering checkpoints:

- Receipt view renders from completed sale facts.
- Reprint or retry behavior can be added later without changing sale facts.

### Sequence 9: Reporting Foundation

Goal:

- Make reporting read-only and query-oriented.

Scope:

- Extract dashboard summaries, transaction list, transaction detail, payment summaries, and stock summaries into query actions.
- Ensure reports read completed source facts only.
- Add filters with validation.

Dependencies:

- Sale facts.
- Payment facts.
- Product catalog.
- Inventory foundation when stock reporting expands.

Engineering checkpoints:

- Reports do not mutate source records.
- Query performance uses eager loading or aggregate queries.
- Date and filter criteria are validated.

### Sequence 10: Authorization Hardening

Goal:

- Enforce responsibility boundaries consistently.

Scope:

- Add policies or gates for product management, checkout, reporting, inventory adjustment, and future administration workflows.
- Keep login/logout behavior compatible.
- Define role assignment strategy only as far as MVP needs require.

Dependencies:

- Auth baseline.
- Interface contract authorization map.

Engineering checkpoints:

- Protected admin routes require authentication.
- Product, inventory, and reporting responsibilities are enforceable.
- Unauthorized responses are consistent.

## Development Milestones

### Milestone 1: Stabilization

Includes:

- Baseline tests.
- Validation boundaries.
- Response conventions.
- View model preparation.

Exit criteria:

- Existing cashier and admin workflows still operate.
- Tests cover current behavior.
- Controllers begin to thin without behavior changes.

### Milestone 2: Product and Checkout Extraction

Includes:

- Product catalog actions.
- Checkout Form Request.
- Complete sale action.
- Checkout business failure handling.

Exit criteria:

- Checkout behavior is application-layer driven.
- Product management behavior is application-layer driven.
- Existing routes remain stable.

### Milestone 3: Consistency and Inventory

Includes:

- Sale/payment/stock transaction boundary.
- Inventory movement design implementation after schema approval.
- Stock adjustment behavior.
- Low stock query.

Exit criteria:

- Sale completion cannot leave inconsistent data.
- Stock changes are traceable where schema supports it.
- Inventory tests cover happy and failure paths.

### Milestone 4: Receipt and Reporting

Includes:

- Receipt view model and output boundary.
- Dashboard reporting queries.
- Transaction detail query.
- Payment and stock summaries.

Exit criteria:

- Reporting is read-only.
- Receipt output is derived from completed sale facts.
- Report tests cover filters and authorization.

### Milestone 5: Authorization and Operational Readiness

Includes:

- Policies or gates.
- Error handling consistency.
- Regression test pass.
- Pint formatting.

Exit criteria:

- Protected workflows are authorized.
- Minimal feature suite passes.
- Code is ready for broader review.

## Implementation Dependencies

| Dependency | Required Before | Notes |
| --- | --- | --- |
| Baseline tests | Refactoring controllers | Prevents silent behavior drift. |
| Form Requests | Thin controller cleanup | Gives controllers validated input. |
| Product catalog actions | Checkout extraction | Checkout depends on sellable products. |
| Checkout action | Sale consistency boundary | Provides one place to control transaction behavior. |
| Physical schema approval | Inventory movement persistence | Avoids ad hoc table changes. |
| Payment behavior | Sale completion | Sale cannot complete without accepted payment. |
| Receipt view model | Receipt output | Keeps receipt rendering separate from sale persistence. |
| Reporting queries | Dashboard and transaction views | Keeps reporting read-only. |
| Authorization rules | Protected workflows | Product, inventory, and reporting need responsibility checks. |

## File and Class Creation Plan

### Near-Term Classes

Recommended future classes:

- `app/Http/Requests/LoginRequest.php`
- `app/Http/Requests/CheckoutRequest.php`
- `app/Http/Requests/StoreProductRequest.php`
- `app/Http/Requests/UpdateProductRequest.php`
- `app/Actions/Products/SaveProductAction.php`
- `app/Actions/Products/DeactivateProductAction.php`
- `app/Actions/Products/RestoreProductAction.php`
- `app/Actions/Sales/CompleteSaleAction.php`
- `app/Actions/Reports/BuildDashboardSummaryAction.php`
- `app/Actions/Reports/GetTransactionDetailAction.php`

### Later Classes After Schema Approval

- `app/Actions/Inventory/AdjustStockAction.php`
- `app/Actions/Inventory/RecordStockMovementAction.php`
- `app/Actions/Inventory/DetectLowStockAction.php`
- `app/Actions/Receipts/BuildReceiptViewModelAction.php`
- `app/Policies/ProductPolicy.php`
- `app/Policies/ReportPolicy.php`
- `app/Policies/InventoryPolicy.php`

### Migration Candidates

Only after physical schema approval:

- Stock movements.
- Stock adjustments.
- Product categories.
- Product units.
- Role or responsibility assignments.
- Store settings.

## Testing Strategy

### Baseline Tests

- Cashier page loads.
- Product lookup returns active products.
- Checkout completes with valid cart and payment.
- Checkout rejects insufficient stock.
- Login accepts valid credentials.
- Admin products page requires auth.
- Product create/update/delete/restore behavior.
- Transaction list and detail behavior.

### Application Behavior Tests

- Product action validates identity and price.
- Complete sale action validates items, payment, and stock.
- Complete sale action rolls back on failure.
- Inventory adjustment requires reason.
- Receipt view model builds from completed sale facts.
- Reporting queries do not mutate data.

### Authorization Tests

- Cashier workflow requires cashier responsibility when roles exist.
- Product management requires product responsibility.
- Inventory adjustment requires inventory responsibility.
- Reporting requires reporting responsibility.

### Regression Tests

- Existing routes remain reachable.
- Existing JSON endpoints remain compatible.
- Existing views render without missing data.

## Rollout Phases

### Phase 1: Non-Behavioral Refactor

- Add tests.
- Add Form Requests.
- Standardize response handling.
- Prepare view models.

Risk:

- Low.

Rollback:

- Revert request/controller changes if validation changes break existing clients.

### Phase 2: Action Extraction

- Extract product and checkout behavior to actions.
- Keep controllers as thin adapters.

Risk:

- Medium.

Rollback:

- Keep old controller logic available during review until action behavior is verified.

### Phase 3: Consistency Boundary

- Add explicit transaction boundary for sale completion.
- Harden stock and payment checks.

Risk:

- High.

Rollback:

- Require focused tests and database backup strategy before production rollout.

### Phase 4: Inventory and Reporting Expansion

- Add stock movement behavior after schema approval.
- Add reporting queries.

Risk:

- Medium.

Rollback:

- Keep existing product stock display until movement model is verified.

### Phase 5: Authorization Hardening

- Add policies or gates.
- Enforce responsibilities.

Risk:

- Medium.

Rollback:

- Feature flags or staged role enforcement may be used if existing admin access patterns are uncertain.

## Engineering Checkpoints

Before each implementation PR:

- Confirm scope maps to one sequence or milestone.
- Confirm no unrelated refactor is included.
- Confirm route compatibility impact.
- Confirm migration impact if any.
- Confirm focused tests exist.

Before merge:

- Run focused tests.
- Run `vendor/bin/pint --dirty --format agent` for PHP changes.
- Confirm no `.env` or generated local secrets are staged.
- Confirm controllers remain thin for changed workflows.
- Confirm business errors are explicit.

Before release:

- Run full test suite.
- Build frontend assets when UI changes are included.
- Review rollback plan.
- Review database migration reversibility.
- Review cashier checkout happy path manually.

## Backward Compatibility Checklist

- Existing route names remain stable where practical.
- Existing cashier page remains available at root.
- Existing product lookup remains available.
- Existing checkout endpoint remains compatible or changes are documented.
- Existing admin product management remains available.
- Existing transaction views remain readable.
- Existing historical transaction data remains understandable.
- Existing product stock behavior remains visible during inventory transition.

## Risk Mitigation Plan

### Checkout Risk

Mitigation:

- Extract into one action.
- Add happy path and failure path tests.
- Use transaction boundary.

### Inventory Risk

Mitigation:

- Do not introduce stock movements without schema approval.
- Preserve existing stock field until replacement is verified.
- Add low stock and adjustment tests.

### Payment Risk

Mitigation:

- Keep supported methods explicit.
- Validate cash received and non-cash confirmation.
- Treat payment acceptance as required before sale completion.

### Reporting Risk

Mitigation:

- Keep reports read-only.
- Use completed source facts.
- Add date/filter validation.

### Authorization Risk

Mitigation:

- Stage policies or gates.
- Test unauthorized access.
- Avoid breaking current admin flows without a migration path.

## Definition of Ready for Feature Implementation

Feature implementation may begin when:

- Implementation sequence is approved.
- Baseline tests exist for the touched workflow.
- Request/response contract is known.
- Data model impact is known.
- Migration need is identified and approved.
- Rollback path is documented.

## Readiness Statement

BroDev Cashier is ready for staged implementation beginning with baseline tests, Form Requests, response/view-model boundaries, and product/checkout action extraction. High-risk changes to sale completion, payment, and stock must be guarded by focused tests and explicit transaction boundaries.

