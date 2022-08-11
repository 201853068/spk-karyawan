<?php

namespace App\Http\Livewire;

use App\Models\Karyawan;
use App\Models\Kriteria;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class LaporanPenilaian extends Component
{
    public $columns;
    public $analisa;
    public $normalisasi;
    public $rangking;

    public function mount()
    {
        $semua_karyawan = Karyawan::with('jabatan')->get();
        $semua_kriteria = Kriteria::all();

        $this->columns = $semua_kriteria
            ->pluck('nama')
            ->prepend('Jabatan')
            ->prepend('Nama');

        $rows = collect([]);
        foreach ($semua_karyawan as $karyawan) {
            $row = [
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
                        $item['k' . $kriteria->id] = $item['k' . $kriteria->id] / $max;
                    }
                    if ($kriteria->tipe == 'COST' && $min > 0) {
                        $item['k' . $kriteria->id] = $min / $item['k' . $kriteria->id];
                    }
                }
                return $item;
            });
        }
        $this->normalisasi = $rows->map('array_values');

        foreach ($semua_kriteria as $kriteria) {
            $rows = $rows->map(function ($item) use ($kriteria) {
                $item['k' . $kriteria->id] = $item['k' . $kriteria->id] * $kriteria->bobot;
                return $item;
            });
        }
        $rows = $rows->map(function ($item) use ($kriteria) {
            $item['rangking'] = collect($item)->skip(2)->sum();
            return $item;
        });
        $this->rangking = $rows->map('array_values');
    }

    public function render()
    {
        return view('livewire.laporan-penilaian');
    }
}
