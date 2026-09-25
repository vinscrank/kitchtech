import { useEffect, useState, type ChangeEvent } from 'react'
import { ApiError } from '../../../shared/api/client'
import { Button } from '../../../shared/ui/button'
import { Loader } from '../../../shared/ui/loader'
import { Card } from '../../../shared/ui/card'
import { Label } from '../../../shared/ui/label'
import { Textarea } from '../../../shared/ui/textarea'
import type { FlashcardInput } from '../model/flashcard'

type FieldProps = {
  id: string
  label: string
  value: string
  error?: string
  onChange: (value: string) => void
}

function Field({ id, label, value, error, onChange }: FieldProps) {
  return (
    <div className="grid gap-2">
      <Label htmlFor={id}>{label}</Label>
      <Textarea id={id} className="min-h-28" value={value} onChange={(event: ChangeEvent<HTMLTextAreaElement>) => onChange(event.target.value)} />
      {error ? <p className="text-sm text-destructive">{error}</p> : null}
    </div>
  )
}

type FlashcardFormProps = {
  initial: FlashcardInput
  pending: boolean
  error: Error | null
  onSubmit: (input: FlashcardInput) => void
}

export function FlashcardForm({ initial, pending, error, onSubmit }: FlashcardFormProps) {
  const [front, setFront] = useState(initial.front)
  const [back, setBack] = useState(initial.back)
  const fields = error instanceof ApiError ? error.fields : {}

  useEffect(() => {
    setFront(initial.front)
    setBack(initial.back)
  }, [initial.front, initial.back])

  return (
    <Card>
      <form
        className="grid gap-5 p-6"
        onSubmit={(event) => {
          event.preventDefault()
          onSubmit({ front, back })
        }}
      >
        <Field id="front" label="Front" value={front} error={fields.front} onChange={setFront} />
        <Field id="back" label="Back" value={back} error={fields.back} onChange={setBack} />
        {error && !(error instanceof ApiError && Object.keys(error.fields).length > 0) ? (
          <p className="text-sm text-destructive">{error.message}</p>
        ) : null}
        <Button className="w-fit justify-self-end" type="submit" disabled={pending}>
          {pending ? <Loader className="size-3.5" /> : 'Save'}
        </Button>
      </form>
    </Card>
  )
}
