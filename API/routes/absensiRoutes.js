const express = require('express');
const router = express.Router();
const AbsensiController = require('../controllers/AbsensiController');
const authMiddleware = require("../middleware/authMiddleware");

// router.get('/absensi/:id_siswa', AbsensiController.getAbsensiById);

router.get("/absensi", authMiddleware, AbsensiController.getAbsensiByToken);
// router.put('/absensi', AbsensiController.updateAbsensi);
router.put('/absensi', authMiddleware, AbsensiController.updateAbsensi);

module.exports = router;
