<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;
    protected $table = 'kelas';

    protected $fillable = ['jenjang', 'nama_kelas', 'wali_kelas', 'id_jurusan'];

    protected $primaryKey = 'id_kelas';

    public $timestamps = false;

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'id_jurusan');
    }

    public function informasi()
    {
        return $this->belongsToMany(Informasi::class, 'informasi_kelas');
    }
}
