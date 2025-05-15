module.exports = (sequelize, DataTypes) => {
    const JadwalPelajaran = sequelize.define('JadwalPelajaran', {
        id_jadwal: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        id_kelas: DataTypes.INTEGER,
        id_hari: DataTypes.INTEGER,
        id_jam_pelajaran: DataTypes.INTEGER,
        id_kodepembelajaran: DataTypes.INTEGER
    }, {
        tableName: 'jadwal_pelajaran',
        timestamps: false
    });
    return JadwalPelajaran;
};
