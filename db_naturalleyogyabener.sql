
CREATE DATABASE IF NOT EXISTS db_naturalle
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE db_naturalle;

-- USERS (pengguna)
CREATE TABLE IF NOT EXISTS users (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(100)        NOT NULL,
  email        VARCHAR(150)        NOT NULL UNIQUE,
  password     VARCHAR(255)        NOT NULL,   -- bcrypt hash
  phone        VARCHAR(20),
  role         ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  is_verified  TINYINT(1)          NOT NULL DEFAULT 0,
  created_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ALAMAT USER
CREATE TABLE IF NOT EXISTS user_addresses (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  user_id      INT           NOT NULL,
  label        VARCHAR(50)   NOT NULL DEFAULT 'Rumah',   -- e.g. Rumah, Kantor
  recipient    VARCHAR(100)  NOT NULL,
  phone        VARCHAR(20)   NOT NULL,
  address      TEXT          NOT NULL,
  city         VARCHAR(100)  NOT NULL,
  province     VARCHAR(100)  NOT NULL,
  postal_code  VARCHAR(10)   NOT NULL,
  is_default   TINYINT(1)    NOT NULL DEFAULT 0,
  created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- CATEGORIUES (kategori produk)
CREATE TABLE IF NOT EXISTS categories (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name_id      VARCHAR(100) NOT NULL,   -- Bahasa Indonesia
  name_en      VARCHAR(100) NOT NULL,   -- English
  slug         VARCHAR(120) NOT NULL UNIQUE,
  created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- PRODUKS (produk)
CREATE TABLE IF NOT EXISTS products (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  category_id    INT             NOT NULL,
  name_id        VARCHAR(200)    NOT NULL,
  name_en        VARCHAR(200)    NOT NULL,
  slug           VARCHAR(220)    NOT NULL UNIQUE,
  desc_id        TEXT,
  desc_en        TEXT,
  price          DECIMAL(12,2)   NOT NULL,
  stock          INT             NOT NULL DEFAULT 0,
  weight_gram    INT             NOT NULL DEFAULT 0,
  image          VARCHAR(255),
  is_active      TINYINT(1)      NOT NULL DEFAULT 1,
  created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
);

-- PRODUCT IMAGES (gambar produk tambahan)
CREATE TABLE IF NOT EXISTS product_images (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT          NOT NULL,
  image      VARCHAR(255) NOT NULL,
  sort_order INT          NOT NULL DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
-- KERANJANG (cart)
CREATE TABLE IF NOT EXISTS cart (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  product_id INT NOT NULL,
  quantity   INT NOT NULL DEFAULT 1,
  added_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_cart_item (user_id, product_id),
  FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- PESANAN (orders)
CREATE TABLE IF NOT EXISTS orders (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  user_id          INT             NOT NULL,
  order_number     VARCHAR(30)     NOT NULL UNIQUE,  -- e.g. NAT-20260505-0001
  status           ENUM(
                     'pending',
                     'paid',
                     'processing',
                     'shipped',
                     'delivered',
                     'cancelled',
                     'refunded'
                   ) NOT NULL DEFAULT 'pending',
  subtotal         DECIMAL(12,2)   NOT NULL,
  shipping_cost    DECIMAL(12,2)   NOT NULL DEFAULT 0,
  discount         DECIMAL(12,2)   NOT NULL DEFAULT 0,
  total            DECIMAL(12,2)   NOT NULL,
  -- Shipping snapshot
  recipient        VARCHAR(100)    NOT NULL,
  phone            VARCHAR(20)     NOT NULL,
  address          TEXT            NOT NULL,
  city             VARCHAR(100)    NOT NULL,
  province         VARCHAR(100)    NOT NULL,
  postal_code      VARCHAR(10)     NOT NULL,
  shipping_method  VARCHAR(80),
  tracking_number  VARCHAR(100),
  notes            TEXT,
  paid_at          DATETIME,
  shipped_at       DATETIME,
  delivered_at     DATETIME,
  created_at       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- ORDER ITEMS (detail produk dalam pesanan)
CREATE TABLE IF NOT EXISTS order_items (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  order_id     INT             NOT NULL,
  product_id   INT,                              -- NULL jika produk dihapus
  product_name VARCHAR(200)    NOT NULL,         -- snapshot nama
  product_img  VARCHAR(255),
  price        DECIMAL(12,2)   NOT NULL,         -- harga saat beli
  quantity     INT             NOT NULL,
  subtotal     DECIMAL(12,2)   NOT NULL,
  FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- PAYMENTS (pembayaran)
CREATE TABLE IF NOT EXISTS payments (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  order_id       INT             NOT NULL UNIQUE,
  method         VARCHAR(50)     NOT NULL,   -- transfer, COD, ewallet, dll
  amount         DECIMAL(12,2)   NOT NULL,
  status         ENUM('pending','verified','failed') NOT NULL DEFAULT 'pending',
  proof_image    VARCHAR(255),               -- bukti transfer
  verified_at    DATETIME,
  created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- ULASAN (reviews)
CREATE TABLE IF NOT EXISTS reviews (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  product_id  INT         NOT NULL,
  user_id     INT         NOT NULL,
  order_id    INT,
  rating      TINYINT     NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment     TEXT,
  is_approved TINYINT(1)  NOT NULL DEFAULT 0,
  created_at  DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_review (product_id, user_id, order_id),
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
  FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE SET NULL
);

-- FAVORITES (wishlist)
CREATE TABLE IF NOT EXISTS wishlists (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  product_id INT NOT NULL,
  added_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_wishlist (user_id, product_id),
  FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
-- DISCOUNT CODES (kode diskon)
CREATE TABLE IF NOT EXISTS discount_codes (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  code            VARCHAR(50)     NOT NULL UNIQUE,
  type            ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
  value           DECIMAL(10,2)   NOT NULL,    -- 10 = 10% atau Rp10.000
  min_order       DECIMAL(12,2)   NOT NULL DEFAULT 0,
  max_uses        INT,                         -- NULL = unlimited
  used_count      INT             NOT NULL DEFAULT 0,
  expires_at      DATETIME,
  is_active       TINYINT(1)      NOT NULL DEFAULT 1,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- DATA SEEDING (contoh data awal)

-- Kategori
INSERT INTO categories (name_id, name_en, slug) VALUES
  ('Perawatan Kulit', 'Skincare',   'skincare'),
  ('Perawatan Rambut', 'Haircare',  'haircare'),
  ('Perawatan Tubuh',  'Body Care', 'bodycare');

-- Produk contoh
INSERT INTO products (category_id, name_id, name_en, slug, desc_id, desc_en, price, stock, weight_gram, image) VALUES
  (1, 'Serum Melati Soothing', 'Jasmine Soothing Serum',
   'serum-melati-soothing',
   'Serum ringan berbasis melati untuk menenangkan kulit sensitif dan mengurangi kemerahan.',
   'A lightweight jasmine-based serum to calm sensitive skin and reduce redness.',
   185000, 50, 30, 'img/skincare.png'),

  (1, 'Krim Kunyit Cerah', 'Turmeric Brightening Cream',
   'krim-kunyit-cerah',
   'Krim malam dengan kunyit dan lidah buaya untuk mencerahkan dan meratakan warna kulit.',
   'Night cream with turmeric and aloe vera to brighten and even out skin tone.',
   210000, 40, 50, 'img/skincare.png'),

  (2, 'Masker Rambut Kelor', 'Moringa Hair Mask',
   'masker-rambut-kelor',
   'Masker rambut kaya protein dari kelor untuk menguatkan dan menutrisi helai rambut.',
   'Protein-rich hair mask from moringa to strengthen and nourish each strand.',
   155000, 60, 150, 'img/haircare.png'),

  (3, 'Sabun Batang Lidah Buaya', 'Aloe Vera Bar Soap',
   'sabun-batang-lidah-buaya',
   'Sabun artisanal dengan lidah buaya segar, lembut untuk kulit sensitif.',
   'Artisan bar soap with fresh aloe vera, gentle for sensitive skin.',
   65000, 100, 100, 'img/bodycare.png'),

  (3, 'Scrub Tubuh Rempah Jogja', 'Jogja Spice Body Scrub',
   'scrub-tubuh-rempah-jogja',
   'Scrub tubuh dari campuran rempah lokal Yogyakarta untuk kulit halus dan segar.',
   'Body scrub from a blend of local Yogyakarta spices for smooth and fresh skin.',
   120000, 45, 200, 'img/bodycare.png');

-- Admin default (password: admin123 — ganti sebelum deploy!)
INSERT INTO users (name, email, password, role, is_verified) VALUES
  ('Admin NATURALLE', 'admin@naturalle.id',
   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
   'admin', 1);

-- Kode diskon contoh
INSERT INTO discount_codes (code, type, value, min_order, max_uses, expires_at) VALUES
  ('WELCOME10', 'percent', 10, 100000, 100, '2026-12-31 23:59:59'),
  ('NATURALLE20', 'percent', 20, 200000, 50, '2026-12-31 23:59:59');

-- FIN
SELECT 'db_naturalle berhasil dibuat!' AS status;