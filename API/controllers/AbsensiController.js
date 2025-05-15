const Absensi = require('../models/Absensi');
const LokasiAbsensi = require('../models/LokasiAbsensi');
const { KodePembelajaran, Pelajaran, JadwalPelajaran } = require('../models'); 
const jwt = require('jsonwebtoken');
const fs = require('fs');
const path = require('path');
const multer = require('multer');

// Konfigurasi penyimpanan untuk multer
const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    const uploadDir = path.join(__dirname, '../public/uploads/absensi');
    
    // Cek apakah direktori ada, jika tidak ada maka buat
    if (!fs.existsSync(uploadDir)) {
      fs.mkdirSync(uploadDir, { recursive: true });
    }
    cb(null, uploadDir);
  },
  filename: function (req, file, cb) {
    // Membuat nama file unik dengan timestamp
    const uniqueSuffix = Date.now() + '-' + Math.round(Math.random() * 1E9);
    const ext = path.extname(file.originalname);
    cb(null, 'absensi-' + uniqueSuffix + ext);
  }
});

// Filter untuk memastikan hanya file gambar yang di-upload
const fileFilter = (req, file, cb) => {
  if (file.mimetype.startsWith('image/')) {
    cb(null, true);
  } else {
    cb(new Error('Hanya file gambar yang diperbolehkan!'), false);
  }
};

// Inisialisasi multer
const upload = multer({ 
  storage: storage,
  fileFilter: fileFilter,
  limits: {
    fileSize: 5 * 1024 * 1024 // Batas ukuran file (5MB)
  }
}).single('foto_absen');

// Fungsi untuk menghitung jarak antara dua koordinat
function hitungJarak(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radius bumi dalam km
    const dLat = (lat2 - lat1) * (Math.PI / 180);
    const dLon = (lon2 - lon1) * (Math.PI / 180);
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c * 1000; // Jarak dalam meter
}

// ✅ GET Absensi by ID Siswa
exports.getAbsensiByToken = async (req, res) => {
    try {
        // Ambil token dari header
        const token = req.header("Authorization")?.replace("Bearer ", "");
        if (!token) {
            return res.status(401).json({ status: "error", message: "Token tidak ditemukan" });
        }

        // Decode token
        const decoded = jwt.verify(token, "secret_key");
        const id_siswa = decoded.id_siswa;

        // Ambil data absensi
        const absensi = await Absensi.findAll({
            where: { id_siswa },
            attributes: [
                'id_absensi',
                'id_siswa',
                'id_kodepembelajaran',
                'waktu_absen',
                'status',
                'batas_waktu_absen',
                'foto_absen' // ✅ Tambahkan atribut foto_absen
            ],
            include: [
                {
                    model: KodePembelajaran,
                    as: "kode_pembelajaran",
                    attributes: ["id_pelajaran"],
                    include: [
                        {
                            model: Pelajaran,
                            as: "Pelajaran",
                            attributes: ["nama_pelajaran"]
                        }
                    ]
                }
            ]
        });

        if (!absensi.length) {
            return res.status(404).json({ status: "error", message: "Data absensi tidak ditemukan" });
        }

        // Buat URL lengkap untuk foto absensi
        const baseUrl = `${req.protocol}://${req.get('host')}`;
        
        // ✅ Pastikan batas_waktu_absen tetap ada dalam response
        const formattedAbsensi = absensi.map(item => ({
            id_absensi: item.id_absensi,
            id_siswa: item.id_siswa,
            id_kodepembelajaran: item.id_kodepembelajaran,
            waktu_absen: item.waktu_absen,
            status: item.status,
            batas_waktu_absen: item.batas_waktu_absen || "Tidak ada batas waktu", // ✅ Jika null, berikan default value
            nama_pelajaran: item.kode_pembelajaran?.Pelajaran?.nama_pelajaran || "Tidak diketahui",
            foto_absen: item.foto_absen ? `${baseUrl}/uploads/absensi/${item.foto_absen}` : null
        }));

        res.status(200).json({ status: "success", data: formattedAbsensi });

    } catch (error) {
        res.status(500).json({ status: "error", message: "Terjadi kesalahan pada server", error: error.message });
    }
};

