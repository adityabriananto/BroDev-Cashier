# Business Domain Model

**Mission:** PROJECT-001  
**Product:** BroDev Cashier

## Domain Overview

BroDev Cashier is centered on retail point of sale operations. The core domain starts with sales, products, inventory, payment, and reporting, then expands into purchasing, suppliers, customers, and settings as the product matures.

## Core Domains

### Authentication

Manages user identity, login, access control, and role-aware application behavior.

### Cashier

Supports the sales transaction workflow, including product selection, quantity entry, subtotal calculation, tax calculation, payment capture, change calculation, and receipt output.

### Products

Manages sellable items, SKU, name, category, unit, price, and product status.

### Inventory

Tracks stock availability, stock movement, stock adjustment, and low stock conditions.

### Sales

Records completed transactions, sale items, totals, tax, payment method, and related cashier information.

### Payments

Records how a transaction was paid, including cash, QRIS, and bank transfer.

### Reports

Provides operational summaries such as sales reports, stock reports, and profit-oriented reports when cost data is available.

### Administration

Manages users, roles, settings, and operational configuration.

## Planned Supporting Domains

- Customers
- Suppliers
- Purchase
- Settings

## Domain Relationships

- A sale contains one or more sale items.
- A sale item references a product.
- A product belongs to product classification data such as category and unit.
- A completed sale produces stock movement.
- A payment belongs to a sale.
- Reports derive from sales, payments, products, and stock movements.

## Architecture Alignment

The initial implementation may use Laravel MVC directly. Service or domain layers should be introduced when workflows such as sales completion, inventory movement, or reporting become too complex for controllers.

