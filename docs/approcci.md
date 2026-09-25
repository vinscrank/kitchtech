# Approcci e tecniche

Catalogo delle scelte. Ogni voce è corta: perché, un esempio, le alternative, la scelta. La specifica vincolante è in [brief.md](brief.md).

## Monolite modulare, un solo modulo

Perché: il codice è diviso per capacità, così una funzione nuova non si mescola al CRUD delle card.

Esempio: oggi esiste solo `Flashcard`. Una sessione di studio futura sarebbe un'altra cartella, una riga nel bootstrap, e una porta sua se deve leggere le card.

Alternative: tre cartelle globali `Controller/Service/Repository`; oppure un microservizio per le card.

Scelta: un modulo solo, più `Shared` per env, PDO, migrazioni, paginazione ed errori HTTP. Un secondo modulo senza una seconda capacità è struttura vuota. `Shared` non importa `Flashcard`. I moduli non si importano tra loro.

## Clean Architecture

Perché: front, back e identità non devono cambiare se cambia il framework o il database.

Esempio: `Flashcard` non contiene tipi di Slim né di PDO. Il test dell'use case gira senza MySQL.

Alternative: modello attivo legato a SQL; oppure controller che chiama PDO diretto.

Scelta: dominio al centro, use case che dipendono da una porta, controller e repository MySQL sul bordo.

## Slim 4

Perché: il brief vieta i framework pesanti e serve comunque un bordo HTTP.

Esempio: Slim registra `POST /flashcards` e il middleware CORS. L'use case `CreateFlashcard` non importa Slim.

Alternative: PHP puro con una tabella di route scritta a mano; Laravel o CodeIgniter.

Scelta: Slim solo per route, middleware e request/response. Laravel è citato dal brief come da evitare.

## Service, una classe per azione

Perché: ogni azione resta un file piccolo e testabile da solo.

Esempio: `ListFlashcards::handle` restituisce tutte le card. Il controller scrive solo il JSON. I file stanno in `Application/Service`.

Alternative: un `FlashcardService` con create, update, delete e list insieme.

Scelta: una classe per azione (`Create`, `Get`, `Update`, `Delete`, `List`), metodo `handle`. La cartella si chiama `Service`. Non è una classe sola.

## PHP-DI

Perché: gli use case ricevono `FlashcardRepository` dal costruttore, senza conoscere MySQL.

Esempio: in `config/container.php` il binding è `FlashcardRepository` verso `MysqlFlashcardRepository`. I test fanno `new CreateFlashcard(new InMemoryFlashcardRepository(), ...)`.

Alternative: `new MysqlFlashcardRepository` dentro l'use case; oppure un service locator chiamato dalle classi.

Scelta: il container sta solo in `container.php` e `public/index.php`. Nessuna classe lo chiama.

## Porta repository

Perché: cambiare motore SQL non tocca gli use case.

Esempio: `add`, `findById`, `update`, `delete`, `findAll` sull'interfaccia. MySQL sta in `MysqlFlashcardRepository`.

Alternative: query SQL negli use case; Eloquent o un altro ORM.

Scelta: interfaccia nel dominio, PDO con prepared statement nell'infrastruttura. In produzione MySQL. Nei test, repository in memoria. Il brief vieta la memoria solo come storage vero.

## DTO

Perché: la forma JSON non entra nell'entità, e l'entità non viene stampata così com'è.

Esempio: il validator produce `FlashcardInput`. La response esce da `FlashcardView` con `id`, `front`, `back`.

Alternative: passare l'array del JSON fino al dominio; serializzare l'entità.

Scelta: DTO in ingresso e in uscita. I campi JSON sconosciuti si ignorano, così un client che manda un campo in più non rompe la scrittura. Si leggono solo `front` e `back`.

## Validazione

Perché: il brief chiede di rifiutare i dati dell'utente con un messaggio utile.

Esempio: front di soli spazi. Il validator risponde 422 con `fields.front`.

Alternative: ripetere la stessa regola nell'entità e con un `CHECK` SQL.

Scelta: solo il validator. L'entità conserva le stringhe già controllate. Le query restano preparate: la validazione non basta contro l'SQL injection.

## Apache, entrypoint, Dockerfile

Perché: l'API gira in Docker insieme a MySQL, come chiede il brief se si usano i container.

Esempio: `GET /flashcards` non è un file su disco. `apache.conf` lo manda a `public/index.php`. All'avvio `entrypoint.sh` riprova la migrazione finché MySQL accetta connessioni, poi parte Apache.

Alternative: server interno di PHP (`php -S`), senza `apache.conf`; migrazione lanciata a mano, senza entrypoint.

Scelta: immagine `php:8.3-apache` pubblicata sulla porta 8080. Quei file non sono codice di dominio: Slim non li legge.

## Route

Perché: il contratto HTTP è una tabella leggibile, registrata in un solo punto.

