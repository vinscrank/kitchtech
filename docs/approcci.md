# Approcci e tecniche

Catalogo da aggiornare quando si implementa una feature. Non è una checklist da completare: è l’elenco delle scelte di cui si può parlare al follow-up tecnico.

Ogni voce dice cos’è, perché sta in questa app di flashcard, e il punto da tenere pronto in sede di colloquio. La specifica vincolante è in [brief.md](brief.md).

## Architettura

Struttura prevista per il backend: Clean Architecture, al posto di una layered architecture classica (controller, service, repository sullo stesso modello di dati).

### Clean Architecture

Il codice è diviso in anelli. Il dominio non importa nulla dagli anelli esterni. Gli use case orchestrano il dominio. Gli adapter traducono HTTP e database verso l’interno. La presentation (handler HTTP) sta sul bordo.

In un CRUD di flashcard il dominio è piccolo (front, back, identità). Il valore non è la complessità del modello, è dimostrare che la regola di business non dipende da PDO né da `$_POST`. Al colloquio: il dominio si testa senza web server e senza database.

### Dependency inversion

Gli use case dipendono da un’interfaccia (la porta), non dalla classe PDO. L’implementazione concreta viene iniettata da fuori.

Senza un container di framework, la composizione sta in un unico punto di avvio: lì si costruiscono repository, validator e handler e si passano le dipendenze. Al colloquio: un solo posto sa quali classi concrete esistono; il resto del codice vede solo contratti.

### Repository come porta

`FlashcardRepository` è un’interfaccia nel dominio o nell’application layer. `SqliteFlashcardRepository` sta nell’infrastruttura.

Cambiare storage (SQLite, MySQL, file) non tocca gli use case. Al colloquio: i test degli handler usano un repository in memoria finto, mentre l’app vera persiste su disco. La specifica vieta lo storage solo in memoria in produzione, non nei test.

### Composizione in un unico punto

Niente service locator sparso. Il front controller (o un bootstrap) istanzia le dipendenze e le consegna.

Al colloquio: si può indicare il file e dire “qui si vede l’intero grafo”. Aggiungere un secondo storage significa cambiare quel file, non cercare `new` nel progetto.

## API e backend PHP

### Front controller

Tutte le richieste passano da un unico `index.php`. Il web server riscrive gli URL verso quel file.

Un solo punto applica header comuni, legge il body, delega al router e traduce le eccezioni in risposte HTTP. Al colloquio: errori e CORS non sono duplicati in ogni endpoint.

### Routing esplicito

Una tabella metodo + path verso un handler. Niente magia da framework.

Risorse previste: `GET /flashcards`, `GET /flashcards/{id}`, `POST /flashcards`, `PUT /flashcards/{id}` oppure `PATCH`, `DELETE /flashcards/{id}`. Al colloquio: la tabella è la documentazione dell’API; un path nuovo è una riga, non una convenzione implicita.

### DTO di input

Il JSON in ingresso diventa un oggetto dedicato (`CreateFlashcard`, `UpdateFlashcard`) prima di toccare il dominio.

Il dominio non conosce la forma HTTP. Campi extra nel payload si ignorano o si rifiutano in modo esplicito, e la scelta va detta. Al colloquio: la validazione lavora sul DTO, non su un array associativo passato di mano in mano.

### Validazione separata dagli handler

Un validator controlla front e back: presenti, stringhe, lunghezza massima, niente solo spazi. L’handler non contiene `if` di formato.

Gli errori di validazione tornano 400 con un messaggio per campo. Un id mancante è 404, non 400. Al colloquio: si distingue input invalido da risorsa assente, e i messaggi sono utili al frontend.

### Envelope di errore coerente

Ogni errore ha la stessa forma, per esempio `{ "error": { "message": "...", "fields": { "front": "..." } } }`.

Il frontend non deve indovinare se il messaggio sta in `error`, `detail` o nel body grezzo. Al colloquio: un solo contratto per il fallimento, status HTTP per la classe dell’errore (400, 404, 422, 500) scelti e motivati, senza mescolarli.

### PDO e query preparate

L’accesso a SQL passa da PDO con prepared statement. I valori utente non vengono concatenati nella query.

È la difesa minima contro SQL injection, obbligatoria anche in un’app senza login. Al colloquio: si mostra il bind dei parametri e si dice che la validazione non sostituisce il prepared statement.

### SQLite come storage

File SQLite sul disco. Persiste tra i riavvii, non richiede un server database, entra in Docker con un volume.

Trade-off: niente concorrenza di scrittura da molti client, niente utenti multipli seri. Per una collezione personale di flashcard è proporzionato. MySQL avrebbe senso con più processi di scrittura o con un ambiente già basato su quel motore; qui aggiungerebbe un servizio da operare senza cambiare il modello. Al colloquio: la scelta è di semplicità operativa, e la porta repository lascia MySQL come sostituzione successiva.

