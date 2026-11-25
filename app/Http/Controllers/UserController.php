<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCollectionRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\MediaResource;
use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Menampilkan daftar seluruh pengguna beserta koleksi medianya.
     */
    public function index()
    {
        $users = User::with('media')->paginate(10);

        return UserResource::collection($users);
    }

    /**
     * Menampilkan detail pengguna beserta koleksi media miliknya.
     */
    public function show(User $user)
    {
        return new UserResource($user->load('media'));
    }

    /**
     * Menambahkan pengguna baru.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(StoreUserRequest $request, User $user)
    {
        $user->update($request->validated());

        return new UserResource($user->load('media'));
    }

    /**
     * Menghapus pengguna.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'Pengguna berhasil dihapus']);
    }

    /**
     * Menampilkan daftar koleksi media milik pengguna tertentu.
     */
    public function collection(User $user)
    {
        return MediaResource::collection($user->media()->paginate(10));
    }

    /**
     * Menambahkan media ke dalam koleksi pribadi pengguna (custom action).
     */
    public function addToCollection(AddToCollectionRequest $request, User $user)
    {
        $mediaId = $request->validated()['media_id'];

        if ($user->media()->where('media_id', $mediaId)->exists()) {
            return response()->json([
                'message' => 'Media sudah ada di koleksi',
            ], 409);
        }

        $user->media()->attach($mediaId);

        return response()->json([
            'message' => 'Media berhasil ditambahkan ke koleksi',
            'data' => new MediaResource(Media::findOrFail($mediaId)),
        ]);
    }

    /**
     * Menghapus media dari koleksi pribadi pengguna (custom action).
     */
    public function removeFromCollection(User $user, Media $media)
    {
        if (! $user->media()->where('media_id', $media->id)->exists()) {
            return response()->json([
                'message' => 'Media tidak ditemukan di koleksi pengguna ini',
            ], 404);
        }

        $user->media()->detach($media->id);

        return response()->json([
            'message' => 'Media berhasil dihapus dari koleksi',
        ]);
    }
}
