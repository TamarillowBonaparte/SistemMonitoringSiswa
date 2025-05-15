<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Informasi extends Model
{
   use HasFactory;
   protected $table = 'informasi';

   protected $fillable = [
         'judul',
         'isi',
         'tanggal',
         'file_pdf'

   ];
   protected $primaryKey = 'id_informasi';

   public $timestamps = false;

   public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'informasi_kelas');
    }
}
