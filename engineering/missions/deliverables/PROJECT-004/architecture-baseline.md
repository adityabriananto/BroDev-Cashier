# Architecture Baseline

**Mission:** PROJECT-004  
**Product:** BroDev Cashier  
**Deliverable:** Implement core logic for architecture baseline definition

## Purpose

This document defines the baseline software architecture for BroDev Cashier by translating the approved business domains and workflows into a maintainable, scalable, and modular Laravel application architecture.

This is an architecture specification only. It does not implement application features, create database tables, or define API endpoints.

## Architectural Direction

BroDev Cashier should evolve from the current Laravel MVC structure into a layered Laravel architecture that keeps business workflow logic explicit while preserving Laravel conventions.

The architecture must remain compatible with the existing repository:

- Laravel 13 and PHP 8.3.
- Blade, Tailwind CSS, and Vite for MVP frontend delivery.
- Eloquent ORM and migrations for persistence work when future data design missions approve it.
- PHPUnit for automated tests.
- FlowForge for mission lifecycle and engineering documentation.

## Current Baseline

The current application structure is simple and Laravel-native:

```text
Browser
  -> Routes
  -> Controllers
  -> Models
  -> Database
```

Current application areas include:

- Authentication
- Cashier
- Administration
- Products
- Transactions

This structure is acceptable for early MVP work, but business workflows such as sale completion and inventory movement require clearer application boundaries as complexity grows.

## Target Layered Architecture

The target architecture should use Laravel conventions while separating responsibilities by layer.

```text
HTTP Layer
  -> Application Layer
  -> Domain Layer
  -> Infrastructure Layer
```

## Layer Responsibilities

### HTTP Layer

The HTTP layer receives browser or route-driven requests and returns responses.

Responsibilities:

- Routes.
- Controllers.
- Form Requests.
- Middleware.
- Blade responses.
- Redirects and validation feedback.

Rules:

- Controllers must stay thin.
- Controllers should orchestrate request and response flow.
- Controllers should not contain complex sales, stock, payment, or reporting logic.
- Form Requests should own non-trivial validation.
- Authorization should be checked before protected workflows execute.

### Application Layer

The application layer coordinates use cases and business workflows.

Responsibilities:

- Sale completion use cases.
- Product management use cases.
- Inventory adjustment use cases.
- Payment recording use cases.
- Reporting query coordination.
- Transaction consistency orchestration.

Recommended patterns:

- Single-purpose Action classes for discrete workflows.
- Service classes when multiple related actions need shared orchestration.
- Constructor dependency injection.
- Clear input and output objects when workflow complexity justifies them.

Rules:

- Application classes may coordinate multiple domain concepts.
- Application classes should not render views.
- Application classes should not depend on controllers.
- Application classes should make transaction boundaries explicit for workflows that update multiple business records.

### Domain Layer

The domain layer represents business concepts and rules.

Responsibilities:

- Sale business rules.
- Product sellability rules.
- Inventory availability and movement rules.
- Payment acceptance rules.
- Receipt readiness concepts.
- Reporting source fact definitions.

Recommended patterns:

- Domain services for business rules that do not naturally belong to one model.
- Value objects for totals, quantities, payment method, or stock movement meaning when complexity justifies them.
- Domain events as conceptual signals for later implementation, not as mandatory infrastructure.

Rules:

- Domain rules must not depend on HTTP requests.
- Domain concepts must not depend on Blade, routes, or controllers.
- Domain decisions should be testable without full UI workflows.

### Infrastructure Layer

The infrastructure layer provides technical implementation details behind the application and domain layers.

Responsibilities:

- Eloquent models.
- Database persistence.
- External payment provider adapters if introduced later.
- Receipt output adapters if printing concerns grow.
- Cache, queue, filesystem, and logging integrations.

Rules:

- Use Eloquent and Laravel facilities by default.
- Depend on contracts only at true system boundaries.
- Avoid repository abstractions unless they remove real complexity.
- Keep infrastructure decisions replaceable when they represent external systems.

## Module Boundaries

The approved business domains become architecture module boundaries. Modules are conceptual boundaries first; folder structure can evolve gradually.

### Authentication and Access

Owns user identity, login, and role-aware access.

Primary responsibilities:

- Protect workflows.
- Define who can perform cashier, admin, and reporting actions.
- Support policies or gates for protected actions.

### Cashier Operations

Owns the active checkout workflow before sale completion.

Primary responsibilities:

- Start sale workflow.
- Add or remove sale items during checkout.
- Adjust quantities.
- Present totals before completion.
- Hand off to payment and sale completion use cases.

### Sales

Owns completed transaction facts.

Primary responsibilities:

- Preserve completed sale identity.
- Preserve sold items and totals.
- Preserve cashier attribution.
- Serve as source facts for reporting.

### Product Catalog

Owns sellable product definitions.

Primary responsibilities:

- Product identity.
- SKU, name, price, category, unit, and active status.
- Sellability rules.

### Inventory

Owns stock concepts and stock movement meaning.

Primary responsibilities:

- Available stock.
- Stock reduction from completed sales.
- Manual stock adjustment.
- Low stock condition.

### Payments

Owns payment method and payment acceptance concepts.

Primary responsibilities:

- Cash payment validation.
- QRIS payment record concepts.
- Bank transfer payment record concepts.
- Cash change calculation.

### Receipts

Owns proof-of-sale output concepts.

Primary responsibilities:

- Receipt readiness.
- Receipt content from completed sale and payment facts.
- Future receipt output adapter boundaries.

### Reporting

Owns business summaries derived from completed source facts.

