# Dokumentasi Endpoint API Event Management

---

## 1. Autentikasi dan Profil Pengguna

Semua endpoint yang memerlukan autentikasi (`Bearer Token`) harus menyertakan header `Authorization: Bearer <token_jwt_anda>`.

### 1.1 Login Pengguna

*   **Endpoint**: `/auth/login`
*   **Metode**: `POST`
*   **Deskripsi**: Mengautentikasi pengguna dan mengembalikan token JWT. Endpoint ini memiliki **rate limiting** (misal, 5 permintaan per menit per IP) untuk mencegah *brute-force*.
*   **Akses**: Publik
*   **Request Body (JSON)**:
    ```json
    {
        "email": "user@example.com",
        "password": "your_password"
    }
    ```
*   **Contoh Response (Status 200 OK)**:
    ```json
    {
        "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
        "user": {
            "id": 1,
            "name": "Nama Pengguna",
            "email": "user@example.com",
            "role": "organizer" // atau "admin"
        }
    }
    ```

### 1.2 Logout Pengguna

*   **Endpoint**: `/auth/logout`
*   **Metode**: `POST`
*   **Deskripsi**: Membatalkan sesi pengguna (misalnya, membatalkan token JWT di sisi server).
*   **Akses**: Terotentikasi (`admin`, `organizer`)
*   **Headers**:
    *   `Authorization: Bearer <token_jwt_anda>`
*   **Contoh Response (Status 200 OK)**:
    ```json
    {
        "message": "Successfully logged out"
    }
    ```

### 1.3 Mendapatkan Profil Pengguna

*   **Endpoint**: `/auth/me`
*   **Metode**: `GET`
*   **Deskripsi**: Mengambil data profil pengguna yang sedang login.
*   **Akses**: Terotentikasi (`admin`, `organizer`)
*   **Headers**:
    *   `Authorization: Bearer <token_jwt_anda>`
*   **Contoh Response (Status 200 OK)**:
    ```json
    {
        "id": 1,
        "name": "Nama Pengguna",
        "email": "user@example.com",
        "role": "organizer"
    }
    ```

---

## 2. Manajemen Event

### 2.1 Membuat Event Baru

*   **Endpoint**: `/events`
*   **Metode**: `POST`
*   **Deskripsi**: Membuat event baru. **Validasi input** diterapkan.
*   **Akses**: Hanya `organizer` atau `admin`.
*   **Headers**:
    *   `Authorization: Bearer <token_jwt_anda>`
    *   `Content-Type: application/json`
*   **Request Body (JSON)**:
    ```json
    {
        "title": "Nama Event Anda",
        "description": "Deskripsi singkat event.",
        "venue": "Lokasi Event",
        "start_datetime": "2024-12-25T09:00:00Z",
        "end_datetime": "2024-12-25T17:00:00Z",
        "status": "draft" // atau "published"
    }
    ```
*   **Contoh Response (Status 201 Created)**:
    ```json
    {
        "id": 101,
        "title": "Nama Event Anda",
        "description": "Deskripsi singkat event.",
        "venue": "Lokasi Event",
        "start_datetime": "2024-12-25T09:00:00Z",
        "end_datetime": "2024-12-25T17:00:00Z",
        "status": "draft",
        "organizer_id": 1 // ID organizer yang membuat event
    }
    ```

### 2.2 Mengambil Daftar Event

*   **Endpoint**: `/events`
*   **Metode**: `GET`
*   **Deskripsi**: Mengambil daftar event dengan dukungan **pencarian**, **filter**, **sorting**, dan **paginasi**.
*   **Akses**: Publik.
*   **Query Parameters (Opsional)**:
    *   `search=<kata_kunci>`: Mencari event berdasarkan `title`.
    *   `filter[status]=<status>`: Memfilter event berdasarkan `status` (misal, `published`). Hanya event dengan status `published` yang akan ditampilkan untuk akses publik.
    *   `sort_by=start_datetime`: Mengurutkan berdasarkan `start_datetime` (kolom yang didukung untuk sorting).
    *   `sort_order=asc|desc`: Urutan ascending (`asc`) atau descending (`desc`). Defaultnya `asc`.
    *   `page=<nomor_halaman>`: Nomor halaman untuk paginasi (default 1).
    *   `per_page=<jumlah_item>`: Jumlah item per halaman (default 10).
