<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelajaran extends Model
{
   use HasFactory;
   protected $table = 'pelajaran';

   protected $fillable = [
         'nama_pelajaran',
   ];
   
   protected $primaryKey = 'id_pelajaran';

   public $timestamps = false;
}
