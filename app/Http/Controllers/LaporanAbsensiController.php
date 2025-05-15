<?php
namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Kelas;
use Illuminate\Http\Request;
use App\Models\RiwayatAbsensi;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanAbsensiController extends Controller
{
    public function index(Request $request)
{
    // Get selected filters
    $rangebulan = $request->input('rangebulan', date('m/Y') . ' - ' . date('m/Y'));
    $hari = $request->input('hari');
    $kelas = $request->input('kelas');
    $pelajaran = $request->input('pelajaran');
    $search = $request->input('search');

    // Initialize start and end dates
    $startDate = null;
    $endDate = null;

    // Parse date based on filters
    if ($hari) {
        // If daily filter is used
        $day_parts = explode('/', $hari);
        if (count($day_parts) == 3) {
            $day = $day_parts[0];
            $month = $day_parts[1];
            $year = $day_parts[2];

            $startDate = "{$year}-{$month}-{$day} 00:00:00"; // Start of the day
            $endDate = "{$year}-{$month}-{$day} 23:59:59";   // End of the day
        }
    } else {
        // If range month filter is used
        $tanggal_range = explode(' - ', $rangebulan);

        // Parse start date
        $start_date_parts = explode('/', trim($tanggal_range[0]));
        $bulan_awal = isset($start_date_parts[0]) ? $start_date_parts[0] : date('m');
        $tahun_awal = isset($start_date_parts[1]) ? $start_date_parts[1] : date('Y');

        // Parse end date
        $end_date_parts = explode('/', trim($tanggal_range[1] ?? $tanggal_range[0]));
        $bulan_akhir = isset($end_date_parts[0]) ? $end_date_parts[0] : date('m');
        $tahun_akhir = isset($end_date_parts[1]) ? $end_date_parts[1] : date('Y');

        $startDate = "{$tahun_awal}-{$bulan_awal}-01 00:00:00"; // Start of the first day of the month
        $endDate = date('Y-m-t 23:59:59', strtotime("{$tahun_akhir}-{$bulan_akhir}-01")); // End of the last day of the month
    }

    // Get list of classes
    $daftarKelas = DB::table('kelas')->get();

    // Get students based on filters
    $query = DB::table('siswa')
        ->join('kelas', 'siswa.id_kelas', '=', 'kelas.id_kelas')
        ->select('siswa.*', 'kelas.nama_kelas');

    if ($kelas) {
        $query->where('siswa.id_kelas', $kelas);
    }

    if ($search) {
        $query->where('siswa.nama_siswa', 'like', '%' . $search . '%');
    }

    $daftarSiswa = $query->get();

    // Get attendance data for the selected period
    $absensiQuery = DB::table('history_absensi')
        ->whereBetween('batas_waktu_absen', [$startDate, $endDate])
        ->select(
            'id_siswa',
            'batas_waktu_absen',
            'keterangan_absen',
            'id_kodepembelajaran' // Include id_kodepembelajaran for filtering
        );

    // Join with jadwal_pelajaran, kode_pembelajaran, and guru to get teacher names
    if ($pelajaran || !$pelajaran) {
        $absensiQuery = DB::table('history_absensi')
            ->join('jadwal_pelajaran', 'history_absensi.id_kodepembelajaran', '=', 'jadwal_pelajaran.id_kodepembelajaran')
            ->join('kode_pembelajaran', 'jadwal_pelajaran.id_kodepembelajaran', '=', 'kode_pembelajaran.id_kodepembelajaran')
            ->join('guru', 'kode_pembelajaran.id_guru', '=', 'guru.id_guru') // Correct join for teacher
            ->whereBetween('batas_waktu_absen', [$startDate, $endDate])
            ->select(
                'history_absensi.id_siswa',
                'history_absensi.batas_waktu_absen',
                'history_absensi.keterangan_absen',
                'guru.nama_guru', // Include teacher's name
                'kode_pembelajaran.id_pelajaran' // Include id_pelajaran for filtering
            );

        // If a specific subject is selected, filter by it
        if ($pelajaran) {
            $absensiQuery->where('kode_pembelajaran.id_pelajaran', $pelajaran);
        }
    }

    $dataAbsensi = $absensiQuery->get();

    // Get subjects based on selected class
    $daftarPelajaran = [];
    if ($kelas) {
        $daftarPelajaran = DB::table('jadwal_pelajaran')
            ->join('kode_pembelajaran', 'jadwal_pelajaran.id_kodepembelajaran', '=', 'kode_pembelajaran.id_kodepembelajaran')
            ->join('pelajaran', 'kode_pembelajaran.id_pelajaran', '=', 'pelajaran.id_pelajaran')
            ->where('jadwal_pelajaran.id_kelas', $kelas)
            ->select('pelajaran.id_pelajaran', 'pelajaran.nama_pelajaran')
            ->distinct()
            ->get();
    }

    return view('laporanabsensi', compact('daftarKelas', 'daftarSiswa', 'dataAbsensi', 'daftarPelajaran'));
}

public function cetakLaporan(Request $request)
{
    // Get filter parameters
    $rangebulan = $request->input('rangebulan', date('m/Y').' - '.date('m/Y'));
    $kelas_id = $request->input('kelas', '');
    $pelajaran_id = $request->input('pelajaran', '');
    $search = $request->input('search', '');

    // Parse range bulan
    $tanggal_range = explode(' - ', $rangebulan);

    // Parse tanggal awal
    $start_date_parts = explode('/', trim($tanggal_range[0]));
    $bulan_awal = isset($start_date_parts[0]) ? $start_date_parts[0] : date('m');
    $tahun_awal = isset($start_date_parts[1]) ? $start_date_parts[1] : date('Y');

    // Parse tanggal akhir
    $end_date_parts = explode('/', trim($tanggal_range[1] ?? $tanggal_range[0]));
    $bulan_akhir = isset($end_date_parts[0]) ? $end_date_parts[0] : date('m');
    $tahun_akhir = isset($end_date_parts[1]) ? $end_date_parts[1] : date('Y');

    // Generate start and end dates
    $startDate = Carbon::createFromDate($tahun_awal, $bulan_awal, 1)->startOfMonth();
    $endDate = Carbon::createFromDate($tahun_akhir, $bulan_akhir, 1)->endOfMonth();

    // Get the date range
    $dateRange = [];
    $current = clone $startDate;
    while ($current <= $endDate) {
        $dateRange[] = clone $current;
        $current->addDay();
    }

    // Get class name
    $kelas = 'Semua Kelas';
    if (!empty($kelas_id)) {
        $kelas = DB::table('kelas')->where('id_kelas', $kelas_id)->value('nama_kelas') ?? 'Kelas tidak ditemukan';
    }

    // Get mapel name
    $mapel = 'Semua Mata Pelajaran';
    if (!empty($pelajaran_id)) {
        $mapel = DB::table('pelajaran')->where('id_pelajaran', $pelajaran_id)->value('nama_pelajaran') ?? 'Mata Pelajaran tidak ditemukan';
    }

    // Query siswa dengan filter kelas dan search
    $query = DB::table('siswa');

    if (!empty($kelas_id)) {
        $query->where('id_kelas', $kelas_id);
    }

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('nama_siswa', 'LIKE', '%' . $search . '%')
              ->orWhere('nis', 'LIKE', '%' . $search . '%');
        });
    }

    $daftarSiswa = $query->get();

    // Format data for the grouped view
    $grouped = [];

    foreach ($daftarSiswa as $siswa) {
        $absensiItems = [];

        // Create a base record for the student with their info
        $baseRecord = (object)[
            'nama' => $siswa->nama_siswa,
            'nisn' => $siswa->nisn,
            'id_siswa' => $siswa->id_siswa
        ];

        // Get actual attendance data
        $absensiQuery = DB::table('history_absensi')
                    ->where('id_siswa', $siswa->id_siswa)
                    ->whereBetween('batas_waktu_absen', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);

        if (!empty($pelajaran_id)) {
            $absensiQuery->join('kode_pembelajaran', 'history_absensi.id_kodepembelajaran', '=', 'kode_pembelajaran.id_kodepembelajaran')
                    ->where('kode_pembelajaran.id_pelajaran', $pelajaran_id)
                    ->select('history_absensi.*');
        }

        $absensiResults = $absensiQuery->get();

        // Add the base record to the collection
        $absensiItems[] = clone $baseRecord;

        // Process and add all attendance records
        foreach ($absensiResults as $item) {
            $itemDate = Carbon::parse(substr($item->batas_waktu_absen, 0, 10));

            // Create attendance record
            $record = clone $baseRecord;
            $record->tanggal = $itemDate->format('Y-m-d');

            // IMPORTANT: Match the status exactly to what's expected in the template
            // These must match the exact CSS class names and the exact strings checked in the template
            switch ($item->keterangan_absen) {
                case 'H': $status = 'Hadir'; break;
                case 'I': $status = 'Izin'; break;
                case 'A': $status = 'Alpha'; break;
                case 'S': $status = 'Sakit'; break;
                case 'T': $status = 'Terlambat'; break;
                default: $status = $item->keterangan_absen;
            }

            $record->keterangan_absen = $status;
            $absensiItems[] = $record;
        }

        $grouped[$siswa->nisn] = $absensiItems;
    }

    return view('cetak', compact('grouped', 'dateRange', 'kelas', 'mapel', 'startDate', 'endDate'));
}

public function getPelajaranByKelas(Request $request)
{
    $kelas = $request->input('kelas');
    $daftarPelajaran = DB::table('jadwal_pelajaran')
        ->join('kode_pembelajaran', 'jadwal_pelajaran.id_kodepembelajaran', '=', 'kode_pembelajaran.id_kodepembelajaran')
        ->join('pelajaran', 'kode_pembelajaran.id_pelajaran', '=', 'pelajaran.id_pelajaran')
        ->where('jadwal_pelajaran.id_kelas', $kelas)
        ->select('pelajaran.id_pelajaran', 'pelajaran.nama_pelajaran')
        ->distinct()
        ->get();

    return response()->json($daftarPelajaran);
}
}
