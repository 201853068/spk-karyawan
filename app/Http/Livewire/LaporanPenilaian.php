<?php

namespace App\Http\Livewire;

use App\Models\Karyawan;
use App\Models\Kriteria;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LaporanPenilaian extends Component
{
    public $is_owner;
    public $columns;
    public $analisa;
    public $normalisasi;
    public $rangking;
    public $pemilihan;

    protected $listeners = ['karyawan_terpilih' => 'penetapan_karyawan'];

    public function mount()
    {
        $this->is_owner = Auth::user()->role_id == 4;
        $semua_karyawan = Karyawan::with('jabatan')->get();
        $semua_kriteria = Kriteria::all();

        $this->columns = $semua_kriteria
            ->pluck('nama')
            ->prepend('Jabatan')
            ->prepend('Nama')
            ->prepend('ID');

        $rows = collect([]);
        foreach ($semua_karyawan as $karyawan) {
            $row = [
                'id' => $karyawan->id,
                'nama' => $karyawan->nama,
                'jabatan' => $karyawan->jabatan->nama,
            ];
            foreach ($semua_kriteria as $kriteria) {
                $row['k' . $kriteria->id] = (int)$karyawan->penilaian()->whereHas('tugas', function (Builder $query) use ($kriteria) {
                    $query->where('kriteria_id', $kriteria->id);
                })->sum('nilai');
            }
            $rows->push($row);
        }
        $this->analisa = $rows->map('array_values');

        foreach ($semua_kriteria as $kriteria) {
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
        $this->normalisasi = $rows->map('array_values');

        foreach ($semua_kriteria as $kriteria) {
            $rows = $rows->map(function ($item) use ($kriteria) {
                $item['k' . $kriteria->id] = round($item['k' . $kriteria->id] * $kriteria->bobot, 2);
                return $item;
            });
        }
        $rows = $rows->map(function ($item) {
            $item['rangking'] = round(collect($item)->skip(3)->sum(), 2);
            return $item;
        });
        $this->rangking = $rows->map('array_values');

        $rows = $rows->map(function ($item, $index) use ($semua_karyawan) {
            $item = collect($item)->only(['id', 'nama', 'jabatan', 'rangking']);
            $item['status'] = $semua_karyawan[$index]->terpilih ? 'Terpilih' : 'Belum Terpilih';
            return $item->toArray();
        });
        $this->pemilihan = $rows->map('array_values');
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
