module.exports = (sequelize, DataTypes) => {
    const KodePembelajaran = sequelize.define('KodePembelajaran', {
        id_kodepembelajaran: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        id_pelajaran: DataTypes.INTEGER,
        id_guru: DataTypes.INTEGER
    }, {
        tableName: 'kode_pembelajaran',
        timestamps: false
    });
    return KodePembelajaran;
};
