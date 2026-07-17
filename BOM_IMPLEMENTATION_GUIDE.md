# 📦 BOM (Bill of Materials) Implementation Guide

## 🎯 Overview

Sistem BOM/Recipe Management memungkinkan tracking otomatis penggunaan bahan baku ketika produk jadi keluar dari stok.

## 📊 Database Schema

### Tabel Baru:

1. **`resep`** - Master resep/formula produk
   - `id` - Primary key
   - `produk_id` - FK ke barang (tipe: produk_jadi)
   - `nama_resep` - Nama resep
   - `deskripsi` - Deskripsi resep

2. **`resep_detail`** - Detail bahan dalam resep
   - `id` - Primary key
   - `resep_id` - FK ke resep
   - `bahan_baku_id` - FK ke barang (tipe: bahan_baku)
   - `jumlah_dibutuhkan` - Jumlah bahan per unit produk

3. **`stok_keluar_detail`** - Track penggunaan bahan baku
   - `id` - Primary key
   - `stok_keluar_id` - FK ke stok_keluar
   - `bahan_baku_id` - FK ke barang
   - `jumlah_terpakai` - Jumlah bahan terpakai

### Modifikasi Tabel:

**`barang`** - Tambah kolom `tipe_barang`:

- `bahan_baku` - Bahan mentah/ingredient
- `produk_jadi` - Produk final/finished goods

## 🚀 Installation

### Step 1: Run Migration

```bash
# Import migration SQL ke database
mysql -u root -p sistem_pengelolaan_stok_db < migration_bom.sql
```

### Step 2: Verify Tables

Cek apakah tabel-tabel sudah terbuat:

- `resep`
- `resep_detail`
- `stok_keluar_detail`
- `barang` (kolom `tipe_barang` sudah ada)

## 📝 Usage Flow

### 1. Setup Bahan Baku

Tambah bahan baku di menu **Data Barang**:

- Tipe: Bahan Baku
- Contoh: Biji Kopi, Susu, Gula

### 2. Setup Produk Jadi

Tambah produk jadi di menu **Data Barang**:

- Tipe: Produk Jadi
- Contoh: Kopi Latte, Cappuccino

### 3. Buat Resep

Menu **Resep/Formula** → Tambah Resep:

1. Pilih produk jadi
2. Beri nama resep
3. Tambahkan ingredients dengan jumlahnya

**Contoh Resep "Kopi Latte":**

- Biji Kopi: 15 gram
- Susu: 200 ml
- Gula: 10 gram

### 4. Stok Keluar dengan Auto-Deduction

Ketika **Stok Keluar** produk jadi (misal: Kopi Latte 5 cup):

**Auto Process:**

- ✅ Stok Kopi Latte keluar: -5 cup
- ✅ Stok Biji Kopi keluar: -75 gram (15g × 5)
- ✅ Stok Susu keluar: -1000 ml (200ml × 5)
- ✅ Stok Gula keluar: -50 gram (10g × 5)

**Record di `stok_keluar_detail`:**

- Track semua bahan yang terpakai
- Untuk audit dan reporting

## 🔧 Features

### ✅ Implemented:

1. **Manajemen Resep**
   - CRUD resep/formula
   - Add/delete ingredients
   - View resep detail

2. **Tipe Barang**
   - Bahan Baku vs Produk Jadi
   - Filter by type

3. **Route & Controller**
   - ResepController (lengkap)
   - ResepModel (lengkap)

### 🚧 Next Steps (To Do):

1. **Update StokKeluarController**
   - Deteksi produk jadi
   - Auto-deduct ingredients
   - Validasi stok bahan cukup

2. **Update Stok Keluar Form**
   - Show ingredient requirements
   - Warning jika stok bahan tidak cukup

3. **Dashboard Enhancement**
   - Widget stok bahan baku rendah
   - Usage report per bahan

4. **Report Enhancement**
   - Report penggunaan bahan
   - COGS calculation

## 📁 File Structure

```
models/
  └── ResepModel.php          ✅ Created

controllers/
  └── ResepController.php     ✅ Created

pages/resep/
  ├── index.php              ✅ Created
  ├── form.php               ✅ Created
  ├── edit.php               ✅ Created
  └── view.php               ✅ Created

routes/
  └── web.php                ✅ Updated

migration_bom.sql            ✅ Created
```

## 🎨 UI Components

**Sidebar Menu:**

- ✅ Menu "Resep/Formula" sudah ditambahkan

**Icon:**

- `bi-journal-text` untuk menu Resep

## 🔐 Permissions

Semua endpoint resep sudah dilindungi dengan `AuthMiddleware`.

## 🐛 Debugging

### Check if migration success:

```sql
DESCRIBE barang;  -- Should have 'tipe_barang' column
SHOW TABLES;      -- Should include resep, resep_detail, stok_keluar_detail
```

### Sample Data:

Migration sudah include sample data:

- 4 bahan baku baru
- 3 produk jadi
- 3 resep lengkap

## 📞 Support

Untuk pertanyaan atau issue, hubungi tim development.

---

**Created:** 2026-07-15  
**Version:** 1.0.0
