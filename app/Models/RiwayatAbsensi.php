<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Siswa;

class RiwayatAbsensi extends Model
{
    protected $table = 'history_absensi';
    protected $primaryKey = 'id_history_absensi';
    protected $fillable = [
        'id_kodepembelajaran',
        'id_siswa',
        'waktu_absen',
        'scan',
        'status',
        'keterangan_absen',
        'selfie_path',
        'surat_izin',
        'ditolak_keterangan',
        'batas_waktu_absen'
    ];


    // public function siswa()
    // {
    //     return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    // }

    public function siswa()
{
    return $this->belongsTo(Siswa::class, 'id_siswa');
}


    public $timestamps = false;


}

