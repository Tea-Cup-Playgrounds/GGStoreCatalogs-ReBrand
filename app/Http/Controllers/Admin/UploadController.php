<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Accept a single image file, store it under storage/app/public/uploads/{folder},
     * and return the public URL.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file'   => 'required|image|max:4096', // 4 MB
            'folder' => 'nullable|string|in:products,categories,brands,banners',
        ]);

        $folder = $request->input('folder', 'uploads');
        $path   = $request->file('file')->store("uploads/{$folder}", 'public');

        return response()->json([
            'url' => Storage::disk('public')->url($path),
        ]);
    }
}
