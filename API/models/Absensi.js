const { DataTypes } = require('sequelize');
const db = require('../config/database');


const Absensi = db.define('absensi', {
    id_absensi: {
        type: DataTypes.INTEGER,
        primaryKey: true,
        autoIncrement: true
    },
    id_siswa: {
        type: DataTypes.INTEGER,
        allowNull: false
    },
    id_kodepembelajaran: {
        type: DataTypes.INTEGER,
        allowNull: false
    },
    waktu_absen: {
        type: DataTypes.DATE,
        allowNull: true
    },
    foto_absen: {
        type: DataTypes.STRING,
        allowNull: true
    },
    status: {
        type: DataTypes.ENUM('menunggu guru', 'diterima', 'ditolak', 'belum absen'),
        defaultValue: 'belum absen'
    },
    keterangan_absen: {
        type: DataTypes.STRING,
        allowNull: true
    },
    batas_waktu_absen: {
        type: DataTypes.DATE,
        allowNull: true
    },
}, {
    tableName: 'absensi',
    timestamps: false
});

module.exports = Absensi;
