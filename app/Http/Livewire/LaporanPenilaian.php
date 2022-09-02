<?php

namespace App\Http\Livewire;

use App\Models\Karyawan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LaporanPenilaian extends Component
{
    public $is_owner;
    public $semua_kriteria;
    public $semua_karyawan;
    public $semua_periode;
    public $periode;
    public $columns;
    public bool $incomplete = false;

    protected $listeners = ['karyawan_terpilih' => 'penetapan_karyawan', 'periode_terpilih' => 'ubah_periode'];

    public function mount($semua_kriteria, $semua_karyawan, $semua_periode)
    {
        $this->is_owner = Auth::user()->role_id == 4;
        $this->semua_kriteria = $semua_kriteria;
        $this->semua_karyawan = $semua_karyawan;
        $this->semua_periode = $semua_periode;
        $this->periode = $this->semua_periode->first();

        $this->columns = $semua_kriteria
            ->pluck('nama')
            ->prepend('Jabatan')
            ->prepend('Nama')
            ->prepend('ID');
    }

    public function init()
    {
        $rows = collect([]);
        $semua_karyawan = $this->semua_karyawan->filter(function ($item) {
            return $item->periode == Carbon::parse($this->periode);
        });
        foreach ($semua_karyawan as $karyawan) {
            $row = [
                'id' => $karyawan->id,
                'nama' => $karyawan->nama,
                'jabatan' => $karyawan->jabatan->nama,
            ];
            foreach ($this->semua_kriteria as $kriteria) {
                $nilai = 0;
                switch ($kriteria->input) {
                    case 'USIA':
                        if ($karyawan->usia <= 20) $nilai = 1;
                        elseif ($karyawan->usia <= 25) $nilai = 2;
                        else $nilai = 3;
                        break;
                    case 'PENDIDIKAN':
                        $nilai_pendidikan = ['SMP' => 1, 'SMA' => 2, 'SARJANA' => 3];
                        $nilai = $nilai_pendidikan[$karyawan->pendidikan];
                        break;
                    case 'TUGAS':
                        $nilai = (int)$karyawan->penilaian()->whereHas('tugas', function (Builder $query) use ($kriteria) {
                            $query->where('kriteria_id', $kriteria->id);
                        })->sum('nilai');
                        break;
                    default:
                        break;
                }
                $row['k' . $kriteria->id] = $nilai;
                if ($nilai == 0) {
                    $this->incomplete = true;
                }
            }
            $rows->push($row);
        }
        $this->dispatchBrowserEvent('analisa-updated', $rows->map('array_values'));
        $this->dispatchBrowserEvent('incomplete-alert', $this->incomplete ? 'true' : 'false');
        if ($this->incomplete) return;

        if ($rows->isEmpty()) return;
        foreach ($this->semua_kriteria as $kriteria) {
            $semua_nilai = $rows->pluck('k' . $kriteria->id)->toArray();
            $max = max($semua_nilai);
            $min = min($semua_nilai);

            $rows = $rows->map(function ($item) use ($kriteria, $max, $min) {
                if ($item['k' . $kriteria->id] > 0) {
                    if ($kriteria->tipe == 'BENEFIT' && $max > 0) {
                        $item['k' . $kriteria->id] = round($item['k' . $kriteria->id] / $max, 2);
                    }
                    if ($kriteria->tipe == 'COST' && $min > 0) {
                        $item['k' . $kriteria->id] = round($min / $item['k' . $kriteria->id], 2);
                    }
                }
                return $item;
            });
        }
        $this->dispatchBrowserEvent('normalisasi-updated', $rows->map('array_values'));

        if ($rows->isEmpty()) return;
        foreach ($this->semua_kriteria as $kriteria) {
            $rows = $rows->map(function ($item) use ($kriteria) {
                $item['k' . $kriteria->id] = round($item['k' . $kriteria->id] * $kriteria->bobot, 2);
                return $item;
            });
        }
        $rows = $rows->map(function ($item) {
            $item['rangking'] = round(collect($item)->skip(3)->sum(), 2);
            return $item;
        });
        $this->dispatchBrowserEvent('rangking-updated', $rows->map('array_values'));

        if ($rows->isEmpty()) return;
        $rows = $rows->map(function ($item) use ($semua_karyawan) {
            $item = collect($item)->only(['id', 'nama', 'jabatan', 'rangking']);
            $item['status'] = $semua_karyawan->find($item['id'])->terpilih ? 'Terpilih' : 'Belum Terpilih';
            return $item->toArray();
        });
        $this->dispatchBrowserEvent('pemilihan-updated', $rows->map('array_values'));
    }

    public function ubah_periode($periode)
    {
        $this->periode = $periode;
        $this->incomplete = false;
        $this->init();
    }

    public function penetapan_karyawan($id)
    {
        $karyawan = Karyawan::find($id);
        $karyawan->terpilih = !$karyawan->terpilih;
        $karyawan->save();
        return redirect()->route('laporan.index');
    }

    public function render()
    {
        return view('livewire.laporan-penilaian');
    }
}
