<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Karyawan extends Model
{
    protected $table = 'karyawan';

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Jabatan', 'jabatan_id');
    }

    public function penilaian()
    {
        return $this->hasMany('App\Models\Penilaian', 'karyawan_id');
    }

    protected $casts = [
        'terpilih' => 'boolean',
        'periode' => 'date',
    ];
}
