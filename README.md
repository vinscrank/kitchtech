# Flashcard Application

A small full-stack flashcard application built with PHP, React, and TypeScript.

The focus of this assessment was to keep the solution simple, testable, and easy to extend without introducing unnecessary framework coupling.

## Setup

### Backend

From the repository root:

```bash
docker compose up --build
```

API:

```text
http://localhost:18080
```

MySQL:

```text
localhost:33066
```

### Frontend

From the `frontend` directory:

```bash
npm install
npm run dev
```

Open:

```text
http://localhost:5173
```

Set `VITE_API_URL` if the API is running at a different address.

## API

| Method | Path |
|---|---|
| GET | `/flashcards` |
| GET | `/flashcards/{id}` |
| POST | `/flashcards` |
| PUT | `/flashcards/{id}` |
| DELETE | `/flashcards/{id}` |

## Architecture

### Backend

The backend follows a lightweight Clean Architecture approach.

```text
HTTP / Slim
    ↓
Application
    ↓
Domain

Infrastructure ──implements──> Domain contracts
```

- **Domain** contains the core model, business rules, `FlashcardId`, and the `FlashcardRepository` contract.
- **Application** contains the use cases and coordinates validation, domain objects, and persistence.
- **Infrastructure** contains Slim controllers/routes and the MySQL repository implementation.

The Domain does not depend on Slim, PDO, or MySQL.

Slim 4 is used only for the HTTP layer. It was chosen instead of plain PHP to avoid reimplementing routing, middleware, and HTTP handling, while remaining much lighter than a full framework such as Laravel.

Dependencies are wired manually in `public/index.php`, which acts as the composition root.

`php-di/slim-bridge` is intentionally not used. The dependency graph is small, so dependencies are wired explicitly in one place and remain easy to inspect.

For example, use cases depend on:

```text
FlashcardRepository
```

rather than directly on:

```text
MysqlFlashcardRepository
```

This allows the persistence implementation to be replaced without changing the use case. Tests can use an in-memory repository instead of MySQL.

### Frontend

The frontend is organized by feature:

```text
src/
├── domains/
│   └── flashcard/
│       ├── api/
│       ├── components/
│       ├── hooks/
│       ├── model/
│       └── pages/
└── shared/
    ├── api/
    ├── query/
    └── ui/
```

Flashcard-specific code stays inside the feature. A new feature can be added as a new folder instead of spreading its files across global `components` and `api` directories.

Generic API and query code lives in `shared`. The screens are styled with Tailwind classes.

TanStack Query manages server state, loading, errors, caching, and cache invalidation after mutations.

Sonner is used for mutation feedback so success messages remain visible after navigating back to the list.

## Technology

- Backend: PHP 8.3, Slim 4, PDO, PHPUnit
- Database: MySQL 8
- Frontend: React 18, TypeScript, Vite, React Router, TanStack Query, Sonner, Tailwind CSS

## Trade-offs

- **Slim over plain PHP:** reduces HTTP boilerplate while keeping the core application independent from a heavy framework.
- **Manual dependency wiring:** more explicit and easier to follow for a small dependency graph; a DI container would become more useful as the application grows.
- **MySQL over SQLite:** adds setup complexity and an extra container, but better represents an external persistence service.
- **Clean Architecture:** introduces more files and abstractions for a small CRUD application, but keeps domain rules, use cases, HTTP, and persistence independently testable and replaceable.
- **TanStack Query:** adds a frontend dependency, but centralizes server-state management, caching, loading, errors, and mutation invalidation.
- **Feature-based frontend structure:** adds some nesting initially, but keeps each feature colocated and makes future domains easier to add.

## Validation and Error Handling

- `FlashcardInputValidator` validates the request shape and normalizes input, while `Flashcard` enforces domain invariants such as non-empty text and the 500-character limit.
- Invalid flashcard data is mapped to a `422 Unprocessable Entity` response with field-level errors.
- `FlashcardId::fromString` accepts only UUID v4 values. Invalid IDs return `400 Bad Request`.
- A valid ID with no matching row returns `null` from the repository, which is converted into `FlashcardNotFound` and returned as `404 Not Found`.

## Testing

Backend tests cover critical domain and application behavior.

Use cases can be tested with an in-memory repository, so most tests do not require MySQL.

## Future Work

With more time, I would add:

- pagination to `GET /flashcards`, which currently returns every row
- a `created_at` column, because the list is currently ordered by UUID rather than creation time
- search across front and back, with an appropriate database index once that query exists
- authentication and card ownership
- API integration tests
- end-to-end frontend tests
