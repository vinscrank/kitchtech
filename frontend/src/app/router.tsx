import { lazy, Suspense } from 'react'
import { Route, Routes } from 'react-router-dom'
import { FlashcardTablePage } from '../domains/flashcard/pages/FlashcardTablePage'
import { StudyPage } from '../domains/flashcard/pages/StudyPage'
import { AsyncStatus } from '../shared/ui/async-status'
import { Page } from './Page'

const FlashcardFormPage = lazy(() =>
  import('../domains/flashcard/pages/FlashcardFormPage').then((module) => ({ default: module.FlashcardFormPage })),
)

export function AppRouter() {
  return (
    <Suspense fallback={<Page title="Flashcards"><AsyncStatus pending error={null} /></Page>}>
      <Routes>
        <Route path="/" element={<StudyPage />} />
        <Route path="/flashcards" element={<FlashcardTablePage />} />
        <Route path="/flashcards/new" element={<FlashcardFormPage />} />
        <Route path="/flashcards/:id" element={<FlashcardFormPage />} />
      </Routes>
    </Suspense>
  )
}
