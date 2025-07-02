#!/bin/bash

# Output colors
RED='\033[0;31m'
GREEN='\033[0;32m'
NC='\033[0m'

print_success() {
    echo -e "${GREEN}$1${NC}"
}

print_error() {
    echo -e "${RED}$1${NC}"
}

# Check dependencies
check_dependency() {
    if ! command -v $1 &> /dev/null; then
        print_error "❌ $1 not installed. Please install it first."
        exit 1
    fi
}

print_success "✅ Checking dependencies..."
check_dependency docker
check_dependency docker-compose
check_dependency php
check_dependency composer

print_success "✅ All dependencies found."

# Create scheduler file if not exists
if [ ! -f docker/scheduler/schedule-cron.sh ]; then
  mkdir -p docker/scheduler
  cat <<EOF > docker/scheduler/schedule-cron.sh
#!/bin/bash
while [ true ]
do
  php artisan schedule:run --verbose --no-interaction
  sleep 60
done
EOF
  chmod +x docker/scheduler/schedule-cron.sh
  print_success "✅ Scheduler script created."
fi

# Create nginx config if not exists
if [ ! -f docker/nginx/default.conf ]; then
  mkdir -p docker/nginx
  print_error "❗ Nginx config missing. Please add docker/nginx/default.conf"
  exit 1
fi

# .env setup
if [ ! -f .env ]; then
  cp .env.example .env
  print_success "✅ .env file created."
fi

# Stop existing containers
docker-compose down -v

# Build and start
print_success "🚀 Starting containers..."
docker-compose up -d --build

# Wait for MySQL
print_success "⏳ Waiting for MySQL..."
sleep 15

# Laravel setup
print_success "⚙️ Running Laravel setup..."
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan storage:link
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan cache:clear

# Run tests
print_success "🧪 Running tests..."
docker-compose exec app php artisan test

# Trigger initial article fetch
print_success "📡 Fetching initial articles..."
docker-compose exec app php artisan fetch:news

print_success "🎉 Setup complete! App is running at http://localhost:8000"
