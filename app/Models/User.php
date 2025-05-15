<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
   use HasFactory;
   protected $table = 'user';

   protected $fillable = [
       'name',
       'username',
       'email',
       'password',
       'id_roleuser',
   ];
   protected $primaryKey = 'id_user';

   public $timestamps = false;
   protected $hidden = [
    'password',
];

   public function roleuser()
   {
       return $this->belongsTo(Role::class, 'id_roleuser', 'id_roleuser');
   }

   public function siswa()
   {
       return $this->hasOne(Siswa::class, 'id_user', 'id_user');
   }

   public function guru()
   {
       return $this->hasOne(Guru::class, 'id_user', 'id_user');
   }
}
