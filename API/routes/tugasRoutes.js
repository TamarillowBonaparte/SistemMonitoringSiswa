const express = require('express');
const router = express.Router();
const TugasController = require('../controllers/tugasController');

// GET tugas berdasarkan ID kelas
router.get('/:id_kelas', TugasController.getTugasByIdKelas);

// GET file tugas
router.get('/files/:id', TugasController.getFileTugas);

module.exports = router;
