# 🚀 Deployment Guide - Member Portal

## Perubahan yang akan di-deploy:

### 1. **Migration Database** ⚠️
- `2025_12_29_161835_add_member_role_to_users_table.php`
  - Menambahkan role `MEMBER` ke enum users table

### 2. **New Features**
- Member Portal (4 halaman): Dashboard, Profile, Simpanan, Transactions
- Login redirect otomatis untuk role MEMBER
- Layout terpisah dengan font Plus Jakarta Sans
- Dark mode support

### 3. **Updated Files**
- Routes: `/member`, `/member/profile`, `/member/simpanan`, `/member/transactions`
- Models: User.php (tambah method isMember())
- Commands: UpdateMemberEmails.php (update email + role)

---

## 📋 Langkah Deployment di Production (cPanel):

### Step 1: Pull & Merge ke Main
```bash
cd /path/to/koperasi-umb

# Fetch branch terbaru
git fetch origin

# Checkout ke main
git checkout main

# Merge feature branch
git merge origin/feature/member-portal

# Atau langsung pull main jika sudah merge di GitHub
git pull origin main
```

### Step 2: ⚠️ **WAJIB - Jalankan Migration**
```bash
php artisan migrate
```
**Output yang diharapkan:**
```
INFO  Running migrations.

2025_12_29_161835_add_member_role_to_users_table ........ DONE
```

### Step 3: ⚠️ **WAJIB - Update Email & Role Member**
```bash
php artisan members:update-emails --with-users
```
**Output yang diharapkan:**
```
Successfully updated XXX member emails to @bermadani.id format.
Updated XXX existing user account emails.
Created 0 new user accounts with password: 'password'
```

### Step 4: Clear Cache
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Step 5: (Optional) Reset Password Member untuk Testing
Jika ingin reset password semua member jadi `password`:
```bash
php artisan tinker --execute="
\$hashedPassword = \Illuminate\Support\Facades\Hash::make('password');
\$count = \App\Models\User::where('role', 'MEMBER')->update(['password' => \$hashedPassword]);
echo 'Reset password for ' . \$count . ' members to: password';
"
```

---

## ✅ Validasi Deployment Berhasil:

1. **Cek Migration:**
   ```bash
   php artisan tinker --execute="
   print_r(\DB::select(\"SHOW COLUMNS FROM users WHERE Field = 'role'\"));
   "
   ```
   Pastikan ada `MEMBER` di enum role.

2. **Cek Email Member:**
   ```bash
   php artisan tinker --execute="
   \$member = \App\Models\Member::first();
   echo 'Email: ' . \$member->email . PHP_EOL;
   echo 'User Email: ' . \$member->user->email . PHP_EOL;
   echo 'User Role: ' . \$member->user->role;
   "
   ```
   Pastikan email format `YYNNNNNN@bermadani.id` dan role `MEMBER`.

3. **Test Login Member:**
   - URL: `https://your-domain.com/login`
   - Email: `{nomorAnggota}@bermadani.id` (contoh: `21000001@bermadani.id`)
   - Password: `password` (atau password yang sudah ada)
   - Seharusnya redirect ke `/member` (member dashboard)

4. **Cek Routes Member Portal:**
   ```bash
   php artisan route:list --name=member
   ```

---

## 🔴 CATATAN PENTING:

### ⚠️ **WAJIB Jalankan:**
1. ✅ `php artisan migrate` - Migration MEMBER role
2. ✅ `php artisan members:update-emails --with-users` - Update email & role

### 📌 **Tidak Perlu SQL Manual:**
- Semua perubahan database sudah ditangani oleh migration & command
- Tidak ada SQL script tambahan yang perlu dijalankan

### 🔐 **Login Member:**
- Email: `{nomorAnggota}@bermadani.id`
- Password: (password existing member, atau reset dengan command di atas)

### 🎨 **Dark Mode:**
- Member portal support dark mode (ikut system preference)
- Toggle di sidebar

---

## 🛠️ Troubleshooting:

**Problem:** Login redirect tidak ke /member
- **Solution:** Clear cache, cek User->role = 'MEMBER'

**Problem:** Email masih format lama
- **Solution:** Jalankan ulang `php artisan members:update-emails --with-users`

**Problem:** "Column 'role' doesn't have value MEMBER"
- **Solution:** Jalankan migration `php artisan migrate`

**Problem:** Simpanan tidak muncul
- **Solution:** Data sudah benar, refresh browser & clear cache

---

## 📊 Ringkasan Deployment:

**Apakah `git pull origin main` cukup?**
❌ **TIDAK CUKUP!**

**Yang HARUS dilakukan:**
```bash
git pull origin main              # 1. Pull code
php artisan migrate               # 2. WAJIB - Add MEMBER role
php artisan members:update-emails --with-users  # 3. WAJIB - Update email & role
php artisan config:clear          # 4. Clear cache
php artisan route:clear
php artisan view:clear
```

Tanpa langkah 2 & 3, member **TIDAK BISA LOGIN** ke portal!

---

Generated: 2026-01-06
Branch: feature/member-portal → main
