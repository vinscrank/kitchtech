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

  return (
    <Page title="Manage">
      <div className="mb-4 flex justify-end">
        <Button asChild>
          <Link to="/flashcards/new">Add</Link>
        </Button>
      </div>
      <AsyncStatus pending={cards.isFetching} error={cards.error ?? remove.error} />
      {cards.data?.length === 0 ? <p className="mt-6 text-sm text-muted-foreground">No cards yet.</p> : null}
      {cards.data && cards.data.length > 0 ? (
        <Card className="mt-6">
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
                      disabled={remove.isPending && remove.variables === card.id}
                      onClick={() => remove.mutate(card.id)}
                    >
                      {remove.isPending && remove.variables === card.id ? <Loader className="size-3.5" /> : 'Delete'}
                    </Button>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </Card>
      ) : null}
    </Page>
  )
}
