# Frontend Architecture

---

# Overview

Frontend menggunakan Laravel Blade.

Stack:

- Blade
- Tailwind CSS
- Vite

---

# View Structure

```
resources/views/

auth/

cashier/

admin/

layouts/
```

---

# Layout

Seluruh halaman menggunakan shared layout.

---

# UI Strategy

Aplikasi menggunakan server-side rendering dengan Blade.

Pendekatan ini dipilih karena:

- sederhana
- cepat
- maintainable
- cocok untuk aplikasi POS

---

# Future Direction

Frontend akan tetap menggunakan Blade selama fase MVP.

Evaluasi React/Vue dilakukan setelah domain bisnis stabil.