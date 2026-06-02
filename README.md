<p align="center">
    <h1 align="center">📦 Inventory Management System</h1>
    <p align="center">A full-featured Inventory Management System built with Laravel 9 and Bootstrap 5</p>
</p>

## Features

### 🔐 Authentication
- User Registration (name, email, password)
- User Login with "Remember Me"
- Password Reset via Email
- Role-based access (Admin/Staff)
- CSRF Protection

### 📊 Dashboard
- Total products, stock quantity, and inventory value
- Low stock alerts (configurable reorder level)
- Top 5 moving products (last 30 days)
- Recent stock transactions

### 📦 Product Management
- Full CRUD operations
- Live search/filter by name
- Pagination (10 per page)
- Reorder level tracking

### 🚚 Supplier Management
- Full CRUD operations
- Product count per supplier
- Phone number tracking

### 📥 Stock In / 📤 Stock Out
- Stock In: Select product + supplier + quantity
- Stock Out: Select product with available stock
- Automatic quantity updates
- Insufficient stock validation
- Transaction history

### 📈 Reports
- Inventory Summary Report (with PDF export)
- Stock History (filterable by date range + product)
- Low Stock Report

### 👥 Role-Based Permissions
- **Admin**: Full CRUD access, PDF export
- **Staff**: View-only access (products, suppliers, stock history)

## Tech Stack

- **Backend**: Laravel 9.x
- **Frontend**: Bootstrap 5.3, JavaScript
- **Database**: MySQL (configurable)
- **PDF**: barryvdh/laravel-dompdf
- **Datepicker**: Flatpickr

## Installation

### Prerequisites
- PHP >= 8.0
- Composer
- Node.js & NPM
- MySQL (or SQLite for development)

### Setup Steps

1. **Clone the repository**
```bash
cd Inventory Management System
```

2. **Install PHP dependencies**
```bash
composer install
```

3. **Install JavaScript dependencies**
```bash
npm install
npm run build
```

4. **Environment Configuration**
```bash
cp .env.example .env
```

Update the `.env` file with your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_db
DB_USERNAME=root
DB_PASSWORD=
```

For SQLite (development):
```env
DB_CONNECTION=sqlite
# Create the database file: touch database/database.sqlite
```

5. **Generate application key**
```bash
php artisan key:generate
```

6. **Run migrations and seeders**
```bash
php artisan migrate --seed
```

7. **Start the development server**
```bash
php artisan serve
```

8. **Build assets (for production)**
```bash
npm run build
```

## Test Credentials

| Role  | Email              | Password  |
|-------|--------------------|-----------|
| Admin | admin@example.com  | password  |
| Staff | staff1@example.com | password  |
| Staff | staff2@example.com | password  |

## Routes

### Authentication (Guest)
| Method | URI                        | Name              |
|--------|----------------------------|-------------------|
| GET    | `/login`                   | login             |
| POST   | `/login`                   | -                 |
| GET    | `/register`                | register          |
| POST   | `/register`                | -                 |
| GET    | `/forgot-password`         | password.request  |
| POST   | `/forgot-password`         | password.email    |
| GET    | `/reset-password/{token}`  | password.reset    |
| POST   | `/reset-password`          | password.update   |

### Authenticated (Admin & Staff)
| Method | URI                        | Name               |
|--------|----------------------------|--------------------|
| POST   | `/logout`                  | logout             |
| GET    | `/dashboard`               | dashboard          |
| GET    | `/products`                | products.index     |
| GET    | `/products/create`         | products.create    |
| POST   | `/products`                | products.store     |
| GET    | `/products/{id}/edit`      | products.edit      |
| PUT    | `/products/{id}`           | products.update    |
| DELETE | `/products/{id}`           | products.destroy   |
| GET    | `/suppliers`               | suppliers.index    |
| ...    | (similar resource routes)  | ...                |
| GET    | `/stock/in`                | stock.in.create    |
| POST   | `/stock/in`                | stock.in.store     |
| GET    | `/stock/out`               | stock.out.create   |
| POST   | `/stock/out`               | stock.out.store    |
| GET    | `/reports/inventory`       | reports.inventory  |
| GET    | `/reports/history`         | reports.history    |
| GET    | `/reports/lowstock`        | reports.lowstock   |
| POST   | `/reports/export/pdf`      | reports.export.pdf |

## Database Schema

### Tables
- **users** - User accounts with role (admin/staff)
- **products** - Product catalog with stock tracking
- **suppliers** - Supplier directory
- **stock_in** - Stock intake records
- **stock_out** - Stock dispatch records

### Stock Logic
- `stock_in` → increases product quantity
- `stock_out` → decreases product quantity (rejected if insufficient)
- Uses database transactions for consistency

## Development

### Compile Assets
```bash
npm run dev    # Development
npm run build  # Production
```
