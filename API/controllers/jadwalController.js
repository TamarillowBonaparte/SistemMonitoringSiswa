const { JadwalPelajaran, Hari, JamPelajaran, KodePembelajaran, Pelajaran, Guru } = require('../models');

exports.getJadwalByKelas = async (req, res) => {
    try {
        const idKelas = req.params.id_kelas;
        
        const jadwal = await JadwalPelajaran.findAll({
            where: { id_kelas: idKelas },
            include: [
                { model: Hari, as: 'Hari', attributes: ['id_hari', 'nama_hari'] },
                { model: JamPelajaran, as: 'JamPelajaran', attributes: ['id_jam_pelajaran', 'jamke', 'jam_range'] },
                { 
                    model: KodePembelajaran,
                    as: 'KodePembelajaran',
                    include: [
                        { model: Pelajaran, as: 'Pelajaran', attributes: ['id_pelajaran', 'nama_pelajaran'] },
                        { model: Guru, as: 'Guru', attributes: ['id_guru', 'nama_guru'] }
                    ]
                }
            ]
        });

        res.json({
            status: 'success',
            message: 'Data jadwal berhasil diambil',
            data: jadwal
        });
    } catch (error) {
        res.json({
            status: 'error',
            message: 'Terjadi kesalahan saat mengambil jadwal',
            error: error.message
        });
    }
};
