<div wire:init="init">
    <h4 class="text-center">Analisa</h4>
    <div id="analisa"></div>
    <div id="perhitungan">
        <h4 class="text-center">Normalisasi</h4>
        <div id="normalisasi"></div>
        <h4 class="text-center">Perangkingan</h4>
        <div id="rangking"></div>
        <h4 class="text-center">Penentuan Karyawan</h4>
        <div id="pemilihan"></div>
    </div>
    <div id="incomplete-alert" class="alert alert-danger hide">
        <ul>
            <li>Untuk melihat hasil laporan, mohon lengkapi semua penilaian terlebih dahulu!</li>
        </ul>
    </div>
</div>

@section('head')
@vite('resources/js/laporan-penilaian.js')
@stop

@push('javascript')
<script>
    document.addEventListener('livewire:load', function () {
        window.analisa = window.grid({
            id: 'analisa',
            columns: @js($this->columns),
            styles: { 0: 'color: red; font-weight: bold' },
        });
        window.addEventListener('analisa-updated', event => {
            window.analisa.updateConfig({data: event.detail}).forceRender();
        })
        window.normalisasi = window.grid({
            id: 'normalisasi',
            columns: @js($this->columns),
        });
        window.addEventListener('normalisasi-updated', event => {
            window.normalisasi.updateConfig({data: event.detail}).forceRender();
        })
        window.rangking = window.grid({
            id: 'rangking',
            columns: [...@js($this->columns), 'Rangking'],
        });
        window.addEventListener('rangking-updated', event => {
            window.rangking.updateConfig({data: event.detail.sort((a, b) => b[9] - a[9])}).forceRender();
        })
        window.pemilihan = window.grid({
            id: 'pemilihan',
            columns: [...@js($this->columns).slice(0, 3), 'Rangking', 'Status'],
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
        window.addEventListener('pemilihan-updated', event => {
            window.pemilihan.updateConfig({data: event.detail.sort((a, b) => b[3] - a[3])}).forceRender();
        })
        window.addEventListener('incomplete-alert', event => {
            if(event.detail == 'true') {
                $('#perhitungan').addClass('hide');
                $('#incomplete-alert').addClass('show');
            } else {
                $('#perhitungan').removeClass('hide');
                $('#incomplete-alert').removeClass('show');
            }
        })

    });
</script>
@endpush