*   **Contoh Permintaan**:
    *   `GET http://127.0.0.1:8000/api/events?search=konferensi&filter[status]=published&sort_by=start_datetime&sort_order=desc&page=1&per_page=5`
*   **Contoh Response (Status 200 OK)**:
    ```json
    {
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
            },
            // ... event lainnya
        ],
        "meta": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 5,
            "total": 23
        }
    }
    ```

### 2.3 Mengambil Detail Event

*   **Endpoint**: `/events/:id`
*   **Metode**: `GET`
*   **Deskripsi**: Mengambil detail satu event berdasarkan ID.
*   **Akses**: Publik.
*   **Path Parameter**:
    *   `id`: ID unik event (contoh: `101`)
*   **Contoh Permintaan**:
    *   `GET http://127.0.0.1:8000/api/events/101`
*   **Contoh Response (Status 200 OK)**:
    ```json
    {
        "id": 101,
        "title": "Nama Event Anda",
        "description": "Deskripsi singkat event.",
        "venue": "Lokasi Event",
        "start_datetime": "2024-12-25T09:00:00Z",
        "end_datetime": "2024-12-25T17:00:00Z",
        "status": "draft",
        "organizer_id": 1
    }
    ```

### 2.4 Memperbarui Event

*   **Endpoint**: `/events/:id`
*   **Metode**: `PUT`
*   **Deskripsi**: Memperbarui detail event berdasarkan ID. **Validasi input** diterapkan.
*   **Akses**: Hanya `owner organizer` atau `admin`. Organizer hanya bisa mengubah event yang dibuatnya sendiri.
*   **Headers**:
    *   `Authorization: Bearer <token_jwt_anda>`
    *   `Content-Type: application/json`
*   **Path Parameter**:
    *   `id`: ID unik event yang akan diperbarui (contoh: `101`)
*   **Request Body (JSON)**:
    *   Bisa berisi sebagian atau seluruh bidang event yang ingin diperbarui.
    ```json
    {
        "title": "Nama Event Baru",
        "status": "published"
    }
    ```
*   **Contoh Response (Status 200 OK)**:
    ```json
    {
        "id": 101,
        "title": "Nama Event Baru",
        "description": "Deskripsi singkat event.",
        "venue": "Lokasi Event",
        "start_datetime": "2024-12-25T09:00:00Z",
        "end_datetime": "2024-12-25T17:00:00Z",
        "status": "published",
        "organizer_id": 1
    }
    ```

### 2.5 Menghapus Event

*   **Endpoint**: `/events/:id`
*   **Metode**: `DELETE`
*   **Deskripsi**: Menghapus event berdasarkan ID.
*   **Akses**: Hanya `owner organizer` atau `admin`. Organizer hanya bisa menghapus event yang dibuatnya sendiri.
*   **Headers**:
    *   `Authorization: Bearer <token_jwt_anda>`
*   **Path Parameter**:
    *   `id`: ID unik event yang akan dihapus (contoh: `101`)
*   **Contoh Response (Status 204 No Content)**:
    *   Tidak ada konten yang dikembalikan, mengindikasikan penghapusan berhasil.

---

## 3. Utilitas

### 3.1 Health Check

*   **Endpoint**: `/health`
*   **Metode**: `GET`
*   **Deskripsi**: Endpoint sederhana untuk memeriksa status kesehatan aplikasi.
*   **Akses**: Publik
*   **Contoh Response (Status 200 OK)**:
    ```json
    {
        "status": "ok",
        "timestamp": "2023-10-27T10:30:00Z"
    }
    ```

---
