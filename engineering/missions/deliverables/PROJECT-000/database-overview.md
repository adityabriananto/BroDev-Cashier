# Database Overview

---

# Database Layer

Laravel Eloquent ORM

Migration digunakan sebagai source of truth.

---

# Current Structure

```
database/

migrations/

seeders/

factories/
```

---

# Planned Domain

Master

- Products
- Categories
- Units
- Customers
- Suppliers

Inventory

- Stock
- Stock Movement
- Stock Adjustment

Sales

- Sales
- Sale Items
- Payments

Reporting

- Sales Report
- Stock Report
- Profit Report

---

# Recommendation

Seluruh perubahan struktur database harus dilakukan menggunakan Migration.