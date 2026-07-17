# 🔄 Refactoring Steps: Resep → Produk, Barang → Bahan Baku

## ✅ Sudah Selesai:

1. ✅ Migration SQL dibuat (`migration_rename_to_produk.sql`)
2. ✅ Model: `ResepModel.php` → `ProdukModel.php` (updated)

## 📋 Yang Perlu Dilakukan Manual:

### 1. **Run Migration Database**

```bash
# Import di MySQL/phpMyAdmin
mysql -u root -p sistem_pengelolaan_stok_db < migration_rename_to_produk.sql
```

### 2. **Rename/Update Files**

#### Controllers:

- Rename: `controllers/ResepController.php` → `controllers/ProdukController.php`
- Rename class: `ResepController` → `ProdukController`
- Update: `use Models\ResepModel` → `use Models\ProdukModel`
- Update semua method calls: `$this->resepModel` → `$this->produkModel`

#### Pages:

- Rename folder: `pages/resep/` → `pages/produk/`
- Update semua file di dalam folder:
  - Ganti kata "Resep" → "Produk"
  - Ganti kata "resep" → "produk"
  - Ganti variabel `$resep` → `$produk`
  - Ganti variabel `$reseps` → `$produks`

#### Routes (`routes/web.php`):

- Update: `use Controllers\ResepController` → `use Controllers\ProdukController`
- Ganti semua route `/resep` → `/produk`
- Ganti `[ResepController::class, ...]` → `[ProdukController::class, ...]`

### 3. **Update BarangModel → BahanBakuModel**

Rename:

- `models/BarangModel.php` → `models/BahanBakuModel.php`
- Class name: `BarangModel` → `BahanBakuModel`
- Update `$table = 'barang'` → `$table = 'bahan_baku'`
- Update semua query: `barang` → `bahan_baku`, `barang_id` → `bahan_baku_id`

### 4. **Update Controllers yang menggunakan BarangModel**

Files to update:

- `controllers/BarangController.php` → `controllers/BahanBakuController.php`
- `controllers/StokMasukController.php`
- `controllers/StokKeluarController.php`
- `controllers/StokTersediaController.php`
- `controllers/DashboardController.php`

Changes:

- `use Models\BarangModel` → `use Models\BahanBakuModel`
- `$this->barangModel` → `$this->bahanBakuModel`
- `$barang` → `$bahan_baku`
- `$barangs` → `$bahan_bakus`

### 5. **Update Pages**

Folders to update:

- `pages/barang/` → `pages/bahan-baku/`
- Update all references: "Barang" → "Bahan Baku", "barang" → "bahan_baku"

Files to update:

- `pages/stok-masuk/*` - Update "Barang" → "Bahan Baku"
- `pages/stok-keluar/*` - Update "Barang" → "Bahan Baku"
- `pages/stok-tersedia/*` - Update "Barang" → "Bahan Baku"
- `pages/dashboard/*` - Update references

### 6. **Update Sidebar Menu (`shared/components.php`)**

```php
$nav = [
    ['href' => '/dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
    ['href' => '/bahan-baku', 'icon' => 'bi-box-seam', 'label' => 'Bahan Baku'],
    ['href' => '/produk', 'icon' => 'bi-grid-3x3-gap', 'label' => 'Produk'],
    ['href' => '/stok-masuk', 'icon' => 'bi-box-arrow-in-down', 'label' => 'Stok Masuk'],
    ['href' => '/stok-keluar', 'icon' => 'bi-box-arrow-up', 'label' => 'Stok Keluar'],
    ['href' => '/stok-tersedia', 'icon' => 'bi-clipboard-data', 'label' => 'Stok Tersedia'],
    ['href' => '/report', 'icon' => 'bi-file-earmark-bar-graph', 'label' => 'Report'],
];
```

### 7. **Update Routes (`routes/web.php`)**

```php
use Controllers\BahanBakuController;
use Controllers\ProdukController;

// Bahan Baku
$router->get('/bahan-baku', [BahanBakuController::class, 'index'])->middleware(...);
// ... all other bahan baku routes

// Produk
$router->get('/produk', [ProdukController::class, 'index'])->middleware(...);
// ... all other produk routes
```

---

## 🎯 Quick Find & Replace Guide

Use IDE Find & Replace (Ctrl+Shift+H) with these patterns:

### Models:

- `ResepModel` → `ProdukModel`
- `BarangModel` → `BahanBakuModel`

### Variables:

- `$resep` → `$produk`
- `$reseps` → `$produks`
- `$barang` → `$bahan_baku`
- `$barangs` → `$bahan_bakus`

### Table Names (in queries):

- `FROM barang` → `FROM bahan_baku`
- `FROM resep` → `FROM produk`
- `resep_detail` → `produk_detail`

### Column Names:

- `barang_id` → `bahan_baku_id`
- `produk_id` (in resep context) → `bahan_baku_id`
- `nama_resep` → `nama_produk`

### URLs/Routes:

- `/resep` → `/produk`
- `/barang` → `/bahan-baku`

### Display Text:

- "Resep" → "Produk"
- "Barang" → "Bahan Baku"
- "Data Barang" → "Bahan Baku"

---

## ⚠️ Important Notes:

1. **Backup database** before running migration!
2. Test thoroughly after each step
3. Update `.htaccess` if needed for new routes
4. Clear any cache/session after changes

---

**Estimated Time:** 30-45 minutes for complete refactoring

**Priority Order:**

1. Database migration (FIRST!)
2. Models
3. Controllers
4. Routes
5. Pages/Views
6. Test everything

Good luck! 🚀
