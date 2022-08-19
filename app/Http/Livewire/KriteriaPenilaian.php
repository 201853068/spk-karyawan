<?php

namespace App\Http\Livewire;

use App\Models\Kriteria;
use Livewire\Component;

class KriteriaPenilaian extends Component
{
    public $title = 'Kriteria Penilaian Tugas';
    public $columns = ['Kriteria', 'Tipe', 'Bobot'];
    public $rows;

    public function mount()
    {
        $this->rows = Kriteria::tugas()->get();
    }

    public function render()
    {
        return view('livewire.kriteria-penilaian');
    }
}
