module.exports = (sequelize, DataTypes) => {
    const Guru = sequelize.define('Guru', {
        id_guru: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        nama_guru: DataTypes.STRING
    }, {
        tableName: 'guru',
        timestamps: false
    });
    return Guru;
};
