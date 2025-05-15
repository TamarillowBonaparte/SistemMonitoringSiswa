const { Ujian, Pelajaran } = require('../models');

// Fungsi untuk mengubah tanggal menjadi nama hari
function getDayName(dateString) {
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const date = new Date(dateString);
    return days[date.getDay()];
}

exports.getUjianByKelas = async (req, res) => {
    try {
        const { id_kelas } = req.params;

        const ujian = await Ujian.findAll({
            where: { id_kelas },
            include: [
                {
                    model: Pelajaran,
                    as: 'pelajaran',
                    attributes: ['nama_pelajaran']
                }
            ]
        });

        const formattedUjian = ujian.map(u => ({
            id_ujian: u.id_ujian,
            id_pelajaran: u.id_pelajaran,
            nama_pelajaran: u.pelajaran ? u.pelajaran.nama_pelajaran : null,
            id_kelas: u.id_kelas,
            jenis_ujian: u.jenis_ujian,
            tanggal_ujian: u.tanggal_ujian,
            hari: getDayName(u.tanggal_ujian), // Menambahkan nama hari
            jam_mulai: u.jam_mulai,
            jam_selesai: u.jam_selesai,
            ruang_ujian: u.ruang_ujian,
            keterangan: u.keterangan
        }));

        if (formattedUjian.length === 0) {
            return res.status(404).json({
                status: "error",
                message: "Tidak ada ujian ditemukan untuk kelas ini."
            });
        }

        res.json({
            status: "success",
            message: "Ujian ditemukan",
            data: formattedUjian
        });
    } catch (error) {
        res.status(500).json({
            status: "error",
            message: "Terjadi kesalahan pada server",
            error: error.message
        });
    }
};
