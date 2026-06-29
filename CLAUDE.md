# CLAUDE.md

Guidance for Claude when working in this repository.

## Project

**Daily Bulletin** — a Laravel 12 / PHP 8.2 news-aggregator REST API. It fetches
articles from multiple providers (NewsOrg, The Guardian, NYTimes), normalizes them
into a unified shape, stores them, and serves them via REST. Auth is via Sanctum;
users set preferences (categories / sources) that drive a personalized feed.

This is a personal architecture-practice project. Code quality, clear design, and
sound architectural reasoning matter more than shipping speed.

## Tech Stack

- PHP 8.2, Laravel 12
- MySQL 8 (SQLite for tests)
- Laravel Sanctum (token auth)
- Queues + Scheduler for ingestion
- PHPUnit, Mockery, Faker
- Scribe (API docs), Pint (formatting)
- Docker (app / nginx / mysql / queue / scheduler)

## Common Commands

```bash
php artisan test                          # run full suite
php artisan test --testsuite=Unit         # unit only
php artisan test --testsuite=Feature      # feature only
php artisan test --filter=SomeTest        # single test
php artisan fetch:news                     # dispatch ingestion jobs
php artisan queue:work                     # process the queue
php artisan schedule:work                  # run the scheduler locally
./vendor/bin/pint                          # format code (PSR-12)
php artisan scribe:generate                # regenerate API docs
docker-compose up -d --build               # full containerized stack
```

## Architecture

Ingestion flow:

```
fetch:news command  ->  FetchNews job (queued, per provider+category)
   -> NewsApiInterface implementation (NewsOrg | Guardian | NYTimes)
   -> AbstractNewsApiService.fetch()  (fetch + normalize)
   -> ArticleService  (persist)
   -> Database
```

Key building blocks:

- **`app/Contracts/NewsApiInterface.php`** — the provider contract
  (`getName`, `getRateLimit`, `fetch`). This is the Strategy/Adapter seam: every
  news source is interchangeable behind it.
- **`app/Services/NewsApi/AbstractNewsApiService.php`** — shared fetch + transform
  pipeline. Concrete providers (`NewsOrg`, `Guardian`, `NYTimes`) supply config
  (URL, endpoint, query params, response key, field mapping) via the
  `app/Traits/Property/*` traits. New providers are added by subclassing this and
  setting their constants/config — not by changing the pipeline.
- **`app/Services/ArticleService.php`** — persistence / domain logic for articles.
- **`app/QueryBuilders/ArticleQueryBuilder.php`** — encapsulates article query
  composition (search, filters by category/source/date).
- **`app/Http/Controllers/Api/*`** — thin controllers; validation lives in
  `app/Http/Requests/*`, output shaping in `app/Http/Resources/*`.

## Conventions

- **Thin controllers, fat services.** Controllers validate (FormRequest) and
  delegate to a Service; business logic does not live in controllers.
- **FormRequests for all input validation** (`app/Http/Requests/`).
- **API Resources for all responses** (`app/Http/Resources/`).
- **New news provider** = new class under `app/Services/NewsApi/` extending
  `AbstractNewsApiService` + provider config. Do not special-case providers in the
  pipeline or the job.
- Follow **PSR-12**; run Pint before considering work done.
- Add/extend tests for any new service or endpoint.

## Layout

```
app/
  Console/Commands/   artisan commands (FetchNews)
  Contracts/          interfaces (NewsApiInterface)
  Http/               Controllers/ Requests/ Resources/
  Jobs/               queued work (FetchNews)
  Models/             Eloquent models
  QueryBuilders/      query composition
  Services/           business logic; Services/NewsApi/ = providers
  Schedules/          scheduled task definitions
  Traits/             Api/ and Property/ provider config traits
tests/  Feature/  Unit/
docker/ nginx/ php/ scheduler/
```

## Working agreement

Rohit is a senior developer training toward technical architect. When working here,
act as a mentor: name the patterns/trade-offs behind suggestions, flag weak designs
and over-engineering, and prefer the simplest design the problem actually needs.
Important design discussions are logged in `.claude/imp-discussions.md`.
