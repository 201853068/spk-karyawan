<?php

namespace App\Models;

use App\Events\PenilaianProcessed;
use Illuminate\Database\Eloquent\Model;


class Penilaian extends Model
{
    protected $table = 'penilaian';

    public function tugas()
    {
        return $this->belongsTo('App\Models\Tugas', 'tugas_id');
    }

    protected $casts = [
        'nilai' => 'integer',
        'tanggal' => 'date',
    ];

}
