# Brodev Cashier Engine

Brodev Cashier Engine is a web-based Point of Sale (POS) system built with **Laravel** and **Vue.js**. It is designed to streamline retail operations, provide efficient inventory management, and facilitate fast and accurate sales transactions.

## 🚀 Key Features

* **Real-time Sales Processing:** Automatic calculation of subtotal, 11% tax, and grand total.
* **Inventory Management:** Real-time stock monitoring with **Low Stock Alerts** for better restocking management.
* **Multi-Payment Integration:** Supports various payment methods including Cash, QRIS, and Bank Transfer.
* **Thermal Receipt Printing:** Optimized for 58mm thermal printers with seamless browser-based printing.
* **Responsive UI:** Fully responsive design, optimized for both desktop and mobile/tablet devices.
* **Live Dashboard:** Real-time clock and performance monitoring integrated into the cashier interface.

## 🛠 Tech Stack

* **Framework:** Laravel 13.8 (PHP 8.3+)
* **Tools:** Laravel Boost, Laravel Pail, Laravel Pint
* **Frontend:** Vue.js 3, Tailwind CSS
* **Build Tool:** Vite

## 📋 Requirements

To run this application, ensure your environment meets the following:

* **PHP:** Version 8.3 or higher
* **Composer:** Latest version
* **Node.js & NPM:** Latest LTS version
* **Database:** MySQL 8.0+ or MariaDB 10.5+
* **Extensions:** `php-mbstring`, `php-xml`, `php-bcmath`, `php-curl`

## 📦 Installation

1.  **Clone the repository:**
    ```bash
    git clone [https://github.com/yourusername/brodev-cashier.git](https://github.com/yourusername/brodev-cashier.git)
    cd brodev-cashier
    ```

2.  **Install PHP and JS dependencies:**
    ```bash
    composer install
    npm install && npm run build
    ```

3.  **Configure environment:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Update your `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD` in the `.env` file.*

4.  **Run migrations:**
    ```bash
    php artisan migrate
    ```

5.  **Serve the application:**
    ```bash
    php artisan serve
    

## 🔐 User Management (Tinker)

You can add a new admin user via the Laravel Tinker console:

```bash
php artisan tinker

// Run the following command:
\App\Models\User::create([
    'name' => 'Admin Name',
    'email' => 'admin@example.com',
    'password' => \Illuminate\Support\Facades\Hash::make('yourpassword')
]);
```

🤝 Contributing
Contributions are welcome! If you have suggestions for new features or bug fixes, feel free to open an issue or submit a pull request.

© 2026 Brodev Cashier System. All rights reserved.
