<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Tugas extends Model
{
    protected $table = 'tugas';

    public function kriteria()
    {
        return $this->belongsTo('App\Models\Kriteria', 'kriteria_id');
    }

    public function penilaian()
    {
        return $this->hasMany('App\Models\Penilaian', 'tugas_id');
    }
}
