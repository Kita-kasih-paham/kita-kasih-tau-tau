# User Tracking in Stok Keluar - Implementation Guide

## Overview

Sistem stok keluar sekarang mencatat informasi user yang melakukan transaksi stok keluar. Setiap transaksi akan menyimpan:

- **user_id**: ID user yang membuat transaksi
- **created_by**: Nama lengkap user (atau username jika nama lengkap tidak ada)
- **created_at**: Timestamp kapan transaksi dibuat

## Migration

Jalankan migration berikut untuk menambahkan kolom tracking ke tabel `stok_keluar`:

```bash
# Jalankan file SQL ini di database
mysql -u username -p database_name < migration_add_user_tracking_to_stok_keluar.sql
```

### What the migration does:

1. Menambahkan kolom `user_id` (BIGINT UNSIGNED, nullable)
2. Menambahkan kolom `created_by` (VARCHAR 255, nullable)
3. Menambahkan kolom `created_at` (TIMESTAMP, default CURRENT_TIMESTAMP)
4. (Optional) Menambahkan foreign key constraint ke tabel `users`

**Note**: Kolom dibuat nullable untuk backward compatibility dengan data existing yang tidak memiliki informasi user.

## Features

### 1. Automatic User Tracking

Setiap kali user membuat transaksi stok keluar (baik manual maupun by produk), sistem otomatis mencatat:

- User ID dari session
- Nama lengkap user (jika ada) atau username sebagai fallback
- Waktu transaksi dibuat

### 2. Display User Information

Tabel stok keluar menampilkan kolom "Dibuat Oleh" dengan informasi:

- **Icon person** + **Nama user** (bold)
- **Timestamp** transaksi (format: DD Mon YYYY, HH:mm)
- Jika data lama (sebelum migration), tampilkan "—"

### 3. Role-Based Tracking

- **Admin**: Bisa melihat siapa yang membuat setiap transaksi
- **Karyawan**: Transaksi mereka otomatis ter-track dengan nama mereka

## Data Structure

### Table: stok_keluar

```sql
CREATE TABLE `stok_keluar` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `bahan_baku_id`  BIGINT UNSIGNED NOT NULL,
    `jumlah`         BIGINT NOT NULL,
    `tanggal`        DATE NOT NULL,
    `keterangan`     TEXT NULL,
    `produk_id`      BIGINT UNSIGNED NULL,           -- From previous migration
    `jumlah_produk`  DECIMAL(10,2) NULL,             -- From previous migration
    `user_id`        BIGINT UNSIGNED NULL,           -- NEW: User who created
    `created_by`     VARCHAR(255) NULL,              -- NEW: User name snapshot
    `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,  -- NEW: Creation time
    CONSTRAINT `stok_keluar_bahan_baku_id_foreign`
        FOREIGN KEY (`bahan_baku_id`) REFERENCES `bahan_baku` (`id`) ON DELETE CASCADE,
    CONSTRAINT `stok_keluar_produk_id_foreign`
        FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE SET NULL
    -- Optional: Add user_id foreign key if needed
);
```

## Code Changes

### Modified Files

#### 1. `models/StokKeluarModel.php`

- Updated `allWithBahanBaku()`: Join dengan tabel `users` untuk ambil informasi user
- Updated `findWithBahanBaku()`: Include user info saat fetch detail
- Added column existence check untuk backward compatibility

#### 2. `controllers/StokKeluarController.php`

- Updated `store()`: Auto-populate `user_id` dan `created_by` dari session
- Updated `bulk()`: Auto-populate `user_id` dan `created_by` untuk bulk produksi

#### 3. `pages/stok-keluar/index.php`

- Added column "Dibuat Oleh" di tabel
- Display user name dengan icon
- Display timestamp dengan format Indonesian
- Show "—" untuk data lama yang tidak memiliki user info

#### 4. `core/helpers.php`

- Added `fmtDateTime()` function untuk format timestamp

## Display Examples

### Stok Keluar Table

| #   | Tanggal             | Kode   | Nama Bahan    | Produk     | Jumlah   | Dibuat Oleh                                               | Keterangan                     |
| --- | ------------------- | ------ | ------------- | ---------- | -------- | --------------------------------------------------------- | ------------------------------ |
| 1   | Senin, 15 Jul 2026  | BB-001 | Tepung Terigu | Roti Tawar | 5.000 kg | **👤 Karyawan Demo**<br><small>15 Jul 2026, 14:30</small> | Produksi: Roti Tawar (10 unit) |
| 2   | Minggu, 14 Jul 2026 | BB-002 | Gula Pasir    | —          | 2.000 kg | **👤 Administrator**<br><small>14 Jul 2026, 09:15</small> | Stok keluar manual             |
| 3   | Sabtu, 13 Jul 2026  | BB-003 | Coklat Bubuk  | —          | 500 g    | —                                                         | Data lama (sebelum tracking)   |

## Use Cases

### 1. Audit Trail

Admin dapat melihat history siapa yang mengeluarkan stok:

```
Tanggal: 15 Jul 2026
Bahan: Tepung Terigu
Jumlah: 5 kg
Dibuat Oleh: Karyawan Demo (15 Jul 2026, 14:30)
Keterangan: Produksi: Roti Tawar (10 unit)
```

### 2. Accountability

Setiap karyawan bertanggung jawab atas transaksi yang mereka buat:

- Karyawan tidak bisa claim "bukan saya yang input"
- Admin bisa track siapa yang sering error input
- Memudahkan investigasi jika ada discrepancy

### 3. Performance Tracking

Admin bisa analisis:

- Berapa banyak transaksi per user
- User mana yang paling aktif
- Pola kerja per shift (berdasarkan timestamp)

## Backward Compatibility

### Existing Data

- Data stok keluar yang sudah ada TIDAK akan memiliki `user_id` dan `created_by`
- Kolom akan bernilai `NULL`
- Display akan menampilkan "—" untuk data lama
- Tidak ada error atau breaking changes

### Migration Strategy

1. Jalankan migration (add columns)
2. Data existing tetap valid
3. Transaksi baru otomatis ter-track
4. (Optional) Update data lama dengan script jika diperlukan

## Future Enhancements

### 1. User Activity Log

Buat tabel terpisah untuk log semua aktivitas user:

```sql
CREATE TABLE `user_activity_log` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `action` VARCHAR(50) NOT NULL,  -- 'create', 'update', 'delete'
    `table_name` VARCHAR(50) NOT NULL,
    `record_id` BIGINT UNSIGNED NULL,
    `description` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
