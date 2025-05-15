<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jampelajaran extends Model
{
   use HasFactory;
   protected $table = 'jam_pelajaran';

   protected $fillable = [
         'jamke',
         'jam_range',
         'id_klasifikasi_hari',
   ];
   protected $primaryKey = 'id_jam_pelajaran';

   public $timestamps = false;

   public function klasifikasiHari()
{
    return $this->belongsTo(Hari::class, 'id_klasifikasi_hari', 'id');
}
}
