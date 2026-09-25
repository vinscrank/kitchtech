import { useState } from 'react'
import type { Flashcard } from '../model/flashcard'

const face = 'absolute inset-0 flex flex-col overflow-hidden rounded-2xl border p-8 shadow-md [backface-visibility:hidden] [-webkit-backface-visibility:hidden]'
const body = 'flex min-h-full items-center justify-center text-center text-xl font-medium leading-relaxed'

export function FlipCard({ card }: { card: Flashcard }) {
  const [flipped, setFlipped] = useState(false)
  const turn = () => setFlipped((value) => !value)

  return (
    <div
      className="h-96 w-full cursor-pointer outline-none [perspective:1200px] focus-visible:ring-2 focus-visible:ring-ring"
      role="button"
      tabIndex={0}
      onClick={turn}
      onKeyDown={(event) => {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault()
          turn()
        }
      }}
    >
      <div className={`relative h-full transition-transform duration-700 ease-out [transform-style:preserve-3d] ${flipped ? '[transform:rotateY(180deg)]' : ''}`}>
        <div className={`${face} bg-card`}>
          <span className="text-sm font-medium text-muted-foreground">Front</span>
          <div className="min-h-0 flex-1 overflow-y-auto py-4">
            <p className={body}>{card.front}</p>
          </div>
          <span className="text-center text-sm text-muted-foreground">Click to flip</span>
        </div>
        <div className={`${face} border-primary bg-primary text-primary-foreground [transform:rotateY(180deg)]`}>
          <span className="text-sm font-medium text-primary-foreground/70">Back</span>
          <div className="min-h-0 flex-1 overflow-y-auto py-4">
            <p className={body}>{card.back}</p>
          </div>
          <span className="text-center text-sm text-primary-foreground/70">Click to flip</span>
        </div>
      </div>
    </div>
  )
}
