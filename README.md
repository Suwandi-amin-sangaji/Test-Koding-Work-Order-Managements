# Sistem Manajemen Work Order

Sistem Manajemen Work Order adalah aplikasi web yang dirancang untuk membantu proses manufaktur dalam mengelola, melacak, dan memperbarui work order. Aplikasi ini dilengkapi dengan Role-Based Access Control (RBAC) untuk membatasi akses berdasarkan peran pengguna (Production Manager dan Operator). Aplikasi ini dibangun dengan menggunakan Laravel sebagai backend dan Vue.js sebagai frontend.

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
