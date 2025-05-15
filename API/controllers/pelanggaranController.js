// controllers/pelanggaranController.js

const Pelanggaran = require('../models/Pelanggaran');
const Siswa = require('../models/Siswa');
const ListPelanggaran = require('../models/ListPelanggaran');

exports.getPelanggaranByKelas = async (req, res) => {
    const { id_kelas } = req.params;

    try {
        const pelanggarans = await Pelanggaran.findAll({
            include: [
                {
                    model: Siswa,
                    as: 'siswa',
                    where: { id_kelas },
                    attributes: ['id_siswa', 'nama_siswa']
                },
                {
                    model: ListPelanggaran,
                    as: 'listPelanggaran', // Ganti dari 'list_pelanggaran' menjadi 'listPelanggaran'
                    attributes: ['nama_pelanggaran', 'skor']
                }
            ]
        });

        res.json({
            status: "success",
            message: "Data pelanggaran berhasil diambil",
            data: pelanggarans
        });
    } catch (error) {
        res.status(500).json({
            status: "error",
            message: "Terjadi kesalahan pada server",
            error: error.message
        });
    }
};
