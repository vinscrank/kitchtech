# KitchTech

API to manage flashcards. Each card has a front and a back. The React client is not in this repository yet.

## Setup

From the repository root, start MySQL and the API. Composer runs inside the image during the build.

```bash
docker compose up --build
```

The API listens on `http://localhost:18080`.

## Architectural decisions

The backend is PHP 8.3 with Slim 4 on the HTTP edge only. Laravel is excluded by the brief. One module, `Flashcard`, follows clean architecture: the entity and the repository interface do not know Slim or MySQL. Actions are one class each under `Application/Service`. PHP-DI is used only in `config/container.php` and `public/index.php`.

Data is stored in MySQL 8. The id is a UUID generated in the domain. The list returns every card, ordered by id. Validation of `front` and `back` lives in one validator and returns a field error with status 422. SQL uses prepared statements.

CORS allows the origin in `CORS_ORIGIN` because the frontend runs on another port.

A fuller catalog of choices, alternatives, and future work is in [docs/approcci.md](docs/approcci.md).

## Future work

Search on front or back, `PATCH`, rate limiting, authentication, HTTPS, optimistic locking, and a second module. The React and TypeScript client is the next piece: a list with loading, error, and empty states, and forms to add, edit, and delete.
