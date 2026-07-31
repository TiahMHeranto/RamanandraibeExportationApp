# Ramanandraibe Exportation App

Operations workspace for **Ramanandraibe Exportation** — manage export clients, product catalog, and ocean freight shipments.

Built with **Symfony 7.1**, Doctrine, Twig, and Hotwire (Stimulus + Turbo).

---

## Features

- Secure login (form authentication)
- Dashboard with live counts
- **Personnels** CRUD (imported from `ListeDesPersonnels.xlsx` — N° + nom)
- **Fournisseurs** CRUD (imported from `LISTE FRNS RAPHIA.xlsx` — code, nom, zone)
- **Hangars** CRUD (hangar numéro + code — traitement)
- **Arrivages** CRUD (N°, fournisseur, origine, poids, date)
- **Clients** CRUD (international buyers)
- **Products** catalog (vanilla, spices, cocoa, oils, …)
- **Shipments** with cargo lines, ports, status workflow
- Demo data fixtures for a ready-to-try workspace

### Import personnels

```bash
php bin/console app:import-personnels
# optional: wipe then reimport
php bin/console app:import-personnels --purge
```

Source CSV: `data/personnels.csv` (275 unique numbers from the Excel list).

### Import fournisseurs

```bash
php bin/console app:import-fournisseurs
php bin/console app:import-fournisseurs --purge
```

Source CSV: `data/fournisseurs.csv` (39 fournisseurs raphia from `LISTE FRNS RAPHIA.xlsx`).

---

## Requirements

- PHP 8.2+ (tested on 8.4) with `pdo_sqlite` (default) or MySQL/MariaDB/PostgreSQL
- Composer 2.x

---

## Quick start

```bash
composer install
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
php -S 127.0.0.1:8000 -t public
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000)

### Demo login

| Field | Value |
|-------|--------|
| Email | `admin@ramanandraibe.mg` |
| Password | `admin123` |

---

## Database

By default the app uses **SQLite** (`var/data.db`) so you can run without installing MariaDB:

```env
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"
```

To use MariaDB/MySQL instead, set in `.env.local`:

```env
DATABASE_URL="mysql://USER:PASSWORD@127.0.0.1:3306/Ramanandraibe_Exportation_Database?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
```

Then recreate schema:

```bash
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction
```

> Note: the committed migration is written for **SQLite**. For MySQL, regenerate with `php bin/console doctrine:migrations:diff` after switching the URL (or ask to add a MySQL migration).

---

## Main routes

| Path | Description |
|------|-------------|
| `/login` | Sign in |
| `/` | Dashboard |
| `/clients` | Buyer directory |
| `/products` | Export catalog |
| `/shipments` | Consignments & cargo |

---

## Project structure

```
src/
  Controller/     Dashboard, Clients, Products, Shipments, Security
  Entity/         User, Client, Product, Shipment, ShipmentLine
  Enum/           ShipmentStatus
  Form/           Symfony forms
  DataFixtures/   Demo admin + sample export data
  Repository/     Doctrine repositories
templates/        Twig UI
assets/           CSS + Stimulus controllers
migrations/       Database schema
```

---

## Useful commands

```bash
php bin/console cache:clear
php bin/console doctrine:fixtures:load --no-interaction
php bin/console debug:router
php bin/phpunit
```

---

## Tech stack

- Symfony 7.1 (Framework, Security, Form, Validator, Mailer, Messenger)
- Doctrine ORM 3 + Migrations + Fixtures
- Twig + Asset Mapper
- Stimulus / Turbo

---

## License

See `LICENSE` (Apache 2.0). `composer.json` may still list `proprietary` — align as needed.
