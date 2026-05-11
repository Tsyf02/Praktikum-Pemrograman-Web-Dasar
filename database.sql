-- =============================================
-- Database: makan  |  Restaurant Reservation
-- =============================================
CREATE DATABASE IF NOT EXISTS makan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE makan;

CREATE TABLE IF NOT EXISTS users (
    id       INT(11) AUTO_INCREMENT PRIMARY KEY,
    email    VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role     ENUM('admin','user') DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS menu (
    id_menu   INT(11) AUTO_INCREMENT PRIMARY KEY,
    nama      VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga     INT(11) NOT NULL,
    kategori  ENUM('Makanan','Minuman','Dessert') NOT NULL,
    gambar    VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reservasi (
    id          INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id     INT(11) NOT NULL,
    nama        VARCHAR(100) NOT NULL,
    tanggal     DATE NOT NULL,
    jam         TIME NOT NULL,
    jumlah_orang INT(11) NOT NULL DEFAULT 1,
    catatan     TEXT,
    status      ENUM('pending','konfirmasi','batal') DEFAULT 'pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS reservasi_menu (
    id           INT(11) AUTO_INCREMENT PRIMARY KEY,
    reservasi_id INT(11) NOT NULL,
    menu_id      INT(11) NOT NULL,
    jumlah       INT(11) DEFAULT 1,
    FOREIGN KEY (reservasi_id) REFERENCES reservasi(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_id)      REFERENCES menu(id_menu) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── Sample data ──────────────────────────────
INSERT INTO menu (nama, deskripsi, harga, kategori) VALUES
('Nasi Goreng Spesial','Nasi goreng dengan telur, ayam, dan bumbu rahasia turun-temurun',35000,'Makanan'),
('Soto Ayam Lamongan','Soto kuah bening khas Lamongan dengan suwiran ayam kampung',28000,'Makanan'),
('Ayam Geprek Crispy','Ayam goreng crispy dengan sambal geprek level 1-5',32000,'Makanan'),
('Gado-Gado Betawi','Sayuran segar dengan bumbu kacang homemade yang kaya rasa',22000,'Makanan'),
('Es Teh Manis','Teh manis segar dengan es batu pilihan berkualitas',8000,'Minuman'),
('Jus Alpukat Susu','Alpukat premium diblender dengan susu kental manis',18000,'Minuman'),
('Es Dawet Ayu','Minuman tradisional dengan cendol, santan, dan gula merah',12000,'Minuman'),
('Puding Cokelat Lava','Puding cokelat hangat dengan molten di dalam, disajikan dingin',25000,'Dessert'),
('Es Krim Ubi Ungu','Es krim premium rasa ubi ungu dengan topping keju',20000,'Dessert');

-- Admin: talithasyifaalfath02@gmail.com / password: admin123
INSERT INTO users (email, password, role) VALUES
('talithasyifaalfath02@gmail.com','$2y$10$TKh8H1.PfYi1FBf/T4MBOuXfY3JD0yQeFp4sxEWj9d5y5VHv9u2Va','admin');
