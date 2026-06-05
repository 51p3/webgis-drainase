import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import api from '@/lib/api'

export interface User {
  id: number
  name: string
  email: string
  phone?: string
  is_active: boolean
  created_at: string
}

export const useUsers = (filters?: any) => {
  return useQuery({
    queryKey: ['users', filters],
    queryFn: async () => {
      const { data } = await api.get('/users', { params: filters })
      return data
    },
  })
}

export const useCreateUser = () => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (user: any) => {
      const { data } = await api.post('/users', user)
      return data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['users'] })
    },
  })
}

export const useUpdateUser = (id: number | string) => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (user: any) => {
      const { data } = await api.put(`/users/${id}`, user)
      return data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['users'] })
    },
  })
}

export const useDeleteUser = () => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (id: number | string) => {
      await api.delete(`/users/${id}`)
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['users'] })
    },
  })
}
