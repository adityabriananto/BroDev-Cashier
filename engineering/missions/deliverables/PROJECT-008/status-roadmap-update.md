# Status and Roadmap Update

**Mission:** PROJECT-008  
**Product:** BroDev Cashier  
**Deliverable:** Update related documentation (STATUS, ROADMAP)

## Status Update

PROJECT-008 defines the implementation strategy for BroDev Cashier.

## Current Mission Status

| Area | Status | Notes |
| --- | --- | --- |
| Module sequencing | Completed | Ten implementation sequences are documented from baseline tests through authorization hardening. |
| Development milestones | Completed | Stabilization, extraction, consistency, receipt/reporting, and operational readiness milestones are defined. |
| Implementation dependencies | Completed | Dependency map links tests, Form Requests, actions, schema approval, payment, receipt, reporting, and authorization. |
| Testing strategy | Completed | Baseline, behavior, authorization, and regression test categories are documented. |
| Rollout phases | Completed | Five staged rollout phases are documented with risk and rollback notes. |
| Engineering checkpoints | Completed | PR, merge, and release checkpoints are documented. |
| Backward compatibility planning | Completed | Route, cashier, admin, transaction, and stock compatibility checks are documented. |
| Feature implementation | Not started | No application code is implemented in this mission. |

## Updated Documentation Baseline

The following mission outputs now define the planning baseline:

- PROJECT-000: Engineering baseline and repository discovery.
- PROJECT-001: Product definition, requirements, coding standards, and roadmap.
- PROJECT-002: Business domain model, boundaries, entities, rules, and aggregate candidates.
- PROJECT-003: Business workflows, cross-domain interactions, exception flows, and state transitions.
- PROJECT-004: Software architecture baseline, module boundaries, dependency rules, and engineering standards.
- PROJECT-005: Canonical business data model, aggregate design, lifecycle states, and persistence strategy.
- PROJECT-006: Application behavior model, use cases, commands, queries, application services, interaction patterns, and state transitions.
- PROJECT-007: External interface contracts, request and response contracts, validation, authorization, view models, and interaction boundaries.
- PROJECT-008: Implementation strategy, module sequencing, milestones, dependencies, testing strategy, rollout phases, and engineering checkpoints.

## Implementation Decisions Captured

- Start implementation with baseline tests before refactoring.
- Introduce Form Requests before thinning controllers.
- Extract product catalog behavior before checkout behavior.
- Extract checkout into an application action before introducing deeper sale/payment/stock consistency changes.
- Treat sale completion as the highest-risk consistency boundary.
- Introduce inventory movement persistence only after schema approval.
- Keep reporting read-only.
- Harden authorization after core behavior boundaries are stable.

## Roadmap Update

### Completed Planning Work

- Engineering baseline.
- Product definition.
- Business domain modelling.
- Business workflow specification.
- Architecture baseline.
- Canonical data model design.
- Application behavior design.
- Interface contract design.
- Implementation planning.

### Next Recommended Mission: Foundation Implementation

The next mission should begin low-risk implementation work.

Recommended deliverables:

- Baseline feature tests for current routes and workflows.
- Form Requests for checkout, login, and product management.
- Response convention for JSON endpoints.
- Initial view model preparation for cashier and admin screens.
- Pint formatting and focused test verification.

### Future Mission: Product and Checkout Action Extraction

After foundation implementation, extract business logic from controllers.

Recommended deliverables:

- Product save/update/deactivate/restore actions.
- Active products query action.
- Checkout request validation.
- Complete sale action.
- Checkout happy path and failure path tests.

### Future Mission: Sale Consistency and Inventory

After checkout extraction, harden consistency and inventory behavior.

Recommended deliverables:

- Sale completion transaction boundary.
- Payment acceptance checks.
- Stock availability checks.
- Stock movement schema and behavior if approved.
- Rollback tests for sale completion failure.

### Future Mission: Reporting and Receipt Boundary

Recommended deliverables:

- Receipt view model builder.
- Transaction detail query.
- Dashboard summary query.
- Sales report query.
- Payment method summary query.
- Low stock report query.

### Future Mission: Authorization Hardening

Recommended deliverables:

- Product policy or gate.
- Reporting policy or gate.
- Inventory policy or gate.
- Unauthorized access tests.
- Role or responsibility strategy if needed.

## Risk Notes

- Checkout is the highest implementation risk because it touches sale facts, payment, inventory, receipt, and reporting.
- Inventory movement should not be added casually; it needs schema approval and rollback planning.
- Controller refactors must be protected by baseline tests.
- Standardizing JSON responses may affect frontend assumptions and should be staged carefully.
- Authorization hardening can disrupt existing admin access if roles are not introduced carefully.

## Readiness Statement

BroDev Cashier is ready for foundation implementation. The first implementation mission should prioritize tests, Form Requests, response conventions, and view model preparation before changing sale completion or inventory behavior.

