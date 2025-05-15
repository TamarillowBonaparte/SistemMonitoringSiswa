<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ujian extends Model
{
    use HasFactory;
    protected $table = 'ujian';
    protected $primaryKey = 'id_ujian';
    protected $fillable = [
        'id_kelas',
        'id_pelajaran',
        'jenis_ujian',
        'tanggal_ujian',
        'jam_mulai',
        'jam_selesai',
        'ruang_ujian',
        'keterangan'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function pelajaran()
    {
        return $this->belongsTo(Pelajaran::class, 'id_pelajaran');
    }

    public $timestamps = false;

}

