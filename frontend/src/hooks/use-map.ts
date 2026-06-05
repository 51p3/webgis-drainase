import { useQuery } from '@tanstack/react-query'
import api from '@/lib/api'

export const useMapData = () => {
  const drainages = useQuery({
    queryKey: ['map', 'drainages'],
    queryFn: async () => {
      const { data } = await api.get('/map/drainages')
      return data
    },
  })

  const floods = useQuery({
    queryKey: ['map', 'floods'],
    queryFn: async () => {
      const { data } = await api.get('/map/floods')
      return data
    },
  })

  return { drainages, floods }
}

export const useDistricts = () => {
  return useQuery({
    queryKey: ['districts'],
    queryFn: async () => {
      const { data } = await api.get('/map/districts')
      return data
    },
  })
}

export const useVillages = (districtId?: number) => {
  return useQuery({
    queryKey: ['villages', districtId],
    queryFn: async () => {
      const { data } = await api.get('/map/villages', {
        params: { district_id: districtId },
      })
      return data
    },
    enabled: !!districtId,
  })
}
