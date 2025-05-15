<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Siswa;

class Location extends Model
{
    protected $table = 'lokasi_absensi';
    protected $primaryKey = 'id_lokasi';
    protected $fillable = [
        'id_lokasi',
        'nama_lokasi',
        'latitude',
        'longitude',
        'status',
        'radius',
    ];

    public $timestamps = false;
}

