const { DataTypes } = require('sequelize');
const db = require('../config/database');

const Tugas = db.define('tugas', {
    id_tugas: {
        type: DataTypes.INTEGER,
        primaryKey: true,
        autoIncrement: true
    },
    id_guru: {
        type: DataTypes.INTEGER,
        allowNull: false
    },
    id_kelas: {
        type: DataTypes.INTEGER,
        allowNull: false
    },
    judul_tugas: {
        type: DataTypes.STRING,
        allowNull: false
    },
    deskripsi: {
        type: DataTypes.TEXT,
        allowNull: true
    },
    deadline: {
        type: DataTypes.DATE,
        allowNull: false
    },
    file_tugas: {
        type: DataTypes.STRING,
        allowNull: true
    },
    link_tugas: {
        type: DataTypes.STRING,
        allowNull: true
    }
}, {
    tableName: 'tugas',
    timestamps: false
});

module.exports = Tugas;
