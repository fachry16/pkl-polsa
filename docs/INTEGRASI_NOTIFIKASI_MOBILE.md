# Integrasi Notifikasi LMS Eduva ke Aplikasi Mobile Perpus

Dokumen ini menjelaskan arsitektur, spesifikasi endpoint API, skema webhook FCM, dan panduan implementasi di sisi aplikasi mobile (maupun backend perpus).

---

## 1. Arsitektur Alur Notifikasi

Integrasi ini mencakup 2 mekanisme:
1. **Push Notification (FCM Background/Foreground)**: Notifikasi muncul di status bar HP saat dosen menerbitkan materi/tugas/forum di Eduva.
2. **In-App Notification Center**: Halaman riwayat notifikasi di dalam aplikasi mobile untuk melihat daftar notifikasi belum dibaca/sudah dibaca.

```
[Aksi Dosen di LMS Eduva] (Tugas, Materi, Forum, Pengumuman, Nilai)
            │
            ▼
    [Eduva Backend]
       ├──> 1. Simpan ke database tabel `notifications`
       └──> 2. HTTP POST Webhook ke Backend Perpus (Payload: NIM, Judul, Pesan, Metadata)
                   │
                   ▼
            [Backend Perpus]
                   │ (Cari device token FCM mahasiswa berdasarkan NIM)
                   ▼
            [Firebase Cloud Messaging (FCM)]
                   │
                   ▼
       [Aplikasi Mobile Mahasiswa] (Pop-up Push Notification)
```

Saat mahasiswa membuka menu "Notifikasi LMS" di aplikasi mobile:
```
[Aplikasi Mobile Mahasiswa]
       │
       ▼ GET /api/v1/external/notifications?nim={NIM} (Header: X-API-KEY)
[Eduva Backend] ──> Mengembalikan list notifikasi JSON + paginasi
```

---

## 2. Kredensial & Autentikasi

Semua request dari aplikasi mobile atau backend perpus ke API Eduva wajib melampirkan API Key di header:

| Header | Nilai | Keterangan |
|---|---|---|
| `X-API-KEY` | `390ad401477143aa3d3b2905c6dc58c7ff09b6ae187be5eb0246dd5b9dbaa419` | API Key Eduva (disimpan di `.env`) |
| `Accept` | `application/json` | Format respons |

> Alternatif header auth: `Authorization: Bearer 390ad401477143aa3d3b2905c6dc58c7ff09b6ae187be5eb0246dd5b9dbaa419`

---

## 3. Spesifikasi REST API Eduva (In-App Notification)

Base URL: `http://<domain-eduva>/api/v1/external`

### A. Ambil Daftar Notifikasi
- **Method**: `GET`
- **URL**: `/notifications`
- **Query Parameters**:
  - `nim` *(required, string)*: NIM Mahasiswa yang sedang login di mobile.
  - `unread` *(optional, boolean)*: `1` jika hanya ingin mengambil yang belum dibaca.
  - `per_page` *(optional, integer)*: Jumlah data per halaman (default: `20`, max: `100`).
  - `page` *(optional, integer)*: Nomor halaman.

#### Contoh Request (cURL):
```bash
curl -X GET "http://localhost:8000/api/v1/external/notifications?nim=220101001&unread=0&page=1" \
  -H "X-API-KEY: 390ad401477143aa3d3b2905c6dc58c7ff09b6ae187be5eb0246dd5b9dbaa419" \
  -H "Accept: application/json"
```

#### Contoh Respons (200 OK):
```json
{
  "success": true,
  "meta": {
    "nim": "220101001",
    "user_name": "Ahmad Fauzi",
    "unread_count": 2,
    "current_page": 1,
    "last_page": 1,
    "per_page": 20,
    "total": 2
  },
  "data": [
    {
      "id": "e8371234-a12b-45cd-89ef-0123456789ab",
      "type": "tugas",
      "title": "Tugas Baru: Analisis Desain Antarmuka",
      "data": {
        "pengampu_id": 14,
        "mata_kuliah": "Interaksi Manusia dan Komputer",
        "mata_kuliah_kode": "MK012",
        "kelas": "3B",
        "judul": "Tugas Baru: Analisis Desain Antarmuka",
        "deadline": "2026-10-20 23:59:00",
        "url": "/mahasiswa/lms/14?tab=tugas_kelas"
      },
      "is_read": false,
      "read_at": null,
      "created_at": "2026-10-13T18:45:00+07:00"
    },
    {
      "id": "f9482345-b23c-56de-90fa-1234567890bc",
      "type": "materi",
      "title": "Materi Baru: Pengantar Heuristic Evaluation",
      "data": {
        "pengampu_id": 14,
        "mata_kuliah": "Interaksi Manusia dan Komputer",
        "mata_kuliah_kode": "MK012",
        "kelas": "3B",
        "judul": "Materi Baru: Pengantar Heuristic Evaluation",
        "url": "/mahasiswa/lms/14?tab=tugas_kelas"
      },
      "is_read": true,
      "read_at": "2026-10-13T18:50:00+07:00",
      "created_at": "2026-10-13T18:30:00+07:00"
    }
  ]
}
```

---

