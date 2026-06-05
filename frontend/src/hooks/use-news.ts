import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import api from '@/lib/api'

export interface News {
  id: number
  title: string
  slug: string
  thumbnail?: string
  content: string
  status: 'draft' | 'published'
  published_at?: string
  user_id: number
  created_at: string
  updated_at: string
  user?: { id: number; name: string; email: string }
}

export const useNewsList = (filters?: any) => {
  return useQuery({
    queryKey: ['news', filters],
    queryFn: async () => {
      const { data } = await api.get('/news', { params: filters })
      return data
    },
  })
}

export const useNews = (id: number | string) => {
  return useQuery({
    queryKey: ['news', id],
    queryFn: async () => {
      const { data } = await api.get(`/news/${id}`)
      return data
    },
    enabled: !!id,
  })
}

export const useCreateNews = () => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (news: FormData) => {
      const { data } = await api.post('/news', news, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      return data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['news'] })
    },
  })
}

export const useUpdateNews = (id: number | string) => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (news: FormData) => {
      const { data } = await api.put(`/news/${id}`, news, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
      return data
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['news', id] })
      queryClient.invalidateQueries({ queryKey: ['news'] })
    },
  })
}

export const useDeleteNews = () => {
  const queryClient = useQueryClient()
  return useMutation({
    mutationFn: async (id: number | string) => {
      await api.delete(`/news/${id}`)
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['news'] })
    },
  })
}
