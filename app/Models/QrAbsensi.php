<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ✅ Model QrAbsensi.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrAbsensi extends Model
{
    protected $table = 'qr_absensi';
    protected $fillable = [
        'id_jadwal',
        'tanggal',
        'qr_code'
    ];
}
