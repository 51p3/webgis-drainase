import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import api from '@/lib/api'

export interface FloodLocation {
  id: number
  name: string
  district_id: number
  village_id: number
  flood_depth: number
  flood_duration?: string
  cause?: string
  description?: string
  geometry: any
  created_at: string
  updated_at: string
  district?: { id: number; name: string }
  village?: { id: number; name: string }
  photos?: any[]
}

export const useFloodLocations = (filters?: any) => {
  return useQuery({
    queryKey: ['flood-locations', filters],
    queryFn: async () => {
      const { data } = await api.get('/flood-locations', { params: filters })
      return data
    },
  })
}

export const useFloodLocation = (id: number | string) => {
  return useQuery({
    queryKey: ['flood-location', id],
    queryFn: async () => {
      const { data } = await api.get(`/flood-locations/${id}`)
      return data
    },
    enabled: !!id,
  })
}

export const useCreateFloodLocation = () => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (flood: Omit<FloodLocation, 'id' | 'created_at' | 'updated_at'>) => {
      const { data } = await api.post('/flood-locations', flood)
      return data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['flood-locations'] })
    },
  })
}

export const useUpdateFloodLocation = (id: number | string) => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (flood: Partial<FloodLocation>) => {
      const { data } = await api.put(`/flood-locations/${id}`, flood)
      return data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['flood-location', id] })
      queryClient.invalidateQueries({ queryKey: ['flood-locations'] })
    },
  })
}

export const useDeleteFloodLocation = () => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (id: number | string) => {
      await api.delete(`/flood-locations/${id}`)
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['flood-locations'] })
    },
  })
}

export const useUploadFloodPhoto = (floodLocationId: number | string) => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (formData: FormData) => {
      const { data } = await api.post(`/flood-locations/${floodLocationId}/photos`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      return data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['flood-location', floodLocationId] })
    },
  })
}
