# Aggregate Candidates

**Mission:** PROJECT-002  
**Product:** BroDev Cashier

## Purpose

This document identifies candidate aggregate concepts for future domain modelling. These are conceptual boundaries only and do not define database schema, ORM models, or implementation structure.

## Sale Aggregate Candidate

### Root Concept

Sale

### Included Concepts

- Sale item
- Sale total
- Tax amount
- Payment outcome
- Cashier attribution
- Receipt readiness

### Business Invariant

A completed sale must have valid sale items, valid totals, a responsible cashier, and a payment outcome.

### Notes

This is the strongest aggregate candidate because checkout completion requires multiple concepts to remain consistent as one business transaction.

## Product Aggregate Candidate

### Root Concept

Product

### Included Concepts

- SKU
- Product name
- Selling price
- Category
- Unit
- Active status

### Business Invariant

A product must be identifiable, understandable, and valid for sale before it can appear in cashier operations.

### Notes

Product is primarily a catalog concept. Inventory should remain a separate concern because stock changes can have different business reasons.

## Inventory Aggregate Candidate

### Root Concept

Stock

### Included Concepts

- Available stock
- Stock movement
- Stock adjustment
- Low stock condition

### Business Invariant

Stock must not become inconsistent with business movements that increase, decrease, or adjust it.

### Notes

Inventory may require strong consistency when sales and stock movements are finalized.

## Payment Aggregate Candidate

### Root Concept

Payment

### Included Concepts

- Payment method
- Payment amount
- Payment status
- Cash change
- External payment reference when applicable

### Business Invariant

A payment must satisfy the sale total before a sale is treated as successfully completed.

### Notes

Payment may remain part of the Sale aggregate for MVP. It can become more independent if payment confirmation, refunds, or external provider reconciliation become complex.

## User Responsibility Aggregate Candidate

### Root Concept

User Responsibility

### Included Concepts

- User
- Role
- Access responsibility

### Business Invariant

A user should only perform workflows allowed by their business responsibility.

### Notes

This is an administration concept and should not complicate cashier transaction modelling.

