const jwt = require('jsonwebtoken');

const authMiddleware = (req, res, next) => {
    const token = req.header('Authorization');
    
    if (!token) {
        return res.status(401).json({ message: 'Akses ditolak! Token tidak ditemukan' });
    }

    try {
        const decoded = jwt.verify(token.replace('Bearer ', ''), 'secret_key');
        req.user = decoded; // Simpan informasi user dari token
        next();
    } catch (error) {
        res.status(401).json({ message: 'Token tidak valid' });
    }
};

module.exports = authMiddleware;
