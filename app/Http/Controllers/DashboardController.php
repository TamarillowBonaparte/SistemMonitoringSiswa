<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Mapel;
use App\Models\Jurusan;
use App\Models\Pelajaran;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $kelas = DB::table('kelas')->get();
        $jumlahSiswa = DB::table('siswa')->count();
        $jumlahGuru = DB::table('guru')->count();
        $jumlahKelas = DB::table('kelas')->count();
        $jumlahUser = DB::table('user')->count();
        $jumlahMapel = DB::table('pelajaran')->count();
        $jumlahJurusan = DB::table('jurusan')->count();

        $siswaPerJurusan = DB::table('siswa')
            ->join('kelas', 'siswa.id_kelas', '=', 'kelas.id_kelas')
            ->join('jurusan', 'kelas.id_jurusan', '=', 'jurusan.id_jurusan')
            ->select('jurusan.nama_jurusan', DB::raw('count(*) as total'))
            ->groupBy('jurusan.nama_jurusan')
            ->get();

        $jurusanLabels = $siswaPerJurusan->pluck('nama_jurusan');
        $jurusanData = $siswaPerJurusan->pluck('total');

        $siswaPerKelas = DB::table('siswa')
            ->join('kelas', 'siswa.id_kelas', '=', 'kelas.id_kelas')
            ->select('kelas.nama_kelas', DB::raw('count(*) as total'))
            ->groupBy('kelas.nama_kelas')
            ->get();

        $kelasLabels = $siswaPerKelas->pluck('nama_kelas');
        $kelasData = $siswaPerKelas->pluck('total');

        // Get data for filters
        $mapelList = DB::table('pelajaran')->get();
        $guruList = DB::table('guru')->get();

        // Add this query for kelasList
        $kelasList = DB::table('kelas')
            ->join('jurusan', 'kelas.id_jurusan', '=', 'jurusan.id_jurusan')
            ->select(
                'kelas.id_kelas',
                'kelas.jenjang',
                'kelas.nama_kelas',
                'jurusan.nama_jurusan'
            )
            ->orderBy('kelas.jenjang')
            ->orderBy('jurusan.nama_jurusan')
            ->orderBy('kelas.nama_kelas')
            ->get();

        // Get saved filters from session
        $savedFilters = session('jadwal_filters', [
            'hari' => '',
            'mapel' => '',
            'guru' => '',
            'kelas' => ''
        ]);

        // Get filtered jadwal
        $jadwalQuery = DB::table('jadwal_pelajaran')
            ->join('kode_pembelajaran', 'jadwal_pelajaran.id_kodepembelajaran', '=', 'kode_pembelajaran.id_kodepembelajaran')
            ->join('pelajaran', 'kode_pembelajaran.id_pelajaran', '=', 'pelajaran.id_pelajaran')
            ->join('guru', 'kode_pembelajaran.id_guru', '=', 'guru.id_guru')
            ->join('kelas', 'jadwal_pelajaran.id_kelas', '=', 'kelas.id_kelas')
            ->join('jurusan', 'kelas.id_jurusan', '=', 'jurusan.id_jurusan')
            ->select(
                'jadwal_pelajaran.id_hari',
                'jadwal_pelajaran.id_jam_pelajaran',
                'pelajaran.nama_pelajaran',
                'guru.nama_guru',
                'kelas.jenjang',
                'kelas.nama_kelas',
                'jurusan.nama_jurusan'
            );

        if (!empty($savedFilters['hari'])) {
            $jadwalQuery->where('jadwal_pelajaran.id_hari', $savedFilters['hari']);
        }
        if (!empty($savedFilters['mapel'])) {
            $jadwalQuery->where('pelajaran.id_pelajaran', $savedFilters['mapel']);
        }
        if (!empty($savedFilters['guru'])) {
            $jadwalQuery->where('guru.id_guru', $savedFilters['guru']);
        }
        if (!empty($savedFilters['kelas'])) {
            $jadwalQuery->where('kelas.id_kelas', $savedFilters['kelas']);
        }

        $jadwalPelajaran = $jadwalQuery
            ->orderBy('jadwal_pelajaran.id_hari')
            ->orderBy('jadwal_pelajaran.id_jam_pelajaran')
            ->get();

        // Get pelanggaran data for the chart
        $pelanggaranData = $this->getPelanggaranData();

        return view('dasboard', compact(
            'jumlahSiswa',
            'jumlahGuru',
            'jumlahKelas',
            'jumlahUser',
            'jumlahMapel',
            'jumlahJurusan',
            'jurusanLabels',
            'jurusanData',
            'kelasLabels',
            'kelasData',
            'mapelList',
            'guruList',
            'kelasList',
            'jadwalPelajaran',
            'savedFilters',
            'kelas',
            'pelanggaranData'
        ));
    }

    private function getPelanggaranData($idKelas = null)
    {
        try {
            $query = DB::table('monitoring_siswa.pelanggaran')
                ->join('monitoring_siswa.list_pelanggaran', 'monitoring_siswa.pelanggaran.id_listpelanggaran', '=', 'monitoring_siswa.list_pelanggaran.id_listpelanggaran')
                ->join('monitoring_siswa.bentuk_pelanggaran', 'monitoring_siswa.list_pelanggaran.id_bentukpelanggaran', '=', 'monitoring_siswa.bentuk_pelanggaran.id_bentukpelanggaran')
                ->join('monitoring_siswa.siswa', 'monitoring_siswa.pelanggaran.id_siswa', '=', 'monitoring_siswa.siswa.id_siswa');

            if ($idKelas) {
                $query->where('monitoring_siswa.siswa.id_kelas', $idKelas);
            }

            $result = $query
                ->select('monitoring_siswa.bentuk_pelanggaran.nama_bentuk_pelanggaran', DB::raw('COUNT(*) as jumlah'))
                ->groupBy('monitoring_siswa.bentuk_pelanggaran.nama_bentuk_pelanggaran')
                ->get();

            return [
                'labels' => $result->pluck('nama_bentuk_pelanggaran')->toArray(),
                'data' => $result->pluck('jumlah')->toArray()
            ];
        } catch (\Exception $e) {
            return [
                'labels' => [],
                'data' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    public function updateFilter(Request $request)
    {
        $filters = [
            'hari' => $request->input('hari'),
            'mapel' => $request->input('mapel'),
            'guru' => $request->input('guru'),
            'kelas' => $request->input('kelas')
        ];

        // Store filters in session
        session(['jadwal_filters' => $filters]);

        return response()->json(['success' => true]);
    }

    public function chartPelanggaran(Request $request)
    {
        try {
            $idKelas = $request->id_kelas;
            $data = $this->getPelanggaranData($idKelas);

            return response()->json([
                'success' => true,
                'labels' => $data['labels'],
                'data' => $data['data']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
