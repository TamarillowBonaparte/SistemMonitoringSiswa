<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListPelanggaran extends Model
{
   use HasFactory;
   protected $table = 'list_pelanggaran';

   protected $fillable = [
         'nama_pelanggaran',
         'skor',
         'nama_pelanggaran',
         'id_bentukpelanggaran'
   ];
   protected $primaryKey = 'id_listpelanggaran';

   public $timestamps = false;

   
}
