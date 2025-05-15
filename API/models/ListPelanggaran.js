const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const ListPelanggaran = sequelize.define('ListPelanggaran', {
    id_listpelanggaran: {
        type: DataTypes.INTEGER,
        autoIncrement: true,
        primaryKey: true
    },
    nama_pelanggaran: {
        type: DataTypes.STRING,
        allowNull: false
    },
    skor: {
        type: DataTypes.INTEGER,
        allowNull: false
    }
}, {
    tableName: 'list_pelanggaran',
    timestamps: false
});

module.exports = ListPelanggaran;
