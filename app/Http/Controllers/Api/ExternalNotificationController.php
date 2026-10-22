<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExternalNotificationController extends Controller
{
    /**
     * Cari user berdasarkan NIM.
     */
    protected function resolveUserByNim(string $nim)
    {
        $mahasiswa = Mahasiswa::where('nim', $nim)->with('user')->first();

        if (! $mahasiswa || ! $mahasiswa->user) {
            return null;
        }

        return $mahasiswa->user;
    }

    /**
     * Ambil daftar notifikasi untuk mahasiswa berdasarkan NIM.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'nim' => ['required', 'string'],
            'unread' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $user = $this->resolveUserByNim($request->nim);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan.',
            ], 404);
        }

        $query = $user->notifications();

        if ($request->boolean('unread')) {
            $query->whereNull('read_at');
        }

        $perPage = (int) $request->input('per_page', 20);
        $paginator = $query->latest()->paginate($perPage);

        $items = collect($paginator->items())->map(function ($notif) {
            $data = is_array($notif->data) ? $notif->data : (json_decode($notif->data, true) ?? []);

            // Format label tipe notifikasi yang bersahabat
            $rawType = class_basename($notif->type);
            $typeMap = [
                'TugasBaru' => 'tugas',
                'MateriBaru' => 'materi',
                'ForumDiskusiBaru' => 'forum',
                'PengumumanBaru' => 'pengumuman',
                'NilaiDiberikan' => 'nilai',
            ];

            return [
                'id' => $notif->id,
                'type' => $typeMap[$rawType] ?? strtolower($rawType),
                'title' => $data['judul'] ?? 'Notifikasi LMS',
                'data' => $data,
                'is_read' => $notif->read_at !== null,
                'read_at' => $notif->read_at?->toIso8601String(),
                'created_at' => $notif->created_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'meta' => [
                'nim' => $request->nim,
                'user_name' => $user->name,
                'unread_count' => $user->unreadNotifications()->count(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'data' => $items,
        ]);
    }

    /**
     * Hitung jumlah notifikasi belum dibaca berdasarkan NIM.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $request->validate([
            'nim' => ['required', 'string'],
        ]);

        $user = $this->resolveUserByNim($request->nim);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'nim' => $request->nim,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'nim' => ['required', 'string'],
        ]);

        $user = $this->resolveUserByNim($request->nim);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan.',
            ], 404);
        }

        $notification = $user->notifications()->where('id', $id)->first();

        if (! $notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan.',
            ], 404);
        }

        if (! $notification->read_at) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil ditandai sebagai sudah dibaca.',
            'id' => $id,
        ]);
    }

    /**
     * Tandai seluruh notifikasi mahasiswa sebagai sudah dibaca.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->validate([
            'nim' => ['required', 'string'],
        ]);

        $user = $this->resolveUserByNim($request->nim);

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Mahasiswa dengan NIM tersebut tidak ditemukan.',
            ], 404);
        }

        $user->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Seluruh notifikasi berhasil ditandai sebagai sudah dibaca.',
        ]);
    }
}
