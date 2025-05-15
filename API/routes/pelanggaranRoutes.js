const express = require('express');
const router = express.Router();
const Pelanggaran = require('../models/Pelanggaran');
const ListPelanggaran = require('../models/ListPelanggaran');
const authMiddleware = require('../middleware/authMiddleware');

router.get('/', authMiddleware, async (req, res) => {
    try {
        // Ambil pelanggaran berdasarkan siswa yang login
        const pelanggaran = await Pelanggaran.findAll({
            where: { id_siswa: req.user.id_siswa },
            include: [{
                model: ListPelanggaran,
                as: 'list_pelanggaran', 
                attributes: ['id_listpelanggaran', 'nama_pelanggaran', 'skor']
            }],
            attributes: ['id_pelanggaran', 'tanggal'] 
        });

        if (!pelanggaran || pelanggaran.length === 0) {
            return res.status(404).json({ 
                status: "error",
                message: "Tidak ada pelanggaran yang ditemukan untuk siswa ini." 
            });
        }

        // Mapping data agar list_pelanggaran keluar dari objek list_pelanggaran
        const hasil = pelanggaran.map(item => ({
            id_pelanggaran: item.id_pelanggaran,
            tanggal: item.tanggal,
            id_listpelanggaran: item.list_pelanggaran?.id_listpelanggaran,
            nama_pelanggaran: item.list_pelanggaran?.nama_pelanggaran,
            skor: item.list_pelanggaran?.skor
        }));

        res.json({
            status: "success",
            message: "Pelanggaran ditemukan",
            data: hasil
        });
    } catch (error) {
        res.status(500).json({
            status: "error",
            message: "Terjadi kesalahan pada server",
            error: error.message
        });
    }
});

module.exports = router;
