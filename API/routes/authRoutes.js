const express = require('express');
const router = express.Router();
const jwt = require('jsonwebtoken');
const User = require('../models/User');
const Siswa = require('../models/Siswa');

router.post('/login', async (req, res) => {
    try {
        const { username, password } = req.body; 

        console.log("Request body:", req.body); 

        // Cari user berdasarkan username
        const user = await User.findOne({ where: { username } });

        if (!user || user.password !== password) {
            return res.status(401).json({
                status: "error",
                message: "Username atau password salah"
            });
        }

        let siswa;
        
        // Cek apakah username diawali dengan "08" (nomor orangtua)
        if (username.startsWith('08')) {
            // Jika username diawali 08, cari siswa berdasarkan no_orangtua
            siswa = await Siswa.findOne({ where: { no_orangtua: username } });
            console.log('Mencari siswa dengan no_orangtua:', username);
        } else {
            // Jika tidak, cari siswa berdasarkan NISN
            siswa = await Siswa.findOne({ where: { nisn: username } });
            console.log('Mencari siswa dengan NISN:', username);
        }

        if (!siswa) {
            return res.status(404).json({
                status: "error",
                message: "Data siswa tidak ditemukan"
            });
        }

        // Generate token
        const token = jwt.sign({ id_siswa: siswa.id_siswa }, 'secret_key');

        res.json({
            status: "success",
            message: "Login successfully",
            token: token
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