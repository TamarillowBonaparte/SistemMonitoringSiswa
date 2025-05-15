const express = require('express');
const router = express.Router();
const { getUjianByKelas } = require('../controllers/UjianController');

router.get('/:id_kelas', getUjianByKelas);

module.exports = router;
