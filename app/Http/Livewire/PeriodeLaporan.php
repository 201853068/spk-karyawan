<?php

namespace App\Http\Livewire;

use Illuminate\Support\Carbon;
use Livewire\Component;

class PeriodeLaporan extends Component
{
    public $options;
    public $selected;

    public function mount($periode)
    {
        $this->options = $periode->map(function ($item) {
            return [
                'value' => $item->format('d-m-Y'),
                'label' => $item->locale('id_ID')->isoFormat('D MMMM Y'),
            ];
        });
        $this->selected = $this->options->first()['value'];
    }

    public function render()
    {
        return view('livewire.periode-laporan');
    }

    public function label_format($label)
    {
        return Carbon::parse($label);
    }

    public function updatedSelected($value)
    {
        $this->emit('periode_terpilih', $value);
    }
}
