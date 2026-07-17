-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 17 Jul 2026 pada 14.22
-- Versi server: 8.0.30
-- Versi PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistem_pengelolaan_stok_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bahan_baku`
--

CREATE TABLE `bahan_baku` (
  `id` bigint UNSIGNED NOT NULL,
  `kode_bahan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nama_bahan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `satuan` varchar(255) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `bahan_baku`
--

INSERT INTO `bahan_baku` (`id`, `kode_bahan`, `nama_bahan`, `satuan`, `keterangan`, `is_active`, `created_at`) VALUES
(18, 'FDFDF', 'Gula Pasir', 'gram', 'dfddf\r\n', 0, '2026-07-15 10:32:51'),
(20, 'AIRGALON', 'air galon', 'ml', '', 1, '2026-07-15 13:22:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id`, `nama_produk`, `deskripsi`, `created_at`, `updated_at`) VALUES
(5, 'Air putih biasa', 'Air putih galon manis chill', '2026-07-15 13:30:28', '2026-07-15 13:38:27');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk_detail`
--

CREATE TABLE `produk_detail` (
  `id` bigint UNSIGNED NOT NULL,
  `produk_id` bigint UNSIGNED NOT NULL,
  `bahan_baku_id` bigint UNSIGNED NOT NULL,
  `jumlah_dibutuhkan` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `produk_detail`
--

INSERT INTO `produk_detail` (`id`, `produk_id`, `bahan_baku_id`, `jumlah_dibutuhkan`) VALUES
(8, 5, 20, 15.00),
(9, 5, 18, 10.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_keluar`
--

CREATE TABLE `stok_keluar` (
  `id` bigint UNSIGNED NOT NULL,
  `bahan_baku_id` bigint UNSIGNED NOT NULL,
  `jumlah` bigint NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `stok_keluar`
--

INSERT INTO `stok_keluar` (`id`, `bahan_baku_id`, `jumlah`, `tanggal`, `keterangan`, `user_id`, `created_by`, `created_at`) VALUES
(21, 20, 300, '2026-07-16', 'Produksi: Air putih biasa (20 unit)', 2, 'Sientod ', '2026-07-16 09:42:14'),
(22, 18, 200, '2026-07-16', 'Produksi: Air putih biasa (20 unit)', 2, 'Sientod ', '2026-07-16 09:42:14'),
(23, 20, 15, '2026-07-16', 'Produksi: Air putih biasa (1 unit)', 2, 'Sientod ', '2026-07-16 09:48:22'),
(24, 18, 10, '2026-07-16', 'Produksi: Air putih biasa (1 unit)', 2, 'Sientod ', '2026-07-16 09:48:22'),
(26, 20, 20, '2026-07-16', 'ngemil', 1, 'Administrator', '2026-07-16 10:05:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_keluar_detail`
--

CREATE TABLE `stok_keluar_detail` (
  `id` bigint UNSIGNED NOT NULL,
  `stok_keluar_id` bigint UNSIGNED NOT NULL,
  `bahan_baku_id` bigint UNSIGNED NOT NULL,
  `jumlah_terpakai` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_masuk`
--

CREATE TABLE `stok_masuk` (
  `id` bigint UNSIGNED NOT NULL,
  `bahan_baku_id` bigint UNSIGNED NOT NULL,
  `jumlah` int NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `stok_masuk`
--

INSERT INTO `stok_masuk` (`id`, `bahan_baku_id`, `jumlah`, `tanggal`, `keterangan`) VALUES
(5, 20, 50, '2026-07-15', ''),
(6, 18, 200, '2026-07-15', ''),
(7, 20, 5000, '2026-07-15', ''),
(8, 20, 200, '2026-07-15', 'Stok masuk untuk produk: Air putih biasa'),
(9, 18, 2000, '2026-07-15', 'Stok masuk untuk produk: Air putih biasa');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `nama_lengkap` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','karyawan') NOT NULL DEFAULT 'karyawan',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `nama_lengkap`, `password`, `role`, `updated_at`) VALUES
(1, 'admin', 'Administrator', '$2y$10$6XpZ9FftgCyTJ2jd8l.QjOM7GH9w5m5Vcn5QfEFjXNlHhn25lBptG', 'admin', '2026-07-16 09:35:04'),
(2, 'karyawan', 'Sientod ', '$2y$10$W.pO4YcT3gKPxF74HzkbPOC7KV55iemMFckY7D2Ino5vyOESP.dO2', 'karyawan', '2026-07-16 09:36:54'),
(3, 'admin 2', 'Siantar', '$2y$10$Cc3RoHuR39VL6GQ8cMjLEeE./kA51jsQJzMv97DTI9cDOplBaVImG', 'admin', '2026-07-16 10:03:55');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bahan_baku`
--
ALTER TABLE `bahan_baku`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barang_kode_barang_unique` (`kode_bahan`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `produk_detail`
--
ALTER TABLE `produk_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `produk_detail_produk_id_foreign` (`produk_id`),
  ADD KEY `produk_detail_bahan_baku_id_foreign` (`bahan_baku_id`);

--
-- Indeks untuk tabel `stok_keluar`
--
ALTER TABLE `stok_keluar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stok_keluar_bahan_baku_id_foreign` (`bahan_baku_id`);

--
-- Indeks untuk tabel `stok_keluar_detail`
--
ALTER TABLE `stok_keluar_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stok_keluar_detail_stok_keluar_id_foreign` (`stok_keluar_id`),
  ADD KEY `stok_keluar_detail_bahan_baku_id_foreign` (`bahan_baku_id`);

--
-- Indeks untuk tabel `stok_masuk`
--
ALTER TABLE `stok_masuk`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stok_masuk_bahan_baku_id_foreign` (`bahan_baku_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bahan_baku`
--
ALTER TABLE `bahan_baku`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `produk_detail`
--
ALTER TABLE `produk_detail`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `stok_keluar`
--
ALTER TABLE `stok_keluar`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT untuk tabel `stok_keluar_detail`
--
ALTER TABLE `stok_keluar_detail`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `stok_masuk`
--
ALTER TABLE `stok_masuk`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `produk_detail`
--
ALTER TABLE `produk_detail`
  ADD CONSTRAINT `produk_detail_bahan_baku_id_foreign` FOREIGN KEY (`bahan_baku_id`) REFERENCES `bahan_baku` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `produk_detail_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `stok_keluar`
--
ALTER TABLE `stok_keluar`
  ADD CONSTRAINT `stok_keluar_bahan_baku_id_foreign` FOREIGN KEY (`bahan_baku_id`) REFERENCES `bahan_baku` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `stok_keluar_detail`
--
ALTER TABLE `stok_keluar_detail`
  ADD CONSTRAINT `stok_keluar_detail_bahan_baku_id_foreign` FOREIGN KEY (`bahan_baku_id`) REFERENCES `bahan_baku` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stok_keluar_detail_stok_keluar_id_foreign` FOREIGN KEY (`stok_keluar_id`) REFERENCES `stok_keluar` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `stok_masuk`
--
ALTER TABLE `stok_masuk`
  ADD CONSTRAINT `stok_masuk_bahan_baku_id_foreign` FOREIGN KEY (`bahan_baku_id`) REFERENCES `bahan_baku` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
