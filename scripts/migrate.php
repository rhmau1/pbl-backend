<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Config\Database;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

$pdo = Database::pdo();

$migDir = __DIR__ . '/../app/database/migrations';

if (!is_dir($migDir)) {
    echo "Migration directory not found: $migDir\n";
    exit(1);
}

$files = glob("$migDir/*.sql");
sort($files);

if (empty($files)) {
    echo "No migration files found in $migDir\n";
    exit;
}

echo "Running migrations...\n";

foreach ($files as $file) {
    $name = basename($file);

    echo "Executing: $name ... ";

    $sql = file_get_contents($file);

    try {
        $pdo->exec($sql);
        echo "Done\n";
    } catch (Throwable $e) {
        echo "FAILED\n";
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\All migrations executed successfully!\n";
