const { DataTypes } = require('sequelize');
const db = require('../config/database');

const LokasiAbsensi = db.define('lokasi_absensi', {
    id_lokasi: {
        type: DataTypes.INTEGER,
        primaryKey: true,
        autoIncrement: true
    },
    nama_lokasi: {
        type: DataTypes.STRING,
        allowNull: false
    },
    latitude: {
        type: DataTypes.DECIMAL(10, 8),
        allowNull: false
    },
    longitude: {
        type: DataTypes.DECIMAL(11, 8),
        allowNull: false
    },
    radius: {
        type: DataTypes.INTEGER,
        allowNull: false
    },
    status: {
        type: DataTypes.ENUM('aktif', 'nonaktif'),
        defaultValue: 'aktif'
    }
}, {
    tableName: 'lokasi_absensi',
    timestamps: false
});

module.exports = LokasiAbsensi;
