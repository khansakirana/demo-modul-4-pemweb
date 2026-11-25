<?php

namespace App\Http\Requests;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Form Request Validation & penanganan error 422
class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('type') && is_string($this->input('type'))) {
            $this->merge([
                'type' => strtolower($this->input('type')),
            ]);
        }
    }

    public function rules(): array
    {
        $isPatch = $this->isMethod('PATCH');
        $media = $this->route('media');
        $mediaId = $media instanceof Media ? $media->id : $media;
        $effectiveType = $this->input('type') ?? ($media instanceof Media ? $media->type : null);

        $countryRules = $isPatch
            ? ['sometimes', 'nullable', 'string', 'max:100']
            : ['required_if:type,drama', 'nullable', 'string', 'max:100'];

        if ($isPatch && $effectiveType === 'drama' && ($this->has('country') || empty($media?->country))) {
            $countryRules = ['required', 'string', 'max:100'];
        }

        return [
            'title' => [
                $isPatch ? 'sometimes' : 'required',
                'string',
                'max:255',
                Rule::unique('media', 'title')->ignore($mediaId),
            ],
            'type' => [
                $isPatch ? 'sometimes' : 'required',
                'string',
                Rule::in(['anime', 'drama']),
            ],
            'genre' => [
                $isPatch ? 'sometimes' : 'required',
                'string',
                'max:100',
            ],
            'year' => [
                $isPatch ? 'sometimes' : 'required',
                'integer',
                'min:1900',
                'max:2100',
            ],
            'episodes' => [
                $isPatch ? 'sometimes' : 'required',
                'integer',
                'min:1',
            ],
            'country' => $countryRules,
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi',
            'title.unique' => 'Judul media sudah terdaftar',
            'type.required' => 'Jenis media wajib diisi',
            'type.in' => 'Jenis media harus anime atau drama',
            'genre.required' => 'Genre media wajib diisi',
            'year.required' => 'Tahun rilis wajib diisi',
            'year.integer' => 'Tahun rilis harus berupa angka',
            'year.min' => 'Tahun rilis minimal 1900',
            'year.max' => 'Tahun rilis maksimal 2100',
            'episodes.required' => 'Jumlah episode wajib diisi',
            'episodes.integer' => 'Jumlah episode harus berupa angka',
            'episodes.min' => 'Jumlah episode minimal 1',
            'country.required_if' => 'Negara asal wajib diisi untuk media jenis drama',
            'country.required' => 'Negara asal wajib diisi untuk media jenis drama',
        ];
    }
}
