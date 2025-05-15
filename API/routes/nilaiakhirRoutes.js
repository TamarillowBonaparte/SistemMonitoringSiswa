const express = require('express');
const router = express.Router();
const { getNilaiAkhir } = require('../controllers/nilaiakhirController');
const authMiddleware = require('../middleware/authMiddleware');

// Route untuk mengambil Nilai Akhir
router.get('/', authMiddleware, getNilaiAkhir);

module.exports = router;
