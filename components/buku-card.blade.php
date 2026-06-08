<div class="card shadow-sm h-100">
    <div class="card-body">

        {{-- Cover / Icon --}}
        <div class="text-center mb-3">
            <span style="font-size: 48px;"></span>
        </div>

        {{-- Badge Kategori --}}
        <div class="mb-2">
            <span class="badge bg-secondary">{{ $buku->kategori }}</span>
        </div>

        {{-- Judul --}}
        <h6 class="card-title fw-bold">{{ $buku->judul }}</h6>

        {{-- Pengarang --}}
        <p class="text-muted mb-1">
            <small>{{ $buku->pengarang }}</small>
        </p>

        {{-- Penerbit --}}
        <p class="text-muted mb-1">
            <small>{{ $buku->penerbit }}</small>
        </p>

        {{-- Harga --}}
        <p class="fw-bold text-success mb-1">
            {{ $buku->harga_format }}
        </p>

        {{-- Stok --}}
        <p class="mb-2">
            Stok: <strong>{{ $buku->stok }}</strong>
        </p>

        {{-- Status Ketersediaan --}}
        <div class="mb-2">
            {!! $buku->status_stok_badge !!}
        </div>

    </div>

    {{-- Button Actions --}}
    @if($showActions)
        <div class="btn-group-vertical d-grid gap-2">
            <a href="{{ route('buku.show', $buku->id) }}" class="btn btn-sm btn-info text-white">
                <i class="bi bi-eye"></i> Detail
            </a>
            <a href="{{ route('buku.edit', $buku->id) }}" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>

            {{-- Delete Button dengan SweetAlert --}}
            <form action="{{ route('buku.destroy', $buku->id) }}" method="POST" class="d-inline delete-form">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-sm btn-danger w-100 btn-delete" data-judul="{{ $buku->judul }}">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </form>

            @push('scripts')
                <script>
                    // SweetAlert confirmation untuk delete
                    document.querySelectorAll('.btn-delete').forEach(button => {
                        button.addEventListener('click', function (e) {
                            e.preventDefault();
                            const form = this.closest('form');
                            const judul = this.getAttribute('data-judul');

                            Swal.fire({
                                title: 'Konfirmasi Hapus',
                                text: `Apakah Anda yakin ingin menghapus buku "${judul}"?`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Ya, Hapus!',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    form.submit();
                                }
                            });
                        });
                    });
                </script>
            @endpush
        </div>
    @endif

    
</div>