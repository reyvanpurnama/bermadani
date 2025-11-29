# 🚀 Koperasi UMB - Setup & Deployment Guide

## 📋 System Requirements

- PHP 8.2+
- MySQL 8.0+ / MariaDB 10.3+
- Composer 2.x
- Node.js 18+ & NPM

## 🔧 Local Development Setup

### 1. Clone Repository
```bash
git clone https://github.com/reyvanevan/koperasi-umb.git
cd koperasi-umb
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Configuration
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
APP_NAME="Koperasi UMB"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=koperasi_umb
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=sync
SESSION_DRIVER=file
CACHE_STORE=file
```

### 4. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

**Default Users Created:**
- **Admin**: admin@koperasiumb.com / password
- **Kasir**: kasir@koperasiumb.com / password

### 5. Run Development Server
```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite (Assets)
npm run dev
```

Access: http://localhost:8000/admin

---

## 🏪 POS Core Features (Week 1 & 2)

### ✅ Completed Features

#### 1. **Database Foundation**
- ✅ All migrations ready (users, categories, products, transactions, etc.)
- ✅ Models with complete relationships
- ✅ Enums for type safety (TransactionType, PaymentMethod, etc.)

#### 2. **Product Management**
- ✅ Category CRUD (Filament)
- ✅ Product CRUD with stock management
- ✅ Low stock monitoring
- ✅ Sample data seeder (16 products across 5 categories)

#### 3. **POS Interface**
- ✅ Modern Livewire-based POS
- ✅ Product search & barcode scanning
- ✅ Category filtering
- ✅ Shopping cart with quantity adjustment
- ✅ Member selection & points tracking
- ✅ Multiple payment methods (Cash, Transfer, Credit)
- ✅ Real-time stock validation

#### 4. **Transaction System**
- ✅ Complete transaction processing
- ✅ Auto stock reduction
- ✅ Invoice generation
- ✅ Transaction history (Filament Resource)
- ✅ Print receipt functionality
- ✅ Member points calculation

#### 5. **User Management**
- ✅ Role-based access (SUPER_ADMIN, ADMIN, KASIR, SUPPLIER)
- ✅ POS access control
- ✅ Filament admin panel integration

---

## 🗂️ Project Structure

```
app/
├── Enums/                    # Type-safe enums
│   ├── TransactionType.php
│   ├── PaymentMethod.php
│   ├── ProductStatus.php
│   └── ...
├── Filament/
│   ├── Pages/
│   │   └── POSPage.php      # POS Filament page
│   └── Resources/            # Admin CRUD
│       ├── CategoryResource.php
│       ├── ProductResource.php
│       ├── TransactionResource.php
│       └── ...
├── Http/Controllers/
│   └── TransactionController.php
├── Livewire/
│   └── POS.php              # Main POS component
└── Models/                   # Eloquent models with relationships

database/
├── migrations/               # Complete DB schema
└── seeders/
    ├── CategorySeeder.php
    └── ProductSeeder.php

resources/views/
├── livewire/
│   └── p-o-s.blade.php      # POS UI
└── transactions/
    └── receipt.blade.php     # Print receipt
```

---

## 🎯 Testing Checklist

### Manual Testing Steps:

1. **Login to Admin**
   - Go to `/admin`
   - Login with admin@koperasiumb.com / password

2. **Test Product Management**
   - Navigate to Inventory > Products
   - View products created by seeder
   - Edit a product, change stock

3. **Test POS**
   - Navigate to POS (Point of Sale)
   - Search for "Indomie"
   - Click product to add to cart
   - Adjust quantity
   - Click "Bayar Sekarang"
   - Select Cash payment
   - Enter amount received
   - Click "Proses"
   - Verify success message

4. **Test Transaction History**
   - Navigate to POS > Transaksi
   - View transaction list
   - Click "View" on a transaction
   - Click "Print Receipt"
   - Verify receipt opens in new tab

5. **Test Low Stock Alert**
   - Sell products until stock < threshold
   - Check navigation badge color changes to red

---

## 📦 Deployment to cPanel (Rumahweb)

### Prerequisites:
- cPanel hosting with PHP 8.2+
- MySQL database created
- SSH access (optional but recommended)

### Step 1: Prepare Files
```bash
# Build production assets
npm run build

# Create deployment archive (exclude dev files)
zip -r koperasi-umb.zip . -x "node_modules/*" ".git/*" "storage/*" ".env"
```

### Step 2: Upload to cPanel
1. Upload `koperasi-umb.zip` via cPanel File Manager
2. Extract to `/home/username/laravel-app/`
3. Move `public/*` contents to `/home/username/public_html/`

### Step 3: Update public/index.php
Edit `public_html/index.php`:
```php
require __DIR__.'/../laravel-app/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel-app/bootstrap/app.php';
```

### Step 4: Configure Environment
Create `.env` in `/home/username/laravel-app/`:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

QUEUE_CONNECTION=sync
SESSION_DRIVER=file
CACHE_STORE=file
```

### Step 5: Run Artisan Commands (SSH)
```bash
cd ~/laravel-app
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 6: Set Permissions
```bash
chmod -R 755 ~/laravel-app
chmod -R 775 ~/laravel-app/storage
chmod -R 775 ~/laravel-app/bootstrap/cache
```

---

## 🔐 Production Security Checklist

- [ ] Change all default passwords
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Configure proper database credentials
- [ ] Enable HTTPS/SSL
- [ ] Set proper file permissions (755 directories, 644 files)
- [ ] Configure firewall rules
- [ ] Regular database backups
- [ ] Update `.htaccess` for security headers

---

## 🐛 Troubleshooting

### "Class not found" error
```bash
composer dump-autoload
php artisan clear-compiled
php artisan optimize:clear
```

### Permission errors
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Assets not loading
```bash
npm run build
php artisan storage:link
```

### Database connection failed
- Check `.env` database credentials
- Verify database exists
- Test connection: `php artisan tinker` → `DB::connection()->getPdo();`

---

## 📞 Support & Documentation

- **Full Migration Docs**: `docs/LARAVEL_MIGRATION/`
- **Database Schema**: `docs/LARAVEL_MIGRATION/01_DATABASE_SCHEMA.md`
- **Deployment Guide**: `docs/LARAVEL_MIGRATION/04_CPANEL_DEPLOYMENT.md`

---

## 🎉 Next Steps (Week 3+)

After successful deployment:

1. **Supplier System** (Week 3)
   - Supplier registration portal
   - Product submission workflow
   - Admin approval process

2. **Consignment System** (Week 4)
   - Batch tracking
   - Sales attribution
   - Settlement & payment

3. **Data Migration** (Week 5)
   - Import existing data from Next.js
   - Verify data integrity
   - Production cutover

---

**Created by**: Aegner & Reyvan  
**Date**: November 29, 2025  
**Laravel Version**: 12.x  
**Filament Version**: 3.x
