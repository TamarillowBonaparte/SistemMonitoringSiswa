const { DataTypes } = require('sequelize');
const db = require('../config/database');
const ListPelanggaran = require('./ListPelanggaran'); // Import model ListPelanggaran

const Pelanggaran = db.define('Pelanggaran', {
    id_pelanggaran: {
        type: DataTypes.INTEGER,
        primaryKey: true,
        autoIncrement: true
    },
    id_siswa: {
        type: DataTypes.INTEGER,
        allowNull: false
    },
    id_listpelanggaran: {
        type: DataTypes.INTEGER,
        allowNull: false
    },
    tanggal: {
        type: DataTypes.DATE,
        allowNull: false
    }
}, {
    tableName: 'pelanggaran',
    timestamps: false
});

// Relasi ke tabel ListPelanggaran
Pelanggaran.belongsTo(ListPelanggaran, { 
    foreignKey: 'id_listpelanggaran',
    as: 'list_pelanggaran'
});

module.exports = Pelanggaran;
