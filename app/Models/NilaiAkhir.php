<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiAkhir extends Model
{
    use HasFactory;

    protected $table = 'nilai_akhir';

    protected $fillable = [
        'id_siswa',
        'id_kodepembelajaran',
        'semester',
        'nilai'
    ];

    // public function siswa()
    // {
    //     return $this->belongsTo(Siswa::class, 'id_siswa');
    // }

    public function kodePembelajaran()
{
    return $this->belongsTo(Kodemapel::class, 'id_kodepembelajaran', 'id_kodepembelajaran');
}
public function siswa()
{
    return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
}


}
