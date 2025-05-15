<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
   use HasFactory;
   protected $table = 'jadwal_pelajaran';

   protected $fillable = [
         'id_kelas',
         'id_pelajaran',
         'id_guru',
         'id_jam_pelajaran',
         'hari',
   ];
   protected $primaryKey = 'id_jadwal';

   public $timestamps = false;

   public function Kodemapel()
{
    return $this->belongsTo(Kodemapel::class, 'id_kodepembelajaran');
}

}
