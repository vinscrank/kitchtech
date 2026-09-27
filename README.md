# Flashcard Application

A small full-stack flashcard application built with PHP, React, and TypeScript.

The goal was to keep the solution simple, clear, and easy to extend without adding unnecessary complexity.

## Setup Instructions

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

If the API runs at a different address, set `VITE_API_URL`.

## API Endpoints

| Method | Path |
|---|---|
| GET | `/flashcards` |
| GET | `/flashcards/{id}` |
| POST | `/flashcards` |
| PUT | `/flashcards/{id}` |
| DELETE | `/flashcards/{id}` |

## Architectural Decisions

### Backend

The backend uses a lightweight Clean Architecture.

```text
HTTP
 ↓
Application
 ↓
Domain

Infrastructure implements Domain contracts
```

- **Domain** contains the Flashcard model, its rules, and the repository contract.
- **Application** contains the use cases: list, get, create, update, and delete.
- **Infrastructure** contains the HTTP layer and the MySQL repository.

The Domain does not depend on Slim, PDO, or MySQL.

Slim 4 is used only for HTTP concerns such as routing and request/response handling. It avoids writing this infrastructure by hand while staying lighter than a full framework.

Dependencies are wired manually in `public/index.php`. The dependency graph is small, so a DI container would add more complexity than value.

Use cases depend on the `FlashcardRepository` contract instead of the MySQL implementation. This keeps persistence replaceable and makes the use cases easier to test.

### Frontend

The frontend is organized by feature.

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

Flashcard-specific code stays together, while reusable API, query, and UI code lives in `shared`.

TanStack Query is used for loading, caching, errors, and refreshing data after create, update, and delete operations.

Tailwind CSS is used for styling, and Sonner is used for user feedback after mutations.

### Technology and Storage

- Backend: PHP 8.3, Slim 4, PDO, PHPUnit
- Database: MySQL 8
- Frontend: React 18, TypeScript, Vite, React Router, TanStack Query, Sonner, Tailwind CSS

MySQL was chosen as persistent storage. SQLite would also have been enough for this task, but MySQL better represents an external database service.

### Trade-offs

- **Slim instead of plain PHP:** less HTTP boilerplate, while keeping the core independent from the framework.
- **Manual dependency wiring:** simple and explicit for a small project; a DI container would make more sense in a larger application.
- **Clean Architecture:** adds some files and structure, but keeps business rules, use cases, HTTP, and persistence separated.
- **MySQL instead of SQLite:** adds setup complexity, but gives a more realistic external persistence layer.
- **TanStack Query:** adds one dependency, but avoids repeating server-state logic in the pages.
- **Feature-based frontend structure:** adds some nesting, but keeps each feature self-contained.

## Validation and Error Handling

Input is validated before it reaches the Domain, while the Flashcard entity still protects its own rules.

The API returns:

- `400 Bad Request` for invalid IDs
- `404 Not Found` when a flashcard does not exist
- `422 Unprocessable Entity` for invalid flashcard data

## Testing

Backend tests cover critical Domain and Application behavior.

Use cases can be tested with an in-memory repository, so most tests do not require MySQL.

## Future Work

With more time, I would add:

- pagination
- a `created_at` field and proper sorting
- search on front and back
- authentication and card ownership
- API integration tests
- end-to-end frontend tests
