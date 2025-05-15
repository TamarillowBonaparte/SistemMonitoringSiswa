const { NilaiAkhir, KodePembelajaran, Guru, Pelajaran } = require('../models');

// Fungsi untuk mengambil data Nilai Akhir berdasarkan id_siswa yang login
const getNilaiAkhir = async (req, res) => {
    try {
        const id_siswa = req.user.id_siswa;

        const nilaiAkhir = await NilaiAkhir.findAll({
            where: { id_siswa },
            include: [
                {
                    model: KodePembelajaran,
                    as: 'kode_pembelajaran_nilai',
                    attributes: ['id_kodepembelajaran', 'kode_mapel', 'id_pelajaran'],
                    include: [
                        {
                            model: Guru,
                            as: 'Guru',
                            attributes: ['nama_guru']
                        },
                        {
                            model: Pelajaran,
                            as: 'Pelajaran',
                            attributes: ['nama_pelajaran']
                        }
                    ]
                }
            ]
        });

        if (nilaiAkhir.length === 0) {
            return res.status(404).json({
                status: "error",
                message: "Nilai akhir tidak ditemukan"
            });
        }

        res.json({
            status: "success",
            message: "Nilai akhir retrieved successfully",
            data: nilaiAkhir
        });
    } catch (error) {
        res.status(500).json({
            status: "error",
            message: "Terjadi kesalahan pada server",
            error: error.message
        });
    }
};

module.exports = { getNilaiAkhir };
