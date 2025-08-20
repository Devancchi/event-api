
````markdown
# REST API Event Management

REST API untuk manajemen event berbasis Laravel 10 dengan autentikasi JWT. Fitur utama termasuk registrasi/login, RBAC, CRUD Event, filter, search, sorting, dan pagination.

---

## 🔹 Stack Teknologi

- PHP 8+
- Laravel 12
- MySQL
- JWT Authentication (`tymon/jwt-auth`)
- Postman / cURL untuk testing

---

## 📥 Clone Project

```bash
git clone <repository_url>
cd <nama_folder_project>
````

---

## ⚙️ Setup Project

1. **Install dependencies**

```bash
composer install
```

2. **Copy environment file & konfigurasi database**

```bash
cp .env.example .env
```

* Sesuaikan database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=event_db
DB_USERNAME=root
DB_PASSWORD=secret
```

3. **Generate application key**

```bash
php artisan key:generate
```

4. **Generate JWT secret**

```bash
php artisan jwt:secret
```

5. **Migrate database & seed data**

```bash
php artisan db:seed
```

* Seed akan membuat minimal 1 admin, 2 organizer, dan 1 event contoh.

6. **Jalankan server**

```bash
php artisan serve
```

Server akan berjalan di: `http://127.0.0.1:8000`

---

## 🛠 Struktur Endpoint

Base URL: `http://127.0.0.1:8000/api`

### 1. Autentikasi

| Endpoint       | Method | Auth | Deskripsi                  |
| -------------- | ------ | ---- | -------------------------- |
| /auth/register | POST   | ❌    | Registrasi user baru       |
| /auth/login    | POST   | ❌    | Login & dapatkan token JWT |
| /auth/me       | GET    | ✅    | Profil user yang login     |
| /auth/logout   | POST   | ✅    | Logout & invalid token     |

**Contoh Request Login**

```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
-H "Content-Type: application/json" \
-d '{"email":"admin@example.com","password":"password"}'
```

**Response**

```json
{
    "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "token_type": "bearer",
    "expires_in": 3600
}
```

---

### 2. Event CRUD

| Endpoint     | Method | Auth                | Deskripsi                                  |
| ------------ | ------ | ------------------- | ------------------------------------------ |
| /events      | GET    | ❌                   | List event + search/filter/sort/pagination |
| /events/{id} | GET    | ❌                   | Detail event                               |
| /events      | POST   | ✅ (admin/organizer) | Create event                               |
| /events/{id} | PUT    | ✅ (owner/admin)     | Update event                               |
| /events/{id} | DELETE | ✅ (owner/admin)     | Delete event                               |

**Contoh Request GET Event List**

```bash
curl "http://127.0.0.1:8000/api/events?search=konferensi&filter[status]=published&sort_by=start_datetime&sort_order=desc&page=1&per_page=5"
```

**Contoh Request Create Event**

```bash
curl -X POST http://127.0.0.1:8000/api/events \
-H "Authorization: Bearer <token>" \
-H "Content-Type: application/json" \
-d '{
    "title": "Workshop Laravel",
    "description": "Belajar CRUD & JWT",
    "venue": "Online",
    "start_datetime": "2025-09-01 09:00:00",
    "end_datetime": "2025-09-01 17:00:00",
    "status": "draft"
}'
```

---

### 3. Health Check

| Endpoint | Method | Auth | Deskripsi      |
| -------- | ------ | ---- | -------------- |
| /health  | GET    | ❌    | Cek status API |

```bash
curl http://127.0.0.1:8000/api/health
```

Response:

```json
{
    "status": "ok",
    "message": "API is healthy"
}
```

---

## 🔒 Role & Permission

* **Admin**: full access ke semua event
* **Organizer**: hanya bisa create/update/delete event miliknya sendiri
* **Publik**: hanya bisa lihat daftar dan detail event yang `published`

---

## ⚡ Tips Testing

* Gunakan **Postman** untuk mempermudah testing query parameters (`search`, `filter[status]`, `sort_by`, `sort_order`, `page`, `per_page`)
* Sertakan **header Authorization** untuk endpoint yang membutuhkan token JWT
* Cek validasi input (misal tanggal mulai > tanggal selesai akan error)

---

## 📌 Catatan

* Pastikan `php artisan migrate --seed` dijalankan agar ada user dan event contoh
* Login user admin/organizer dari seed untuk mencoba fitur protected routes
* Semua endpoint menggunakan **JSON** request & response
* Rate limiting login: maksimal 5 request per menit
