<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBukuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode_buku' => 'required|string|max:20|unique:buku,kode_buku',
            'judul' => 'required|string|max:200',
            'kategori' => 'required|in:Programming,Database,Web Design,Networking,Data Science',
            'pengarang' => 'required|string|max:100',
            'penerbit' => 'required|string|max:100',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
            'isbn' => 'nullable|string|max:20',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string',
            'bahasa' => 'required|string|max:20',
        ];
    }

    /**
     * Get custom error messages.
     */
    public function messages(): array
    {
        return [
            'kode_buku.required' => 'Kode buku wajib diisi.',
            'kode_buku.unique' => 'Kode buku sudah digunakan.',
            'kode_buku.max' => 'Kode buku maksimal 20 karakter.',
            'judul.required' => 'Judul buku wajib diisi.',
            'judul.max' => 'Judul buku maksimal 200 karakter.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'kategori.in' => 'Kategori tidak valid.',
            'pengarang.required' => 'Nama pengarang wajib diisi.',
            'penerbit.required' => 'Nama penerbit wajib diisi.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.integer' => 'Tahun terbit harus berupa angka.',
            'tahun_terbit.min' => 'Tahun terbit tidak valid.',
            'tahun_terbit.max' => 'Tahun terbit tidak boleh melebihi tahun sekarang.',
            'isbn.max' => 'ISBN maksimal 20 karakter.',
            'harga.required' => 'Harga buku wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga tidak boleh negatif.',
            'stok.required' => 'Stok wajib diisi.',
            'stok.integer' => 'Stok harus berupa angka bulat.',
            'stok.min' => 'Stok tidak boleh negatif.',
            'bahasa.required' => 'Bahasa wajib diisi.',
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'kode_buku' => 'kode buku',
            'judul' => 'judul buku',
            'kategori' => 'kategori',
            'pengarang' => 'nama pengarang',
            'penerbit' => 'nama penerbit',
            'tahun_terbit' => 'tahun terbit',
            'isbn' => 'ISBN',
            'harga' => 'harga',
            'stok' => 'stok',
            'bahasa' => 'bahasa',
        ];
    }
}
