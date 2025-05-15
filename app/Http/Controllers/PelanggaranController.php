<?php

namespace App\Http\Controllers;

use App\Models\listPelanggaran;
use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PelanggaranController extends Controller
{
    // public function index()
    // {
    //     $siswa = Siswa::all();
    //     $pelanggaran = listPelanggaran::all();
    //     $inputtanpelanggaran = Pelanggaran::all();
    //     $riwayat = \App\Models\Siswa::with(['pelanggarans.listPelanggaran', 'kelas'])
    //         ->whereHas('pelanggarans')
    //         ->get();

    //     return view('pelanggaran', compact('siswa', 'pelanggaran', 'inputtanpelanggaran', 'riwayat'));
    // }

    public function index()
{
    $siswa = Siswa::all();
    $pelanggaran = listPelanggaran::all();
    $inputtanpelanggaran = Pelanggaran::all();
    $riwayat = \App\Models\Siswa::with(['pelanggarans.listPelanggaran', 'kelas'])
        ->whereHas('pelanggarans')
        ->get();

    // Get violation statistics
    $stats = DB::table('pelanggaran')
        ->join('list_pelanggaran', 'pelanggaran.id_listpelanggaran', '=', 'list_pelanggaran.id_listpelanggaran')
        ->select('list_pelanggaran.nama_pelanggaran', DB::raw('count(*) as total'))
        ->groupBy('list_pelanggaran.nama_pelanggaran')
        ->orderBy('total', 'desc')
        ->limit(10)
        ->get();

    return view('pelanggaran', compact('siswa', 'pelanggaran', 'inputtanpelanggaran', 'riwayat', 'stats'));
}

    public function store(Request $request)
{
    $request->validate([
        'id_siswa' => 'required',
        'pilihpelanggaran' => 'required',
        'tanggal' => 'required',
    ]);

    Pelanggaran::create([
        'id_siswa' => $request->id_siswa,
        'id_listpelanggaran' => $request->pilihpelanggaran,
        'tanggal' => $request->tanggal,
    ]);

    $siswa = Siswa::with('kelas')->find($request->id_siswa);
    $listPelanggaran = listPelanggaran::find($request->pilihpelanggaran);

    $this->kirimNotifikasiPelanggaranWhatsApp($siswa, $listPelanggaran, $request->tanggal);

    return redirect()->back()->with('success', 'Pelanggaran berhasil ditambahkan!');
}


    // New detail method to fetch student violation details via AJAX
    public function detail(Request $request)
    {
        $idSiswa = $request->id_siswa;

        $siswa = Siswa::with('kelas')->findOrFail($idSiswa);
        $pelanggaran = Pelanggaran::with('listPelanggaran')
            ->where('id_siswa', $idSiswa)
            ->get();

        return response()->json([
            'success' => true,
            'siswa' => $siswa,
            'pelanggaran' => $pelanggaran
        ]);
    }

    // Updated update method to work with the new UI
    public function update(Request $request)
    {
        $request->validate([
            'id_pelanggaran' => 'required|exists:pelanggaran,id_pelanggaran',
            'pilihpelanggaran' => 'required',
            'tanggal' => 'required|date',
        ]);

        $pelanggaran = Pelanggaran::findOrFail($request->id_pelanggaran);
        $pelanggaran->update([
            'id_listpelanggaran' => $request->pilihpelanggaran,
            'tanggal' => $request->tanggal,
        ]);

        return redirect()->back()->with('success', 'Pelanggaran berhasil diperbarui!');
    }

    // Updated destroy method to work with the new UI
    public function destroy(Request $request)
    {
        $request->validate([
            'id_pelanggaran' => 'required|exists:pelanggaran,id_pelanggaran',
        ]);

        $pelanggaran = Pelanggaran::findOrFail($request->id_pelanggaran);
        $pelanggaran->delete();

        return redirect()->back()->with('success', 'Pelanggaran berhasil dihapus!');
    }

//

    private function kirimNotifikasiPelanggaranWhatsApp($siswa, $listPelanggaran, $tanggal)
    {
    $apiKey = "CFwxvW52cgTBRSxKSprj"; // Ganti dengan API key Fonnte kamu

    if (!$siswa->no_orangtua) {
        Log::warning("Nomor orang tua tidak tersedia untuk siswa ID: {$siswa->id_siswa}");
        return;
    }

    // Hitung total skor pelanggaran siswa
    $totalSkor = Pelanggaran::where('id_siswa', $siswa->id_siswa)
        ->join('list_pelanggaran', 'pelanggaran.id_listpelanggaran', '=', 'list_pelanggaran.id_listpelanggaran')
        ->sum('list_pelanggaran.skor');

    $jamSekarang = now()->format('H:i');
    $tanggalIndo = \Carbon\Carbon::parse($tanggal)->format('d-m-Y');
    $namaKelasLengkap = "{$siswa->kelas->jenjang} - {$siswa->kelas->nama_kelas}";

    $pesan = "🚨 *PELANGGARAN SISWA* 🚨\n\n"
        . "*Nama Siswa:* {$siswa->nama_siswa}\n"
        . "*NISN:* {$siswa->nisn}\n"
        . "*Kelas:* {$namaKelasLengkap}\n"
        . "*Jenis Pelanggaran:* {$listPelanggaran->nama_pelanggaran}\n"
        . "*Skor Pelanggaran:* {$listPelanggaran->skor}\n"
        . "*Tanggal:* {$tanggalIndo}\n"
        . "*Jam:* {$jamSekarang} WIB\n"
        . "*Total Skor Saat Ini:* {$totalSkor}\n"
        . "Berikut ini adalah ketentuan skor pelanggaran siswa beserta tindak lanjutnya:\n\n"
        . "*1. Pelanggaran Ringan:*\n"
        . "• Skor 10 - 35  ➜ Peringatan ke I (Petugas ketertiban)\n"
        . "• Skor 36 - 55  ➜ Peringatan ke II (Koord ketertiban)\n\n"

        . "*2. Pelanggaran Sedang:*\n"
        . "• Skor 56 - 75   ➜ Panggilan Orang Tua ke I (Wali Kelas)\n"
        . "• Skor 76 - 95   ➜ Panggilan Orang Tua ke II (Guru BK)\n"
        . "• Skor 96 - 150 ➜ Panggilan Orang Tua ke III (Koord BK)\n\n"

        . "*3. Pelanggaran Berat:*\n"
        . "• Skor 151 - 249 ➜ Skorsing (Wakasek Kesiswaan)\n"
        . "• Skor 250 ke atas ➜ Dikembalikan ke Orang Tua (Kepala Sekolah)\n\n"

        . "\nMohon untuk menjadi perhatian bersama.\n"
        . "*Guru BK SMKN 2 Jember*";

    try {
        $response = Http::asForm()->withHeaders([
            'Authorization' => $apiKey,
        ])->post('https://api.fonnte.com/send', [
            'target' => $siswa->no_orangtua,
            'message' => $pesan,
            'delay' => 1,
            'countryCode' => '62',
        ]);

        log::info("WA dikirim ke: {$siswa->no_orangtua}");
        Log::info("Pesan: $pesan");
        Log::info("Respons: " . $response->body());

    } catch (\Exception $e) {
        Log::error('Error kirim WA pelanggaran: ' . $e->getMessage());
    }
    }


// Add this method to PelanggaranController.php
public function getViolationStats()
{
    // Get all violations grouped by type with counts
    $stats = DB::table('pelanggaran')
        ->join('list_pelanggaran', 'pelanggaran.id_listpelanggaran', '=', 'list_pelanggaran.id_listpelanggaran')
        ->select('list_pelanggaran.nama_pelanggaran', DB::raw('count(*) as total'))
        ->groupBy('list_pelanggaran.nama_pelanggaran')
        ->orderBy('total', 'desc')
        ->get();

    return response()->json([
        'success' => true,
        'stats' => $stats
    ]);
}

public function getTrendStats(Request $request)
{
    $year = $request->year ?? date('Y');

    // Get monthly trends for the selected year
    $monthlyTrends = DB::table('pelanggaran')
        ->selectRaw('MONTH(tanggal) as month, COUNT(*) as total')
        ->whereYear('tanggal', $year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    // Get all available years in the database
    $availableYears = DB::table('pelanggaran')
        ->selectRaw('YEAR(tanggal) as year')
        ->distinct()
        ->orderBy('year')
        ->pluck('year');

    // Format data for the chart
    $monthLabels = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    $monthlyData = array_fill(0, 12, 0);
    foreach ($monthlyTrends as $trend) {
        // Month in DB is 1-based, array is 0-based
        $monthlyData[$trend->month - 1] = $trend->total;
    }

    return response()->json([
        'success' => true,
        'months' => $monthLabels,
        'data' => $monthlyData,
        'years' => $availableYears
    ]);
}



}
