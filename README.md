# UP-ITDC Inventory Management System

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

---

## 📖 About the Project

The **UP-ITDC Inventory Management System** is a Laravel 12-based web application designed to manage staff, departments, and equipment efficiently.
It provides features such as:

* Staff & Department Management
* Equipment Issuance, Return, and Status Tracking
* History Logs with Filtering & Export
* Dashboard with Key Statistics
* TailwindCSS-powered Frontend

---

## 📦 Prerequisites

Make sure to install these tools first:

* **PHP** ≥ 8.2 → [Download](https://windows.php.net/download/)
* **Composer** → [Download](https://getcomposer.org/Composer-Setup.exe)
* **MySQL** (XAMPP or standalone) → [XAMPP](https://www.apachefriends.org/)
* **Node.js (LTS)** → [Download](https://nodejs.org/)
* **Git** (optional) → [Download](https://git-scm.com/download/win)

---

## ⚙️ Installation & Setup

### 1. Clone the Repository

```bash
git clone https://github.com/your-username/inventory-management-system.git
cd inventory-management-system
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Setup Environment

```bash
cp .env.example .env
```

Update `.env` with:

```
DB_DATABASE=inventory_db
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate App Key

```bash
php artisan key:generate
```

### 5. Run Migrations

```bash
php artisan migrate
```

### 6. Link Storage

```bash
php artisan storage:link
```

### 7. (Optional) Seed Database

```bash
php artisan db:seed
```

---

## 🎨 Frontend (TailwindCSS)

Install and build assets:

```bash
npm install
npm run dev
```

---

## 🔁 Useful Commands

```bash
php artisan serve        # Start the server
npm run dev              # Compile frontend assets
php artisan migrate:fresh --seed
php artisan storage:link
```

---

## 📧 Email Testing

For local development, use log channel:

```
MAIL_MAILER=log
```

📌 Emails will be stored in: `storage/logs/laravel.log`

---

## 🛠️ Recommended VS Code Extensions

* Laravel Extra Intellisense
* Laravel Blade Snippets
* Tailwind CSS IntelliSense
* PHP Intelephense
* DotENV

---

## ✅ Final Setup Checklist

* [x] PHP ≥ 8.2
* [x] Composer Installed
* [x] Node.js + npm Installed
* [x] MySQL + Database Created
* [x] `.env` Configured
* [x] Migrations Run
* [x] Assets Compiled
* [x] App Key Generated

---

## 💡 Launch the App

```bash
php artisan serve
```

Open in browser → [http://localhost:8000](http://localhost:8000)

---

## 📌 System Requirements

| Component | Minimum             | Recommended         |
| --------- | ------------------- | ------------------- |
| OS        | Windows 10 (64-bit) | Windows 11 (64-bit) |
| Processor | Dual-core 2.0 GHz   | Quad-core 2.5 GHz+  |
| RAM       | 4 GB                | 8 GB+               |
| Storage   | 10 GB free          | 20 GB+ SSD          |
| Internet  | Required            | Stable broadband    |

---

## 📌 Optional Tools

* **Postman** — API Testing
* **Docker Desktop** — For Laravel Sail
* **Laravel Debugbar / Telescope** — Debugging
* **MySQL GUI** — HeidiSQL / Workbench / phpMyAdmin

---

## 🤝 Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you’d like to change.

---

## 🔒 Security Vulnerabilities

If you discover a security vulnerability, please send an e-mail to the project maintainer.

---
