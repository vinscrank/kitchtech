# KitchTech Developer Take-Home Assessment

Documento di riferimento del progetto. Rileggerlo prima di ogni feature: le istruzioni sotto sono il vincolo, il promemoria in testa è il criterio con cui valutare il lavoro.

## Promemoria operativo

- Il colloquio valuta approccio, scelte e ragionamento, non la copertura completa delle feature.
- Ogni decisione va motivata (tecnologia, storage, trade-off) e poi riportata nel README.
- Vincoli non negoziabili: PHP senza framework pesante, persistenza reale, React e TypeScript senza Next.js, test sulle parti critiche del backend, Docker se si procede con i container.
- Tempo massimo indicato: 6/7 ore. Quello che non entra nel tempo va descritto in Future work, non implementato a metà.
- Questo file resta la specifica. Il catalogo di tecniche e scelte sta in [approcci.md](approcci.md). Il README alla radice del repository è il deliverable pubblico (setup, decisioni architetturali, future work).

## Overview

Create an application to manage a collection of flashcards for memorisation. Each flashcard will have a front (a hint or question) and a back (the answer or word being memorised).

## Backend

The backend will be responsible for data persistence and exposure through a RESTful API.

- **Technology:** The backend should be built with PHP, but without a heavy framework (e.g. Laravel, CodeIgniter).
- **Data Storage:** The data must be stored in a persistent method of the candidate's choice. In-memory storage is not permitted.
- **Functionality:** CRUD Endpoints. The API must expose endpoints to create, read, update, and delete a flashcard.

## Frontend

The frontend will be a single-page application that consumes the backend API.

- **Technology:** A React and TypeScript application. Use any 3rd-party libraries you need, but avoid Next.js / any full frameworks (react-router is allowed).
- **Functionality:**
  - A page to view all flashcards in a list.
  - A user interface to add, edit, and delete flashcards.

## General Requirements and Deliverables

### Code and Practices

- **Clean Code:** The code should be well-structured, readable, and follow appropriate design patterns.
- **Validation and Error Handling:** User-submitted data should be handled defensively, and errors should be handled appropriately with useful error messages.
- **Testing:** Demonstrate an understanding of testing by including unit tests for critical parts of the backend logic (e.g., API handlers, data validation).

### Documentation

- **Git Repository:** The final deliverable should be a link to a Git repository.
- **README.md:** This must include:
  - **Setup Instructions:** Clear, step-by-step instructions on how to set up and run both the backend and the frontend. If you are familiar with Docker, please use it to run the app and include docker files in the repo.
  - **Architectural Decisions:** A brief explanation of the design choices, including the chosen technologies, data storage method, and any trade-offs made.
  - **Future Work:** A section detailing additional features or improvements that could be implemented with more time.

## Other Notes

Emphasis should be placed on design approach rather than delivering a working app. Please do not spend too much time trying to complete this task. Do what you can within a reasonable timeframe (upper limit of 6/7 hours, but no more than you think is necessary), and let us know in the README.md how you might have proceeded with additional time.

The purpose of this exercise is to understand how you approach a software development problem, make technical decisions, structure an application, and communicate your reasoning.

There is no expectation that you write every line of code yourself. We are more interested in how you implement a solution, why you chose to build it that way, and how effectively you use AI to turn that intent into a working solution. You are explicitly allowed and encouraged to use AI development tools such as ChatGPT, GitHub Copilot, Claude, Cursor or similar tools. We are not assessing your ability to write code without AI assistance but you should remain responsible for the design, intent and implementation of your solution.

As part of the assessment process, we will have a follow-up technical session where you will be asked to go through parts of your implementation, discuss your technical decisions, and make changes to the application.
