import { Link, useNavigate, useParams } from 'react-router-dom'
import { Page } from '../../../app/Page'
import { AsyncStatus } from '../../../shared/ui/async-status'
import { Button } from '../../../shared/ui/button'
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
    <Page title={id ? 'Edit card' : 'New card'}>
      <Button variant="outline" asChild>
        <Link to="/flashcards">Back</Link>
      </Button>
      <AsyncStatus pending={Boolean(id) && existing.isFetching} error={id ? existing.error : null} />
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
