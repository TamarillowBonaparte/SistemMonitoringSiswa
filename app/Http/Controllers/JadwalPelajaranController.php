<?php
// app/Http/Controllers/JadwalPelajaranController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\Hari;
use App\Models\JamPelajaran;
use App\Models\KodePembelajaran;
use App\Models\JadwalPelajaran;
use App\Models\Kodemapel;

class JadwalPelajaranController extends Controller
{
    public function index()
    {
        $jurusan = Jurusan::all();
        $kelas = Kelas::all();
        $jadwalpelajaran = JadwalPelajaran::all();
        $hari = Hari::all();
        $kode_pembelajaran = Kodemapel::all();

        return view('jadwalpelajaran', compact('jurusan', 'kelas', 'jadwalpelajaran', 'hari', 'kode_pembelajaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas' => 'required',
            'hari' => 'required',
            'total_jam' => 'required|integer|min:1',
        ]);

        $id_kelas = $request->kelas;
        $id_hari = $request->hari;
        $total_jam = $request->total_jam;

        // Cek apakah hari Jumat (misal id_hari = 5 untuk Jumat)
        $isJumat = $id_hari == 5;

        // Jika hari Jumat, ambil id_jam_pelajaran 16-30, selain itu 1-15
        $jamPelajaran = DB::table('jam_pelajaran')
            ->whereBetween('id_jam_pelajaran', $isJumat ? [16, 30] : [1, 15])
            ->orderBy('id_jam_pelajaran', 'asc')
            ->pluck('id_jam_pelajaran');

        // Menyesuaikan jumlah jam yang diambil (termasuk istirahat)
        $jamTerpilih = $jamPelajaran->take($total_jam + floor($total_jam / 4));

        foreach ($jamTerpilih as $jam) {
            DB::table('jadwal_pelajaran')->insert([
                'id_kelas' => $id_kelas,
                'id_jam_pelajaran' => $jam,
                'id_hari' => $id_hari,
                'id_kodepembelajaran' => null, // Sesuaikan jika ada kode pembelajaran
            ]);
        }

        return redirect()->back()->with('success', 'Jadwal berhasil disimpan termasuk jam istirahat!');
    }

   
       // Modifikasi method getJadwal di JadwalPelajaranController
    public function getJadwal(Request $request)
    {
        $jadwal = DB::table('jadwal_pelajaran')
            ->join('hari', 'jadwal_pelajaran.id_hari', '=', 'hari.id_hari')
            ->join('jam_pelajaran', 'jadwal_pelajaran.id_jam_pelajaran', '=', 'jam_pelajaran.id_jam_pelajaran')
            ->leftJoin('kode_pembelajaran', 'jadwal_pelajaran.id_kodepembelajaran', '=', 'kode_pembelajaran.id_kodepembelajaran')
            ->leftJoin('guru', 'kode_pembelajaran.id_guru', '=', 'guru.id_guru')  // Join dengan tabel guru
            ->select(
                'jadwal_pelajaran.id_jadwal',
                'hari.nama_hari',
                'hari.id_hari',
                'jam_pelajaran.jamke',
                'jam_pelajaran.id_jam_pelajaran',
                'jam_pelajaran.jam_range',
                'kode_pembelajaran.kode_mapel',
                'guru.nama_guru',
                'jadwal_pelajaran.id_kodepembelajaran'
            )
            ->where('jadwal_pelajaran.id_kelas', $request->id_kelas)
            ->orderBy('hari.id_hari')
            ->orderBy('jadwal_pelajaran.id_jadwal')  // Urut berdasarkan id_jadwal
            ->get();

        return response()->json($jadwal);
    }


    // Add this method to JadwalPelajaranController
    public function updatePembelajaran(Request $request)
    {
        $jadwal = DB::table('jadwal_pelajaran')
            ->where('id_kelas', $request->id_kelas)
            ->where('id_hari', $request->id_hari)
            ->where('id_jam_pelajaran', $request->id_jam_pelajaran)
            ->update(['id_kodepembelajaran' => $request->id_kodepembelajaran]);

        return response()->json(['success' => true]);
    }
}
