<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Klasifikasi_Hari extends Model
{
    use HasFactory;
    protected $table = 'klasifikasi_hari';

    protected $fillable = ['hari'];

    protected $primaryKey = 'id';

    public $timestamps = false;

}
