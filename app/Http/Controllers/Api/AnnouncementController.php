<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $announcements = Announcement::where('tenant_id', Tenant::current()->id)
            ->where('published_at', '<=', now())
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->paginate(25);

        return response()->json($announcements);
    }

    public function show(Announcement $announcement): JsonResponse
    {
        abort_unless($announcement->tenant_id === Tenant::current()->id, 404);

        return response()->json([
            'data' => $announcement->load('author'),
        ]);
    }
}