### B. Ambil Jumlah Notifikasi Belum Dibaca (Unread Badge)
- **Method**: `GET`
- **URL**: `/notifications/unread-count`
- **Query Parameters**:
  - `nim` *(required)*: NIM Mahasiswa

#### Contoh Respons (200 OK):
```json
{
  "success": true,
  "nim": "220101001",
  "unread_count": 2
}
```

---

### C. Tandai Satu Notifikasi Sudah Dibaca
- **Method**: `PATCH`
- **URL**: `/notifications/{id}/read`
- **Query Parameters / Body**:
  - `nim` *(required)*: NIM Mahasiswa

#### Contoh Respons (200 OK):
```json
{
  "success": true,
  "message": "Notifikasi berhasil ditandai sebagai sudah dibaca.",
  "id": "e8371234-a12b-45cd-89ef-0123456789ab"
}
```

---

### D. Tandai Semua Notifikasi Sudah Dibaca
- **Method**: `POST`
- **URL**: `/notifications/mark-all-read`
- **Body (JSON)**:
  ```json
  {
    "nim": "220101001"
  }
  ```

#### Contoh Respons (200 OK):
```json
{
  "success": true,
  "message": "Seluruh notifikasi berhasil ditandai sebagai sudah dibaca."
}
```

---

## 4. Spesifikasi Push Webhook (Eduva &rarr; Backend Perpus)

Jika ingin real-time push notifikasi via FCM, pasang URL endpoint backend perpus di `.env` Eduva:
```env
PERPUS_NOTIFICATION_WEBHOOK_URL=https://perpus.polsa.ac.id/api/webhook/eduva-notification
PERPUS_NOTIFICATION_WEBHOOK_SECRET=perpus_secret_signature_key
```

### Payload Webhook yang Diterima Backend Perpus:
```json
{
  "nim": "220101001",
  "user_name": "Ahmad Fauzi",
  "type": "tugas_baru",
  "title": "Tugas Baru: Analisis Desain Antarmuka",
  "body": "Tugas baru untuk mata kuliah Interaksi Manusia dan Komputer. Deadline: 20/10/2026 23:59",
  "metadata": {
    "pengampu_id": 14,
    "tugas_id": 5,
    "mata_kuliah": "Interaksi Manusia dan Komputer",
    "kelas": "3B",
    "deadline": "2026-10-20T23:59:00+07:00"
  },
  "timestamp": "2026-10-13T18:45:00+07:00"
}
```

### Alur Backend Perpus:
1. Terima payload di route `/api/webhook/eduva-notification`.
2. Ambil `nim`.
3. Query tabel user perpus / token FCM perpus berdasarkan NIM tersebut.
4. Kirim FCM message dengan payload:
   - `notification.title` = `$request->title`
   - `notification.body` = `$request->body`
   - `data` = `$request->metadata`

---

## 5. Panduan Implementasi di Aplikasi Mobile (Flutter / Kotlin / React Native)

### Langkah 1: Simpan NIM Mahasiswa Saat Login
Pastikan user yang login di aplikasi mobile memiliki field `nim` yang tersimpan di state / Local Storage / Secure Storage.

### Langkah 2: Buat Service Notifikasi (Contoh Dart / Flutter)
```dart
class EduvaNotificationService {
  static const String baseUrl = 'https://eduva.polsa.ac.id/api/v1/external';
  static const String apiKey = '390ad401477143aa3d3b2905c6dc58c7ff09b6ae187be5eb0246dd5b9dbaa419';

  static Map<String, String> get _headers => {
    'X-API-KEY': apiKey,
    'Accept': 'application/json',
  };

  // 1. Ambil jumlah unread untuk badge icon lonceng
  static Future<int> getUnreadCount(String nim) async {
    final res = await http.get(
      Uri.parse('$baseUrl/notifications/unread-count?nim=$nim'),
      headers: _headers,
    );
    if (res.statusCode == 200) {
      final json = jsonDecode(res.body);
      return json['unread_count'] ?? 0;
    }
    return 0;
  }

  // 2. Ambil list notifikasi
  static Future<List<dynamic>> getNotifications(String nim, {int page = 1}) async {
    final res = await http.get(
      Uri.parse('$baseUrl/notifications?nim=$nim&page=$page'),
      headers: _headers,
    );
    if (res.statusCode == 200) {
      final json = jsonDecode(res.body);
      return json['data'] ?? [];
    }
    return [];
  }

  // 3. Tandai sudah dibaca saat item diklik
  static Future<void> markAsRead(String notifId, String nim) async {
    await http.patch(
      Uri.parse('$baseUrl/notifications/$notifId/read?nim=$nim'),
      headers: _headers,
    );
  }
}
```

### Langkah 3: Penanganan Navigasi Ketika Notifikasi Diklik
Setiap notifikasi memiliki tipe (`type`):
- `tugas`: Tampilkan detail tugas atau tenggat waktu.
- `materi`: Tampilkan informasi materi mata kuliah baru.
- `forum`: Tampilkan topik diskusi forum.
- `pengumuman`: Tampilkan detail teks pengumuman.
- `nilai`: Tampilkan informasi nilai yang didapatkan.
