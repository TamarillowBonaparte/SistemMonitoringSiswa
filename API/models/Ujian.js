module.exports = (sequelize, DataTypes) => {
    const Ujian = sequelize.define('Ujian', {
        id_ujian: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        id_pelajaran: DataTypes.INTEGER,
        id_kelas: DataTypes.INTEGER,
        jenis_ujian: DataTypes.STRING,
        tanggal_ujian: DataTypes.DATE,
        jam_mulai: DataTypes.TIME,
        jam_selesai: DataTypes.TIME,
        ruang_ujian: DataTypes.STRING,
        keterangan: DataTypes.TEXT
    }, {
        tableName: 'ujian',
        timestamps: false
    });

    Ujian.associate = models => {
        Ujian.belongsTo(models.Pelajaran, {
            foreignKey: 'id_pelajaran',
            as: 'pelajaran'
        });
    };

    return Ujian;
};
