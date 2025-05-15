const { Sequelize, DataTypes } = require('sequelize');
const sequelize = new Sequelize('monitoring_siswa', 'root', '', {
    host: 'localhost',
    dialect: 'mysql'
});

// Import semua model
const JadwalPelajaran = require('./JadwalPelajaran')(sequelize, DataTypes);
const Hari = require('./Hari')(sequelize, DataTypes);
const JamPelajaran = require('./JamPelajaran')(sequelize, DataTypes);
const KodePembelajaran = require('./KodePembelajaran')(sequelize, DataTypes);
const Pelajaran = require('./Pelajaran')(sequelize, DataTypes);
const Guru = require('./Guru')(sequelize, DataTypes);
const Ujian = require('./Ujian')(sequelize, DataTypes);
const NilaiAkhir = require('./NilaiAkhir')(sequelize, DataTypes);
const Absensi = require('./Absensi');
const HistoryAbsensi = require('./history_absensi');


// Relasi Model dengan Alias yang Sesuai
JadwalPelajaran.belongsTo(Hari, { foreignKey: 'id_hari', as: 'Hari' });
Hari.hasMany(JadwalPelajaran, { foreignKey: 'id_hari', as: 'JadwalPelajaran' });

JadwalPelajaran.belongsTo(JamPelajaran, { foreignKey: 'id_jam_pelajaran', as: 'JamPelajaran' });
JamPelajaran.hasMany(JadwalPelajaran, { foreignKey: 'id_jam_pelajaran', as: 'JadwalPelajaran' });

JadwalPelajaran.belongsTo(KodePembelajaran, { foreignKey: 'id_kodepembelajaran', as: 'KodePembelajaran' });
KodePembelajaran.hasMany(JadwalPelajaran, { foreignKey: 'id_kodepembelajaran', as: 'JadwalPelajaran' });

KodePembelajaran.belongsTo(Pelajaran, { foreignKey: 'id_pelajaran', as: 'Pelajaran' });
Pelajaran.hasMany(KodePembelajaran, { foreignKey: 'id_pelajaran', as: 'KodePembelajaran' });

KodePembelajaran.belongsTo(Guru, { foreignKey: 'id_guru', as: 'Guru' });
Guru.hasMany(KodePembelajaran, { foreignKey: 'id_guru', as: 'KodePembelajaran' });

Ujian.belongsTo(Pelajaran, { foreignKey: 'id_pelajaran', as: 'pelajaran' });

// Perbaikan Relasi untuk NilaiAkhir
NilaiAkhir.belongsTo(KodePembelajaran, { foreignKey: 'id_kodepembelajaran', as: 'kode_pembelajaran_nilai' });

Absensi.belongsTo(KodePembelajaran, { foreignKey: "id_kodepembelajaran", as: "kode_pembelajaran" });

// HistoryAbsensi.belongsTo(KodePembelajaran, {
//     foreignKey: 'id_kodepembelajaran',
//     as: 'kode_pembelajaran_history'
// });

KodePembelajaran.hasMany(HistoryAbsensi, {
    foreignKey: 'id_kodepembelajaran',
    as: 'history_absensi'
});

HistoryAbsensi.belongsTo(KodePembelajaran, {
  foreignKey: 'id_kodepembelajaran',
  as: 'kode_pembelajaran_history'
});






// Ekspor semua model
module.exports = {
    sequelize,
    JadwalPelajaran,
    Hari,
    NilaiAkhir,
    JamPelajaran,
    KodePembelajaran,
    Pelajaran,
    Guru,
    Ujian,
    Absensi,
    HistoryAbsensi,
  
};
