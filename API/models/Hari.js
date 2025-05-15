module.exports = (sequelize, DataTypes) => {
    const Hari = sequelize.define('Hari', {
        id_hari: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        nama_hari: DataTypes.STRING
    }, {
        tableName: 'hari',
        timestamps: false
    });
    return Hari;
};
