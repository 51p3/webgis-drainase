<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\District;
use App\Models\Village;
use App\Models\Drainage;
use App\Models\DrainagePhoto;
use App\Models\FloodLocation;
use App\Models\FloodPhoto;
use App\Models\News;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = ['super_admin', 'admin', 'operator', 'viewer'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'api']);
        }

        // Create permissions
        $permissions = [
            'create_drainage',
            'edit_drainage',
            'delete_drainage',
            'view_drainage',
            'create_flood',
            'edit_flood',
            'delete_flood',
            'view_flood',
            'create_news',
            'edit_news',
            'delete_news',
            'publish_news',
            'manage_users',
            'manage_settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // Assign permissions to roles
        $superAdminRole = Role::where('name', 'super_admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $operatorRole = Role::where('name', 'operator')->first();
        $viewerRole = Role::where('name', 'viewer')->first();

        $superAdminRole->syncPermissions($permissions);
        $adminRole->syncPermissions($permissions);
        $operatorRole->syncPermissions(['create_drainage', 'edit_drainage', 'view_drainage', 'create_flood', 'edit_flood', 'view_flood', 'create_news', 'edit_news', 'view_flood']);
        $viewerRole->syncPermissions(['view_drainage', 'view_flood']);

        // Create users
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('super_admin');

        $admin = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        $operator = User::firstOrCreate(
            ['email' => 'operator@example.com'],
            [
                'name' => 'Operator',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $operator->assignRole('operator');

        // Create districts
        $districts = [
            ['name' => 'Kecamatan Brebes', 'code' => 'KBR01'],
            ['name' => 'Kecamatan Wanasari', 'code' => 'KBR02'],
            ['name' => 'Kecamatan Tanjung', 'code' => 'KBR03'],
            ['name' => 'Kecamatan Bulakamba', 'code' => 'KBR04'],
            ['name' => 'Kecamatan Banjarharjo', 'code' => 'KBR05'],
        ];

        foreach ($districts as $district) {
            District::firstOrCreate(
                ['code' => $district['code']],
                ['name' => $district['name']]
            );
        }

        // Create villages
        $districtData = District::all();
        $villages = [
            ['name' => 'Desa A', 'code' => 'DSA01'],
            ['name' => 'Desa B', 'code' => 'DSB01'],
            ['name' => 'Desa C', 'code' => 'DSC01'],
            ['name' => 'Desa D', 'code' => 'DSD01'],
            ['name' => 'Desa E', 'code' => 'DSE01'],
        ];

        foreach ($villages as $index => $village) {
            $districtId = $districtData[$index % count($districtData)]->id;
            Village::firstOrCreate(
                ['code' => $village['code']],
                [
                    'name' => $village['name'],
                    'district_id' => $districtId,
                ]
            );
        }

        // Create sample drainages
        $villageData = Village::all();
        for ($i = 1; $i <= 10; $i++) {
            $village = $villageData->random();
            Drainage::firstOrCreate(
                ['code' => 'DRN' . str_pad($i, 4, '0', STR_PAD_LEFT)],
                [
                    'name' => 'Saluran Drainase ' . $i,
                    'district_id' => $village->district_id,
                    'village_id' => $village->id,
                    'length' => rand(100, 5000),
                    'width' => rand(1, 5),
                    'height' => rand(1, 3),
                    'type' => ['U-Ditch', 'Concrete', 'Stone Masonry', 'Earth Channel'][rand(0, 3)],
                    'condition' => ['Good', 'Moderate', 'Damaged'][rand(0, 2)],
                    'description' => 'Saluran drainase untuk menangani air permukaan di wilayah ' . $village->name,
                    'geometry' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => [
                            [108.5 + rand(-100, 100) / 1000, -7.3 + rand(-100, 100) / 1000],
                            [108.5 + rand(-100, 100) / 1000, -7.3 + rand(-100, 100) / 1000],
                        ]
                    ]),
                ]
            );
        }

        // Create sample flood locations
        for ($i = 1; $i <= 8; $i++) {
            $village = $villageData->random();
            FloodLocation::firstOrCreate(
                ['name' => 'Lokasi Genangan ' . $i],
                [
                    'district_id' => $village->district_id,
                    'village_id' => $village->id,
                    'flood_depth' => rand(50, 300) / 100,
                    'flood_duration' => rand(1, 10) . ' jam',
                    'cause' => ['Curah hujan tinggi', 'Drainase tersumbat', 'Sistem drainase kurang memadai'][rand(0, 2)],
                    'description' => 'Lokasi genangan di ' . $village->name,
                    'geometry' => json_encode([
                        'type' => 'Point',
                        'coordinates' => [108.5 + rand(-100, 100) / 1000, -7.3 + rand(-100, 100) / 1000]
                    ]),
                ]
            );
        }

        // Create settings
        $settings = [
            ['key' => 'institution_name', 'value' => 'Dinas Pekerjaan Umum Bidang Cipta Karya Kabupaten Brebes'],
            ['key' => 'map_center_lat', 'value' => '-7.2575'],
            ['key' => 'map_center_lng', 'value' => '108.7392'],
            ['key' => 'map_zoom', 'value' => '10'],
            ['key' => 'footer_text', 'value' => 'WebGIS Drainase dan Genangan © 2024'],
            ['key' => 'contact_email', 'value' => 'info@brebes.go.id'],
            ['key' => 'contact_phone', 'value' => '+62-283-XXXXXX'],
            ['key' => 'contact_address', 'value' => 'Jl. Abdulrahman Saleh, Brebes, Indonesia'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }

        // Create sample news
        for ($i = 1; $i <= 5; $i++) {
            News::firstOrCreate(
                ['slug' => 'berita-drainase-' . $i],
                [
                    'title' => 'Berita Drainase dan Genangan ' . $i,
                    'content' => 'Ini adalah konten berita tentang drainase dan genangan. Informasi penting tentang penanganan drainase dan pencegahan genangan di Kabupaten Brebes. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    'user_id' => $admin->id,
                    'status' => 'published',
                    'published_at' => now()->subDays(rand(0, 30)),
                ]
            );
        }
    }
}
