export type Flashcard = {
  id: string
  front: string
  back: string
}

export type FlashcardInput = {
  front: string
  back: string
}

export const flashcardKeys = {
  all: ['flashcards'] as const,
  detail: (id: string) => ['flashcards', id] as const,
}
