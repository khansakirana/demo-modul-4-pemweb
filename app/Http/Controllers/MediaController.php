<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMediaRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use Illuminate\Http\Request;

// CRUD API lengkap (index, show, store, update, destroy) dengan status code tepat
class MediaController extends Controller
{
    /**
     * Menampilkan seluruh media dengan dukungan filter judul dan jenis media.
     */
    // Eager loading: with('users')
    public function index(Request $request)
    {
        $media = Media::with('users')
            ->searchTitle($request->query('title'))
            ->ofType($request->query('type'))
            ->paginate(10);

        return MediaResource::collection($media);
    }

    /**
     * Menampilkan detail satu media beserta pengguna yang mengoleksinya.
     */
    // Route Model Binding (404 otomatis) + load('users')
    public function show(Media $media)
    {
        return new MediaResource($media->load('users'));
    }

    /**
     * Menambahkan media baru ke dalam sistem.
     */
    // Status code 201 Created
    public function store(StoreMediaRequest $request)
    {
        $data = $request->validated();
        if (($data['type'] ?? null) === 'anime') {
            $data['country'] = null;
        }

        $media = Media::create($data);

        return (new MediaResource($media))->response()->setStatusCode(201);
    }

    /**
     * Memperbarui data media berdasarkan ID.
     */
    // Update data media (PATCH)
    public function update(StoreMediaRequest $request, Media $media)
    {
        $data = $request->validated();
        if (($data['type'] ?? $media->type) === 'anime') {
            $data['country'] = null;
        }

        $media->update($data);

        return new MediaResource($media->load('users'));
    }

    /**
     * Menghapus media dari sistem.
     */
    // Hapus media (DELETE)
    public function destroy(Media $media)
    {
        $media->delete();

        return response()->json(['message' => 'Media berhasil dihapus']);
    }
}
