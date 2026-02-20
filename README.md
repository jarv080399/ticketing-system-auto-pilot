# NextGen IT Helpdesk & Ticketing System

A modern, full-featured IT helpdesk and ticketing system built with **Laravel 12**, **Vue.js 3**, **TailwindCSS 4**, **MySQL Percona**, and **Docker**.

---

## Tech Stack

| Layer     | Technology                  |
| --------- | --------------------------- |
| Backend   | Laravel 12 (PHP 8.3)        |
| Frontend  | Vue.js 3 (Composition API)  |
| Bundler   | Vite                        |
| Styling   | TailwindCSS 4               |
| Database  | MySQL 8+ (Percona Server)   |
| Cache     | Redis 7                     |
| Auth      | Laravel Sanctum + Socialite |
| Container | Docker + Docker Compose     |

## Quick Start

### Prerequisites

- Docker & Docker Compose
- Node.js 20+ (for local dev)
- PHP 8.2+ & Composer (for local dev)

### Docker (Recommended)

```bash
# 1. Clone the repository
git clone https://github.com/jarv080399/ticketing-system-auto-pilot.git
cd ticketing-system-auto-pilot

# 2. Copy environment file
cp .env.example .env

# 3. Build and start containers
docker compose up -d --build

# 4. Run migrations inside the container
docker compose exec app php artisan migrate

# 5. Visit the app
# App:     http://localhost:9093
# Mailpit: http://localhost:8025
```

### Local Development

```bash
# 1. Install PHP dependencies
composer install

# 2. Install Node dependencies
npm install

# 3. Copy environment file and generate key
cp .env.example .env
php artisan key:generate

# 4. Run migrations
php artisan migrate

# 5. Start dev servers (2 terminals)
php artisan serve        # Backend  → http://localhost:8000
npm run dev              # Frontend → handled by Vite HMR
```

## Project Structure

```
ticketing-system/
├── .agent/                # AI agent skills, rules, roadmap
├── app/
│   ├── Actions/           # Single-responsibility business logic
│   ├── Http/
│   │   ├── Controllers/   # Thin controllers
│   │   ├── Requests/      # Form Request validation
│   │   └── Resources/     # API Resource transformers
│   ├── Models/            # Eloquent models
│   ├── Policies/          # Authorization policies
│   └── Services/          # Complex business logic
├── database/
│   ├── migrations/        # Schema changes
│   ├── factories/         # Model factories
│   └── seeders/           # Seed data
├── docker/                # Docker configs (nginx, supervisor)
├── resources/
│   ├── js/                # Vue.js source
│   │   ├── components/    # Reusable Vue components
│   │   ├── composables/   # Composition API hooks
│   │   ├── layouts/       # Page layouts (App, Agent, Admin)
│   │   ├── pages/         # Route-level page components
│   │   ├── plugins/       # Axios, etc.
│   │   ├── router/        # Vue Router config
│   │   └── stores/        # Pinia stores
│   └── css/
│       └── app.css        # TailwindCSS imports & design tokens
├── routes/
│   ├── api.php            # API routes (v1)
│   └── web.php            # SPA catch-all
├── docker-compose.yml
├── Dockerfile
└── vite.config.js
```

## Documentation

- **[BRD.md](./BRD.md)** — Business Requirements Document
- **[FRD.md](./FRD.md)** — Functional Requirements Document
- **[Project-Setup.md](./Project-Setup.md)** — Architecture & Setup Guide
- **[Roadmap](./. agent/roadmap/)** — Module-by-module implementation plans

## License

Private — Internal Use Only
