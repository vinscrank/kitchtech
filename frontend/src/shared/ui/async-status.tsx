import { Loader } from './loader'

type AsyncStatusProps = {
  pending: boolean
  error: Error | null
}

export function AsyncStatus({ pending, error }: AsyncStatusProps) {
  if (pending) {
    return (
      <p className="text-sm text-muted-foreground">
        <Loader />
      </p>
    )
  }

  if (error) {
    return <p className="text-sm text-destructive">{error.message}</p>
  }

  return null
}
