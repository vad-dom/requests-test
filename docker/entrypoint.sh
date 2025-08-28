#!/bin/bash
set -e

# Ждем, пока MySQL станет доступен
echo "⏳ Жду, пока база данных будет доступна..."
until mysqladmin ping -h"$DB_HOST" --silent; do
  sleep 3
done

echo "✅ База данных доступна."

# Создаем папки, если их нет
echo "🔧 Создаю папки..."
mkdir -p /var/www/html/runtime /var/www/html/vendor /var/www/html/web/assets

# Устанавливаем зависимости
echo "📦 Устанавливаю зависимости Composer..."
composer install --no-interaction --optimize-autoloader

# Выполняем миграции
echo "🛠️ Выполняю миграции Yii2..."
php yii migrate --interactive=0 || true

# Выставляем права
echo "🔧 Настраиваю права доступа..."
chown -R www-data:www-data /var/www/html/runtime /var/www/html/vendor /var/www/html/web/assets
chmod -R 775 /var/www/html/runtime /var/www/html/vendor /var/www/html/web/assets

# Запускаем Apache
echo "🚀 Запуск Apache..."
exec apache2-foreground