### Paginazione offset

`GET /flashcards?page=1&per_page=20` con `LIMIT` e `OFFSET`, più un totale per sapere quante pagine esistono.

La lista non cresce senza limite: il brief chiede “tutte le flashcard”, ma caricarle tutte insieme non regge se la collezione aumenta. Offset è semplice da spiegare; il cursore è migliore se si inserisce in testa mentre si pagina, e può restare in Future work. Al colloquio: offset va bene finché gli insert non spostano le pagine sotto i piedi dell’utente.

### Filtri

Query opzionale su front o back, applicata nel repository con `LIKE` e parametro bound, non nel PHP dopo aver letto tutta la tabella.

Al colloquio: il filtro sta nella query, così la paginazione conta solo le righe che matchano.

### CORS

Il frontend (altra origin in sviluppo) può chiamare l’API solo se il backend manda gli header CORS giusti, incluso `OPTIONS` per il preflight.

Al colloquio: in Docker le due app hanno porte diverse, quindi CORS è un requisito reale, non un extra. In produzione stessa origin si può restringere l’allowlist.

### Delete idempotente

`DELETE` su un id già rimosso può rispondere 204 sempre, oppure 404 la seconda volta. La scelta va fissata e testata.

Al colloquio: 204 ripetuto è più semplice per il client che riprova; 404 è più preciso. Si dice quale si è scelto e perché, senza lasciare il comportamento al caso.

## Test

### PHPUnit su validator e handler

I test coprono le parti critiche citate dal brief: validazione e handler, non il framework HTTP.

Casi minimi: front vuoto, back solo spazi, payload non JSON, id inesistente in update e delete, creazione che persiste i due campi. Al colloquio: si apre un test e si legge il comportamento atteso senza avviare il server.

### Repository finto

Gli handler ricevono un’implementazione in memoria dell’interfaccia repository.

Il test non tocca il file SQLite e non dipende dall’ordine dei test sul disco. Un test separato, più stretto, può coprire le query del repository SQLite se resta tempo. Al colloquio: l’interfaccia esiste perché i test ne avevano bisogno, non come ornamento.

## Frontend

### SPA con tre stati espliciti

La lista gestisce loading, errore e vuoto come stati diversi, oltre alla lista piena.

Una collezione vuota non è un errore di rete. Al colloquio: l’utente capisce se riprovare o se creare la prima card.

### Form controllati

Aggiunta e modifica usano input il cui valore vive nello stato React. La stessa forma di validazione (campi obbligatori, lunghezza) vive vicino al form e non duplica in silenzio regole diverse dal backend.

Il backend resta l’autorità: il frontend anticipa l’errore, non lo sostituisce. Al colloquio: un 400 dal server viene mostrato sui campi, non solo in console.

### Client API tipizzato

Un modulo unico fa `fetch`, controlla lo status e restituisce tipi TypeScript (`Flashcard`, errore con campi). I componenti non costruiscono URL a mano.

Al colloquio: cambiare l’envelope di errore significa toccare un file, non ogni bottone.

### UI separata dalle chiamate HTTP

I componenti disegnano lista e form. Un hook o un modulo di data loading chiama il client.

Si può ragionare sulla UI senza il server acceso, e sul contratto HTTP senza il markup. Al colloquio: react-router è ammesso se servono due viste (lista e form); Next.js no, perché il brief vieta i framework full-stack.

## Performance e colloquio

### Paginazione prima del resto

La prima leva è non trasferire e non renderizzare l’intera collezione. `per_page` ha un massimo (per esempio 100) così un client non chiede `per_page=100000`.

Al colloquio: è la ottimizzazione proporzionata a questo dominio. Il resto è secondario finché la lista è paginata.

### Indice sulla ricerca

Se il filtro è su front o back, un indice (o due) evita lo scan completo quando le righe aumentano.

Su SQLite e poche centinaia di card l’effetto è invisibile. Va detto: l’indice si mette quando la query è un requisito, non per abitudine. Al colloquio: si motiva con il filtro, non con un benchmark inventato.

### Niente over-fetch

La lista chiede solo i campi che mostra. Il dettaglio, se esiste, è un’altra chiamata.

Per due stringhe il guadagno è piccolo. Il punto da dire è il criterio: la response di lista non cresce con dati che la UI non usa.

### Cosa non ottimizzare

Cache HTTP, Redis, memoizzazione React, virtualizzazione della lista: su un CRUD di due campi costano più di quanto rendono, finché la paginazione c’è.

Al colloquio: saper nominare la tecnica e spiegare perché non è nel codice vale più che averla messa. Se il tempo avanza, l’ordine sensato è cursore al posto dell’offset, poi indice, poi virtualizzazione solo se una pagina diventa lunga davvero.
