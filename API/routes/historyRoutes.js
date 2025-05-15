const express = require('express');
const router = express.Router();
const { getHistoryAbsensiByToken } = require('../controllers/HistoryAbsensiController');
const authMiddleware = require('../middleware/authMiddleware');


router.get('/history', authMiddleware, getHistoryAbsensiByToken);


module.exports = router;
