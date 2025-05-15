<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kodemapel extends Model
{
    use HasFactory;
    protected $table = 'kode_pembelajaran';

    protected $fillable = ['kode_mapel', 'id_pelajaran',  'id_guru'];
    protected $primaryKey = 'id_kodepembelajaran';

    public $timestamps = false;

    public function pelajaran()
    {
        return $this->belongsTo(Pelajaran::class, 'id_pelajaran');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }

    public function kelas() {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
    // Dalam model Kodemapel.php
    public function jadwal()
    {
    return $this->hasMany(JadwalPelajaran::class, 'id_kodepembelajaran', 'id_kodepembelajaran');
    }

    
}
