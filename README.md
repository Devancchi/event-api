---

# Event Management API

API backend untuk manajemen event, termasuk autentikasi, CRUD event, filter, pencarian, dan paginasi.

**Base URL:** `http://127.0.0.1:8000/api`

---

## 1. Autentikasi dan Profil Pengguna

Semua endpoint yang membutuhkan autentikasi harus menyertakan header:

```
Authorization: Bearer <token_jwt_anda>
```

### 1.1 Register (Daftar Pengguna Baru)

* **Endpoint:** `/auth/register`
* **Method:** `POST`
* **Akses:** Publik
* **Request Body (JSON):**

```json
{
  "name": "Nama Pengguna",
  "email": "user@example.com",
  "password": "password123",
  "role": "organizer" 
}
```

* **Contoh Request `curl`:**

```bash
curl -X POST http://127.0.0.1:8000/api/auth/register \
-H "Content-Type: application/json" \
-d '{"name":"Nama Pengguna","email":"user@example.com","password":"password123","role":"organizer"}'
```

* **Response (201 Created)**:

```json
{
  "id": 1,
  "name": "Nama Pengguna",
  "email": "user@example.com",
  "role": "organizer"
}
```

---

### 1.2 Login

* **Endpoint:** `/auth/login`
* **Method:** `POST`
* **Akses:** Publik
* **Rate Limit:** 5 permintaan/menit per IP
* **Request Body (JSON):**

```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

* **Contoh Request `curl`:**

```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
-H "Content-Type: application/json" \
-d '{"email":"user@example.com","password":"password123"}'
```

* **Response (200 OK)**:

```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "name": "Nama Pengguna",
    "email": "user@example.com",
    "role": "organizer"
  }
}
```

---

### 1.3 Logout

* **Endpoint:** `/auth/logout`

* **Method:** `POST`

* **Akses:** Terotentikasi

* **Headers:** `Authorization: Bearer <token_jwt_anda>`

* **Contoh Request `curl`:**

```bash
curl -X POST http://127.0.0.1:8000/api/auth/logout \
-H "Authorization: Bearer <token_jwt_anda>"
```

* **Response (200 OK)**:

```json
{
  "message": "Successfully logged out"
}
```

---

### 1.4 Profil Pengguna

* **Endpoint:** `/auth/me`

* **Method:** `GET`

* **Akses:** Terotentikasi

* **Headers:** `Authorization: Bearer <token_jwt_anda>`

* **Contoh Request `curl`:**

```bash
curl -X GET http://127.0.0.1:8000/api/auth/me \
-H "Authorization: Bearer <token_jwt_anda>"
```

* **Response (200 OK)**:

```json
{
  "id": 1,
  "name": "Nama Pengguna",
  "email": "user@example.com",
  "role": "organizer"
}
```

---

## 2. Event Management

### 2.1 Membuat Event Baru

* **Endpoint:** `/events`
* **Method:** `POST`
* **Akses:** `admin` / `organizer`
* **Headers:**

```
Authorization: Bearer <token_jwt_anda>
Content-Type: application/json
```

* **Request Body (JSON)**:

```json
{
  "title": "Nama Event",
  "description": "Deskripsi singkat event",
  "venue": "Lokasi Event",
  "start_datetime": "2025-05-01T09:00:00Z",
  "end_datetime": "2025-08-31T18:00:00Z",
  "status": "draft"
}
```

* **Contoh Request `curl`:**

```bash
curl -X POST http://127.0.0.1:8000/api/events \
-H "Content-Type: application/json" \
-H "Authorization: Bearer <token_jwt_anda>" \
-d '{"title":"Nama Event","description":"Deskripsi","venue":"Lokasi","start_datetime":"2025-05-01T09:00:00Z","end_datetime":"2025-08-31T18:00:00Z","status":"draft"}'
```

* **Response (201 Created)**:

```json
{
  "id": 101,
  "title": "Nama Event",
  "description": "Deskripsi singkat event",
  "venue": "Lokasi Event",
  "start_datetime": "2025-05-01T09:00:00Z",
  "end_datetime": "2025-08-31T18:00:00Z",
  "status": "draft",
  "organizer_id": 1
}
```

---

### 2.2 Daftar Event (Public)

* **Endpoint:** `/events`

* **Method:** `GET`

* **Akses:** Publik

* **Query Parameters**:

```
search=<kata_kunci>             # cari di title
filter[status]=published        # filter status
sort_by=start_datetime          # kolom untuk sorting
sort_order=asc|desc             # urutan sorting
page=1                          # nomor halaman
per_page=5                       # jumlah item per halaman
```

* **Contoh Request `curl`:**

```bash
curl -X GET "http://127.0.0.1:8000/api/events?search=konferensi&filter[status]=published&sort_by=start_datetime&sort_order=desc&page=1&per_page=5"
```

* **Response (200 OK)**:

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 102,
      "title": "Konferensi Blockchain 2024",
      "description": "Pembahasan teknologi blockchain terkini.",
      "venue": "Convention Center",
      "start_datetime": "2024-11-15T09:00:00Z",
      "end_datetime": "2024-11-15T18:00:00Z",
      "status": "published",
      "organizer_id": 2
    }
  ],
  "per_page": 5,
  "total": 23
}
```

---

### 2.3 Detail Event (Public)

* **Endpoint:** `/events/:id`

* **Method:** `GET`

* **Akses:** Publik

* **Contoh Request `curl`:**

```bash
curl -X GET http://127.0.0.1:8000/api/events/101
```

* **Response (200 OK)**:

```json
{
  "id": 101,
  "title": "Nama Event",
  "description": "Deskripsi singkat event",
  "venue": "Lokasi Event",
  "start_datetime": "2025-05-01T09:00:00Z",
  "end_datetime": "2025-08-31T18:00:00Z",
  "status": "draft",
  "organizer_id": 1
}
```

---

### 2.4 Memperbarui Event

* **Endpoint:** `/events/:id`

* **Method:** `PUT`

* **Akses:** Hanya `owner organizer` / `admin`

* **Contoh Request `curl`:**

```bash
curl -X PUT http://127.0.0.1:8000/api/events/101 \
-H "Content-Type: application/json" \
-H "Authorization: Bearer <token_jwt_anda>" \
-d '{"title":"Updated Event","status":"published"}'
```

* **Response (200 OK)**: Sama format seperti detail event.

---

### 2.5 Menghapus Event

* **Endpoint:** `/events/:id`

* **Method:** `DELETE`

* **Akses:** Hanya `owner organizer` / `admin`

* **Contoh Request `curl`:**

```bash
curl -X DELETE http://127.0.0.1:8000/api/events/101 \
-H "Authorization: Bearer <token_jwt_anda>"
```

* **Response (204 No Content)**: Tidak ada konten, penghapusan berhasil.

---

## 3. Utilitas

### 3.1 Health Check

* **Endpoint:** `/health`

* **Method:** `GET`

* **Akses:** Publik

* **Contoh Request `curl`:**

```bash
curl -X GET http://127.0.0.1:8000/api/health
```

* **Response (200 OK)**:

```json
{
  "status": "ok"
}
```

---

### Catatan

* **RBAC**: `organizer` hanya bisa mengelola event miliknya sendiri, `admin` bisa semua.
* **Validasi Input**: Semua input harus divalidasi di server.
* \*\*Seed Data


\*\*: Minimal 1 admin, 2 organizer, 1 event agar API bisa langsung diuji.

---

