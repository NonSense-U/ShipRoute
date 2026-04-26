# Laravel 13 Project

A Laravel 13 open-source project with a Docker-based development environment for quick and easy setup.

This project supports both:

* Native local development (PHP + Composer + Node.js installed locally)
* Full Docker-based development using Docker Compose

---

## Requirements

### For Native Development

Make sure you have installed:

* PHP (compatible with Laravel 13)
* Composer
* Node.js + npm
* Docker + Docker Compose (for MySQL + Redis services)

---

## Initial Setup

### 1. Clone Repository

```bash
git clone <repo-url>
cd <project-folder>
```

### 2. Environment Setup

```bash
cp .env.example .env
```

#### docker env
```bash
cp .env.docker.example .env.docker
```

### 3. Install Dependencies

#### PHP dependencies

```bash
composer install
```

#### Node dependencies

```bash
npm install
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

---

## Running Natively

If you prefer running Laravel directly on your machine while using Docker only for services like MySQL and Redis:

### Start Database Services

```bash
docker compose up mysql
```

This will start:

* Postgres
* Redis

### Run Migrations (first time setup)

```bash
php artisan migrate:fresh --seed
```

### Start Frontend Dev Server

```bash
npm run dev
```

### Start Laravel Server

You can then safely run either:

```bash
php artisan serve
```

or

```bash
composer run dev
```

---

## Running Fully with Docker

If you want the full project environment inside Docker:

```bash
docker compose up --build
```

This will:

* Build the required Docker images
* Start the full application stack
* Launch all required services automatically

# Docker Guide

When using Docker Compose, you can interact with the Laravel application using:

```bash
docker compose exec app
```

This allows you to run Artisan, Composer, npm, and other Laravel commands inside the container.

---

## Common Commands

### Reset Database

```bash
docker compose exec app php artisan migrate:fresh --seed
```

---

### Run Migrations

```bash
docker compose exec app php artisan migrate
```

---

### Seed Database

```bash
docker compose exec app php artisan db:seed
```

---

### Clear Application Cache

```bash
docker compose exec app php artisan optimize:clear
```

---

### Install Composer Dependencies

```bash
docker compose exec app composer install
```

---

### Install Node Dependencies

```bash
docker compose exec app npm install
```

---

### Run Tests

```bash
docker compose exec app php artisan test
```

---

### Open Laravel Tinker

```bash
docker compose exec app php artisan tinker
```

---

## Stopping Containers

To stop all running containers:

```bash
docker compose down
```

To stop and remove volumes:

```bash
docker compose down -v
```

---

## Notes

* Use `composer run dev` for the best local development experience
* Use Docker if you want environment consistency across all machines
* Redis and MySQL are included in the Docker setup for convenience
* Frontend assets are managed via Vite (`npm run dev`)

---

## License

This project is open-source and available under the MIT License.
