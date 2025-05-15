<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformasiKelas extends Model
{
   use HasFactory;
   protected $table = 'informasi_kelas';

   protected $fillable = [
         'id_informasi',
         'id_kelas'
   ];
   protected $primaryKey = 'id';

   public $timestamps = false;

    public function kelas()
     {
          return $this->belongsToMany(Kelas::class, 'informasi_kelas');
     }
}
