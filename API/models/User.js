const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const User = sequelize.define('user', {
    id_user: { type: DataTypes.INTEGER, primaryKey: true },
    username: DataTypes.STRING,
    password: DataTypes.STRING
}, { tableName: 'user', timestamps: false });

module.exports = User;