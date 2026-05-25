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
  role VARCHAR(50) NOT NULL DEFAULT 'viewer', -- super_admin, admin, surveyor, viewer
  status VARCHAR(50) DEFAULT 'aktif', -- aktif, nonaktif
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

-- Data utama drainase
CREATE TABLE drainase (
  id SERIAL PRIMARY KEY,
  uuid UUID DEFAULT uuid_generate_v4() UNIQUE,
  nama_saluran VARCHAR(255) NOT NULL,
  jenis_saluran VARCHAR(50) NOT NULL, -- terbuka, tertutup, kombinasi
  kecamatan_id INTEGER REFERENCES wilayah(id),
  kelurahan_id INTEGER REFERENCES wilayah(id),
  panjang_meter DECIMAL(10,2),
  lebar_meter DECIMAL(10,2),
  tinggi_meter DECIMAL(10,2),
  material VARCHAR(100), -- beton, tanah, pasangan batu, dll
  kondisi VARCHAR(50) NOT NULL DEFAULT 'baik', -- baik, rusak ringan, rusak sedang, rusak berat
  status_sedimentasi VARCHAR(50), -- tidak ada, sedikit, banyak
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
  status_penanganan VARCHAR(50), -- belum, sedang, selesai
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
  status VARCHAR(50) DEFAULT 'draft', -- draft, published, archived
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
  file_type VARCHAR(50), -- shp, geojson
  tahun_data INTEGER,
  total_records INTEGER,
  status VARCHAR(50), -- sukses, gagal, proses
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
  tipe VARCHAR(50), -- string, integer, boolean, json
  deskripsi TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- INSERT INITIAL DATA
-- =====================================================

-- Insert tahun data
INSERT INTO tahun_data (tahun, status_aktif) VALUES
  (2023, true),
  (2024, true),
  (2025, true),
  (2026, true);

-- Insert kategori berita
INSERT INTO kategori_berita (nama, slug, deskripsi) VALUES
  ('Berita Umum', 'berita-umum', 'Berita umum tentang drainase'),
  ('Perbaikan', 'perbaikan', 'Berita tentang perbaikan drainase'),
  ('Genangan', 'genangan', 'Berita tentang genangan/banjir'),
  ('Edukasi', 'edukasi', 'Berita edukasi dan awareness');

-- Insert default settings
INSERT INTO settings (key, value, tipe, deskripsi) VALUES
  ('app_name', 'WebGIS Drainase Kabupaten Brebes', 'string', 'Nama aplikasi'),
  ('organization', 'DINAS PEKERJAAN UMUM Bidang Cipta Karya', 'string', 'Nama organisasi'),
  ('location', 'Kabupaten Brebes', 'string', 'Lokasi/Kabupaten'),
  ('app_version', '1.0.0', 'string', 'Versi aplikasi'),
  ('enable_pwA', 'true', 'boolean', 'Aktifkan Progressive Web App');

-- =====================================================
-- VIEWS & FUNCTIONS
-- =====================================================

-- View: Statistik drainase per kecamatan
CREATE OR REPLACE VIEW view_statistik_kecamatan AS
SELECT 
  w.kecamatan,
  COUNT(d.id) as total_drainase,
  SUM(CASE WHEN d.kondisi = 'baik' THEN 1 ELSE 0 END) as total_baik,
  SUM(CASE WHEN d.kondisi IN ('rusak ringan', 'rusak sedang', 'rusak berat') THEN 1 ELSE 0 END) as total_rusak,
  ROUND(SUM(d.panjang_meter)::NUMERIC, 2) as total_panjang_meter,
  ROUND(AVG(d.panjang_meter)::NUMERIC, 2) as rata_panjang_meter
FROM drainase d
RIGHT JOIN wilayah w ON d.kecamatan_id = w.id
WHERE d.tahun_pendataan = (SELECT MAX(tahun) FROM tahun_data WHERE status_aktif = true)
GROUP BY w.kecamatan
ORDER BY total_drainase DESC;

-- View: Statistik genangan per kecamatan
CREATE OR REPLACE VIEW view_statistik_genangan_kecamatan AS
SELECT 
  w.kecamatan,
  COUNT(g.id) as total_genangan,
  ROUND(AVG(g.tinggi_air_cm)::NUMERIC, 2) as rata_tinggi_air,
  COUNT(CASE WHEN g.status_penanganan = 'belum' THEN 1 END) as belum_ditangani
FROM genangan g
RIGHT JOIN wilayah w ON g.kecamatan_id = w.id
WHERE g.tahun_data = (SELECT MAX(tahun) FROM tahun_data WHERE status_aktif = true)
GROUP BY w.kecamatan
ORDER BY total_genangan DESC;

-- View: Drainase dengan foto terbaru
CREATE OR REPLACE VIEW view_drainase_dengan_foto AS
SELECT 
  d.*,
  dp.file_path,
  dp.file_name,
  ROW_NUMBER() OVER (PARTITION BY d.id ORDER BY dp.urutan ASC) as foto_urutan
FROM drainase d
LEFT JOIN drainase_photos dp ON d.id = dp.drainase_id;

-- Function: Update statistik tahunan otomatis
CREATE OR REPLACE FUNCTION update_statistik_tahunan()
RETURNS TRIGGER AS $$
BEGIN
  INSERT INTO statistik_tahunan (tahun, total_drainase, total_drainase_baik, total_drainase_rusak, total_panjang_meter)
  SELECT 
    NEW.tahun_pendataan,
    COUNT(*),
    SUM(CASE WHEN kondisi = 'baik' THEN 1 ELSE 0 END),
    SUM(CASE WHEN kondisi IN ('rusak ringan', 'rusak sedang', 'rusak berat') THEN 1 ELSE 0 END),
    COALESCE(SUM(panjang_meter), 0)
  FROM drainase
  WHERE tahun_pendataan = NEW.tahun_pendataan
  ON CONFLICT (tahun) DO UPDATE SET
    total_drainase = EXCLUDED.total_drainase,
    total_drainase_baik = EXCLUDED.total_drainase_baik,
    total_drainase_rusak = EXCLUDED.total_drainase_rusak,
    total_panjang_meter = EXCLUDED.total_panjang_meter,
    updated_at = CURRENT_TIMESTAMP;
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER trigger_update_statistik_drainase
AFTER INSERT OR UPDATE ON drainase
FOR EACH ROW
EXECUTE FUNCTION update_statistik_tahunan();

-- =====================================================
-- CONSTRAINTS & INDEXES
-- =====================================================

-- Unique constraint untuk mencegah duplikasi
ALTER TABLE drainase 
ADD CONSTRAINT unique_drainase_per_tahun 
UNIQUE (nama_saluran, kecamatan_id, tahun_pendataan);

-- Check constraint
ALTER TABLE drainase 
ADD CONSTRAINT check_panjang_positif CHECK (panjang_meter > 0),
ADD CONSTRAINT check_lebar_positif CHECK (lebar_meter > 0),
ADD CONSTRAINT check_tinggi_positif CHECK (tinggi_meter > 0);

ALTER TABLE genangan
ADD CONSTRAINT check_radius_positif CHECK (radius_terdampak_meter > 0);

-- =====================================================
-- GRANTS (untuk aplikasi user)
-- =====================================================

-- CREATE USER webgis_user WITH PASSWORD 'your_secure_password';
-- GRANT CONNECT ON DATABASE webgis_drainase TO webgis_user;
-- GRANT USAGE ON SCHEMA public TO webgis_user;
-- GRANT SELECT, INSERT, UPDATE, DELETE ON ALL TABLES IN SCHEMA public TO webgis_user;
-- GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO webgis_user;
