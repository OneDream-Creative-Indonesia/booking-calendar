<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            ['title' => 'Basic', 'description' => 'Paket Basic - Rp100.000'],
            ['title' => 'Premium', 'description' => 'Paket Premium - Rp250.000'],
            ['title' => 'Exclusive', 'description' => 'Paket Exclusive - Rp500.000'],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
