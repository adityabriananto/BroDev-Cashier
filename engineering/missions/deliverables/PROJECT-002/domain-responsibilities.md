# Domain Responsibilities

**Mission:** PROJECT-002  
**Product:** BroDev Cashier

## Purpose

This document lists the responsibilities of each business domain in BroDev Cashier.

## Cashier Operations

- Guide the checkout workflow from item selection to sale completion.
- Present selected items, quantities, and totals clearly.
- Accept payment information during checkout.
- Prevent completion when the business state is invalid.
- Trigger the creation of a receipt after a sale is completed.

## Sales

- Represent completed transactions.
- Preserve the sold product lines and quantities.
- Preserve sale totals and tax values.
- Preserve the cashier or user responsible for the transaction.
- Provide reliable business records for reports.

## Product Catalog

- Define sellable products.
- Maintain product identity and business labels.
- Maintain selling price.
- Organize products by category and unit when those concepts are active.
- Control whether a product can be sold.

## Inventory

- Represent available stock.
- Represent why stock increased or decreased.
- Enforce the concept that unavailable stock should not be sold.
- Identify low stock conditions.
- Support future reconciliation and audit workflows.

## Payments

- Represent how a completed sale was paid.
- Support cash, QRIS, and bank transfer concepts.
- Preserve payment amount and payment status.
- Represent cash change where applicable.
- Provide payment data for operational reporting.

## Receipts

- Represent the customer-facing proof of sale.
- Include sale identity, items, totals, payment method, and completion time.
- Support thermal receipt expectations as a business output.

## Reporting

- Summarize sales activity.
- Summarize stock conditions.
- Summarize payment method usage.
- Support owner or manager review.
- Avoid changing source business records.

## Administration

- Represent users and responsibilities.
- Control access to cashier, admin, and management workflows.
- Maintain operational settings.
- Keep management activities separate from cashier checkout activities.

