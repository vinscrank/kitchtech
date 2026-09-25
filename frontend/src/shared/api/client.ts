const baseUrl = import.meta.env.VITE_API_URL ?? 'http://localhost:18080'

export class ApiError extends Error {
  constructor(
    message: string,
    readonly status: number,
    readonly fields: Record<string, string> = {},
  ) {
    super(message)
  }
}

export async function api<T>(path: string, init?: RequestInit): Promise<T> {
  const response = await fetch(`${baseUrl}${path}`, {
    ...init,
    headers: {
      Accept: 'application/json',
      ...(init?.body ? { 'Content-Type': 'application/json' } : {}),
      ...init?.headers,
    },
  })

  if (response.status === 204) {
    return undefined as T
  }

  const body = await response.json()
  if (!response.ok) {
    throw new ApiError(body.error?.message ?? 'Request failed', response.status, body.error?.fields ?? {})
  }

  return body as T
}
