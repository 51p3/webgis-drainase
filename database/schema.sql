-- =====================================================
-- WebGIS Pendataan Saluran Drainase Kabupaten Brebes
-- Database Schema with PostGIS
-- =====================================================

-- Enable PostGIS
CREATE EXTENSION IF NOT EXISTS postgis;
CREATE EXTENSION IF NOT EXISTS postgis_topology;
CREATE EXTENSION IF NOT EXISTS uuid-ossp;

-- =====================================================
-- 1. MASTER DATA TABLES
-- =====================================================

-- Master tahun data
CREATE TABLE tahun_data (
  id SERIAL PRIMARY KEY,
  tahun INTEGER UNIQUE NOT NULL,
  status_aktif BOOLEAN DEFAULT true,
  deskripsi TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Master wilayah (kecamatan, kelurahan)
CREATE TABLE wilayah (
  id SERIAL PRIMARY KEY,
  kecamatan VARCHAR(100) NOT NULL,
  kelurahan VARCHAR(100) NOT NULL,
  kode_wilayah VARCHAR(20) UNIQUE,
  geometry GEOMETRY(POLYGON, 4326),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_wilayah_kecamatan ON wilayah(kecamatan);
CREATE INDEX idx_wilayah_geometry ON wilayah USING GIST(geometry);

-- Master kategori berita
CREATE TABLE kategori_berita (
  id SERIAL PRIMARY KEY,
  nama VARCHAR(100) NOT NULL UNIQUE,
  slug VARCHAR(100) NOT NULL UNIQUE,
  deskripsi TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- 2. USER & AUTHENTICATION
-- =====================================================

CREATE TABLE users (
  id SERIAL PRIMARY KEY,
  uuid UUID DEFAULT uuid_generate_v4() UNIQUE,
  nama_lengkap VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'viewer',
  status VARCHAR(50) DEFAULT 'aktif',
  foto_profil VARCHAR(255),
  nomor_hp VARCHAR(20),
  alamat TEXT,
  kecamatan_id INTEGER REFERENCES wilayah(id),
  is_email_verified BOOLEAN DEFAULT false,
  last_login TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);

-- Audit log
CREATE TABLE audit_logs (
  id SERIAL PRIMARY KEY,
  user_id INTEGER REFERENCES users(id),
  aksi VARCHAR(100) NOT NULL,
  tabel_target VARCHAR(100),
  record_id INTEGER,
  data_lama JSONB,
  data_baru JSONB,
  ip_address VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_audit_logs_user ON audit_logs(user_id);
CREATE INDEX idx_audit_logs_created ON audit_logs(created_at);

-- =====================================================
-- 3. DRAINASE DATA
-- =====================================================

CREATE TABLE drainase (
  id SERIAL PRIMARY KEY,
  uuid UUID DEFAULT uuid_generate_v4() UNIQUE,
  nama_saluran VARCHAR(255) NOT NULL,
  jenis_saluran VARCHAR(50) NOT NULL,
  kecamatan_id INTEGER REFERENCES wilayah(id),
  kelurahan_id INTEGER REFERENCES wilayah(id),
  panjang_meter DECIMAL(10,2),
  lebar_meter DECIMAL(10,2),
  tinggi_meter DECIMAL(10,2),
  material VARCHAR(100),
  kondisi VARCHAR(50) NOT NULL DEFAULT 'baik',
  status_sedimentasi VARCHAR(50),
  tahun_pembangunan INTEGER,
  tahun_perbaikan INTEGER,
  keterangan TEXT,
  latitude DECIMAL(10,8),
  longitude DECIMAL(11,8),
  geometry GEOMETRY(LINESTRING, 4326),
  tahun_pendataan INTEGER NOT NULL,
  surveyor_id INTEGER REFERENCES users(id),
  created_by INTEGER REFERENCES users(id),
  updated_by INTEGER REFERENCES users(id),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_drainase_kecamatan ON drainase(kecamatan_id);
CREATE INDEX idx_drainase_kondisi ON drainase(kondisi);
CREATE INDEX idx_drainase_tahun ON drainase(tahun_pendataan);
CREATE INDEX idx_drainase_geometry ON drainase USING GIST(geometry);

-- Foto drainase
CREATE TABLE drainase_photos (
  id SERIAL PRIMARY KEY,
  uuid UUID DEFAULT uuid_generate_v4() UNIQUE,
  drainase_id INTEGER NOT NULL REFERENCES drainase(id) ON DELETE CASCADE,
  file_path VARCHAR(255) NOT NULL,
  file_name VARCHAR(255),
  file_size BIGINT,
  mime_type VARCHAR(50),
  deskripsi TEXT,
  urutan INTEGER DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_drainase_photos_drainase ON drainase_photos(drainase_id);

-- Riwayat/Historis drainase per tahun
CREATE TABLE drainase_histori (
  id SERIAL PRIMARY KEY,
  drainase_id INTEGER NOT NULL REFERENCES drainase(id) ON DELETE CASCADE,
  tahun INTEGER NOT NULL,
  kondisi VARCHAR(50),
  panjang_meter DECIMAL(10,2),
  lebar_meter DECIMAL(10,2),
  tinggi_meter DECIMAL(10,2),
  status_sedimentasi VARCHAR(50),
  geometry GEOMETRY(LINESTRING, 4326),
  catatan TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_drainase_histori_drainase ON drainase_histori(drainase_id);
CREATE INDEX idx_drainase_histori_tahun ON drainase_histori(tahun);
CREATE INDEX idx_drainase_histori_geometry ON drainase_histori USING GIST(geometry);

-- =====================================================
-- 4. GENANGAN/BANJIR DATA
-- =====================================================

CREATE TABLE genangan (
  id SERIAL PRIMARY KEY,
  uuid UUID DEFAULT uuid_generate_v4() UNIQUE,
  nama_lokasi VARCHAR(255) NOT NULL,
  kecamatan_id INTEGER REFERENCES wilayah(id),
  kelurahan_id INTEGER REFERENCES wilayah(id),
  latitude DECIMAL(10,8) NOT NULL,
  longitude DECIMAL(11,8) NOT NULL,
  geometry GEOMETRY(POINT, 4326),
  tinggi_air_cm INTEGER,
  penyebab TEXT,
  radius_terdampak_meter DECIMAL(10,2),
  status_penanganan VARCHAR(50),
  durasi_genangan VARCHAR(100),
  tahun_data INTEGER NOT NULL,
  keterangan TEXT,
  reporter_id INTEGER REFERENCES users(id),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_genangan_kecamatan ON genangan(kecamatan_id);
CREATE INDEX idx_genangan_geometry ON genangan USING GIST(geometry);
CREATE INDEX idx_genangan_tahun ON genangan(tahun_data);

-- Foto genangan
CREATE TABLE genangan_photos (
  id SERIAL PRIMARY KEY,
  uuid UUID DEFAULT uuid_generate_v4() UNIQUE,
  genangan_id INTEGER NOT NULL REFERENCES genangan(id) ON DELETE CASCADE,
  file_path VARCHAR(255) NOT NULL,
  file_name VARCHAR(255),
  file_size BIGINT,
  mime_type VARCHAR(50),
  deskripsi TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_genangan_photos_genangan ON genangan_photos(genangan_id);

-- =====================================================
-- 5. BERITA & INFORMASI
-- =====================================================

CREATE TABLE berita (
  id SERIAL PRIMARY KEY,
  uuid UUID DEFAULT uuid_generate_v4() UNIQUE,
  judul VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  konten TEXT NOT NULL,
  ringkasan VARCHAR(500),
  thumbnail VARCHAR(255),
  kategori_id INTEGER REFERENCES kategori_berita(id),
  author_id INTEGER REFERENCES users(id),
  status VARCHAR(50) DEFAULT 'draft',
  tanggal_publikasi TIMESTAMP,
  view_count INTEGER DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_berita_status ON berita(status);
CREATE INDEX idx_berita_kategori ON berita(kategori_id);
CREATE INDEX idx_berita_slug ON berita(slug);

-- =====================================================
-- 6. STATISTIK TAHUNAN
-- =====================================================

CREATE TABLE statistik_tahunan (
  id SERIAL PRIMARY KEY,
  tahun INTEGER NOT NULL UNIQUE,
  total_drainase INTEGER DEFAULT 0,
  total_drainase_baik INTEGER DEFAULT 0,
  total_drainase_rusak INTEGER DEFAULT 0,
  total_panjang_meter DECIMAL(15,2) DEFAULT 0,
  total_lebar_meter DECIMAL(15,2) DEFAULT 0,
  total_genangan INTEGER DEFAULT 0,
  kecamatan_terbanyak_genangan VARCHAR(100),
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_statistik_tahunan_tahun ON statistik_tahunan(tahun);

-- =====================================================
-- 7. UPLOAD HISTORY (SHP, GeoJSON)
-- =====================================================

CREATE TABLE upload_histori (
  id SERIAL PRIMARY KEY,
  uuid UUID DEFAULT uuid_generate_v4() UNIQUE,
  file_name VARCHAR(255) NOT NULL,
  file_path VARCHAR(255),
  file_type VARCHAR(50),
  tahun_data INTEGER,
  total_records INTEGER,
  status VARCHAR(50),
  error_message TEXT,
  user_id INTEGER REFERENCES users(id),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_upload_histori_user ON upload_histori(user_id);
CREATE INDEX idx_upload_histori_tahun ON upload_histori(tahun_data);

-- =====================================================
-- 8. SYSTEM SETTINGS
-- =====================================================

CREATE TABLE settings (
  id SERIAL PRIMARY KEY,
  key VARCHAR(100) NOT NULL UNIQUE,
  value TEXT,
  tipe VARCHAR(50),
  deskripsi TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- INSERT INITIAL DATA
-- =====================================================

INSERT INTO tahun_data (tahun, status_aktif) VALUES
  (2023, true),
  (2024, true),
  (2025, true),
  (2026, true);

INSERT INTO kategori_berita (nama, slug, deskripsi) VALUES
  ('Berita Umum', 'berita-umum', 'Berita umum tentang drainase'),
  ('Perbaikan', 'perbaikan', 'Berita tentang perbaikan drainase'),
  ('Genangan', 'genangan', 'Berita tentang genangan/banjir'),
  ('Edukasi', 'edukasi', 'Berita edukasi dan awareness');

INSERT INTO settings (key, value, tipe, deskripsi) VALUES
  ('app_name', 'WebGIS Drainase Kabupaten Brebes', 'string', 'Nama aplikasi'),
  ('organization', 'DINAS PEKERJAAN UMUM Bidang Cipta Karya', 'string', 'Nama organisasi'),
  ('location', 'Kabupaten Brebes', 'string', 'Lokasi/Kabupaten'),
  ('app_version', '1.0.0', 'string', 'Versi aplikasi'),
  ('enable_pwa', 'true', 'boolean', 'Aktifkan Progressive Web App');