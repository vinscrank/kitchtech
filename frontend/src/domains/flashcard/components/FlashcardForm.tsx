import { useEffect, useState, type ChangeEvent } from "react"
import { ApiError } from "../../../shared/api/client"
import { Loader } from "../../../shared/ui/loader"
import type { FlashcardInput } from "../model/flashcard"

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
      <label htmlFor={id} className="text-sm font-medium leading-none">
        {label}
      </label>
      <textarea
        id={id}
        className="flex min-h-28 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
        value={value}
        onChange={(event: ChangeEvent<HTMLTextAreaElement>) =>
          onChange(event.target.value)
        }
      />
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

export function FlashcardForm({
  initial,
  pending,
  error,
  onSubmit,
}: FlashcardFormProps) {
  const [front, setFront] = useState(initial.front)
  const [back, setBack] = useState(initial.back)
  const fields = error instanceof ApiError ? error.fields : {}

  useEffect(() => {
    setFront(initial.front)
    setBack(initial.back)
  }, [initial.front, initial.back])

  return (
    <div className="overflow-hidden rounded-xl border bg-card text-card-foreground shadow-sm">
      <form
        className="grid gap-5 p-6"
        onSubmit={(event) => {
          event.preventDefault()
          onSubmit({ front, back })
        }}
      >
        <Field
          id="front"
          label="Front"
          value={front}
          error={fields.front}
          onChange={setFront}
        />
        <Field
          id="back"
          label="Back"
          value={back}
          error={fields.back}
          onChange={setBack}
        />
        {error &&
        !(error instanceof ApiError && Object.keys(error.fields).length > 0) ? (
          <p className="text-sm text-destructive">{error.message}</p>
        ) : null}
        <button
          className="inline-flex h-9 w-fit items-center justify-center justify-self-end gap-2 whitespace-nowrap rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50"
          type="submit"
          disabled={pending}
        >
          {pending ? <Loader className="size-3.5" /> : "Save"}
        </button>
      </form>
    </div>
  )
}
