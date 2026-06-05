import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import api from '@/lib/api'

export interface Drainage {
  id: number
  code: string
  name: string
  district_id: number
  village_id: number
  length: number
  width: number
  height: number
  type: string
  condition: string
  description?: string
  geometry: any
  created_at: string
  updated_at: string
  district?: { id: number; name: string }
  village?: { id: number; name: string }
  photos?: any[]
}

export const useDrainages = (filters?: any) => {
  return useQuery({
    queryKey: ['drainages', filters],
    queryFn: async () => {
      const { data } = await api.get('/drainages', { params: filters })
      return data
    },
  })
}

export const useDrainage = (id: number | string) => {
  return useQuery({
    queryKey: ['drainage', id],
    queryFn: async () => {
      const { data } = await api.get(`/drainages/${id}`)
      return data
    },
    enabled: !!id,
  })
}

export const useCreateDrainage = () => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (drainage: Omit<Drainage, 'id' | 'created_at' | 'updated_at'>) => {
      const { data } = await api.post('/drainages', drainage)
      return data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['drainages'] })
    },
  })
}

export const useUpdateDrainage = (id: number | string) => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (drainage: Partial<Drainage>) => {
      const { data } = await api.put(`/drainages/${id}`, drainage)
      return data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['drainage', id] })
      queryClient.invalidateQueries({ queryKey: ['drainages'] })
    },
  })
}

export const useDeleteDrainage = () => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (id: number | string) => {
      await api.delete(`/drainages/${id}`)
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['drainages'] })
    },
  })
}

export const useUploadDrainagePhoto = (drainageId: number | string) => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (formData: FormData) => {
      const { data } = await api.post(`/drainages/${drainageId}/photos`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      return data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['drainage', drainageId] })
    },
  })
}
