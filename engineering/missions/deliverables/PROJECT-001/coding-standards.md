# Coding Standards

**Mission:** PROJECT-001  
**Product:** BroDev Cashier

## Baseline

BroDev Cashier must follow the PROJECT-000 stack: Laravel 13, PHP 8.3, Blade, Tailwind CSS, Vite, PHPUnit, and FlowForge. Existing Laravel conventions in the repository take priority over introducing new styles.

## PHP and Laravel

- Use explicit parameter types and return types.
- Use PHP 8 constructor property promotion when constructor dependencies are needed.
- Use curly braces for all control structures.
- Use descriptive method and variable names.
- Prefer Laravel conventions for controllers, models, requests, migrations, policies, jobs, and tests.
- Use Eloquent and query builder APIs instead of raw SQL for user-driven data.
- Keep `env()` calls inside configuration files.
- Use named routes and `route()` for URL generation.

## Controllers and Domain Logic

- Keep controllers focused on request handling and response orchestration.
- Move complex sales, stock, payment, or report behavior into service or action classes when complexity grows.
- Avoid putting business logic in Blade templates.
- Avoid querying directly from views.
- Use authorization checks for protected workflows.

## Database

- Use migrations as the source of truth for schema changes.
- Use factories and seeders for test and development data.
- Add indexes for frequently filtered, joined, or sorted fields.
- Use transactions for operations that update sales, sale items, payments, and stock together.

## Validation and Security

- Prefer Form Request classes for non-trivial validation.
- Use `$request->validated()` instead of trusting all request input.
- Escape output in Blade with standard `{{ }}` syntax.
- Use CSRF protection for state-changing forms.
- Never commit `.env` secrets.

## Frontend

- Use Blade and Tailwind CSS for MVP screens.
- Keep UI components consistent with existing layouts.
- Use Vite for bundling.
- Keep cashier screens dense, clear, and optimized for repeated operational use.

## Testing and Formatting

- Use PHPUnit tests.
- Use factories for model setup in tests.
- Run focused tests for changed behavior.
- Run Laravel Pint on changed PHP files before finalizing PHP changes.

