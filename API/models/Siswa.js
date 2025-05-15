const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');
const Kelas = require('./Kelas');

const Siswa = sequelize.define('siswa', {
    id_siswa: { type: DataTypes.INTEGER, primaryKey: true },
    nama_siswa: DataTypes.STRING,
    nisn: DataTypes.STRING,
    id_kelas: DataTypes.INTEGER,
    no_orangtua: DataTypes.STRING,
    
}, { tableName: 'siswa', timestamps: false });

Siswa.belongsTo(Kelas, { foreignKey: 'id_kelas', as: 'kelas' });
module.exports = Siswa;
