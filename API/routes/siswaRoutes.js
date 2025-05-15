const express = require('express');
const path = require('path');
const fs = require('fs');
const router = express.Router();
const Siswa = require('../models/Siswa');
const Kelas = require('../models/Kelas');
const User = require('../models/User');
const jwt = require('jsonwebtoken');
const { Op } = require('sequelize');
const authMiddleware = require('../middleware/authMiddleware');

// Path tempat foto disimpan di Laravel (Ganti sesuai dengan lokasi sebenarnya)
const LARAVEL_STORAGE_PATH = "D:/Tugas Akhir Website/SistemPemantauan/public/uploads/siswa/";
const BASE_IMAGE_URL = "http://localhost:5000/uploads/siswa/";

// Endpoint login (Baru)
router.post('/login', async (req, res) => {
    try {
        console.log('Request body:', req.body);

        const { username, password } = req.body;

        // Cari user berdasarkan username
        const user = await User.findOne({ where: { username } });

        console.log('User ditemukan:', user);

        if (!user) {
            return res.status(401).json({ message: 'Username tidak ditemukan di database' });
        }

        console.log('Password di database:', user.password);
        console.log('Password dari request:', password);

        // Cek apakah password sesuai
        if (user.password !== password) {
            return res.status(401).json({ message: 'Password salah' });
        }

        // Coba cari siswa berdasarkan NISN (username) atau no_orangtua
        const siswa = await Siswa.findOne({ 
            where: { 
                [Op.or]: [
                    { nisn: username },
                    { no_orangtua: username }
                ] 
            } 
        });

        console.log('Siswa ditemukan:', siswa);

        if (!siswa) {
            return res.status(404).json({ message: 'Siswa tidak ditemukan' });
        }

        // Generate token
        const token = jwt.sign({ id_siswa: siswa.id_siswa }, 'secret_key');
        return res.json({ token });

    } catch (error) {
        console.error('Error:', error);
        return res.status(500).json({ message: 'Server error' });
    }
});

router.get('/', authMiddleware, async (req, res) => {
    try {
        // Cari siswa yang login dan sertakan data kelas
        const siswa = await Siswa.findOne({
            where: { id_siswa: req.user.id_siswa },
            include: [{
                model: Kelas,
                as: 'kelas', 
                attributes: ['id_kelas', 'nama_kelas', 'jenjang']
            }]
        });

        if (!siswa) {
            return res.status(404).json({ 
                status: "error",
                message: "Siswa tidak ditemukan" 
            });
        }

        // Cek apakah file foto ada
        let fotoSiswaUrl = null;
        if (siswa.foto_siswa) {
            const filePath = path.join(LARAVEL_STORAGE_PATH, siswa.foto_siswa);
            if (fs.existsSync(filePath)) {
                fotoSiswaUrl = `${BASE_IMAGE_URL}${siswa.foto_siswa}`;
            }
        }

        res.json({
            status: "success",
            message: "Profile retrieved successfully",
            data: {
                id_siswa: siswa.id_siswa,
                nama: siswa.nama_siswa,
                nisn: siswa.nisn,
                id_kelas: siswa.kelas ? siswa.kelas.id_kelas : null,
                kelas: siswa.kelas ? `${siswa.kelas.jenjang} - ${siswa.kelas.nama_kelas}` : null,
                foto: fotoSiswaUrl
            }
        });
    } catch (error) {
        res.status(500).json({ 
            status: "error",
            message: "Terjadi kesalahan pada server", 
            error: error.message 
        });
    }
});

// Middleware untuk menyajikan gambar langsung dari storage Laravel
router.use('/uploads/siswa', express.static(LARAVEL_STORAGE_PATH));

module.exports = router;