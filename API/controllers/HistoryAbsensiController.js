const jwt = require('jsonwebtoken');
const HistoryAbsensi = require('../models/history_absensi');
const { KodePembelajaran, Pelajaran } = require('../models');

exports.getHistoryAbsensiByToken = async (req, res) => {
  try {
    const token = req.header("Authorization")?.replace("Bearer ", "");
    if (!token) {
      return res.status(401).json({ status: "error", message: "Token tidak ditemukan" });
    }

    const decoded = jwt.verify(token, "secret_key");
    const id_siswa = decoded.id_siswa;

    const history = await HistoryAbsensi.findAll({
      where: { id_siswa },
      attributes: [
        'id_historyabsensi',
        'waktu_absen',
        'status',
        'keterangan_absen',
        'foto_absen',
        'id_kodepembelajaran'
      ],
      include: [
        {
          model: KodePembelajaran,
          as: "kode_pembelajaran_history",
          attributes: ['id_pelajaran'],
          include: [
            {
              model: Pelajaran,
              as: 'Pelajaran',
              attributes: ['nama_pelajaran']
            }
          ]
        }
      ]
    });

    if (!history.length) {
      return res.status(404).json({ status: "error", message: "Riwayat absensi tidak ditemukan" });
    }

    const baseUrl = `${req.protocol}://${req.get('host')}`;

    const formatted = history.map(item => ({
      id_historyabsensi: item.id_historyabsensi,
      waktu_absen: item.waktu_absen,
      status: item.status,
      keterangan_absen: item.keterangan_absen || "-",
      foto_absen: item.foto_absen ? `${baseUrl}/uploads/absensi/${item.foto_absen}` : null,
      nama_pelajaran: item.kode_pembelajaran_history?.Pelajaran?.nama_pelajaran || "Tidak diketahui"
    }));

    res.status(200).json({ status: "success", data: formatted });

  } catch (error) {
    res.status(500).json({ status: "error", message: "Terjadi kesalahan pada server", error: error.message });
  }
};
