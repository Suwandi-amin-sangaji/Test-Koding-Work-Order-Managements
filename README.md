# Sistem Manajemen Work Order

Sistem Manajemen Work Order adalah aplikasi web yang dirancang untuk membantu proses manufaktur dalam mengelola, melacak, dan memperbarui work order. Aplikasi ini dilengkapi dengan Role-Based Access Control (RBAC) untuk membatasi akses berdasarkan peran pengguna (Production Manager dan Operator). Aplikasi ini dibangun dengan menggunakan Laravel sebagai backend dan Vue.js sebagai frontend.


# Screenshoot Aplikasi

Berikut adalah beberapa tampilan dari aplikasi:

### 1. Tampilan Login
![Tampilan Login](https://github.com/Suwandi-amin-sangaji/Test-Koding-Work-Order-Managements/blob/main/screnshoot/1.png?raw=true)

### 2. Tampilan Dashboard Manager
![Tampilan](https://github.com/Suwandi-amin-sangaji/Test-Koding-Work-Order-Managements/blob/main/screnshoot/2.png?raw=true)

![Tampilan](https://github.com/Suwandi-amin-sangaji/Test-Koding-Work-Order-Managements/blob/main/screnshoot/3.png?raw=true)
![Tampilan](https://github.com/Suwandi-amin-sangaji/Test-Koding-Work-Order-Managements/blob/main/screnshoot/4.png?raw=true)


![Tampilan](https://github.com/Suwandi-amin-sangaji/Test-Koding-Work-Order-Managements/blob/main/screnshoot/5.png?raw=true)

![Tampilan](https://github.com/Suwandi-amin-sangaji/Test-Koding-Work-Order-Managements/blob/main/screnshoot/6.png?raw=true)
### 3. Tampilan Dashboard Operator
![Tampilan](https://github.com/Suwandi-amin-sangaji/Test-Koding-Work-Order-Managements/blob/main/screnshoot/7.png?raw=true)
![Tampilan](https://github.com/Suwandi-amin-sangaji/Test-Koding-Work-Order-Managements/blob/main/screnshoot/8.png?raw=true)

![Tampilan](https://github.com/Suwandi-amin-sangaji/Test-Koding-Work-Order-Managements/blob/main/screnshoot/9.png?raw=true)


#Fitur Utama

Role-Based Access Control (RBAC):

Production Manager:

    Membuat, memperbarui, dan melihat daftar work order.

    Menetapkan operator ke work order.

Operator:

    Melihat work order yang ditugaskan.

    Memperbarui status work order (Pending → In Progress → Completed) dengan menyertakan jumlah quantity.

Manajemen Work Order:

    Pembuatan work order dengan detail:

    Nomor Work Order (otomatis, contoh: WO-20240226-001).

    Nama Produk, Jumlah, Tenggat Waktu Produksi, Status, dan Operator yang ditugaskan.

    Filter daftar work order berdasarkan status atau tanggal.

Pelacakan Progres Work Order (Opsional):

    Operator dapat mencatat keterangan tahapan produksi saat status work order In Progress.

    Sistem mencatat waktu yang dihabiskan pada setiap status.

Laporan:

    Rekapitulasi work order:

    Nama barang, total quantity untuk setiap status (Pending, In Progress, Completed, Canceled).

Laporan hasil per operator:

    Nama barang, total quantity status Completed.

Teknologi yang Digunakan

    Backend: Laravel (PHP Framework)

    Frontend: Vue.js (JavaScript Framework)

    Database: MySQL

    Autentikasi: Laravel Santum

Lainnya:

    Axios (HTTP Client)

    Vue Router

    Vuex (State Management)

    Docker (Opsional, untuk deployment)

Persyaratan Sistem

    PHP >= 8.0

    Composer

    Node.js >= 14.x

    NPM atau Yarn

    MySQL >= 5.7


Instalasi
1. Clone Repository

Clone repository proyek dari GitHub:
bash
Copy

git clone https://github.com/Suwandi-amin-sangaji/Test-Koding-Work-Order-Managements.git
cd Test-Koding-Work-Order-Managements

2. Instalasi Backend (Laravel)
a. Install Dependencies

Jalankan perintah berikut untuk menginstal dependensi Laravel:
bash
Copy

composer install

b. Setup Environment

Salin file .env.example menjadi .env:
bash
Copy

cp .env.example .env

Generate application key:
bash
Copy

php artisan key:generate

c. Konfigurasi Database

Buka file .env dan sesuaikan konfigurasi database:
env
Copy

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=username_database
DB_PASSWORD=password_database

d. Migrasi Database

Jalankan migrasi dan seeder untuk mengisi database dengan data awal:
bash
Copy

php artisan migrate --seed

e. Jalankan Server Laravel

Jalankan server Laravel:
bash
Copy

php artisan serve

Server akan berjalan di http://localhost:9000.
3. Instalasi Frontend (Vue.js)
a. Masuk ke Direktori Frontend

Masuk ke direktori frontend:
bash
Copy

cd frontend

b. Install Dependencies

Jalankan perintah berikut untuk menginstal dependensi Vue.js:
bash
Copy

npm install
# atau
yarn install

c. Konfigurasi Environment Frontend

Buat file .env di direktori frontend (jika belum ada) dan sesuaikan dengan konfigurasi backend:
env
Copy

VUE_APP_API_URL=http://localhost:9000

d. Jalankan Server Vue.js

Jalankan server development Vue.js:
bash
Copy

npm run dev
# atau
yarn dev

Server akan berjalan di http://localhost:3000.
4. Integrasi Backend dan Frontend
a. Konfigurasi CORS di Laravel

Pastikan Laravel mengizinkan request dari frontend. Buka file config/cors.php dan sesuaikan:
php
Copy

'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_origins' => ['http://localhost:3000'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],

b. Gunakan Axios untuk Komunikasi API

Di frontend, gunakan Axios untuk mengirim request ke backend Laravel. Contoh:
javascript
Copy

import axios from 'axios';

axios.get('http://localhost:9000/api/work-orders')
  .then(response => {
    console.log(response.data);
  })
  .catch(error => {
    console.error(error);
  }); 
