<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'guru';


    protected $primaryKey = 'id_guru';

    protected $fillable = [
        'nama_guru',
        'jenis_kelamin',
        'agama',
        'nip',
        'no_telp_guru',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id_guru', 'id_guru');
    }


    public $timestamps = false;
}
