<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $table = 'roleuser';
    protected $fillable = [
        'nama_role'
    ];
    public $timestamps = false;

    protected $primaryKey = 'id_roleuser';

    public function users()
    {
        return $this->hasMany(User::class, 'id_roleuser', 'id_roleuser');
    }
}
