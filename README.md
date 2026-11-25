# FlipPlay REST API (Modul 4 - Pemrograman Web)

REST API untuk platform **FlipPlay** menggunakan **Laravel 12**, Eloquent ORM, Relasi Many-to-Many (`belongsToMany`), Form Request Validation, dan API Resource.

## Setup Awal

1. Pastikan MySQL berjalan (melalui Laragon / XAMPP).
2. Salin `.env.example` menjadi `.env` (jika belum ada) dan sesuaikan konfigurasi database (`DB_DATABASE=flipplay_db`).
3. Jalankan perintah berikut:
   ```bash
   composer install
   php artisan key:generate
   php artisan migrate --seed
   php artisan serve
   ```

## Daftar Endpoint API

| Method | Endpoint | Deskripsi |
| --- | --- | --- |
| `GET` | `/api/media` | Menampilkan seluruh media (pagination). Mendukung filter `?title=...` dan `?type=anime\|drama` |
| `GET` | `/api/media/{media}` | Menampilkan detail satu media beserta pengguna yang mengoleksinya |
| `POST` | `/api/media` | Menambahkan media baru (`anime` / `drama`) |
| `PATCH` | `/api/media/{media}` | Mengubah sebagian/seluruh data media |
| `DELETE` | `/api/media/{media}` | Menghapus media |
| `GET` | `/api/users` | Menampilkan seluruh pengguna beserta koleksi medianya |
| `GET` | `/api/users/{user}` | Menampilkan detail pengguna beserta koleksi medianya |
| `POST` | `/api/users` | Menambahkan pengguna baru |
| `PATCH` | `/api/users/{user}` | Memperbarui data pengguna |
| `DELETE` | `/api/users/{user}` | Menghapus pengguna |
| `GET` | `/api/users/{user}/collection` | Menampilkan daftar koleksi media milik pengguna |
| `POST` | `/api/users/{user}/collection` | Menambahkan media ke koleksi pengguna (`{ "media_id": 1 }`) |
| `DELETE` | `/api/users/{user}/collection/{media}` | Menghapus media dari koleksi pengguna |
