<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';


    protected $primaryKey = 'id_siswa';

    protected $fillable = [
        'nama_siswa',
        'nisn',
        'no_orangtua',
        'jenis_kelamin',
        'tempat_tgl_lahir',
        'alamat',
        'nama_ayah',
        'pendidikan_ayah',
        'pekerjaan_ayah',
        'nama_ibu',
        'pendidikan_ibu',
        'pekerjaan_ibu',
        'nama_wali',
        'pendidikan_wali',
        'pekerjaan_wali',
        'id_kelas',
        'foto_siswa',
    ];

    public $timestamps = false;
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function nilaiAkhir()
{
    return $this->hasMany(NilaiAkhir::class, 'id_siswa');
}

public function pelanggarans()
{
    return $this->hasMany(\App\Models\Pelanggaran::class, 'id_siswa');
}


}


