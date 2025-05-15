const express = require('express');
const router = express.Router();
//const { JadwalPelajaran, Hari, JamPelajaran, KodePembelajaran, Pelajaran, Guru } = require('../models');

const { JadwalPelajaran, Hari, JamPelajaran, KodePembelajaran, Pelajaran, Guru } = require('../models/index');

router.get('/jadwal/:id_kelas', async (req, res) => {
    const { id_kelas } = req.params;

    try {
        const jadwal = await JadwalPelajaran.findAll({
            where: { id_kelas: id_kelas }, // Filter berdasarkan id_kelas
            include: [
                { model: Hari, as: 'Hari' },
                { model: JamPelajaran, as: 'JamPelajaran' },
                { 
                    model: KodePembelajaran, 
                    as: 'KodePembelajaran',
                    include: [
                        { model: Pelajaran, as: 'Pelajaran' },
                        { model: Guru, as: 'Guru' }
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
        res.status(500).json({
            status: 'error',
            message: 'Terjadi kesalahan saat mengambil jadwal',
            error: error.message
        });
    }
});

module.exports = router;
