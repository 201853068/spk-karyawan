<div>
    <h4 class="text-center">Analisa</h4>
    <div id="analisa"></div>
    <h4 class="text-center">Normalisasi</h4>
    <div id="normalisasi"></div>
    <h4 class="text-center">Perangkingan</h4>
    <div id="rangking"></div>
    <h4 class="text-center">Penentuan Karyawan</h4>
    <div id="pemilihan"></div>
</div>

@section('head')
@vite('resources/js/laporan-penilaian.js')
@stop

@push('javascript')
<script>
    document.addEventListener('livewire:load', function () {
        window.grid({
            id: 'analisa',
            columns: @js($this->columns),
            data: @js($this->analisa),
        });
        window.grid({
            id: 'normalisasi',
            columns: @js($this->columns),
            data: @js($this->normalisasi),
        });
        window.grid({
            id: 'rangking',
            columns: [...@js($this->columns), 'Rangking'],
            data: @js($this->rangking).sort((a, b) => b[9] - a[9]),
        });
        window.grid({
            id: 'pemilihan',
            columns: [...@js($this->columns).slice(0, 3), 'Rangking', 'Status'],
            data: @js($this->pemilihan).sort((a, b) => b[3] - a[3]),
            @if($this->is_owner)
            action: {
                name: 'Aksi',
                builder: (row) => {
                    const id = row[0];
                    const terpilih = row[4] === 'Terpilih';
                    const className = terpilih ? 'btn btn-danger' : 'btn btn-primary';
                    const label = terpilih ? 'Batalkan' : 'Pilih';
                    return {
                        type: 'button',
                        className,
                        label,
                        onClick: () => Livewire.emit('karyawan_terpilih', id),
                    };
                },
            }
            @endif
        });
    });
</script>
@endpush
