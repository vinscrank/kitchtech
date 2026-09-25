import { useState } from 'react'
import { Link } from 'react-router-dom'
import { Page } from '../../../app/Page'
import { AsyncStatus } from '../../../shared/ui/async-status'
import { Button } from '../../../shared/ui/button'
import { FlipCard } from '../components/FlipCard'
import { useFlashcards } from '../hooks/useFlashcards'

export function StudyPage() {
  const cards = useFlashcards()
  const [index, setIndex] = useState(0)
  const list = cards.data ?? []
  const current = Math.min(index, Math.max(list.length - 1, 0))
  const card = list[current]

  return (
    <Page
      title="Flashcards"
      action={
        <Button asChild>
          <Link to="/flashcards/new">Add</Link>
        </Button>
      }
    >
      <AsyncStatus pending={cards.isPending} error={cards.error} />
      {list.length === 0 && !cards.isPending && !cards.isError ? <p className="text-sm text-muted-foreground">No cards yet.</p> : null}
      {card ? (
        <div className="mx-auto flex w-full max-w-lg flex-col items-center gap-8">
          <p className="text-sm font-medium tabular-nums text-muted-foreground">
            {current + 1} / {list.length}
          </p>
          <FlipCard key={card.id} card={card} />
          {list.length > 1 ? (
            <div className="flex gap-2">
              <Button variant="outline" disabled={current === 0} onClick={() => setIndex(current - 1)}>
                Previous
              </Button>
              <Button variant="outline" disabled={current === list.length - 1} onClick={() => setIndex(current + 1)}>
                Next
              </Button>
            </div>
          ) : null}
        </div>
      ) : null}
    </Page>
  )
}
