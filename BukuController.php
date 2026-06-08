<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use App\Rules\KodeBukuFormat;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua data buku dari database
        $bukus = Buku::latest()->get();

        // Statistik untuk card
        $totalBuku = Buku::count();
        $bukuTersedia = Buku::where('stok', '>', 0)->count();
        $bukuHabis = Buku::where('stok', 0)->count();

        // Return view dengan data
        return view('buku.index', compact(
            'bukus',
            'totalBuku',
            'bukuTersedia',
            'bukuHabis'
        ));
    }


    public function export()
    {
        $bukus = Buku::all();

        $filename = 'buku_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($bukus) {
            $file = fopen('php://output', 'w');

            // Header CSV
            fputcsv($file, [
                'Kode Buku',
                'Judul',
                'Kategori',
                'Pengarang',
                'Penerbit',
                'Tahun',
                'ISBN',
                'Harga',
                'Stok'
            ]);

            // Data
            foreach ($bukus as $buku) {
                fputcsv($file, [
                    $buku->kode_buku,
                    $buku->judul,
                    $buku->kategori,
                    $buku->pengarang,
                    $buku->penerbit,
                    $buku->tahun_terbit,
                    $buku->isbn,
                    $buku->harga,
                    $buku->stok,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Akan diimplementasi di pertemuan 12
        return view('buku.create');
    }

    public function store(Request $request)
    {
        // ===== VALIDASI =====
        $rules = [
            'kode_buku'    => ['required', 'unique:buku,kode_buku', new KodeBukuFormat],
            'judul'        => 'required|string|max:200',
            'kategori'     => 'required|in:Programming,Database,Web Design,Networking,Data Science',
            'pengarang'    => 'required|string|max:100',
            'penerbit'     => 'required|string|max:100',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
            'isbn'         => 'nullable|string|max:20',
            'harga'        => 'required|numeric|min:0',
            'stok'         => 'required|integer|min:0',
            'deskripsi'    => 'nullable|string',
            'bahasa'       => 'required|string|max:20',
        ];

        // ===== CONDITIONAL VALIDATION =====
        // Jika kategori Programming, bahasa harus Inggris
        if ($request->kategori == 'Programming') {
            $rules['bahasa'] = 'required|in:Inggris';
        }

        // Jika tahun terbit < 2000, stok maksimal 5
        if ($request->tahun_terbit < 2000) {
            $rules['stok'] = 'required|integer|min:0|max:5';
        }

        // ===== CUSTOM ERROR MESSAGES =====
        $messages = [
            'kode_buku.required'    => 'Kode buku wajib diisi.',
            'kode_buku.unique'      => 'Kode buku sudah digunakan.',
            'judul.required'        => 'Judul buku wajib diisi.',
            'judul.max'             => 'Judul buku maksimal 200 karakter.',
            'kategori.required'     => 'Kategori wajib dipilih.',
            'kategori.in'           => 'Kategori yang dipilih tidak valid.',
            'pengarang.required'    => 'Nama pengarang wajib diisi.',
            'pengarang.max'         => 'Nama pengarang maksimal 100 karakter.',
            'penerbit.required'     => 'Nama penerbit wajib diisi.',
            'penerbit.max'          => 'Nama penerbit maksimal 100 karakter.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.integer'  => 'Tahun terbit harus berupa angka.',
            'tahun_terbit.min'      => 'Tahun terbit minimal 1900.',
            'tahun_terbit.max'      => 'Tahun terbit tidak boleh melebihi tahun ini.',
            'harga.required'        => 'Harga buku wajib diisi.',
            'harga.numeric'         => 'Harga harus berupa angka.',
            'harga.min'             => 'Harga tidak boleh negatif.',
            'stok.required'         => 'Stok buku wajib diisi.',
            'stok.integer'          => 'Stok harus berupa angka bulat.',
            'stok.min'              => 'Stok tidak boleh negatif.',
            'stok.max'              => 'Stok maksimal 5 untuk buku terbitan sebelum tahun 2000.',
            'bahasa.required'       => 'Bahasa wajib diisi.',
            'bahasa.in'             => 'Buku kategori Programming harus berbahasa Inggris.',
        ];

        $request->validate($rules, $messages);

        // ===== SIMPAN DATA =====
        Buku::create($request->all());

        return redirect()->route('buku.index')
            ->with('success', 'Buku berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $buku = Buku::findOrFail($id);

        return view('buku.show', compact('buku'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $buku = Buku::findOrFail($id);

        return view('buku.edit', compact('buku'));
    }



    public function update(Request $request, $id)
    {
        $buku = Buku::findOrFail($id);

        // ===== VALIDASI =====
        $rules = [
            'kode_buku'    => ['required', 'unique:buku,kode_buku,' . $id, new KodeBukuFormat],
            'judul'        => 'required|string|max:200',
            'kategori'     => 'required|in:Programming,Database,Web Design,Networking,Data Science',
            'pengarang'    => 'required|string|max:100',
            'penerbit'     => 'required|string|max:100',
            'tahun_terbit' => 'required|integer|min:1900|max:' . date('Y'),
            'isbn'         => 'nullable|string|max:20',
            'harga'        => 'required|numeric|min:0',
            'stok'         => 'required|integer|min:0',
            'deskripsi'    => 'nullable|string',
            'bahasa'       => 'required|string|max:20',
        ];

        // ===== CONDITIONAL VALIDATION =====
        if ($request->kategori == 'Programming') {
            $rules['bahasa'] = 'required|in:Inggris';
        }

        if ($request->tahun_terbit < 2000) {
            $rules['stok'] = 'required|integer|min:0|max:5';
        }

        // ===== CUSTOM ERROR MESSAGES =====
        $messages = [
            'kode_buku.required'    => 'Kode buku wajib diisi.',
            'kode_buku.unique'      => 'Kode buku sudah digunakan.',
            'judul.required'        => 'Judul buku wajib diisi.',
            'judul.max'             => 'Judul buku maksimal 200 karakter.',
            'kategori.required'     => 'Kategori wajib dipilih.',
            'kategori.in'           => 'Kategori yang dipilih tidak valid.',
            'pengarang.required'    => 'Nama pengarang wajib diisi.',
            'tahun_terbit.required' => 'Tahun terbit wajib diisi.',
            'tahun_terbit.min'      => 'Tahun terbit minimal 1900.',
            'tahun_terbit.max'      => 'Tahun terbit tidak boleh melebihi tahun ini.',
            'harga.required'        => 'Harga buku wajib diisi.',
            'harga.numeric'         => 'Harga harus berupa angka.',
            'stok.required'         => 'Stok buku wajib diisi.',
            'stok.max'              => 'Stok maksimal 5 untuk buku terbitan sebelum tahun 2000.',
            'bahasa.in'             => 'Buku kategori Programming harus berbahasa Inggris.',
        ];

        $request->validate($rules, $messages);

        // ===== UPDATE DATA =====
        $buku->update($request->all());

        return redirect()->route('buku.index')
            ->with('success', 'Buku berhasil diperbarui!');
    }

    public function bulkDelete(Request $request)
    {
        // Validasi ada data yang dipilih
        $request->validate([
            'buku_ids'   => 'required|array',
            'buku_ids.*' => 'exists:buku,id',
        ], [
            'buku_ids.required' => 'Pilih minimal satu buku untuk dihapus.',
            'buku_ids.array'    => 'Data tidak valid.',
        ]);

        $ids = $request->buku_ids;
        Buku::whereIn('id', $ids)->delete();

        return redirect()->route('buku.index')
            ->with('success', count($ids) . ' buku berhasil dihapus!');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $buku = Buku::findOrFail($id);
            $judulBuku = $buku->judul;

            // Delete buku
            $buku->delete();

            // Redirect dengan success message
            return redirect()->route('buku.index')
                ->with('success', "Buku '{$judulBuku}' berhasil dihapus!");
        } catch (\Exception $e) {
            // Redirect dengan error message jika gagal
            return redirect()->back()
                ->with('error', 'Gagal menghapus buku: ' . $e->getMessage());
        }
    }

    /**
     * Filter buku berdasarkan kategori.
     */
    public function filterKategori($kategori)
    {
        $bukus = Buku::where('kategori', $kategori)->latest()->get();

        $totalBuku = $bukus->count();
        $bukuTersedia = $bukus->where('stok', '>', 0)->count();
        $bukuHabis = $bukus->where('stok', 0)->count();

        return view('buku.index', compact(
            'bukus',
            'totalBuku',
            'bukuTersedia',
            'bukuHabis',
            'kategori'
        ));
    }


    public function search(Request $request)
    {
        $query = Buku::query();

        // Filter keyword (judul, pengarang, penerbit)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', '%' . $keyword . '%')
                    ->orWhere('pengarang', 'like', '%' . $keyword . '%')
                    ->orWhere('penerbit', 'like', '%' . $keyword . '%');
            });
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->where('tahun_terbit', $request->tahun);
        }

        // Filter ketersediaan
        if ($request->filled('ketersediaan')) {
            if ($request->ketersediaan == 'tersedia') {
                $query->where('stok', '>', 0);
            } elseif ($request->ketersediaan == 'habis') {
                $query->where('stok', 0);
            }
        }

        $bukus = $query->latest()->get();

        // Ambil data untuk dropdown
        $kategoris = ['Programming', 'Database', 'Web Design', 'Networking', 'Data Science'];
        $tahuns    = Buku::selectRaw('DISTINCT tahun_terbit')
            ->orderBy('tahun_terbit', 'desc')
            ->pluck('tahun_terbit');

        return view('buku.search', compact('bukus', 'kategoris', 'tahuns'));
    }
}
