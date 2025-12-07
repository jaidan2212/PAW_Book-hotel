Ainur Raftuzzaki – 240411100034 - Username = AinurRaftuzzaki

Muhammad Izzul Millah Aqil – 240411100087 - Username = Izzulgtg 

Verdi Setyawan Ardiansyah Putra – 240411100100 - Username = GREED2308

Muhammad Zaidan Nabil Rafi – 240411100068 Username = jaidan2212

Deo Candra Saputra – 240411100137 - Username = Deocandra


🏨 Hotel Booking System — Project Overview

📌 Deskripsi Proyek

Hotel Booking System adalah aplikasi berbasis web yang digunakan untuk mengelola pemesanan hotel. Sistem ini mencakup fitur login admin, manajemen data booking, serta tampilan user untuk melakukan pemesanan kamar. Proyek dibuat menggunakan PHP Native, MySQL, dan Bootstrap.

Aplikasi ini dibuat sebagai bagian dari Tugas Akhir Praktikum PAW.

🚀 Fitur Utama

Hotel Booking System

│

├── 🔐 Autentikasi

│   ├── Login

│   │   ├── Admin

│   │   └── User

│   └── Logout

│

├── 🛎️ Admin Dashboard

│   ├── Manajemen Booking

│   │   ├── Lihat daftar pemesanan

│   │   ├── Edit data pemesanan

│   │   └── Hapus data pemesanan

│   └── Terhubung ke Database MySQL

│

├── 👤 User Dashboard

│   ├── Form Pemesanan Kamar

│   ├── Lihat Status Booking

│   └── Redirect ke Dashboard User

│

├── 🗄️ Database (db_hotel_booking.sql)

│   ├── Tabel Users

│   ├── Tabel Admin

│   ├── Tabel Bookings

│   └── Relasi antar tabel

│

└── 🧰 Teknologi

├── PHP Native (Backend)
    
├── MySQL (Database)
    
├── HTML, CSS, Bootstrap (UI/UX)
    
├── JavaScript (Interaksi)
    
├── Git (Version Control)
    
└── Apache / Laragon (Local Server)

📂 Struktur Folder

Tugas_Akhir_PAW/

├── admin/                -> Halaman untuk admin

├── user/                 -> Halaman untuk user

├── assets/               -> CSS, JS, dan gambar

├── config.php            -> Konfigurasi koneksi database

├── db.php                -> File penghubung database

├── db_hotel_booking.sql  -> Struktur database

├── index.php             -> Landing page

├── login.php             -> Halaman login

├── process_login.php     -> Proses login

└── logout.php            -> Proses logout

⚙️ Cara Menjalankan Proyek

1️⃣ Clone Repository
git clone https://github.com/username/repo.git
cd repo

2️⃣ Import Database

Buka phpMyAdmin

Buat database baru, contoh: hotel_booking

Import file:
db_hotel_booking.sql

3️⃣ Konfigurasi Koneksi Database

Edit file config.php:

$koneksi = mysqli_connect("localhost", "root", "", "hotel_booking");

4️⃣ Buka di Browser
http://localhost/Tugas_Akhir_PAW


📝 Changelog
v1.0 — Initial Release

Login User & Admin

CRUD Booking

Dashboard Admin

Landing Page

📤 Deployment 

Link Hosting : https://solazresort.animenesia.site/

Cara deploy:

Upload semua file ke public_html

Import SQL ke database hosting

Sesuaikan config.php

Selesai

🌿 Branching Guide 

main → branch stabil

dev → pengembangan

feature/* → fitur baru

fix/* → perbaikan bug

Format commit:

feat: tambah fitur booking
fix: perbaiki bug login
style: rapikan UI

📝 Screenshots

<img width="1888" height="1097" alt="image" src="https://github.com/user-attachments/assets/228a40e8-0184-4397-ac4c-78a8970afee7" />
<img width="1886" height="1090" alt="image" src="https://github.com/user-attachments/assets/8588a62d-c9e5-4f9f-9f4c-645b04e6c082" />



👨‍💻 Kontributor

Ainur Raftuzzaki – admin(management rooms)

Muhammad Izzul Millah Aqil – user(hompage, login& register,payment user)

Verdi Setyawan Ardiansyah Putra –

Muhammad Zaidan Nabil Rafi – user(Rooms,booking,payment user)

Deo Candra Saputra – admin(dashboard,management payment)

Project dibuat untuk memenuhi tugas praktikum PAW.

📄 Lisensi

Proyek ini bersifat open-source dan bebas digunakan untuk keperluan pembelajaran.

