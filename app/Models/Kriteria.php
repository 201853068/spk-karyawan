<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Kriteria extends Model
{
    protected $table = 'kriteria';

    public function getLabelAttribute()
    {
        return $this->nama . ' (' . $this->bobot . '%)';
    }
}
