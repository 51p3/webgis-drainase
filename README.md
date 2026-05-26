# WebGIS Pendataan Saluran Drainase Perkotaan
**DINAS PEKERJAAN UMUM Bidang Cipta Karya  Kabupaten Brebes**

Aplikasi WebGIS modern berbasis teknologi terkini untuk pendataan, monitoring, dan analisis saluran drainase perkotaan dengan visualisasi geospasial interaktif.

## 🎯 Fitur Utama

### 📍 Peta Interaktif WebGIS
- **Multi-layer GIS**: Drainase primer, sekunder, tersier, titik genangan
- **Layer Management**: Show/hide layers, opacity control, layer compare
- **Interactive Features**: Zoom, popup info, basemap switcher, fullscreen
- **Measurement Tools**: Measure distance & area
- **Drawing Tools**: Draw polygon, line, point, edit vertex
- **Geolocation**: Current location tracking
- **Coordinate Cursor**: Real-time coordinate display

### 📊 Dashboard Statistik
- Total drainase per status
- Analisis kondisi drainase (baik/rusak)
- Grafik panjang saluran per kecamatan
- Statistik titik genangan
- Tren kerusakan per tahun
- Export laporan (Excel, PDF, GeoJSON, SHP)

### 📋 Pendataan Drainase
- **CRUD Lengkap**: Create, Read, Update, Delete data drainase
- **Data Spasial**: Latitude, longitude, geometry GeoJSON
- **Upload SHP**: Impor data dari shapefile
- **Multiple Upload**: Upload foto drainase
- **Edit Geometry**: Draw dan edit feature langsung di map
- **Validasi Data**: Input validation lengkap

### 🌊 Modul Titik Genangan
- Input titik banjir/genangan dengan foto
- Tinggi air dan penyebab
- Status penanganan
- Radius terdampak
- Heatmap visualisasi

### 📰 CMS Berita & Informasi
- CRUD berita dengan editor teks
- Upload thumbnail
- Kategori berita
- Slider berita di homepage

### 👨‍💼 Admin Dashboard
- Sidebar navigation
- Statistik realtime
- Tabel data interaktif
- Export data
- Multi-user role management

### 🔐 Autentikasi & Keamanan
- JWT Authentication
- Password hashing (bcrypt)
- Role-based access control (RBAC)
- Protected routes
- Session management
- Input validation
- SQL injection protection
- XSS protection

### 📅 Manajemen Data Per Tahun
- Filter data berdasarkan tahun
- Perbandingan data antar tahun
- Time slider pada peta
- Historical data tracking
- Statistik tahunan
- Arsip data per tahun

### 📱 Responsive & Progressive
- Mobile-friendly UI
- Dark/light mode toggle
- Smooth animations (Framer Motion)
- Loading skeleton
- Toast notifications
- PWA support
- Offline mode

## 🛠️ Tech Stack

### Frontend
- **React 18** + Vite
- **TailwindCSS** - Styling
- **Leaflet.js** - WebGIS mapping
- **Axios** - HTTP client
- **React Router** - Navigation
- **Framer Motion** - Animations
- **Recharts** - Data visualization
- **react-leaflet-draw** - Drawing tools
- **shpjs** - Shapefile support
- **turf.js** - GIS analysis

### Backend
- **Node.js** + Express.js
- **JWT** - Authentication
- **Multer** - File upload
- **Sequelize** ORM
- **Express validator** - Validation
- **Helmet** - Security headers
- **CORS** - Cross-origin requests
- **dotenv** - Environment configuration

### Database
- **PostgreSQL** 14+
- **PostGIS** - Spatial database
- **PostGIS extensions** untuk spatial queries

### Deployment
- **Docker** - Containerization
- **Docker Compose** - Multi-container
- **Nginx** - Reverse proxy
- **PM2** - Process manager
- **Ubuntu VPS** - Production server

## 📁 Struktur Folder

