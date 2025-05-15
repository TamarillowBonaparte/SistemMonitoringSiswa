<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Siswa;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $primaryKey = 'id_absensi';
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

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    // Relasi ke Mapel
    public function mapel()
    {
        return $this->belongsTo(Pelajaran::class, 'id_kodepembelajaran');
    }

    public function kodePembelajaran()
{
    return $this->belongsTo(Kodemapel::class, 'id_kodepembelajaran');
}

    public $timestamps = false;
}

