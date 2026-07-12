# Business Rules

**Mission:** PROJECT-002  
**Product:** BroDev Cashier

## Purpose

This document defines business rules for the BroDev Cashier domain. These rules describe intended business behavior only and do not specify implementation details.

## Cashier Rules

- A sale cannot be completed without at least one sale item.
- A cashier must be associated with a completed sale.
- Item quantities in a sale must be greater than zero.
- The displayed sale total must reflect the selected items, quantities, tax, and payment effect.
- A cashier should not complete a sale when required payment information is missing.

## Product Rules

- A product must be identifiable by the business before it can be sold.
- A product intended for sale must have a selling price.
- Inactive products should not be selectable for new cashier transactions.
- Product naming should be clear enough for cashier selection and receipt output.

## Inventory Rules

- A product cannot be sold in a quantity greater than available stock.
- Completing a sale reduces stock for the sold products.
- Stock changes should have a business reason.
- Manual stock corrections should be distinguishable from stock reductions caused by sales.
- Low stock should be identifiable before stock reaches zero where thresholds are defined.

## Payment Rules

- A completed sale must have a payment method.
- Supported initial payment methods are cash, QRIS, and bank transfer.
- Cash payment must be enough to cover the grand total before change is returned.
- QRIS and bank transfer payments may require a reference or confirmation concept when the workflow matures.
- Payment data should be available for reporting.

## Receipt Rules

- A receipt is produced only for a completed sale.
- A receipt must communicate the sale items, totals, payment method, and completion time.
- Receipt content should be understandable to both customer and store staff.

## Reporting Rules

- Reports should be based on completed business records.
- Reports should distinguish sales totals from payment method summaries.
- Stock reports should reflect inventory concepts, not only product catalog concepts.
- Reports should not alter source business records.

## Administration Rules

- Administrative workflows should be limited to users with the appropriate responsibility.
- Cashier workflows should remain separate from management configuration workflows.
- Changes to operational settings should be controlled because they may affect sales and receipts.

