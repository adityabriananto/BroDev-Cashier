# Development Principles

**Mission:** PROJECT-001  
**Product:** BroDev Cashier

## Principle 1: Business Correctness First

Sales, payment, and stock behavior must preserve business correctness. A completed sale should never leave stock, sale items, or payment data in an inconsistent state.

## Principle 2: Keep the MVP Focused

The first product phase should stabilize the core POS workflow before expanding into advanced purchasing, reporting, loyalty, multi-branch, or accounting capabilities.

## Principle 3: Laravel Conventions by Default

Use Laravel defaults and established repository patterns unless the project has a clear reason to introduce another approach.

## Principle 4: Simple Architecture Before Abstraction

Start with Laravel MVC. Introduce service, action, or domain classes when they reduce real complexity in sales, inventory, payment, or reporting workflows.

## Principle 5: Traceable Data Changes

Inventory and sales records must be auditable enough for business review. Stock changes should be represented as movements or adjustments when the inventory workflow requires traceability.

## Principle 6: Test the Risk

Prioritize tests around transaction completion, stock reduction, payment handling, authorization, and report calculations.

## Principle 7: Operational UI

Interfaces should be optimized for real work: fast scanning, clear actions, responsive layout, and minimal distraction.

## Principle 8: Controlled Dependency Growth

Dependencies should stay lightweight. Add packages only when they solve a real project need and fit the Laravel baseline.

## Principle 9: Mission-Based Engineering

Use FlowForge missions to define scope, capture decisions, and separate planning deliverables from implementation work.

