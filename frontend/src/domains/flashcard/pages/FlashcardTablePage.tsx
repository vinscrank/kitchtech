import { useState } from 'react'
import { Link } from 'react-router-dom'
import { Page } from '../../../app/Page'
import { AsyncStatus } from '../../../shared/ui/async-status'
import { Button } from '../../../shared/ui/button'
import { Loader } from '../../../shared/ui/loader'
import { Card } from '../../../shared/ui/card'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '../../../shared/ui/table'
import { useDeleteFlashcard, useFlashcards } from '../hooks/useFlashcards'

export function FlashcardTablePage() {
  const cards = useFlashcards()
  const remove = useDeleteFlashcard()
  const [confirmId, setConfirmId] = useState<string | null>(null)

  return (
    <Page
      title="Manage"
      action={
        <Button asChild>
          <Link to="/flashcards/new">Add</Link>
        </Button>
      }
    >
      <AsyncStatus pending={cards.isPending} error={cards.error ?? remove.error} />
      {cards.data?.length === 0 ? <p className="text-sm text-muted-foreground">No cards yet.</p> : null}
      {cards.data && cards.data.length > 0 ? (
        <Card>
          <Table className="table-fixed">
            <TableHeader>
              <TableRow>
                <TableHead>Front</TableHead>
                <TableHead>Back</TableHead>
                <TableHead className="w-40 text-right"> </TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {cards.data.map((card) => (
                <TableRow key={card.id}>
                  <TableCell className="truncate font-medium" title={card.front}>{card.front}</TableCell>
                  <TableCell className="truncate text-muted-foreground" title={card.back}>{card.back}</TableCell>
                  <TableCell className="whitespace-nowrap text-right">
                    <Button variant="outline" size="sm" asChild>
                      <Link to={`/flashcards/${card.id}`}>Edit</Link>
                    </Button>
                    <Button
                      className="ml-1 text-muted-foreground hover:text-destructive"
                      variant="outline"
                      size="sm"
                      onClick={() => setConfirmId(card.id)}
                    >
                      Delete
                    </Button>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </Card>
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
          <Card className="w-full max-w-sm p-6" onClick={(event) => event.stopPropagation()}>
            <p id="delete-card-title" className="text-sm font-medium">
              Delete this card?
            </p>
            <div className="mt-6 flex justify-end gap-2">
              <Button variant="outline" autoFocus disabled={remove.isPending} onClick={() => setConfirmId(null)}>
                Cancel
              </Button>
              <Button
                variant="destructive"
                disabled={remove.isPending}
                onClick={() => remove.mutate(confirmId, { onSettled: () => setConfirmId(null) })}
              >
                {remove.isPending ? <Loader className="size-3.5" /> : 'Delete'}
              </Button>
            </div>
          </Card>
        </div>
      ) : null}
    </Page>
  )
}
