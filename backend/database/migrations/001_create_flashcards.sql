CREATE TABLE flashcards (
    id CHAR(36) NOT NULL,
    front TEXT NOT NULL,
    back TEXT NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
