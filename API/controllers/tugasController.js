const Tugas = require('../models/Tugas');
const path = require('path');
const fs = require('fs');

// GET: Ambil Tugas Berdasarkan ID Kelas + Tambahkan URL File
exports.getTugasByIdKelas = async (req, res) => {
    try {
        const { id_kelas } = req.params;
        const tugas = await Tugas.findAll({
            where: { id_kelas }
        });

        if (tugas.length === 0) {
            return res.status(404).json({
                status: "error",
                message: "Tugas tidak ditemukan untuk ID Kelas tersebut."
            });
        }

        const host = req.get('host');
        const protocol = req.protocol;
        const baseUrl = `${protocol}://${host}`;

        const transformedTugas = tugas.map(item => {
            const data = item.get({ plain: true });

            if (data.file_tugas) {
                data.file_url = `${baseUrl}/tugas/files/${data.id_tugas}`;
            } else {
                data.file_url = null;
            }

            return data;
        });

        res.status(200).json({
            status: "success",
            data: transformedTugas
        });
    } catch (error) {
        res.status(500).json({
            message: "Terjadi kesalahan server.",
            error: error.message
        });
    }
};

// GET: Serve File PDF Tugas
const mime = require('mime-types'); // Tambahkan di atas (install via: npm install mime-types)

exports.getFileTugas = async (req, res) => {
    try {
        const id = req.params.id;
        const tugas = await Tugas.findByPk(id);

        if (!tugas) {
            return res.status(404).json({
                status: "error",
                message: "Tugas tidak ditemukan"
            });
        }

        if (!tugas.file_tugas) {
            return res.status(404).json({
                status: "error",
                message: "File tugas tidak tersedia"
            });
        }

        const fileName = path.basename(tugas.file_tugas);

        const possiblePaths = [
            path.join(__dirname, '../../storage/app/public/uploads/tugas', fileName),
            path.join(__dirname, '../../../storage/app/public/uploads/tugas', fileName),
            path.join(__dirname, '../storage/app/public/uploads/tugas', fileName),
            path.join(process.cwd(), 'storage/app/public/uploads/tugas', fileName),
            path.join(process.cwd(), '../storage/app/public/uploads/tugas', fileName),
            "D:/Tugas Akhir Website/SistemPemantauan/storage/app/public/uploads/tugas/" + fileName
        ];

        let filePath = null;
        for (const pathToCheck of possiblePaths) {
            if (fs.existsSync(pathToCheck)) {
                filePath = pathToCheck;
                break;
            }
        }

        if (filePath) {
            // Deteksi tipe file secara otomatis
            const contentType = mime.lookup(filePath) || 'application/octet-stream';

            res.setHeader('Content-Type', contentType);
            res.setHeader('Content-Disposition', `inline; filename="${fileName}"`);

            fs.createReadStream(filePath).pipe(res);
        } else {
            return res.status(404).json({
                status: "error",
                message: "File tidak ditemukan di server",
                debug: {
                    checkedPaths: possiblePaths
                }
            });
        }
    } catch (error) {
        res.status(500).json({
            status: "error",
            message: "Terjadi kesalahan saat mengakses file",
            error: error.message
        });
    }
};

