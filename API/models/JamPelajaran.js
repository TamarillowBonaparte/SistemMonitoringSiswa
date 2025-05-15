module.exports = (sequelize, DataTypes) => {
    const JamPelajaran = sequelize.define('JamPelajaran', {
        id_jam_pelajaran: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        jamke: DataTypes.STRING,
        jam_range: DataTypes.STRING
    }, {
        tableName: 'jam_pelajaran',
        timestamps: false
    });
    return JamPelajaran;
};
