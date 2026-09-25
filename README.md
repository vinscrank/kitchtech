# KitchTech

API and React client to manage flashcards. Each card has a front and a back. The API runs in Docker. The client is in `frontend` and is not containerized.

## Setup

Docker is required. Composer is not required on the host: it runs inside the image during the build.

From the repository root:

```bash
docker compose up --build
```

MySQL is published on `localhost:33066`. The API listens on `http://localhost:18080`.

| Method | Path |
| --- | --- |
| GET | `/flashcards` |
| GET | `/flashcards/{id}` |
| POST | `/flashcards` |
| PUT | `/flashcards/{id}` |
| DELETE | `/flashcards/{id}` |

`POST` and `PUT` send `Content-Type: application/json` and a body with `front` and `back`. `POST` responds `201` with a `Location` header.

Stop the stack with Ctrl+C, then:

```bash
docker compose down
```

`docker compose down -v` also removes the MySQL volume.

## Frontend

The client is a React and TypeScript app in `frontend`. It is not containerized. The API must already be running.

```bash
cd frontend
npm install
npm run dev
```

Open `http://localhost:5173`. The dev server calls `http://localhost:18080`. Set `VITE_API_URL` to point elsewhere.

## Architectural decisions

The backend is PHP 8.3 with Slim 4 on the HTTP edge only. Laravel is excluded by the brief. One module, `Flashcard`, follows clean architecture: the entity and the repository interface do not know Slim or MySQL. Actions are one class each under `Application/Service`. PHP-DI is used only in `config/container.php` and `public/index.php`.

Data is stored in MySQL 8. The id is a UUID generated in the domain. The list returns every card, ordered by id. Validation of `front` and `back` lives in one validator and returns a field error with status 422. SQL uses prepared statements.

CORS allows the origin in `CORS_ORIGIN` because the frontend runs on another port.

A fuller catalog of choices, alternatives, and future work is in [docs/approcci.md](docs/approcci.md).

## Future work

Search on front or back, `PATCH`, rate limiting, authentication, HTTPS, optimistic locking, and a second module. The client already lists, flips, adds, edits, and deletes cards.
