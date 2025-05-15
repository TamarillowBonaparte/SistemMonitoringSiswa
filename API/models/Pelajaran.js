module.exports = (sequelize, DataTypes) => {
    const Pelajaran = sequelize.define('Pelajaran', {
        id_pelajaran: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        nama_pelajaran: DataTypes.STRING
    }, {
        tableName: 'pelajaran',
        timestamps: false
    });
    return Pelajaran;
};
