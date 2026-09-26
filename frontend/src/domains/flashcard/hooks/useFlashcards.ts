import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query"
import { toast } from "sonner"
import { flashcardApi } from "../api/flashcardApi"
import { flashcardKeys, type FlashcardInput } from "../model/flashcard"

export function useFlashcards() {
  return useQuery({
    queryKey: flashcardKeys.all,
    queryFn: flashcardApi.list,
  })
}

export function useFlashcard(id: string | undefined) {
  return useQuery({
    queryKey: flashcardKeys.detail(id ?? ""),
    queryFn: () => flashcardApi.get(id ?? ""),
    enabled: Boolean(id),
  })
}

export function useCreateFlashcard() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (input: FlashcardInput) => flashcardApi.create(input),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: flashcardKeys.all })
      toast.success("Saved")
    },
  })
}

export function useUpdateFlashcard(id: string) {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: (input: FlashcardInput) => flashcardApi.update(id, input),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: flashcardKeys.all })
      queryClient.invalidateQueries({ queryKey: flashcardKeys.detail(id) })
      toast.success("Saved")
    },
  })
}

export function useDeleteFlashcard() {
  const queryClient = useQueryClient()

  return useMutation({
    mutationFn: flashcardApi.remove,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: flashcardKeys.all })
      toast.success("Deleted")
    },
  })
}