```
webgis-drainase/
├── frontend/                    # React + Vite application
│   ├── src/
│   │   ├── components/
│   │   │   ├── Map/            # Leaflet components
│   │   │   ├── Dashboard/      # Dashboard components
│   │   │   ├── Admin/          # Admin components
│   │   │   ├── Auth/           # Auth components
│   │   │   └── Common/         # Reusable components
│   │   ├── pages/              # Page components
│   │   ├── hooks/              # Custom React hooks
│   │   ├── services/           # API services
│   │   ├── context/            # Context API
│   │   ├── utils/              # Utility functions
│   │   ├── styles/             # Global styles
│   │   └── App.jsx
│   ├── public/
│   ├── .env.example
│   ├── vite.config.js
│   ├── tailwind.config.js
│   └── package.json
│
├── backend/                     # Node.js + Express application
│   ├── src/
│   │   ├── controllers/        # Business logic
│   │   ├── models/             # Sequelize models
│   │   ├── routes/             # API routes
│   │   ├── middleware/         # Express middleware
│   │   ├── services/           # Business services
│   │   ├── validators/         # Input validation
│   │   ├── utils/              # Utility functions
│   │   ├── config/             # Configuration
│   │   └── server.js
│   ├── migrations/             # Database migrations
│   ├── seeders/                # Database seeders
│   ├── .env.example
│   ├── package.json
│   └── ecosystem.config.js
│
├── database/                    # Database files
│   ├── schema.sql              # Database schema
│   ├── migrations/
│   └── seeders/
│
├── docker/
│   ├── Dockerfile.frontend
│   ├── Dockerfile.backend
│   └── docker-compose.yml
│
├── nginx/
│   ├── nginx.conf
│   └── ssl/                    # SSL certificates
│
├── scripts/
│   ├── deploy.sh               # Deploy script
│   ├── backup.sh               # Backup script
│   └── setup.sh                # Setup script
│
├── docs/
│   ├── API.md                  # API documentation
│   ├── SETUP.md                # Setup guide
│   ├── DEPLOYMENT.md           # Deployment guide
│   ├── DATABASE.md             # Database schema
│   └── FEATURES.md             # Feature documentation
│
└── .gitignore
```

## 🚀 Instalasi & Setup

### Prerequisites
- Node.js 16+
- PostgreSQL 14+
- PostGIS extension
- Docker & Docker Compose (optional)
- Git

### 1. Clone Repository
```bash
git clone https://github.com/51p3/webgis-drainase.git
cd webgis-drainase
```

### 2. Setup Database
```bash
# Create PostgreSQL database
createdb webgis_drainase

# Enable PostGIS extension
psql webgis_drainase -c "CREATE EXTENSION IF NOT EXISTS postgis;"
psql webgis_drainase -c "CREATE EXTENSION IF NOT EXISTS postgis_topology;"

# Load schema
psql webgis_drainase < database/schema.sql
```

### 3. Setup Backend
```bash
cd backend
npm install
cp .env.example .env
# Edit .env dengan konfigurasi Anda
npm run migrate
npm run seed
npm run dev
```

### 4. Setup Frontend
```bash
cd ../frontend
npm install
cp .env.example .env
# Edit .env dengan API URL
npm run dev
```

### 5. Buka Browser
- Frontend: http://localhost:5173
- Backend API: http://localhost:3000
- Admin: http://localhost:5173/admin

## 🐳 Docker Deployment

```bash
# Build dan run dengan Docker Compose
docker-compose -f docker/docker-compose.yml up -d

# Stop
docker-compose -f docker/docker-compose.yml down

# View logs
docker-compose -f docker/docker-compose.yml logs -f
```

## 📚 Dokumentasi

- [API Documentation](docs/API.md)
- [Database Schema](docs/DATABASE.md)
- [Setup Guide](docs/SETUP.md)
- [Deployment Guide](docs/DEPLOYMENT.md)
- [Features Guide](docs/FEATURES.md)

## 🔒 Security Features

- ✅ JWT Authentication dengan refresh token
- ✅ Password hashing dengan bcrypt
- ✅ CORS configuration
- ✅ Helmet security headers
- ✅ Rate limiting
- ✅ Input validation & sanitization
- ✅ SQL injection prevention
- ✅ XSS protection
- ✅ Environment variables
- ✅ Audit logging

## 👥 User Roles

| Role | Permissions |
|------|-------------|
| **Super Admin** | Full access, user management |
| **Admin** | CRUD drainase, berita, genangan |
| **Surveyor** | Create drainase, upload SHP |
| **Viewer** | Read-only access |

## 📊 Data Per Tahun

- Filter data berdasarkan tahun pendataan
- Perbandingan statistik antar tahun
- Time slider untuk visualisasi historis
- Export laporan tahunan
- Tracking perubahan kondisi drainase

## 🎨 UI/UX Features

- Modern smart city dashboard design
- Glassmorphism elements
- Dark/light mode toggle
- Smooth animations
- Loading skeletons
- Toast notifications
- Responsive breakpoints
- Mobile-first approach

## 📞 Support & Contact

- **Organization**: DINAS PEKERJAAN UMUM Bidang Cipta Karya
- **Location**: Kabupaten Brebes
- **Contact**: [Tambahkan kontak sesuai kebutuhan]

## 📄 License

MIT License - Feel free to use and modify

## 🤝 Contributing

Kontribusi, issues, dan feature requests dipersilahkan!

---

**Terakhir diupdate**: 2026-05-25
**Status**: Production Ready
**Version**: 1.0.0
