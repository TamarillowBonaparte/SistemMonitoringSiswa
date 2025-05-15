const Siswa = require('../models/Siswa');

exports.getSiswa = async (req, res) => {
    try {
        const siswa = await Siswa.findByPk(req.user.id_siswa);
        if (!siswa) return res.status(404).json({ message: 'Siswa not found' });
        res.json(siswa);
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
};