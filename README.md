# Ramanandraibe Exportation App

Web application foundation for **Ramanandraibe Exportation** — built with **Symfony 7.1** and intended to manage exportation-related business operations (orders, clients, shipments, and related workflows).

> **Current status:** This repository is a **Symfony webapp skeleton** prepared for domain development. Core Symfony packages (Doctrine, Security, Twig, Mailer, Messenger, Asset Mapper, Stimulus/Turbo) are installed and configured, but **no business features are implemented yet** (no entities, controllers, migrations, or custom UI beyond the default base template).

---

## Table of contents

- [Overview](#overview)
- [Tech stack](#tech-stack)
- [Project structure](#project-structure)
- [Requirements](#requirements)
- [Getting started](#getting-started)
- [Environment configuration](#environment-configuration)
- [Docker services](#docker-services)
- [Database](#database)
- [Frontend assets](#frontend-assets)
- [Security](#security)
- [Testing](#testing)
- [Useful commands](#useful-commands)
- [Current state & roadmap](#current-state--roadmap)
- [License](#license)

---

## Overview

| Item | Detail |
|------|--------|
| **Project name** | RamanandraibeExportationApp |
| **Framework** | Symfony 7.1 |
| **PHP** | 8.2+ |
| **ORM** | Doctrine ORM 3.x |
| **Templates** | Twig |
| **Frontend** | Symfony Asset Mapper + Stimulus + Turbo (Hotwire) |
| **Intended database** | MariaDB / MySQL (local `.env`) or PostgreSQL 16 (Docker Compose recipe) |
| **Purpose** | Starting point for an exportation management application for Ramanandraibe Exportation |

The application entry point is `public/index.php`. Routing is attribute-based and loads controllers from `src/Controller/` (currently empty).

---

## Tech stack

### Backend

| Component | Package / version |
|-----------|-------------------|
| PHP | `>=8.2` |
| Symfony | `7.1.*` |
| Doctrine ORM | `^3.2` |
| Doctrine DBAL | `^3` |
| Doctrine Migrations | `^3.3` |
| Security | `symfony/security-bundle` |
| Forms & validation | `symfony/form`, `symfony/validator` |
| Mailer | `symfony/mailer` |
| Messenger | `symfony/messenger` + Doctrine transport |
| Notifier | `symfony/notifier` |
| Logging | `symfony/monolog-bundle` |
| Serialization | `symfony/serializer` |
| Internationalization | `symfony/translation`, `symfony/intl` |

### Frontend

| Component | Detail |
|-----------|--------|
| Asset pipeline | Symfony Asset Mapper (no Node/npm required) |
| Stimulus | `@hotwired/stimulus` 3.2.2 via `symfony/stimulus-bundle` |
| Turbo | `@hotwired/turbo` 7.3.0 via `symfony/ux-turbo` |
| Styles | `assets/styles/app.css` |
| Import map | `importmap.php` (entrypoint: `app`) |

### Development tooling

| Tool | Purpose |
|------|---------|
| Maker Bundle | Code generation (`make:entity`, `make:controller`, …) |
| Web Profiler | Debug toolbar (dev) |
| Debug Bundle | Dev debugging helpers |
| PHPUnit 9 + Symfony PHPUnit Bridge | Automated tests |
| Docker Compose | PostgreSQL + Mailpit |

---

## Project structure

```
RamanandraibeExportationApp/
├── assets/                 # JS, CSS, Stimulus controllers
│   ├── app.js
│   ├── bootstrap.js
│   ├── controllers/        # Stimulus controllers (demo: hello_controller)
│   └── styles/app.css
├── bin/
│   ├── console             # Symfony CLI
│   └── phpunit
├── config/
│   ├── bundles.php
│   ├── packages/           # Framework, Doctrine, Security, Mailer, …
│   ├── routes/             # Framework / security / profiler routes
│   ├── routes.yaml
│   └── services.yaml
├── migrations/             # Doctrine migrations (empty — ready for schema)
├── public/
│   └── index.php           # Front controller
├── src/
│   ├── Controller/         # HTTP controllers (empty)
│   ├── Entity/             # Doctrine entities (empty)
│   ├── Repository/         # Repositories (empty)
│   └── Kernel.php
├── templates/
│   └── base.html.twig      # Base Twig layout
├── tests/                  # PHPUnit tests (bootstrap only)
├── translations/           # i18n files (empty)
├── compose.yaml            # PostgreSQL service
├── compose.override.yaml   # Dev ports + Mailpit
├── composer.json
├── importmap.php
├── phpunit.xml.dist
└── .env                    # Environment defaults (do not commit secrets)
```

Application services under `App\` are autowired and autoconfigured via `config/services.yaml`. Entity classes and `Kernel` are excluded from service registration as usual.

---

## Requirements

- **PHP** 8.2 or higher, with extensions: `ctype`, `iconv`, and typical Symfony extensions (`pdo`, `json`, `mbstring`, `xml`, …)
- **Composer** 2.x
- **Database:** either
  - **MariaDB / MySQL** (matches the active `DATABASE_URL` pattern), or
  - **PostgreSQL 16** via Docker Compose
- **Docker** (optional) for database + Mailpit
- **Symfony CLI** (optional, recommended) for the local web server

---

## Getting started

### 1. Clone and install dependencies

```bash
git clone https://github.com/TiahMHeranto/RamanandraibeExportationApp.git
cd RamanandraibeExportationApp
composer install
```

### 2. Configure environment

Copy defaults and override locally (recommended — keep credentials out of git):

```bash
# Windows (PowerShell)
Copy-Item .env .env.local

# macOS / Linux
cp .env .env.local
```

Edit `.env.local`:

- Set a unique `APP_SECRET`
- Set `DATABASE_URL` for your database
- Set `MAILER_DSN` (required by mailer config), e.g. Mailpit:

```env
MAILER_DSN=smtp://localhost:1025
```

### 3. Start infrastructure (optional Docker)

```bash
docker compose up -d
```

This starts:

- **PostgreSQL** on port `5432`
- **Mailpit** SMTP on `1025`, web UI on `8025`

If you use Compose PostgreSQL, point `DATABASE_URL` at Postgres, for example:

```env
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
```

If you use a local MariaDB/MySQL instance instead, keep a MySQL-style URL and skip (or ignore) the Compose database service.

### 4. Create the database (once entities/migrations exist)

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

> These commands will only be meaningful after entities and migrations are added.

### 5. Run the application

With Symfony CLI:

```bash
symfony server:start
```

Or with PHP’s built-in server:

```bash
php -S 127.0.0.1:8000 -t public
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000). Until a homepage controller is added, the root URL will return **404**.

---

## Environment configuration

Symfony loads env files in this order (later wins):

1. `.env`
2. `.env.local` (uncommitted overrides — preferred for secrets)
3. `.env.$APP_ENV`
4. `.env.$APP_ENV.local`
5. Real OS environment variables

### Main variables

| Variable | Description |
|----------|-------------|
| `APP_ENV` | Environment (`dev`, `test`, `prod`) |
| `APP_SECRET` | Application secret (change in every environment) |
| `DATABASE_URL` | Doctrine connection URL |
| `MESSENGER_TRANSPORT_DSN` | Async messaging transport (default: Doctrine) |
| `MAILER_DSN` | Mailer DSN (must be set when mailer is used) |

### Docker Compose variables

| Variable | Default | Description |
|----------|---------|-------------|
| `POSTGRES_VERSION` | `16` | PostgreSQL image tag |
| `POSTGRES_DB` | `app` | Database name |
| `POSTGRES_USER` | `app` | Database user |
| `POSTGRES_PASSWORD` | `!ChangeMe!` | Database password |

> **Security note:** Do not commit real passwords or production secrets. Prefer `.env.local` and Symfony secrets for production. Rotate any credentials that were previously committed in `.env`.

---

## Docker services

Defined in `compose.yaml` and `compose.override.yaml`:

| Service | Image | Ports | Role |
|---------|-------|-------|------|
| `database` | `postgres:16-alpine` | `5432` | Application database (Compose recipe) |
| `mailer` | `axllent/mailpit` | `1025` (SMTP), `8025` (UI) | Local email catcher |

```bash
docker compose up -d          # start
docker compose ps             # status
docker compose down           # stop
```

Mailpit UI: [http://127.0.0.1:8025](http://127.0.0.1:8025)

---

## Database

- Doctrine is configured with **attribute mapping** on `src/Entity`
- Naming strategy: underscore
- Migrations live in `migrations/`
- Test environment appends a `_test` suffix to the database name

**Important alignment note:** Docker Compose ships a **PostgreSQL** recipe, while the active project `.env` is oriented toward **MariaDB** (`Ramanandraibe_Exportation_Database`). Choose one stack and keep `DATABASE_URL` + Compose consistent before developing schema features.

---

## Frontend assets

No Node.js build step is required. Assets are managed by **Asset Mapper** and the PHP import map.

| File | Role |
|------|------|
| `assets/app.js` | Main entry (loads Stimulus bootstrap + CSS) |
| `assets/bootstrap.js` | Starts the Stimulus application |
| `assets/controllers/hello_controller.js` | Demo Stimulus controller |
| `assets/styles/app.css` | Global styles |
| `importmap.php` | JS dependency map |

After dependency changes:

```bash
php bin/console importmap:install
php bin/console asset-map:compile   # for production
```

---

## Security

Current setup is the **default Symfony Security recipe**:

- Password hasher: `auto`
- User provider: **in-memory** (`memory: null` — no users defined)
- Firewalls: `dev` (profiler/assets) and `main` (lazy, no login authenticator yet)
- Access control rules: commented examples only

Authentication, user entities, roles, and login/logout flows are **not implemented** yet. They should be added when building the first protected features (for example with `make:user` and form login or a custom authenticator).

---

## Testing

PHPUnit is configured via `phpunit.xml.dist`. Test bootstrap loads Dotenv from `tests/bootstrap.php`.

```bash
php bin/phpunit
```

There are **no application test classes yet**. Add unit/functional tests under `tests/` as features are built.

---

## Useful commands

```bash
# Cache
php bin/console cache:clear

# Code generation
php bin/console make:controller
php bin/console make:entity
php bin/console make:migration
php bin/console make:user
php bin/console make:auth

# Doctrine
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:schema:validate

# Routes / debug
php bin/console debug:router
php bin/console debug:autowiring
php bin/console about
```

---

## Current state & roadmap

### What is already in place

- Symfony 7.1 webapp skeleton with Flex recipes
- Doctrine ORM + Migrations wiring
- Security, Mailer, Messenger, Notifier, Monolog
- Twig base layout
- Asset Mapper + Stimulus + Turbo
- Docker Compose for PostgreSQL and Mailpit
- PHPUnit bootstrap
- Project naming and database naming oriented toward Ramanandraibe Exportation

### What is not built yet

- Domain entities and repositories
- Controllers, forms, and business services
- Authentication and authorization (roles, login)
- Application Twig pages / product UI
- Database migrations and schema
- Automated tests for features
- Documented business workflows (clients, products, shipments, documents, etc.)

### Suggested next steps

1. Align database choice (MariaDB **or** PostgreSQL) and document the chosen URL in `.env.local`
2. Set `MAILER_DSN` for local development
3. Model the exportation domain (entities) with Maker + migrations
4. Add authentication (`User` entity + login)
5. Build first CRUD / dashboards for core business objects
6. Add functional tests and a clear contribution workflow

---

## License

This repository includes an **Apache License 2.0** (`LICENSE`).  
`composer.json` currently declares `"license": "proprietary"` — update that field if the public Apache license is the intended project license.
