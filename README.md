# GG Store Catalogs — ReBrand

A product catalog web app for GG Case Store, built with Laravel 12, Inertia.js v2, and Vue 3.

## Tech Stack

- **Backend:** PHP 8.2, Laravel 12
- **Frontend:** Vue 3, Inertia.js v2, TypeScript
- **Styling:** Tailwind CSS v3, shadcn/ui (Radix Vue)
- **Build:** Vite
- **Testing:** Pest v3
- **DB:** SQLite (default)

## Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18
- npm

## Getting Started

```bash
# Install PHP dependencies
composer install

# Install JS dependencies
npm install

# Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# Create the SQLite database and run migrations
touch database/database.sqlite
php artisan migrate

# Seed the database
php artisan db:seed

# Start the development server
composer run dev
```

The app will be available at `http://localhost:8000`.

## Useful Commands

```bash
# Run tests
php artisan test --compact

# Build for production
npm run build

# Lint & format
npm run lint
npm run format
vendor/bin/pint
```

## Project Structure

```
app/
  Http/Controllers/   — Page & API controllers
  Models/             — Eloquent models (Product, Brand, Category, etc.)
resources/
  js/
    pages/            — Inertia page components
    components/       — Shared & feature components
    layouts/          — App layouts
database/
  migrations/         — Database schema
  seeders/            — Sample data
```
