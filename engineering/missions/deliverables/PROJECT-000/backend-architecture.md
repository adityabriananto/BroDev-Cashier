# Backend Architecture

---

# Overview

Backend mengikuti pola standar Laravel.

```
Browser
    │
Routes
    │
Controllers
    │
Models
    │
Database
```

---

# Current Layers

- Routes
- Controllers
- Models
- Database

---

# Planned Layers

Seiring berkembangnya aplikasi, arsitektur akan diperluas menjadi:

```
Controller

↓

Service

↓

Domain

↓

Model

↓

Database
```

Repository Pattern hanya akan digunakan apabila benar-benar diperlukan.

---

# Domain

Saat ini domain yang telah teridentifikasi:

- Authentication
- Cashier
- Administration

Domain berikutnya:

- Products
- Inventory
- Customers
- Suppliers
- Purchase
- Sales
- Reports
- Settings