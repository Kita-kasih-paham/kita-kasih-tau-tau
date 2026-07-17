# Sistem Pengelolaan Stok (SPS)

Aplikasi web untuk mengelola stok bahan baku, produk, dan transaksi stok masuk/keluar dengan sistem role-based access control dan user tracking.

## 🆕 Latest Updates (15 Juli 2026)

### Role-Based Access Control ✅

- 2 Role: **Admin** (full access) & **Karyawan** (Stok Keluar only)
- User Management untuk admin
- Role-based sidebar & menu display
- AdminMiddleware untuk route protection

### User Tracking in Stok Keluar ✅

- Track user yang membuat setiap transaksi
- Display nama user + timestamp di tabel
- Audit trail untuk accountability
- Backward compatible dengan data lama

## 📋 Features

### Untuk Admin

- ✅ Dashboard dengan statistik lengkap
- ✅ Manajemen Bahan Baku (CRUD, active/inactive status, low stock warning)
- ✅ Manajemen Produk dengan BOM (Bill of Materials)
- ✅ Stok Masuk (manual + bulk by produk)
- ✅ Stok Keluar (manual + bulk produksi) **dengan user tracking**
- ✅ Stok Tersedia (view realtime)
- ✅ Report dengan filter bulan/range & export Excel
- ✅ **Manajemen User** (CRUD, roles, passwords)
- ✅ Ganti password sendiri

### Untuk Karyawan

- ✅ Stok Keluar (CRUD + bulk produksi)
- ✅ Transaksi otomatis ter-track dengan nama
- ✅ Ganti password sendiri
- ❌ Tidak bisa akses menu lain (auto redirect)

## 🔐 Default Login

### Admin

```
Username: admin
Password: admin123
Role: Admin
Access: Full system access
```

### Karyawan (Sample)

```
Username: karyawan
Password: karyawan123
Role: Karyawan
Access: Stok Keluar only
```

## 🚀 Installation

### Prerequisites

- PHP 7.4+ atau 8.x
- MySQL 5.7+ atau MariaDB
- Web Server (Apache with mod_rewrite / Nginx)
- Composer (optional)

### Setup Steps

1. **Clone/Download Project**

   ```bash
   git clone <repo-url>
   cd sps
   ```

2. **Import Database**

   ```bash
   mysql -u root -p < database.sql
   ```

3. **Run Migrations (In Order)**

   ```bash
   mysql -u root -p sistem_pengelolaan_stok_db < migration_rename_to_produk.sql
   mysql -u root -p sistem_pengelolaan_stok_db < migration_remove_bahan_baku_from_produk.sql
   mysql -u root -p sistem_pengelolaan_stok_db < migration_add_is_active_to_bahan_baku.sql
   mysql -u root -p sistem_pengelolaan_stok_db < migration_add_produk_to_stok_keluar.sql
   mysql -u root -p sistem_pengelolaan_stok_db < migration_add_role_to_users.sql
   mysql -u root -p sistem_pengelolaan_stok_db < migration_add_user_tracking_to_stok_keluar.sql
   ```

4. **Configure Environment**
   - Edit `.env` file dengan database credentials Anda

   ```
   DB_HOST=localhost
   DB_NAME=sistem_pengelolaan_stok_db
   DB_USER=root
   DB_PASS=your_password
   ```

5. **Configure Web Server**
   - Point document root ke folder project
   - Pastikan `.htaccess` berfungsi (Apache) atau configure Nginx

6. **Access Application**

   ```
   http://localhost/sps
   atau
   http://your-domain.com
   ```

7. **Login & Test**
   - Login sebagai admin
   - Buat user karyawan baru di "Manajemen User"
   - Logout, login sebagai karyawan
   - Test create stok keluar
   - Verify nama karyawan muncul di kolom "Dibuat Oleh"

## 📖 Documentation

Baca panduan lengkap di:

- [**ROLE_SYSTEM_GUIDE.md**](ROLE_SYSTEM_GUIDE.md) - Role system & permissions
- [**USER_TRACKING_GUIDE.md**](USER_TRACKING_GUIDE.md) - User tracking implementation
- [**IMPLEMENTATION_SUMMARY.md**](IMPLEMENTATION_SUMMARY.md) - Complete summary
- [**BOM_IMPLEMENTATION_GUIDE.md**](BOM_IMPLEMENTATION_GUIDE.md) - Bill of Materials

## 🗂️ Project Structure

