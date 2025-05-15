const express = require('express');
const path = require('path');
const sequelize = require('./config/database');
const authRoutes = require('./routes/authRoutes');
const siswaRoutes = require('./routes/siswaRoutes');
const tugasRoutes = require('./routes/tugasRoutes');
const pelanggaranRoutes = require('./routes/pelanggaranRoutes');
const informasiRoutes = require('./routes/informasiRoutes');
const jadwalRoutes = require("./routes/jadwalRoutes");
const ujianRoutes = require("./routes/ujianRoutes");
const nilaiAkhirRoute = require('./routes/nilaiakhirRoutes');
const absensiRoutes = require('./routes/absensiRoutes');
const historyAbsensiRoute = require('./routes/historyRoutes');

const app = express();

// Middleware untuk parsing JSON dan form data
app.use(express.json());
app.use(express.urlencoded({ extended: true })); // Middleware untuk menangani x-www-form-urlencoded

// Menyajikan file statis dari direktori public
app.use(express.static(path.join(__dirname, 'public')));

// Registrasi routes
app.use('/auth', authRoutes);
app.use('/siswa', siswaRoutes);
// app.use(tugasRoutes);
app.use('/tugas', tugasRoutes);
// app.use('/jadwal', jadwalRoutes);
app.use('/pelanggaran', pelanggaranRoutes);
// app.use('/informasi', informasiRoutes);
app.use('/api/informasi', informasiRoutes);
app.use(jadwalRoutes);
app.use('/ujian', ujianRoutes);
app.use('/nilaiakhir', nilaiAkhirRoute);
app.use('/api', absensiRoutes);
app.use('/uploads', express.static(path.join(__dirname, 'public/uploads')));

app.use('/api', historyAbsensiRoute);

app.use("/", authRoutes);

// Membuat direktori uploads jika belum ada
const fs = require('fs');
const uploadDir = path.join(__dirname, 'public/uploads/absensi');
if (!fs.existsSync(uploadDir)) {
  fs.mkdirSync(uploadDir, { recursive: true });
  console.log('Directory created:', uploadDir);
}


const PORT = 5000;
app.listen(PORT, () => {
  console.log(`Server running on http://localhost:${PORT}`);
  console.log(`Upload directory available at: ${uploadDir}`);
});