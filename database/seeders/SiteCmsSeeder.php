<?php

namespace Database\Seeders;

use App\Support\Site;
use Illuminate\Database\Seeder;

class SiteCmsSeeder extends Seeder
{
    public function run(): void
    {
        Site::ensureSeeded(true);
    }
}