```
sps/
├── assets/              # CSS, images, favicon
├── config/              # App configuration
├── controllers/         # Controller classes
│   ├── AuthController.php
│   ├── DashboardController.php
│   ├── BahanBakuController.php
│   ├── ProdukController.php
│   ├── StokMasukController.php
│   ├── StokKeluarController.php
│   ├── StokTersediaController.php
│   ├── ReportController.php
│   ├── ProfileController.php
│   └── UserController.php          # NEW: User management
├── core/                # Core framework files
│   ├── Database.php
│   ├── Router.php
│   ├── Model.php
│   ├── Validator.php
│   ├── Flash.php
│   ├── Middleware.php
│   └── helpers.php
├── middleware/          # Middleware classes
│   ├── AuthMiddleware.php
│   └── AdminMiddleware.php         # NEW: Admin protection
├── models/              # Model classes
│   ├── UserModel.php
│   ├── BahanBakuModel.php
│   ├── ProdukModel.php
│   ├── StokMasukModel.php
│   └── StokKeluarModel.php
├── pages/               # View files
│   ├── auth/
│   ├── dashboard/
│   ├── bahan-baku/
│   ├── produk/
│   ├── stok-masuk/
│   ├── stok-keluar/
│   ├── stok-tersedia/
│   ├── report/
│   ├── profile/
│   └── users/                      # NEW: User management pages
├── routes/              # Route definitions
│   └── web.php
├── shared/              # Shared components
│   └── components.php
├── .env                 # Environment config
├── .htaccess            # Apache rewrite rules
├── index.php            # Application entry point
├── database.sql         # Initial database
├── migration_*.sql      # Database migrations
└── README.md            # This file
```

## 🔒 Security Features

- ✅ Password hashing (bcrypt)
- ✅ Session-based authentication
- ✅ Role-based authorization (AdminMiddleware)
- ✅ CSRF protection (form validation)
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS prevention (htmlspecialchars)
- ✅ User self-delete protection
- ✅ Last admin deletion protection

## 🎨 Tech Stack

- **Backend**: PHP 7.4+ (Custom MVC Framework)
- **Database**: MySQL 5.7+
- **Frontend**: Bootstrap 5.3, Bootstrap Icons
- **JavaScript**: Vanilla JS + jQuery (for DataTables)
- **Libraries**: DataTables, PhpSpreadsheet (for Excel export)

## 📊 Key Concepts

### Bill of Materials (BOM)

Setiap produk memiliki resep (list bahan baku + jumlah yang dibutuhkan per unit). Ketika produksi, sistem otomatis menghitung kebutuhan bahan dan validasi stok.

### User Tracking

Setiap transaksi stok keluar mencatat:

- User ID (siapa yang input)
- Created By (nama user - snapshot)
- Created At (kapan dibuat)

### Role-Based Access

- **Admin**: Full control + user management
- **Karyawan**: Limited to stok keluar operations

## 🧪 Testing

### Manual Testing

1. Login sebagai admin → Test all menus
2. Create user karyawan → Test user management
3. Login sebagai karyawan → Verify limited access
4. Create stok keluar → Verify user tracking
5. Check "Dibuat Oleh" column → Verify nama muncul

### Test Scenarios

- [ ] Admin bisa akses semua menu
- [ ] Karyawan redirect dari dashboard ke stok keluar
- [ ] Karyawan dapat 403 saat akses admin routes
- [ ] User tracking muncul di stok keluar table
- [ ] Old data (before migration) shows "—"
- [ ] Cannot delete self
- [ ] Cannot delete last admin

## 🐛 Troubleshooting

### Issue: 404 Not Found

**Solution**: Check `.htaccess` dan pastikan `mod_rewrite` enabled

### Issue: Database connection failed

**Solution**: Verify `.env` credentials & MySQL service running

### Issue: User tracking tidak muncul

**Solution**: Run `migration_add_user_tracking_to_stok_keluar.sql`

### Issue: Access denied for karyawan

**Solution**: This is expected! Karyawan hanya bisa akses /stok-keluar

## 📝 Changelog

### v2.0.0 (15 Juli 2026)

- ✅ Added Role-Based Access Control (Admin & Karyawan)
- ✅ Added User Management (CRUD users)
- ✅ Added User Tracking in Stok Keluar
- ✅ Added AdminMiddleware for route protection
- ✅ Updated sidebar/topbar with role-based display
- ✅ Added created_by & created_at columns to stok_keluar

### v1.0.0 (Previous)

- ✅ Initial release
- ✅ Basic CRUD for Bahan Baku, Produk, Stok
- ✅ BOM (Bill of Materials) implementation
- ✅ Bulk operations (stok masuk/keluar by produk)
- ✅ Report with Excel export

## 🤝 Contributing

Untuk contribute atau report bugs:

1. Fork the repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

## 📄 License

Proprietary - All rights reserved

## 👨‍💻 Developer

- **Developed by**: Your Team
- **AI Assistant**: Kiro
- **Last Update**: 15 Juli 2026

---

**🎉 System Ready for Production!**

Jalankan migrations, test dengan kedua role, dan sistem siap digunakan.
