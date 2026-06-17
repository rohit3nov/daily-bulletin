# Daily Bulletin

A Laravel-based News Aggregator API that collects articles from multiple news providers, normalizes the data into a unified structure, stores them efficiently, and exposes RESTful APIs for consumption by web or mobile applications.

---

## Features

### News Aggregation

* Aggregates articles from multiple providers
* Supports:

  * NewsOrg
  * The Guardian
  * New York Times
* Normalizes data into a common format
* Stores articles and categories in a centralized database

### Authentication

* User Registration
* Login
* Logout
* Forgot Password
* Reset Password
* Change Password

Powered by Laravel Sanctum.

### Articles

* Paginated article listing
* Article detail endpoint
* Search support
* Filter by:

  * Category
  * Source
  * Date

### User Preferences

* Preferred categories
* Preferred news sources
* Personalized news feed

### Background Processing

* Queue-based article ingestion
* Scheduled news fetching
* Rate-limited provider access

### Documentation

* Scribe-generated API documentation

### Testing

* Unit Tests
* Feature Tests
* Integration Tests

### Containerization

* Dockerized application
* Dedicated containers for:

  * PHP Application
  * Nginx
  * MySQL
  * Queue Worker
  * Scheduler

---

## Architecture

```text
fetch:news Command
        │
        ▼
FetchNews Job
        │
        ▼
NewsApiInterface
        │
 ┌──────┼──────┐
 ▼      ▼      ▼
NewsOrg Guardian NYTimes
        │
        ▼
ArticleService
        │
        ▼
Database
```

---

## Tech Stack

* PHP 8.2
* Laravel 12
* MySQL 8
* Laravel Sanctum
* Docker
* Nginx
* PHPUnit
* Scribe

---

## Installation

### Clone Repository

```bash
git clone https://github.com/rohit3nov/daily-bulletin.git
cd daily-bulletin
```

### Install Dependencies

```bash
composer install
```

### Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Configure database credentials and API keys.

### Run Migrations

```bash
php artisan migrate
```

---

## External News Provider Configuration

Configure API credentials inside `.env`.

Example:

```env
NEWSORG_API_KEY=
GUARDIAN_API_KEY=
NYTIMES_API_KEY=
```

---

## Running Locally

```bash
php artisan serve
```

Run queue worker:

```bash
php artisan queue:work
```

Run scheduler:

```bash
php artisan schedule:work
```

---

## Fetch News Manually

Dispatch news aggregation jobs:

```bash
php artisan fetch:news
```

This command:

* Iterates through configured providers
* Iterates through configured categories
* Dispatches queue jobs
* Fetches articles asynchronously

---

## Docker Setup

Build and start containers:

```bash
docker-compose up -d --build
```

Application:

```text
http://localhost:8000
```

### Available Containers

| Container      | Purpose             |
| -------------- | ------------------- |
| news-app       | Laravel Application |
| news-nginx     | Web Server          |
| news-mysql     | Database            |
| news-queue     | Queue Worker        |
| news-scheduler | Scheduler           |

---

## Scheduler Setup

The project includes a dedicated scheduler container.

Scheduler script:

```text
docker/scheduler/schedule-cron.sh
```

Executes:

```bash
php artisan schedule:run
```

every minute.

---

## API Documentation

Generate docs:

```bash
php artisan scribe:generate
```

Access documentation:

```text
/public/docs
```

---

## Testing

Run all tests:

```bash
php artisan test
```

Run specific suites:

```bash
php artisan test --testsuite=Unit
```

```bash
php artisan test --testsuite=Feature
```

---

## Project Structure

```text
app/
├── Console/
├── Contracts/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
├── Jobs/
├── Models/
├── Services/
└── Providers/

tests/
├── Feature/
└── Unit/

docker/
├── nginx/
├── php/
└── scheduler/
```

---

## Future Improvements

* Redis caching
* Elasticsearch integration
* Real-time notifications
* Article recommendation engine
* AI-powered summarization
* Trending articles analytics
* Multi-language support

---

## Author

Rohit

Daily Bulletin was built as a production-style Laravel News Aggregator demonstrating API design, service-oriented architecture, queue processing, scheduling, testing, and containerized deployment.
