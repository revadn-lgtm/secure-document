-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 18 Nov 2026 pada 16.32
-- Versi server: 10.11.16-MariaDB
-- Versi PHP: 8.3.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `secure_document_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`id`, `username`, `password_hash`, `created_at`) VALUES
(1, 'admin', '$2y$10$5T4ub7vp7g.RQRrHN0tsueWdrTZ/c2giEowe/D9eVu6.qdDTYyjlS', '2026-11-18 12:52:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `ciphertext` longtext NOT NULL,
  `iv` varchar(255) NOT NULL,
  `auth_tag` varchar(255) NOT NULL,
  `encrypted_aes_key` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `documents`
--

INSERT INTO `documents` (`id`, `title`, `category`, `ciphertext`, `iv`, `auth_tag`, `encrypted_aes_key`, `created_at`) VALUES
(2, 'Pengujian keamanan Data', 'Arsip', 'qZ2qpJBjfaASu7FF4+tAfudJnGm6+fAO4VyiiFEGNEy/hwPOu5vQaQnNJmp551eznn4EKLHck0EnFim5iW8XRb5UQ4HHt5UKCplipmJy', 'G4/VRVtodYgi+ZlF', 'fG/2po/dZfqnSRnCPoxSWQ==', 'YKc51R18C6BK+HVmZge3dSEHEB8x4xT/0nJioYwXcGTuo2pP4AZQnHUQjIOnvhBtRuTfSrNQ5HgdTqNnr6Yj1CvYQzKVV6ixG3CVFEiOFxBRCr8JugN896saMmdCn3j9XVJ93PlnEliGgre36c1gRQFHXYRmNeHLxHmePYdnK3UJ3Psy7TC9XoetQ0Q0RPfaEZbsrk+VbrvLxPGBd9V6VJZq7yi13P8dhyILNQM7ku2zttof0vZZ4JAJ/AVNRD7ui8yBTEq2+a9lPhyshexAn1L6cDLMF5GtSNpNEF2IL7ZhnUFuZbpypZT5SXCKhzOrcSZKAmMZy/iswSejA8HZLg==', '2026-11-18 13:16:04'),
(6, 'Tugas', 'Laporan', '61SWlhZlSWO/6yudnmyWa7rhDOU6OvdYkg==', 'WOv5BB8TWqlfNKVv', 'RznG8tQ+0+ob8Yc/NA3gJQ==', 'MCHoVwdJuqjIvgGsMV1O9QThfs5ChwolDNvgB+szjW9g7xpLguuVaxzqkR7u4Xt19dElU9mABNqxJMi65v2cSTOP+m50R08DQA2XEztyPBjwoDxQ7EfreG1+8glFN/a/LkKXBoxeCrF5Zk2MtOhkfXIOZx3hY87aTzo57+H+CeWZuyt86XyUQjC4nwsMRjWJElv/zodJ1vdUGqx+ntyat/mZdr8JzaUFlEghOQsizu6V92PbSFmsBsReUN7BF8GoXCjT1T5Z0bhvpnU2nAszmu5CXu1dsAUWX1a8dz9dZOpQDVT65/D6K+FobdBjcl6UooOMXznJ1xSVHf9bBw0DZQ==', '2026-11-18 15:16:32');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
