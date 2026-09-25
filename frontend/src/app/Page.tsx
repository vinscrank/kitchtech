import type { ReactNode } from 'react'
import { Link, useLocation } from 'react-router-dom'

type PageProps = {
  title: string
  action?: ReactNode
  children: ReactNode
}

export function Page({ title, action, children }: PageProps) {
  const path = useLocation().pathname

  return (
    <main className="mx-auto min-h-screen max-w-3xl px-6 py-12">
      <header className="flex items-center justify-between gap-4 border-b pb-6">
        <h1 className="text-2xl font-semibold tracking-tight">{title}</h1>
        <nav className="flex h-9 items-center rounded-lg bg-muted p-1 text-sm font-medium">
          <Link to="/" className={`flex h-7 items-center rounded-md px-3 ${path === '/' ? 'bg-card shadow-sm' : 'text-muted-foreground'}`}>
            Study
          </Link>
          <Link to="/flashcards" className={`flex h-7 items-center rounded-md px-3 ${path === '/flashcards' ? 'bg-card shadow-sm' : 'text-muted-foreground'}`}>
            Manage
          </Link>
        </nav>
      </header>
      <div className="mb-6 mt-6 flex h-9 justify-end">{action}</div>
      {children}
    </main>
  )
}
