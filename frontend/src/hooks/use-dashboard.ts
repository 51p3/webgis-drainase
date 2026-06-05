import { useQuery } from '@tanstack/react-query'
import api from '@/lib/api'

export const useDashboardStats = () => {
  return useQuery({
    queryKey: ['dashboard'],
    queryFn: async () => {
      const { data } = await api.get('/dashboard')
      return data
    },
  })
}
