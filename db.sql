
DROP DATABASE IF EXISTS db_umkm_perigi;
CREATE DATABASE db_umkm_perigi;
USE db_umkm_perigi;


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100),
    username VARCHAR(50) UNIQUE,
    password VARCHAR(50),
    role ENUM('admin','owner','kasir'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50)
);

CREATE TABLE barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    kode_barang VARCHAR(20) UNIQUE,
    nama_barang VARCHAR(100),
    id_kategori INT,
    harga_beli DECIMAL(12,0),
    harga_jual DECIMAL(12,0),
    stok INT,
    supplier VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
);


CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    total_harga DECIMAL(12,0),
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20),
    FOREIGN KEY (id_user) REFERENCES users(id)
);


CREATE TABLE detail_transaksi (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT,
    id_barang INT,
    qty INT,
    harga_saat_ini DECIMAL(12,0),
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
    FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
);

INSERT INTO users (nama, username, password, role) VALUES 
('Administrator', 'admin', 'admin123', 'admin'),
('Pemilik Toko', 'owner', 'owner123', 'owner'),
('Kasir Toko', 'kasir', 'kasir123', 'kasir');


INSERT INTO kategori (nama_kategori) VALUES 
('Makanan'), ('Minuman'), ('Snack'), ('ATK'), ('Perlengkapan');


INSERT INTO barang (kode_barang, nama_barang, id_kategori, harga_beli, harga_jual, stok, supplier) VALUES 
('BRG001', 'Mie Goreng', 1, 1500, 2500, 50, 'Supplier A'),
('BRG002', 'Kopi Sachet', 2, 1000, 2000, 100, 'Supplier B'),
('BRG003', 'Kerupuk', 3, 2000, 3500, 30, 'Supplier A'),
('BRG004', 'Pensil', 4, 500, 1000, 25, 'Supplier C'),
('BRG005', 'Sabun Mandi', 5, 3000, 5000, 40, 'Supplier D');