# Tech Stack Documentation

## Overview
Web Koperasi UMB - Laravel-based cooperative management system with Livewire for reactive components.

## Core Stack

### Backend
- **Laravel 12.0** - PHP framework with Eloquent ORM
- **PHP 8.2** - Server-side language with modern features (enums, typed properties)
- **MySQL** - Relational database
- **Composer** - PHP dependency manager

### Frontend
- **Livewire 3.x** - Server-side component framework (reactive UI without JavaScript SPA)
- **Tailwind CSS 4.0** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript for interactivity (dropdowns, modals)
- **Blade** - Laravel templating engine

### Build Tools
- **Vite 7.0.7** - Modern build tool (replaces Webpack/Laravel Mix)
  - 10-100x faster builds than Webpack
  - Native ES modules
  - Hot Module Replacement (HMR) for instant updates
  - Uses `laravel-vite-plugin` and `@tailwindcss/vite`

- **Concurrently 9.0.1** - Run parallel dev processes:
  ```bash
  npm run dev  # Runs 4 parallel processes:
  # 1. php artisan serve (Laravel server)
  # 2. php artisan queue:listen (Queue worker)
  # 3. php artisan pail (Log viewer)
  # 4. npm run vite (Vite dev server with HMR)
  ```

## Architecture Pattern

### TALL-ish Stack (NOT full TALL)
- **T**ailwind CSS 4.0
- **A**lpine.js (via CDN)
- **L**aravel 12.0
- **L**ivewire 3.x

Note: NO Turbo/Hotwire - using traditional server-side rendering with Livewire reactivity.

### Authentication System
**Custom Multi-Guard Auth (NOT Laravel Breeze/Jetstream)**

Implementation in `routes/web.php` (lines 20-130+):
- Multi-credential login: email, member number, or phone
- Multi-guard system:
  - `web` guard: Admin, Kasir, Developer
  - `supplier` guard: Supplier portal
- Activity logging on login/logout
- Password change enforcement

### Authorization System
**Custom Role-Based with CheckRole Middleware**

File: `app/Http/Middleware/CheckRole.php`

Role hierarchy (ENUM in User table):
1. SUPER_ADMIN (highest)
2. ADMIN
3. KASIR
4. DEVELOPER
5. SUPPLIER
6. MEMBER (lowest)

Usage:
```php
Route::middleware('role:ADMIN,SUPER_ADMIN')->group(function() {
    // Only ADMIN and SUPER_ADMIN can access
});
```

## Key Packages

### Backend Packages
- **livewire/livewire** - Reactive components
- **dompdf/dompdf 3.1** - PDF generation
- **maatwebsite/excel 3.1** - CSV import/export
- **laravel/pail** - Artisan log viewer
- **spatie/laravel-activitylog** - Audit logging

### Frontend Packages
- **axios 1.11.0** - HTTP client (configured with CSRF in `bootstrap.js`)
- **boxicons** - Icon library (CDN)
- **NO Vue/React/Angular** - Pure server-side rendering

## Project Structure

```
├── app/
│   ├── Livewire/          # Livewire components
│   ├── Models/            # Eloquent models
│   ├── Enums/             # PHP 8.2 enums
│   └── Http/
│       ├── Controllers/   # Traditional controllers
│       └── Middleware/    # Custom middleware (CheckRole, LogActivity)
├── resources/
│   ├── views/             # Blade templates
│   │   └── livewire/      # Livewire component views
│   ├── css/
│   │   └── app.css        # Tailwind entry point
│   └── js/
│       ├── app.js         # Vite entry point
│       └── bootstrap.js   # Axios setup
├── routes/
│   └── web.php            # All web routes + custom auth
├── database/
│   ├── migrations/        # Database schema
│   └── seeders/           # Data seeders
└── vite.config.js         # Vite configuration
```

## Development Workflow

### Starting Dev Environment
```bash
npm run dev
```
This starts 4 parallel processes:
1. Laravel server (http://localhost:8000)
2. Queue listener (background jobs)
3. Pail logs (real-time log viewer)
4. Vite HMR server (http://localhost:5173)

### Building for Production
```bash
npm run build
```
Vite compiles and minifies CSS/JS to `public/build/`

### Database Operations
```bash
php artisan migrate              # Run migrations
php artisan db:seed              # Run seeders
php artisan migrate:fresh --seed # Fresh database
```

## Why Vite? (vs Webpack/Laravel Mix)

### Performance
- **10-100x faster** cold starts
- **Instant HMR** (< 100ms updates)
- Native ES modules (no bundling in dev)

### Modern Features
- Built-in TypeScript support
- Tree shaking by default
- Code splitting automatic
- CSS code splitting

### Developer Experience
- Simpler configuration (30 lines vs 200+ in Webpack)
- Better error messages
- Faster feedback loop

## Authentication Flow

### Admin/Kasir Login
1. User enters email/phone/member number + password
2. `routes/web.php` validates credentials
3. Check user role (must be ADMIN, KASIR, or SUPER_ADMIN)
4. Verify `isActive` status
5. Log activity
6. Redirect to `/admin/home`

### Supplier Login
1. Supplier enters credentials at `/supplier/login`
2. Use `supplier` guard for authentication
3. Check `SupplierStatus` enum
4. Redirect to `/supplier/dashboard`

### Member Portal
1. Member logs in at `/member/login`
2. Standard `web` guard
3. Role check: MEMBER only
4. Redirect to member dashboard

## Common Patterns

### Livewire Component Structure
```php
class BankAuditTool extends Component
{
    // Properties (public = reactive)
    public $csvFiles;
    public $activeTab = 'upload';
    
    // Methods (public = callable from view)
    public function processUploads() { }
    
    // Render
    public function render()
    {
        return view('livewire.admin.bank-audit-tool');
    }
}
```

### Blade + Livewire
```blade
<div>
    <!-- Wire:model for two-way binding -->
    <input wire:model="search" type="text">
    
    <!-- Wire:click for actions -->
    <button wire:click="processUploads">Upload</button>
    
    <!-- Loading states -->
    <div wire:loading>Processing...</div>
</div>
```

### Alpine.js for Client-Side
```blade
<div x-data="{ open: false }">
    <button @click="open = !open">Toggle</button>
    <div x-show="open">Dropdown content</div>
</div>
```

## Database Conventions

- Migration naming: `YYYY_MM_DD_HHMMSS_description.php`
- Table names: plural snake_case (`bank_transactions`)
- Foreign keys: `{table}_id` (`user_id`)
- Timestamps: `created_at`, `updated_at` (automatic)
- Soft deletes: `deleted_at`
- Enums: PHP 8.2 backed enums in `app/Enums/`

## Environment Variables

Key `.env` settings:
```env
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_DATABASE=koperasi_umb
VITE_PORT=5173
```

## Production Deployment

1. `composer install --optimize-autoloader --no-dev`
2. `npm run build`
3. `php artisan config:cache`
4. `php artisan route:cache`
5. `php artisan view:cache`
6. Set `APP_ENV=production` and `APP_DEBUG=false`
