<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\JadwalPelajaran;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AbsensiController extends Controller
{
    public function index()
    {

        $user = auth()->user(); // Ambil user yang sedang login
        $kelasId = request()->query('kelas');

        // Cek role dari session atau user relationship
        $isAdmin = session('nama_role') === 'Admin' || optional($user)->roleuser->nama_role === 'Admin';
        // dd(session('id_guru'), $isAdmin);

        // Ambil data kelas tergantung role
        if ($isAdmin) {
            $kelas = Kelas::all();
        } else {
            $idGuru = session('id_guru');
            $kelas = JadwalPelajaran::join('kode_pembelajaran', 'jadwal_pelajaran.id_kodepembelajaran', '=', 'kode_pembelajaran.id_kodepembelajaran')
                ->join('kelas', 'jadwal_pelajaran.id_kelas', '=', 'kelas.id_kelas')
                ->where('kode_pembelajaran.id_guru', $idGuru)
                ->select('kelas.*')
                ->distinct()
                ->get();
        }

        // Ambil data absensi sesuai kelas jika dipilih
        if ($kelasId) {
            $absensi = Absensi::with('siswa')->whereHas('siswa', function ($query) use ($kelasId) {
                $query->where('id_kelas', $kelasId);
            })->get();
        } else {
            $absensi = Absensi::with('siswa')->get();
        }

        return view('absensi', compact('absensi', 'kelas'));
    }

    public function create()
    {
        $user = auth()->user();
        $isAdmin = session('nama_role') === 'Admin' || optional($user)->roleuser->nama_role === 'Admin';

        // dd(session('nama_role'), optional($user)->roleuser->nama_role, $isAdmin);

        if ($isAdmin) {
            $kelas = Kelas::all();
        } else {
            $idGuru = session('id_guru');
            $kelas = JadwalPelajaran::join('kode_pembelajaran', 'jadwal_pelajaran.id_kodepembelajaran', '=', 'kode_pembelajaran.id_kodepembelajaran')
                ->join('kelas', 'jadwal_pelajaran.id_kelas', '=', 'kelas.id_kelas')
                ->where('kode_pembelajaran.id_guru', $idGuru)
                ->select('kelas.*')
                ->distinct()
                ->get();
        }

        $absensi = Absensi::all();
        return view('absensi', compact('kelas', 'absensi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required',
            'id_kodepembelajaran' => 'required',
            'siswa_ids' => 'required|array',
            'siswa_ids.*' => 'required|exists:siswa,id_siswa',
            'batas_waktu_absen' => 'required|date_format:Y-m-d\TH:i',
        ]);

        foreach ($request->siswa_ids as $siswaId) {
            DB::table('absensi')->insert([
                'id_siswa' => $siswaId,
                'id_kodepembelajaran' => $request->id_kodepembelajaran,
                'status' => 'belum absen',
                'batas_waktu_absen' => date('Y-m-d H:i:s', strtotime($request->batas_waktu_absen)),
            ]);
        }

        return redirect()->back()->with('success', 'Data Absensi berhasil disimpan untuk semua siswa yang dipilih.');
    }

    public function update(Request $request, $id)
    {
        $absensi = Absensi::where('id_absensi', $id)->firstOrFail(); // Menggunakan id_absensi
        $absensi->status = $request->status;
        $absensi->save();

        return redirect()->route('absensi')->with('success', 'Status Absensi berhasil diperbarui.');
    }

    // public function updateketerangan(Request $request, $id)
    // {
    //     $request->validate([
    //         'keterangan_absen' => 'required|in:Hadir,Sakit,Izin,Alpha',
    //     ]);

    //     $absensi = Absensi::where('id_absensi', $id)->firstOrFail();
    //     $absensi->keterangan_absen = $request->keterangan_absen;
    //     $absensi->save();
    //     return redirect()->back()->with('success', 'Keterangan Absensi berhasil diperbarui.');
    // }

        public function updateketerangan(Request $request, $id)
    {
        $request->validate([
            'keterangan_absen' => 'required|in:Hadir,Sakit,Izin,Alpha',
        ]);

        $absensi = Absensi::findOrFail($id); // Pastikan data ditemukan
        $absensi->keterangan_absen = $request->keterangan_absen;
        $absensi->save();

        return response()->json(['success' => 'Keterangan Absensi berhasil diperbarui.']);
    }

        public function validasiMassal(Request $request)
    {
        $request->validate([
            'absensi_ids' => 'required|array',
            'aksi' => 'required|in:diterima,ditolak',
        ]);

        Absensi::whereIn('id_absensi', $request->absensi_ids)->update([
            'status' => $request->aksi
        ]);

        return redirect()->back()->with('success', 'Validasi massal berhasil dilakukan.');
    }

    // public function selesaikanValidasi()
    // {
    //     $absensi = Absensi::all();

    //     foreach ($absensi as $item) {
    //         DB::table('history_absensi')->insert([
    //             'id_siswa'           => $item->id_siswa,
    //             'id_kodepembelajaran'=> $item->id_kodepembelajaran,
    //             'waktu_absen'        => $item->waktu_absen,
    //             'foto_absen'         => $item->foto_absen,
    //             'status'             => $item->status,
    //             'keterangan_absen'   => $item->keterangan_absen,
    //             'surat_izin'         => $item->surat_izin,
    //             'ditolak_keterangan' => $item->ditolak_keterangan,
    //             'batas_waktu_absen'  => $item->batas_waktu_absen,
    //         ]);
    //     }

    //     Absensi::truncate();

    //     return redirect()->back()->with('success', 'Semua data absensi telah dipindahkan ke history dan dikosongkan dari tabel absensi.');
    // }

    public function selesaikanValidasi()
{
    // Ambil semua data absensi yang akan diproses
    $absensi = Absensi::all();

    // Pindahkan data absensi ke tabel history_absensi
    foreach ($absensi as $item) {
        DB::table('history_absensi')->insert([
            'id_siswa'           => $item->id_siswa,
            'id_kodepembelajaran'=> $item->id_kodepembelajaran,
            'waktu_absen'        => $item->waktu_absen,
            'foto_absen'         => $item->foto_absen,
            'status'             => $item->status,
            'keterangan_absen'   => $item->keterangan_absen,
            'surat_izin'         => $item->surat_izin,
            'ditolak_keterangan' => $item->ditolak_keterangan,
            'batas_waktu_absen'  => $item->batas_waktu_absen,
        ]);

        // Kirim notifikasi WhatsApp ke orang tua siswa
        $this->kirimNotifikasiWhatsApp($item);
    }

    // Kosongkan tabel absensi
    Absensi::truncate();

    return redirect()->back()->with('success', 'Semua data absensi telah dipindahkan ke history dan dikosongkan dari tabel absensi.');
}

private function kirimNotifikasiWhatsApp($absensi)
{
    $apiKey = "CFwxvW52cgTBRSxKSprj";

    $siswa = Siswa::find($absensi->id_siswa);

    if (!$siswa || !$siswa->no_orangtua) {
        Log::warning("Nomor orang tua tidak tersedia untuk siswa ID: {$absensi->id_siswa}");
        return;
    }

    // Ambil informasi pelajaran & jam absensi dari relasi
    $jadwal = DB::table('kode_pembelajaran')
        ->join('pelajaran', 'kode_pembelajaran.id_pelajaran', '=', 'pelajaran.id_pelajaran')
        ->where('kode_pembelajaran.id_kodepembelajaran', $absensi->id_kodepembelajaran)
        ->select('pelajaran.nama_pelajaran')
        ->first();

    $namaPelajaran = $jadwal->nama_pelajaran ?? 'Tidak diketahui';
    $jamAbsen = $absensi->waktu_absen ? date('H:i', strtotime($absensi->waktu_absen)) : 'Belum Absen';

    $pesan = "📢 *NOTIFIKASI ABSENSI* 📢\n\n"
        . "*Assalamu'alaikum Wr. Wb.*\n"
        . "*Shalom*\n"
        . "*Om Swastiastu*\n"
        . "*Namo Buddhaya dan Salam Kebajikan*\n"
        . "\n"
        . "Berikut adalah informasi absensi untuk siswa:\n"
        . "- Nama Siswa: {$siswa->nama_siswa}\n"
        . "- NISN: {$siswa->nisn}\n"
        . "- Mata Pelajaran: {$namaPelajaran}\n"
        . "- Jam Absen: {$jamAbsen} WIB\n"
        . "- Status Absensi: *{$absensi->status}*\n"
        . "- Keterangan: *{$absensi->keterangan_absen}*\n"
        . "\n"
        . "Terima kasih atas perhatian Anda.\n"
        . "\n"
        . "*Humas SMKN 2 Jember*";

    try {
        $response = Http::asForm()->withHeaders([
            'Authorization' => $apiKey,
        ])->post('https://api.fonnte.com/send', [
            'target' => $siswa->no_orangtua,
            'message' => $pesan,
            'delay' => 2,
            'countryCode' => '62',
        ]);

        Log::info('Nomor tujuan: ' . $siswa->no_orangtua);
        Log::info('Isi pesan: ' . $pesan);
        Log::info('WhatsApp API response code: ' . $response->status());
        Log::info('WhatsApp API response: ' . $response->body());

        if ($response->successful()) {
            Log::info("Notifikasi WhatsApp berhasil dikirim ke nomor: {$siswa->no_orangtua}");
        } else {
            Log::error("Gagal mengirim notifikasi WhatsApp ke nomor: {$siswa->no_orangtua}");
        }
    } catch (\Exception $e) {
        Log::error('WhatsApp API error: ' . $e->getMessage());
        Log::error('Exception trace: ' . $e->getTraceAsString());
    }
}


    public function getSiswaByKelas($idKelas)
    {
        $siswa = DB::table('siswa')
            ->where('id_kelas', $idKelas)
            ->select('id_siswa', 'nama_siswa', 'nisn')
            ->get();
        return response()->json($siswa);
    }

    public function getMapelByKelas($idKelas)
    {
        $user = auth()->user();
        $isAdmin = session('nama_role') === 'Admin' || optional($user)->roleuser->nama_role === 'Admin';
        // dd(session('id_guru'), $isAdmin);

        if ($isAdmin) {
            $mapel = DB::table('jadwal_pelajaran')
            ->join('kode_pembelajaran', 'jadwal_pelajaran.id_kodepembelajaran', '=', 'kode_pembelajaran.id_kodepembelajaran')
            ->join('pelajaran', 'kode_pembelajaran.id_pelajaran', '=', 'pelajaran.id_pelajaran')
            ->join('guru', 'kode_pembelajaran.id_guru', '=', 'guru.id_guru')
            ->where('jadwal_pelajaran.id_kelas', $idKelas)
            ->select('kode_pembelajaran.id_kodepembelajaran', 'pelajaran.nama_pelajaran', 'guru.nama_guru')
            ->distinct()
            ->get();

        } else {
            $idGuru = session('id_guru');
            $mapel = DB::table('jadwal_pelajaran')
                ->join('kode_pembelajaran', 'jadwal_pelajaran.id_kodepembelajaran', '=', 'kode_pembelajaran.id_kodepembelajaran')
                ->join('pelajaran', 'kode_pembelajaran.id_pelajaran', '=', 'pelajaran.id_pelajaran')
                ->join('guru', 'kode_pembelajaran.id_guru', '=', 'guru.id_guru')
                ->where('jadwal_pelajaran.id_kelas', $idKelas)
                ->where('kode_pembelajaran.id_guru', $idGuru)
                ->select('kode_pembelajaran.id_kodepembelajaran', 'pelajaran.nama_pelajaran', 'guru.nama_guru')
                ->distinct()
                ->get();
        }

        return response()->json($mapel);
    }

    public function destroy($id)
{
    try {
        // Find the absensi record
        $absensi = Absensi::findOrFail($id);

        // Delete the record
        $absensi->delete();

        // Optionally, delete the associated image if it exists
        if ($absensi->foto_absen) {
            $imagePath = public_path('uploads/absensi/' . $absensi->foto_absen);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        return redirect()->back()->with('success', 'Data absensi berhasil dihapus!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal menghapus data absensi: ' . $e->getMessage());
    }
}
}
