<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "<pre>";
echo "🧹 Clearing all Laravel cache...\n";

$kernel->call('optimize:clear');
echo $kernel->output();
