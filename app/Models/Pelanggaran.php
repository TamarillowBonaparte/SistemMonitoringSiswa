<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggaran extends Model
{
    use HasFactory;
    protected $table = 'pelanggaran';

    protected $fillable = [
        'tanggal',
        'id_listpelanggaran',
        'id_siswa'
  ];

    protected $primaryKey = 'id_pelanggaran';

    public $timestamps = false;

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa', 'id_siswa');
    }

    public function listPelanggaran() {
        return $this->belongsTo(ListPelanggaran::class, 'id_listpelanggaran');
    }

}
