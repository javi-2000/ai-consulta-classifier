#!/bin/sh
set -e

cd /var/www/html

echo "Waiting for MySQL at ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
until php -r '
$host = getenv("DB_HOST") ?: "mysql";
$port = getenv("DB_PORT") ?: "3306";
$db = getenv("DB_DATABASE") ?: "ecig_ai";
$user = getenv("DB_USERNAME") ?: "eciglogistica";
$pass = getenv("DB_PASSWORD") ?: "eciglogistica_2026";
try {
    new PDO("mysql:host={$host};port={$port};dbname={$db}", $user, $pass);
    exit(0);
} catch (Throwable $e) {
    exit(1);
}
'; do
  sleep 2
done
echo "MySQL is ready."

if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
  echo "Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

if [ ! -f .env ]; then
  echo ".env not found. Creating from template..."
  cp .env.template .env
fi


if ! grep -qE '^APP_KEY=base64:' .env; then
  echo "Generating APP_KEY..."
  php artisan key:generate --force
else
  echo "APP_KEY already exists."
fi

echo "Running migrations..."
php artisan migrate --force

# Seed only the first time (when reference categories are missing).
NEEDS_SEED="$(php -r '
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo App\Models\Category::query()->exists() ? "0" : "1";
')"

if [ "$NEEDS_SEED" = "1" ]; then
  echo "Seeding database (first boot)..."
  php artisan db:seed --force
else
  echo "Database already seeded. Skipping db:seed."
fi

echo "Starting Laravel..."
exec php artisan serve --host=0.0.0.0 --port=8000
