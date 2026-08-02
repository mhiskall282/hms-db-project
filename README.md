# Hotel Management System (HMS) — Final Year Project

[![Live Demo](https://img.shields.io/badge/Live_Demo-Render-brightgreen?style=for-the-badge&logo=render)](https://hms-hotel-management-system.onrender.com)

**Live Production URL:** [https://hms-hotel-management-system.onrender.com](https://hms-hotel-management-system.onrender.com)

A comprehensive, multi-role **Hotel Management System (HMS)** built with **Laravel 11**, **Spatie Laravel Permission**, **Alpine.js**, and **Tailwind CSS v3**.

This system digitises the complete operational lifecycle of a single hotel property — from room-type configuration and inventory management, guest registration, and availability searching to reservation booking, front-desk check-in/out, itemized invoicing, payment collection, and management reporting.

---

## 🌟 Key Modules & Functional Requirements

| Module | Requirements | Description |
|--------|--------------|-------------|
| **Auth & RBAC** | FR-1.1 – FR-1.4 | Multi-role authentication with 5 distinct staff roles (`admin`, `manager`, `receptionist`, `housekeeping`, `accountant`), account deactivation, and 30-minute idle session timeouts. |
| **Room Management** | FR-2.1 – FR-2.4 | Room type and room inventory CRUD, filterable room grid, housekeeping status tracking (`dirty`, `available`, `maintenance`), and database-level anti-double-booking locks. |
| **Guest Management** | FR-3.1 – FR-3.3 | Guest registration, directory search (name, phone, email, ID), and guest booking history tracking. |
| **Booking Engine** | FR-4.1 – FR-4.5 | Date-range room availability search, reservation creation, reference generator (`HMS-XXXXXXXX`), free cancellation, and multi-night fee calculation. |
| **Check-In / Out** | FR-5.1 – FR-5.3 | Front-desk arrivals/departures desk, atomic check-in (sets room to `occupied`), check-out (sets room to `dirty`), and timestamp tracking. |
| **Billing & Payments** | FR-6.1 – FR-6.5 | Auto-generated invoices, itemized service charges (room service, laundry, etc.), payment recording (cash, card, MoMo), invoice PDF downloads, and outstanding balance tracking. |
| **Reporting & Dashboard**| FR-7.1 – FR-7.4 | Live KPI dashboard (occupancy %, revenue, arrivals, outstanding bills), date-filtered occupancy and revenue reports with PDF and CSV exports. |

---

## 🛠️ Technology Stack

- **Backend Framework:** Laravel 11 (PHP 8.2+)
- **Database:** MySQL / MariaDB (or SQLite for cloud deployments)
- **RBAC:** `spatie/laravel-permission` (v6.25)
- **Frontend Stack:** Laravel Blade + Alpine.js + Tailwind CSS v3
- **PDF Export:** `barryvdh/laravel-dompdf` (v3.1)
- **CSV Export:** `maatwebsite/excel` (v3.1)
- **Deployment:** Docker + Render Blueprint (`render.yaml`)
- **Test Suite:** PHPUnit / Pest (37 passed feature & unit tests)

---

## 🚀 Deployment on Render (Step-by-Step)

This repository includes a pre-configured `Dockerfile`, `docker-entrypoint.sh`, and `render.yaml` Blueprint for 1-click or automated deployment on **Render.com**.

### Option A: Using Render Blueprints (Recommended)
1. Push this repository to your GitHub account.
2. Log in to [Render Dashboard](https://dashboard.render.com/).
3. Click **New +** &rarr; **Blueprint**.
4. Connect your `hms-db-project` repository.
5. Render will automatically detect `render.yaml` and provision the Docker web service.
6. Click **Apply**. Render will build the image, run database setup & seeders, and launch the application live!

### Option B: Manual Web Service Setup on Render
1. Click **New +** &rarr; **Web Service**.
2. Select **Build and deploy from a Git repository**.
3. Choose Language: **Docker**.
4. Environment Variables:
   - `APP_ENV`: `production`
   - `APP_DEBUG`: `false`
   - `APP_KEY`: *(Generate using `php artisan key:generate --show`)*
   - `DB_CONNECTION`: `sqlite` *(or `mysql` if connecting a Render MySQL instance)*
5. Click **Create Web Service**. The container handles database auto-creation, migrations, seeding, and dynamic `$PORT` binding automatically upon startup.

---

## 📋 Step-by-Step Local Installation Guide

### Prerequisites
- **PHP** >= 8.2 with `gd`, `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json` extensions.
- **Composer** >= 2.0
- **Node.js** >= 20.0 and **npm**
- **MySQL** / **MariaDB** server (e.g. via XAMPP)

---

### Step 1: Clone the Repository
```bash
git clone https://github.com/mhiskall282/hms-db-project.git
cd hms-db-project
```

---

### Step 2: Install PHP & Node Dependencies
```bash
composer install
npm install
```

---

### Step 3: Configure Environment File
Copy `.env.example` to create `.env`:
```bash
cp .env.example .env
```

Configure your database credentials in `.env`:
```env
APP_NAME="Hotel Management System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hms_db
DB_USERNAME=root
DB_PASSWORD=
```

Generate application key:
```bash
php artisan key:generate
```

---

### Step 4: Database Setup & Migration
```bash
php artisan migrate
```

---

### Step 5: Seed the Database
```bash
php artisan db:seed
```

---

### Step 6: Build Assets & Start Server

Compile frontend assets:
```bash
npm run build
```

Start the Laravel development server:
```bash
php artisan serve
```

Visit **`http://localhost:8000`** in your browser.

---

## 🔑 Pre-Seeded Staff Login Credentials

All accounts are pre-seeded with password: **`password`**

| Role | Email Login | System Scope & Permissions |
|------|-------------|----------------------------|
| **System Admin** | `admin@hms.local` | Staff user creation/deactivation, full system config |
| **Hotel Manager** | `manager@hms.local` | Reports, room rates, inventory & staff oversight |
| **Receptionist** | `receptionist@hms.local` | Guest registration, room availability, bookings, check-in/out |
| **Housekeeper** | `housekeeping@hms.local` | Housekeeping dashboard, marking dirty rooms clean/available |
| **Accountant / Cashier** | `accountant@hms.local` | Payment recording, invoices, outstanding balances report |

---

## 🧪 Running Automated Tests

Run the complete test suite:
```bash
php artisan test
```

---

## 📄 License & Attribution

Developed for **ICT Education Final Year Project**.  
Licensed under the [MIT License](LICENSE).
