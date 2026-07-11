# Functional Requirements

**Mission:** PROJECT-001  
**Product:** BroDev Cashier

## Authentication and Access

- The system must allow authorized users to log in.
- The system must support role-aware access for cashier, admin, and owner or manager responsibilities.
- The system must protect administrative workflows from unauthorized users.

## Cashier Sales

- The system must allow cashiers to select products for a sale.
- The system must allow item quantities to be adjusted before checkout.
- The system must calculate subtotal, tax, and grand total.
- The system must support payment recording.
- The system must calculate change for cash payments.
- The system must prevent sales that exceed available stock.
- The system must persist completed sales and sale items.

## Product Management

- The system must allow admins to create, update, view, and deactivate products.
- The system must store product SKU, name, price, and stock-related information.
- The system should support product categorization and units as the domain matures.

## Inventory Management

- The system must track current stock per product.
- The system must reduce stock when a sale is completed.
- The system should record stock movements for traceability.
- The system should identify products below low stock thresholds.

## Payments

- The system must support cash payments.
- The system must support QRIS payment records.
- The system must support bank transfer payment records.
- The system should keep payment method data available for reporting.

## Receipts

- The system must generate a receipt for completed sales.
- The receipt layout should be compatible with 58mm thermal printing.
- Receipt printing should work through browser-based printing.

## Reports and Dashboard

- The system should show daily sales activity.
- The system should show stock conditions.
- The system should provide sales and stock reports.
- The system should support future profit reporting when cost data exists.

## Administration

- The system should allow user management.
- The system should allow business settings to be configured when needed.
- The system should keep administrative workflows separate from cashier workflows.

