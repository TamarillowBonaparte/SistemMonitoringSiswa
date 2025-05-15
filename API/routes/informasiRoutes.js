// routes/informasi.js
const express = require('express');
const router = express.Router();
const Informasi = require('../models/Informasi');
const path = require('path');
const fs = require('fs');

// Endpoint GET informasi
router.get('/', async (req, res) => {
    try {
        // Ambil semua data informasi dari database
        const informasi = await Informasi.findAll({
            order: [['tanggal', 'DESC']] // Urutkan berdasarkan tanggal terbaru
        });
        
        // Get the host from the request
        const host = req.get('host');
        const protocol = req.protocol;
        const baseUrl = `${protocol}://${host}`;
        
        // Transform the data to include file URL
        const transformedInformasi = informasi.map(item => {
            const data = item.get({ plain: true });
            
            // Add file URL if file_pdf exists and is not NULL
            if (data.file_pdf) {
                // Use our Express API endpoint for file download
                data.file_url = `${baseUrl}/api/informasi/files/${data.id_informasi}`;
            } else {
                data.file_url = null;
            }
            
            return data;
        });
        
        res.json({
            status: "success",
            message: "Informasi retrieved successfully",
            data: transformedInformasi
        });
    } catch (error) {
        console.error("Error retrieving informasi:", error);
        res.status(500).json({
            status: "error",
            message: "Terjadi kesalahan saat mengambil informasi",
            error: error.message
        });
    }
});

// File download endpoint - notice that this has the correct path
router.get('/files/:id', async (req, res) => {
    try {
        const id = req.params.id;
        console.log(`Attempting to retrieve file for informasi ID: ${id}`);
        
        const informasi = await Informasi.findByPk(id);
        
        if (!informasi) {
            return res.status(404).json({
                status: "error",
                message: "Informasi tidak ditemukan"
            });
        }
        
        if (!informasi.file_pdf) {
            return res.status(404).json({
                status: "error",
                message: "File PDF tidak tersedia"
            });
        }
        
        // Get the filename
        const fileName = informasi.file_pdf;
        console.log(`Looking for file: ${fileName}`);
        
        // List of possible storage paths - check all common locations
        const possiblePaths = [
            path.join(__dirname, '../../storage/app/public/upload/informasi', fileName),
            path.join(__dirname, '../../public/storage/upload/informasi', fileName),
            path.join(__dirname, '../../../storage/app/public/upload/informasi', fileName),
            path.join(__dirname, '../storage/app/public/upload/informasi', fileName),
            path.join(process.cwd(), 'storage/app/public/upload/informasi', fileName),
            path.join(process.cwd(), '../storage/app/public/upload/informasi', fileName),
        
            // Tambahkan path Laravel-mu yang sebenarnya
            "D:/Tugas Akhir Website/SistemPemantauan/storage/app/public/uploads/Informasi/" + fileName
        ];
        
        
        // Try each path until we find the file
        let filePath = null;
        for (const pathToCheck of possiblePaths) {
            console.log(`Checking path: ${pathToCheck}`);
            if (fs.existsSync(pathToCheck)) {
                filePath = pathToCheck;
                console.log(`File found at: ${filePath}`);
                break;
            }
        }
        
        if (filePath) {
            // Set appropriate headers
            res.setHeader('Content-Type', 'application/pdf');
            res.setHeader('Content-Disposition', `inline; filename="${fileName}"`);
            
            // Send the file
            fs.createReadStream(filePath).pipe(res);
        } else {
            // File not found in any location
            return res.status(404).json({
                status: "error",
                message: "File tidak ditemukan di server",
                debug: {
                    checkedPaths: possiblePaths
                }
            });
        }
    } catch (error) {
        console.error("Error serving file:", error);
        res.status(500).json({
            status: "error",
            message: "Terjadi kesalahan saat mengakses file",
            error: error.message
        });
    }
});

module.exports = router;