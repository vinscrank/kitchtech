import { useState } from 'react'
import { Link } from 'react-router-dom'
import { Page } from '../../../app/Page'
import { AsyncStatus } from '../../../shared/ui/async-status'
import { Loader } from '../../../shared/ui/loader'
import { useDeleteFlashcard, useFlashcards } from '../hooks/useFlashcards'

export function FlashcardTablePage() {
  const cards = useFlashcards()
  const remove = useDeleteFlashcard()
  const [confirmId, setConfirmId] = useState<string | null>(null)

  return (
    <Page
      title="Manage"
      action={
        <Link
          to="/flashcards/new"
          className="inline-flex h-9 items-center justify-center gap-2 whitespace-nowrap rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        >
          Add
        </Link>
      }
    >
      <AsyncStatus pending={cards.isPending} error={cards.error ?? remove.error} />
      {cards.data?.length === 0 ? <p className="text-sm text-muted-foreground">No cards yet.</p> : null}
      {cards.data && cards.data.length > 0 ? (
        <div className="overflow-hidden rounded-xl border bg-card text-card-foreground shadow-sm">
          <div className="w-full overflow-auto">
            <table className="w-full table-fixed text-sm">
              <thead className="[&_tr]:border-b">
                <tr className="border-b transition-colors hover:bg-muted/40">
                  <th className="h-11 px-3 text-left align-middle text-sm font-medium text-muted-foreground">Front</th>
                  <th className="h-11 px-3 text-left align-middle text-sm font-medium text-muted-foreground">Back</th>
                  <th className="h-11 w-40 px-3 text-right align-middle text-sm font-medium text-muted-foreground"> </th>
                </tr>
              </thead>
              <tbody className="[&_tr:last-child]:border-0">
                {cards.data.map((card) => (
                  <tr key={card.id} className="border-b transition-colors hover:bg-muted/40">
                    <td className="truncate p-3 align-middle font-medium" title={card.front}>{card.front}</td>
                    <td className="truncate p-3 align-middle text-muted-foreground" title={card.back}>{card.back}</td>
                    <td className="whitespace-nowrap p-3 text-right align-middle">
                      <Link
                        to={`/flashcards/${card.id}`}
                        className="inline-flex h-8 items-center justify-center gap-2 whitespace-nowrap rounded-md border border-input bg-background px-3 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                      >
                        Edit
                      </Link>
                      <button
                        className="ml-1 inline-flex h-8 items-center justify-center gap-2 whitespace-nowrap rounded-md border border-input bg-background px-3 text-sm font-medium text-muted-foreground shadow-sm transition-colors hover:bg-accent hover:text-destructive focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50"
                        onClick={() => setConfirmId(card.id)}
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      ) : null}
      {confirmId ? (
        <div
          className="fixed inset-0 z-50 flex items-center justify-center bg-foreground/20 px-6"
          role="dialog"
          aria-modal="true"
          aria-labelledby="delete-card-title"
          onClick={() => {
            if (!remove.isPending) setConfirmId(null)
          }}
        >
          <div
            className="w-full max-w-sm overflow-hidden rounded-xl border bg-card p-6 text-card-foreground shadow-sm"
            onClick={(event) => event.stopPropagation()}
          >
            <p id="delete-card-title" className="text-sm font-medium">
              Delete this card?
            </p>
            <div className="mt-6 flex justify-end gap-2">
              <button
                className="inline-flex h-9 items-center justify-center gap-2 whitespace-nowrap rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50"
                autoFocus
                disabled={remove.isPending}
                onClick={() => setConfirmId(null)}
              >
                Cancel
              </button>
              <button
                className="inline-flex h-9 items-center justify-center gap-2 whitespace-nowrap rounded-md bg-destructive px-4 py-2 text-sm font-medium text-destructive-foreground shadow-sm transition-colors hover:bg-destructive/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50"
                disabled={remove.isPending}
                onClick={() => remove.mutate(confirmId, { onSettled: () => setConfirmId(null) })}
              >
                {remove.isPending ? <Loader className="size-3.5" /> : 'Delete'}
              </button>
            </div>
          </div>
        </div>
      ) : null}
    </Page>
  )
}
