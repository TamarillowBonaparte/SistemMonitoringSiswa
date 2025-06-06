<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hari extends Model
{
   use HasFactory;
   protected $table = 'hari';

   protected $fillable = [
         'nama_hari',
         'status',
   ];
   protected $primaryKey = 'id_hari';

   public $timestamps = false;

   public function jamPelajaran()
{
    return $this->hasMany(JamPelajaran::class, 'id_klasifikasi_hari', 'id');
}

}