Primary responsibilities:

- Sales summary.
- Stock summary.
- Payment method summary.
- Operational dashboard source data.

### Administration

Owns management workflows and operational settings.

Primary responsibilities:

- Management access.
- User responsibility coordination.
- Store settings.

## Dependency Rules

### Allowed Direction

```text
HTTP -> Application -> Domain
HTTP -> Application -> Infrastructure
Application -> Domain
Application -> Infrastructure
Infrastructure -> Domain only when representing persisted domain concepts
```

### Disallowed Direction

```text
Domain -> HTTP
Domain -> Blade
Domain -> Controllers
Domain -> Routes
Application -> Controllers
Infrastructure -> Controllers
```

## Communication Patterns

### Synchronous Use Cases

Use synchronous application actions for business-critical workflows where the user needs an immediate result.

Examples:

- Complete sale.
- Record payment.
- Adjust stock.
- Update product.

### Domain Events

Use domain events as explicit workflow signals when implementation requires decoupling.

Candidate events:

- SaleCompleted.
- StockReduced.
- LowStockDetected.
- PaymentRecorded.
- ReceiptIssued.

Events should not hide critical consistency rules. Sale completion and stock reduction must remain transactionally understandable.

### Deferred or Queued Work

Use deferred or queued work only for non-critical side effects.

Candidates:

- Analytics logging.
- Non-critical report cache refresh.
- Future notification work.

Business-critical sale and stock changes should complete in the primary workflow.

## Transaction Consistency Strategy

Sale completion is the highest-risk workflow and should have an explicit consistency boundary.

The sale completion use case should coordinate:

- Sale fact creation.
- Sale item preservation.
- Payment recording.
- Stock reduction.
- Receipt readiness.

Architectural rule:

- If a workflow changes multiple business facts that must stay consistent, the application layer owns the transaction boundary.
- Inventory availability must be checked before sale completion.
- Future implementation should consider atomic locks or database row locking for stock-sensitive workflows.

## Authorization Strategy

Authorization belongs before protected use cases execute.

Recommended approach:

- Middleware for broad authentication requirements.
- Policies or gates for protected business actions.
- Form Requests or application use cases may assume authorization has already passed when the boundary requires it.

Protected workflows:

- Product management.
- Stock adjustment.
- Reporting access.
- User administration.
- Sale voiding if introduced later.

## Validation Strategy

Validation should be layered:

- Form Requests validate request shape and user input.
- Application actions validate workflow preconditions.
- Domain rules validate business truth.

Examples:

- Request validation checks required fields.
- Application validation checks that sale can move to payment.
- Domain validation checks that stock cannot be sold beyond availability.

## Error Handling Strategy

Business errors should be explicit and recoverable where possible.

Examples:

- Product not sellable.
- Insufficient stock.
- Payment not accepted.
- Unauthorized action.
- Receipt output failure.

Rules:

- Invalid business state should stop the workflow before source facts become inconsistent.
- Receipt output failure must not invalidate a completed sale.
- Reporting errors must not mutate source records.

## Testing Strategy

Testing should follow risk and business importance.

Priority test areas:

- Sale completion happy path.
- Insufficient stock failure.
- Cash payment below grand total failure.
- Product inactive failure.
- Stock adjustment authorization.
- Reporting access authorization.
- Receipt readiness after completed sale.

Recommended test levels:

- Feature tests for HTTP workflows.
- Unit tests for domain rules and value objects when introduced.
- Application tests for action classes when workflows grow.

## Naming and Structure Guidance

Use Laravel conventions and introduce structure gradually.

Recommended future folders when needed:

```text
app/
  Actions/
    Cashier/
    Inventory/
    Products/
    Reports/
    Sales/
  Domain/
    Cashier/
    Inventory/
    Payments/
    Products/
    Sales/
  Services/
  Http/
    Controllers/
    Requests/
```

Rules:

- Do not create folders before there is real complexity to place in them.
- Use single-purpose action names such as `CompleteSaleAction` or `AdjustStockAction`.
- Use services for cohesive workflow coordination, not as generic dumping grounds.
- Keep models aligned with Eloquent conventions.

## Clean Architecture Alignment

Clean Architecture principles apply through dependency direction and boundary clarity, not by forcing framework-agnostic code everywhere.

BroDev Cashier should:

- Keep domain rules away from HTTP and view concerns.
- Keep application workflows explicit.
- Use Laravel infrastructure pragmatically.
- Introduce contracts at external system boundaries.
- Avoid unnecessary abstraction around Eloquent unless business complexity demands it.

## SOLID Alignment

- Single Responsibility: controllers handle HTTP; actions handle use cases; domain services handle business rules.
- Open/Closed: new payment or receipt strategies should be added without rewriting cashier workflow rules.
- Liskov Substitution: contracts should only be introduced when implementations can truly substitute each other.
- Interface Segregation: contracts should be small and boundary-specific.
- Dependency Inversion: application workflows should depend on contracts at external boundaries.

## Backward Compatibility

The architecture baseline does not require immediate rewriting of existing controllers, models, or views.

Migration path:

1. Keep existing Laravel MVC flow working.
2. Extract complex cashier and inventory logic into actions when touched by future missions.
3. Add Form Requests for non-trivial input validation.
4. Add policies or gates for protected workflows.
5. Introduce domain services or value objects only when rules become repeated or difficult to test.

## Architecture Readiness

This baseline is ready to guide future feature implementation, database design, API/interface contracts, and testing strategy. Future missions should preserve the business domain boundaries and workflow states defined in PROJECT-002 and PROJECT-003.

