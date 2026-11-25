<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// [PENANDA VALIDASI CUSTOM ACTION - 422] Cek media_id wajib ada di tabel media
class AddToCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'media_id' => 'required|integer|exists:media,id',
        ];
    }

    public function messages(): array
    {
        return [
            'media_id.required' => 'ID media wajib diisi',
            'media_id.integer' => 'ID media harus berupa angka',
            'media_id.exists' => 'Media tidak ditemukan di sistem',
        ];
    }
}
