import { Link, useNavigate, useParams } from 'react-router-dom'
import { Page } from '../../../app/Page'
import { AsyncStatus } from '../../../shared/ui/async-status'
import { FlashcardForm } from '../components/FlashcardForm'
import { useCreateFlashcard, useFlashcard, useUpdateFlashcard } from '../hooks/useFlashcards'

export function FlashcardFormPage() {
  const { id } = useParams()
  const navigate = useNavigate()
  const existing = useFlashcard(id)
  const create = useCreateFlashcard()
  const update = useUpdateFlashcard(id ?? '')
  const save = id ? update : create

  return (
    <Page
      title={id ? 'Edit card' : 'New card'}
      action={
        <Link
          to="/flashcards"
          className="inline-flex h-9 items-center justify-center gap-2 whitespace-nowrap rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        >
          Back
        </Link>
      }
    >
      <AsyncStatus pending={Boolean(id) && existing.isPending} error={id ? existing.error : null} />
      {!id || existing.data ? (
        <FlashcardForm
          initial={{ front: existing.data?.front ?? '', back: existing.data?.back ?? '' }}
          pending={save.isPending}
          error={save.error}
          onSubmit={(input) => save.mutate(input, { onSuccess: () => navigate('/flashcards') })}
        />
      ) : null}
    </Page>
  )
}
