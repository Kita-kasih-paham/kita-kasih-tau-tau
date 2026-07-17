# Role System Implementation Guide

## Overview

Sistem ini sekarang mendukung 2 role user:

- **Admin**: Akses penuh ke semua fitur sistem
- **Karyawan**: Hanya dapat mengakses fitur Stok Keluar

## Migration

Jalankan migration berikut untuk menambahkan role ke tabel users:

```bash
# Jalankan file SQL ini di database
mysql -u username -p database_name < migration_add_role_to_users.sql
```

Migration ini akan:

1. Menambahkan kolom `role` (ENUM: 'admin', 'karyawan') dengan default 'karyawan'
2. Menambahkan kolom `nama_lengkap` (VARCHAR 255, nullable)
3. Update user 'admin' yang sudah ada menjadi role 'admin'
4. Menambahkan sample user karyawan

## Login Credentials

### Admin

- Username: `admin`
- Password: `admin123`
- Akses: **Semua fitur**

### Karyawan (Sample)

- Username: `karyawan`
- Password: `karyawan123`
- Akses: **Hanya Stok Keluar**

## Role Permissions

### Admin Role

Admin memiliki akses penuh ke:

- ✅ Dashboard (statistik lengkap)
- ✅ Bahan Baku (CRUD + toggle active/inactive)
- ✅ Produk (CRUD + manage ingredients)
- ✅ Stok Masuk (CRUD + bulk input by produk)
- ✅ Stok Keluar (CRUD + bulk produksi by produk)
- ✅ Stok Tersedia (view)
- ✅ Report (view + export Excel)
- ✅ Manajemen User (CRUD users, change roles, change passwords)
- ✅ Profile (change own password)

### Karyawan Role

Karyawan hanya memiliki akses ke:

- ✅ Stok Keluar (CRUD + bulk produksi by produk)
- ✅ Profile (change own password)
- ❌ Dashboard → Auto redirect ke /stok-keluar
- ❌ Semua menu lainnya → Access denied

## Features

### 1. User Management (Admin Only)

Admin dapat mengelola user melalui menu **"Manajemen User"**:

- Tambah user baru (pilih role: admin / karyawan)
- Edit user (username, nama lengkap, role)
- Change password user lain
- Hapus user (dengan proteksi: tidak bisa hapus diri sendiri & admin terakhir)

### 2. Role-Based Sidebar

Sidebar menampilkan menu sesuai role:

- Admin: Semua menu + section "Admin" dengan "Manajemen User"
- Karyawan: Hanya menu "Stok Keluar"

### 3. User Info Display

Sistem menampilkan informasi user:

- Sidebar: Nama lengkap + badge role
- Topbar: Nama lengkap + role subtitle
- Dropdown menu: Nama lengkap + role dengan icon

### 4. Middleware Protection

Routes diproteksi dengan 2 middleware:

- `AuthMiddleware`: Memastikan user sudah login
- `AdminMiddleware`: Memastikan user adalah admin (check role)

### 5. Dashboard Redirect

Ketika karyawan login dan mengakses `/dashboard`, otomatis redirect ke `/stok-keluar`

## File Changes

### New Files

- `middleware/AdminMiddleware.php` - Middleware untuk proteksi admin-only routes
- `controllers/UserController.php` - Controller untuk CRUD user
- `pages/users/index.php` - Halaman list users
- `pages/users/form.php` - Form tambah user baru
- `pages/users/edit.php` - Form edit user
- `migration_add_role_to_users.sql` - Migration untuk add role

### Modified Files

- `routes/web.php` - Update middleware untuk semua routes
- `shared/components.php` - Update sidebar & topbar dengan role-based display
- `models/UserModel.php` - Add `countByRole()` method
- `controllers/DashboardController.php` - Add redirect untuk karyawan
- `core/helpers.php` - Add `fmtDateTime()` function

## Route Protection Summary

### Admin Only Routes

```
/dashboard
/bahan-baku (all actions)
/produk (all actions)
/stok-masuk (all actions)
/stok-tersedia
/report (view + export)
/users (all CRUD actions)
```

### Authenticated Routes (Both Roles)

```
/stok-keluar (all actions)
/profile (change own password)
```

### Public Routes

```
/login
/logout
```

## Security Features

1. **Password Hashing**: Semua password di-hash menggunakan `password_hash()` (bcrypt)
2. **Role Validation**: AdminMiddleware memvalidasi role sebelum akses admin-only routes
3. **Self-Delete Protection**: User tidak bisa menghapus dirinya sendiri
4. **Last Admin Protection**: Tidak bisa menghapus admin terakhir
5. **Username Uniqueness**: Validasi username unique saat create/update

## Testing Checklist

### As Admin

- [x] Login sebagai admin
- [x] Akses semua menu (Dashboard, Bahan Baku, Produk, Stok Masuk, Stok Keluar, Stok Tersedia, Report)
- [x] Akses Manajemen User
- [x] Tambah user karyawan baru
- [x] Edit user
- [x] Hapus user
- [x] Ganti password user lain
- [x] Ganti password sendiri via Profile

### As Karyawan

- [x] Login sebagai karyawan
- [x] Verify redirect dari /dashboard ke /stok-keluar
- [x] Verify hanya menu "Stok Keluar" yang muncul di sidebar
- [x] Akses fitur Stok Keluar (view, create, edit, delete)
- [x] Akses fitur bulk produksi
- [x] Try akses /bahan-baku (should get access denied)
- [x] Try akses /produk (should get access denied)
- [x] Try akses /users (should get access denied)
- [x] Ganti password sendiri via Profile

## Future Enhancements

- Add "View Activity Log" for tracking user actions
- Add role "Manager" dengan akses read-only ke semua fitur
- Add email notification when user is created/deleted
- Add password reset via email
- Add session timeout after inactivity