```

### 2. Edit Tracking

Track juga user yang melakukan edit/delete:

- Add `updated_by` dan `updated_at` columns
- Add `deleted_by` untuk soft delete

### 3. Report by User

Buat laporan khusus:

- Total transaksi per user
- Total stok keluar per user
- Performance comparison antar karyawan

### 4. Notification

Notify admin jika:

- User mengeluarkan stok dalam jumlah besar
- User melakukan banyak transaksi dalam waktu singkat
- Pattern yang mencurigakan

## Security Considerations

### 1. Session Validation

- System menggunakan `$_SESSION['user']` yang sudah divalidasi oleh AuthMiddleware
- User ID dan nama tidak bisa di-manipulate dari frontend

### 2. Data Integrity

- `user_id` adalah foreign key (optional) yang referensi ke tabel `users`
- Jika user dihapus, `user_id` bisa SET NULL tapi `created_by` tetap ada (nama ter-snapshot)

### 3. Audit Trail

- Data tracking tidak bisa diubah oleh user biasa
- Hanya melalui direct database access (by DBA) jika diperlukan

## Testing Checklist

### As Admin

- [x] Login sebagai admin
- [x] Buat transaksi stok keluar manual
- [x] Verify nama "Administrator" muncul di kolom "Dibuat Oleh"
- [x] Buat transaksi by produk (bulk)
- [x] Verify semua items di bulk memiliki nama admin
- [x] Check timestamp sesuai dengan waktu input

### As Karyawan

- [x] Login sebagai karyawan
- [x] Buat transaksi stok keluar
- [x] Verify nama karyawan muncul di kolom "Dibuat Oleh"
- [x] Buat transaksi by produk
- [x] Verify nama karyawan ter-track di semua items

### Data Validation

- [x] Data lama (sebelum migration) menampilkan "—"
- [x] Data baru semua ter-track dengan benar
- [x] Timestamp format sesuai (DD Mon YYYY, HH:mm)
- [x] Nama user ditampilkan dengan bold + icon

## Troubleshooting

### Issue: User tracking tidak muncul

**Solution**:

1. Pastikan migration sudah dijalankan
2. Check apakah session user tersedia
3. Verify column `user_id` dan `created_by` ada di database

### Issue: Semua data menampilkan "—"

**Solution**:

1. Check apakah column exists di database
2. Pastikan query JOIN dengan users table di Model
3. Check backward compatibility logic di Model

### Issue: Timestamp salah format

**Solution**:

1. Check timezone setting di PHP (`date_default_timezone_set()`)
2. Verify `fmtDateTime()` function ada di helpers.php
3. Check database timestamp column type

## Conclusion

Fitur user tracking memberikan transparency dan accountability untuk setiap transaksi stok keluar. Dengan informasi ini, admin bisa melakukan audit, tracking, dan analisis yang lebih baik untuk operasional gudang.