Esempio: `GET /flashcards`, `GET /flashcards/{id}`, `POST /flashcards`, `PUT /flashcards/{id}`, `DELETE /flashcards/{id}`.

Alternative: route sparse nei controller; `PATCH` per l'aggiornamento parziale.

Scelta: le route del modulo stanno in `routes.php` e il bootstrap le registra. `PUT` richiede front e back. `PATCH` resta future work.

## Status HTTP

Perché: il client deve distinguere richiesta illeggibile, campo invalido e card assente.

Esempio: `{"front":""}` è 422 con `fields.front`. Un id che non esiste è 404. Un body che non è JSON è 400.

Alternative: 400 per tutti gli errori di input.

Scelta: 400 se il JSON è illeggibile, 415 senza `application/json`, 422 campi, 404 assente, 500 generico se `APP_DEBUG` è false. `POST` risponde 201 con `Location`.

## Envelope

Perché: il frontend legge sempre gli stessi campi.

Esempio: errore `{ "error": { "message", "fields" } }`. Lista e dettaglio `{ "data" }`.

Alternative: messaggio a volte in `error`, a volte in `detail`, a volte nel body grezzo.

Scelta: un solo formato di errore e un solo involucro `data` per le risposte riuscite. Lo scrive `JsonErrorHandler`.

## File del bordo HTTP

Perché: le scelte architetturali stanno nei confini del modulo, non nel numero di file.

Esempio: `Shared/Http` ha CORS, controllo del JSON, `HttpProblem`, `ExceptionMapper` e `JsonErrorHandler`.

Alternative: una classe per ogni dettaglio (`ErrorPayload`, `JsonResponder`, `MappedError`, un middleware per un solo header).

Scelta: cinque file. Il mapper esiste perché `Shared` non deve importare le eccezioni di `Flashcard`.

## MySQL

Perché: serve persistenza vera, e un server SQL rende credibili vincoli e più istanze dell'API.

Esempio: tabella `flashcards` con `id`, `front`, `back`. L'id è un UUID in `CHAR(36)` generato nel dominio. La lista ordina per `id`. Un UUID non segue l'ordine di creazione.

Alternative: SQLite, un file solo, meno servizi da avviare; PostgreSQL, con il tipo `UUID` nativo.

Scelta: MySQL 8 in Docker, volume persistente, `pdo_mysql`. Il dominio non nomina MySQL. SQLite sarebbe bastato per una collezione personale: si è accettato un servizio in più.

## Migrazioni

Perché: lo schema è versione nel git e si applica da solo all'avvio.

Esempio: `001_create_flashcards.sql` parte una volta. Il nome del file finisce in `schema_migrations`.

Alternative: creare la tabella a mano; un migrator di framework.

Scelta: file SQL e `bin/migrate.php`, senza dipendere da Laravel.

## Sicurezza senza login

Perché: non c'è autenticazione, ma l'API è comunque esposta al browser.

Esempio: il frontend su un'altra porta chiama l'API solo se la risposta ha l'origin di `CORS_ORIGIN`, anche sulla `OPTIONS`.

Alternative: CORS aperto a `*`; nessun controllo sul body.

Scelta: un solo origin, solo `application/json` su `POST` e `PUT`, prepared statement, 500 senza SQL, `expose_php` spento. Rate limit, login e HTTPS restano future work: in locale Docker parla HTTP.

## Delete

Perché: il client deve sapere se la card c'era.

Esempio: la seconda `DELETE` sullo stesso id risponde 404, come la `GET`.

Alternative: 204 anche se l'id è già assente, più comodo per un retry.

Scelta: 404. Stesso significato di "risorsa assente", coperto dai test.

## Test

Perché: il brief chiede test sulla logica critica, non sul framework.

Esempio: front vuoto, back solo spazi, stringa oltre 500, id assente in get/update/delete, create che salva i due campi.

Alternative: test HTTP contro Slim; test di integrazione contro MySQL.

Scelta: PHPUnit su validator e use case, con il repository in memoria in `tests/Support`.

## Frontend

Non è in questo giro. Quando si fa: SPA React e TypeScript. Next.js no, è un framework full-stack vietato dal brief. `react-router` sì, se servono lista e form.

Alternative per i dati: fetch sparso nei componenti. Scelta: un client tipizzato solo, così l'envelope di errore si legge in un file.

La lista ha tre stati distinti (caricamento, errore, vuoto). I form tengono il valore nello stato React. Un 422 del server compare sul campo: il backend resta l'autorità.

## Fuori dal codice

Futuro, senza implementazione a metà: filtro su front/back (e solo allora un indice di ricerca), `PATCH`, rate limit, concorrenza con `updated_at`, cancellazione logica, CQRS, secondo modulo.

Cache HTTP, Redis, memoizzazione React, virtualizzazione: su due campi pesano più di quanto servano.