// ✅ PUT Update Absensi dengan Upload Gambar
exports.updateAbsensi = async (req, res) => {
    try {
        // Handle upload file dengan multer
        upload(req, res, async function(err) {
            if (err instanceof multer.MulterError) {
                return res.status(400).json({ status: "error", message: "Kesalahan saat upload file", error: err.message });
            } else if (err) {
                return res.status(400).json({ status: "error", message: err.message });
            }
            
            // Proses data setelah upload berhasil
            const { id_siswa, id_kodepembelajaran, waktu_absen, latitude, longitude, status, keterangan_absen } = req.body;

            console.log("Body request:", req.body);
            console.log("File:", req.file);

            // Cek apakah absensi tersedia
            const absensi = await Absensi.findOne({ where: { id_siswa, id_kodepembelajaran } });
            if (!absensi) {
                // Hapus file jika absensi tidak ditemukan
                if (req.file) {
                    fs.unlinkSync(req.file.path);
                }
                return res.status(404).json({ status: "error", message: "Data absensi tidak ditemukan" });
            }

            // Ambil lokasi sekolah yang aktif
            const lokasi = await LokasiAbsensi.findOne({ where: { status: 'aktif' } });
            if (!lokasi) {
                // Hapus file jika lokasi tidak tersedia
                if (req.file) {
                    fs.unlinkSync(req.file.path);
                }
                return res.status(400).json({ status: "error", message: "Lokasi absensi tidak tersedia" });
            }

            // Hitung jarak siswa ke lokasi sekolah
            const jarak = hitungJarak(latitude, longitude, parseFloat(lokasi.latitude), parseFloat(lokasi.longitude));

            if (jarak > lokasi.radius) {
                // Hapus file jika siswa diluar radius
                if (req.file) {
                    fs.unlinkSync(req.file.path);
                }
                return res.status(400).json({ status: "error", message: "Anda tidak berada di radius sekolah" });
            }

            // Hapus foto lama jika ada dan jika ada foto baru
            if (absensi.foto_absen && req.file) {
                try {
                    // Pastikan foto_absen adalah string
                    if (typeof absensi.foto_absen === 'string') {
                        const oldFilePath = path.join(__dirname, '../public/uploads/absensi', absensi.foto_absen);
                        console.log("Mencoba menghapus file:", oldFilePath);
                        
                        if (fs.existsSync(oldFilePath)) {
                            fs.unlinkSync(oldFilePath);
                            console.log("File lama berhasil dihapus");
                        } else {
                            console.log("File lama tidak ditemukan");
                        }
                    } else {
                        console.log("foto_absen bukan string:", typeof absensi.foto_absen);
                    }
                } catch (err) {
                    console.error("Error saat menghapus file lama:", err);
                    // Lanjutkan proses meskipun ada error saat menghapus
                }
            }

            // Update absensi
            await Absensi.update(
                {
                    waktu_absen: waktu_absen || new Date(),
                    foto_absen: req.file ? req.file.filename : absensi.foto_absen,
                    status,
                    keterangan_absen
                },
                { where: { id_siswa, id_kodepembelajaran } }
            );

            res.status(200).json({ 
                status: "success", 
                message: "Absensi berhasil diperbarui",
                data: {
                    foto_absen: req.file ? req.file.filename : absensi.foto_absen,
                    foto_url: req.file ? `${req.protocol}://${req.get('host')}/uploads/absensi/${req.file.filename}` : null
                }
            });
        });
    } catch (error) {
        console.error("Error:", error);
        res.status(500).json({ status: "error", message: "Terjadi kesalahan pada server", error: error.message });
    }
};