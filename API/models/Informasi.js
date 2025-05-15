const { DataTypes } = require('sequelize');
const db = require('../config/database');

const Informasi = db.define('informasi', {
    id_informasi: {
        type: DataTypes.INTEGER,
        primaryKey: true,
        autoIncrement: true
    },
    judul: {
        type: DataTypes.STRING(255),
        allowNull: false
    },
    isi: {
        type: DataTypes.STRING(255),
        allowNull: false
    },
    tanggal: {
        type: DataTypes.DATE,
        allowNull: false
    },
    file_pdf: {
        type: DataTypes.STRING(255),
        allowNull: true
    }
}, {
    tableName: 'informasi',
    timestamps: false
});

module.exports = Informasi;
