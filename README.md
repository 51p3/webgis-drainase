# WebGIS Drainase dan Genangan Kabupaten Brebes

Sistem Informasi Geografis (GIS) untuk inventaris drainase dan pemetaan lokasi genangan di Kabupaten Brebes.

## Fitur Utama

- 📊 Dashboard dengan KPI dan grafik analitik
- 🗺️ Peta interaktif WebGIS menggunakan Leaflet
- 🌊 Manajemen data drainase dengan dokumentasi foto
- 🌧️ Pemetaan lokasi genangan
- 📰 Manajemen berita dan informasi publik
- 📑 Generate laporan PDF dan Excel
- 👥 Sistem manajemen user dengan role-based access
- ⚙️ Pengaturan sistem yang fleksibel

## Tech Stack

### Frontend
- React 19 + Vite
- TypeScript
- TailwindCSS + ShadCN UI
- React Query (Tanstack Query)
- Zustand (State Management)
- React Hook Form
- Leaflet + Leaflet Draw
- Chart.js

### Backend
- Laravel 12
- PHP 8.3
- Laravel Sanctum (API Authentication)
- Spatie Laravel Permission

### Database
- PostgreSQL 16
- PostGIS (Spatial Database)

### Deployment
- Docker & Docker Compose
- Nginx
- Ubuntu VPS

## Quick Start

### Prerequisites
- Docker & Docker Compose
- Node.js 20+
- PHP 8.3
- PostgreSQL 16

### Installation

#### 1. Clone Repository
```bash
git clone https://github.com/51p3/webgis-drainase.git
cd webgis-drainase
```

#### 2. Setup dengan Docker Compose
```bash
cp .env.example .env
docker-compose up -d
```

#### 3. Backend Setup
```bash
docker-compose exec backend php artisan key:generate
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan db:seed
```

#### 4. Frontend Setup
```bash
cd frontend
npm install
npm run dev
```

### Access Application
- Frontend: http://localhost:5173
- Backend API: http://localhost:8000/api
- Admin Panel: http://localhost:3000

## Project Structure

```
webgis-drainase/
├── frontend/           # React + Vite application
├── backend/            # Laravel API server
├── docker-compose.yml  # Docker orchestration
├── nginx/              # Nginx configuration
└── docs/               # Documentation
```

## Default Login Credentials

**Super Admin**
- Email: admin@example.com
- Password: password123

## API Documentation

API endpoints are available at `/api/docs`

## Database Schema

Main tables:
- `users` - Sistem user
- `roles` - Role dan permissions
- `districts` - Kecamatan
- `villages` - Desa
- `drainages` - Data drainase (dengan geometry LineString)
- `drainage_photos` - Dokumentasi foto drainase
- `flood_locations` - Lokasi genangan (dengan geometry Point)
- `flood_photos` - Dokumentasi foto genangan
- `news` - Berita dan informasi
- `settings` - Pengaturan sistem
- `activity_logs` - Log aktivitas user

## User Roles

1. **Super Admin** - Full access
2. **Admin** - Manajemen data dan user
3. **Operator** - Input dan edit data
4. **Viewer** - Hanya view data

## Support & Contact

Untuk pertanyaan atau dukungan, hubungi:
- Email: support@example.com
- Telepon: +62-XXX-XXXX-XXXX

## License

MIT License
