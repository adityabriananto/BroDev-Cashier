# Domain Events

**Mission:** PROJECT-002  
**Product:** BroDev Cashier

## Purpose

This document identifies important business events in the BroDev Cashier domain. These are conceptual events only and do not define software events, queues, listeners, endpoints, or implementation details.

## Cashier Events

### Sale Started

A cashier begins a new checkout workflow.

### Item Added To Sale

A product is added to the active checkout workflow.

### Item Quantity Changed

The quantity of a selected product changes during checkout.

### Checkout Total Calculated

The sale subtotal, tax, and grand total are determined for the active checkout workflow.

## Sales Events

### Sale Completed

A checkout workflow becomes a completed sale.

### Sale Voided

A completed sale is marked as invalid or canceled by an authorized business process. This is a future concept and should require strict controls.

## Inventory Events

### Stock Reduced

Available stock decreases because a sale was completed.

### Stock Adjusted

Available stock changes because of a manual business correction.

### Low Stock Detected

Available stock falls below a defined threshold.

## Product Events

### Product Added

A new sellable product is introduced to the catalog.

### Product Updated

Business information for a product changes.

### Product Deactivated

A product is no longer available for new sales.

## Payment Events

### Payment Recorded

Payment information is associated with a completed sale.

### Cash Change Calculated

Cash payment exceeds the grand total and change is determined.

## Receipt Events

### Receipt Issued

A customer-facing proof of sale is produced for a completed sale.

## Reporting Events

### Report Requested

A user asks for a business summary.

### Operational Summary Reviewed

An owner, manager, or admin reviews summarized business activity.

