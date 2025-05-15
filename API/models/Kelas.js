const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const Kelas = sequelize.define('Kelas', {
    id_kelas: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    nama_kelas: { type: DataTypes.STRING },
    jenjang: { type: DataTypes.STRING }
});

module.exports = Kelas;
