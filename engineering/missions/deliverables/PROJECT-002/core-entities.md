# Core Entities

**Mission:** PROJECT-002  
**Product:** BroDev Cashier

## Purpose

This document defines conceptual business entities. It does not define database tables, columns, keys, models, or API resources.

## User

A person who accesses the system. A user may act as cashier, admin, owner, manager, or another future business role.

## Role

A business responsibility assigned to a user. Roles determine which workflows a user is allowed to perform.

## Product

A sellable item in the store catalog. Product concepts include SKU, name, selling price, category, unit, and active status.

## SKU

A store-controlled product identifier used to distinguish products operationally.

## Category

A grouping concept used to organize products.

## Unit

The measurement or packaging concept used for a product.

## Sale

A completed retail transaction. A sale records what was sold, the totals, the payment result, the responsible cashier, and the completion time.

## Sale Item

A product line within a sale. It represents which product was sold and in what quantity.

## Payment

The business record of how a sale was paid. Payment may be cash, QRIS, or bank transfer.

## Receipt

The proof of a completed sale provided to the customer.

## Stock

The available quantity concept for a product.

## Stock Movement

The business reason for stock increasing or decreasing.

## Stock Adjustment

A manual correction to stock used for audit, damage, discrepancy, or reconciliation.

## Low Stock Condition

A business condition indicating that product stock is below an expected threshold.

## Customer

A person or organization buying products. This is a future-supporting concept for richer sales history and customer workflows.

## Supplier

A person or organization providing products to the store. This supports future purchasing and restocking workflows.

## Report

A business summary derived from operational activity, such as sales, stock, or payment data.

## Store Setting

An operational configuration concept such as tax behavior, receipt identity, or business display information.

