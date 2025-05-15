// models/NilaiAkhir.js
module.exports = (sequelize, DataTypes) => {
    const NilaiAkhir = sequelize.define('NilaiAkhir', {
        id: {
            type: DataTypes.INTEGER,
            primaryKey: true,
            autoIncrement: true
        },
        id_siswa: DataTypes.INTEGER,
        id_kodepembelajaran: DataTypes.INTEGER,
        semester: DataTypes.STRING(10),
        nilai: DataTypes.DECIMAL(5, 2),
        created_at: DataTypes.DATE,
        updated_at: DataTypes.DATE
    }, {
        tableName: 'nilai_akhir',
        timestamps: false // Set menjadi false agar Sequelize tidak mencari kolom createdAt dan updatedAt
    });

    return NilaiAkhir;
};
