const jwt = require('jsonwebtoken');
const User = require('../models/User');
const Siswa = require('../models/Siswa');

exports.login = async (req, res) => {
    try {
        console.log('Request body:', req.body); // Debug request

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
};