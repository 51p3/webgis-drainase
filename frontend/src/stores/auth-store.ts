import { create } from 'zustand'

interface User {
  id: number
  name: string
  email: string
  phone?: string
  is_active: boolean
  created_at: string
}

interface AuthState {
  user: User | null
  token: string | null
  roles: string[]
  login: (email: string, password: string) => Promise<void>
  logout: () => void
  setUser: (user: User | null, token: string | null, roles: string[]) => void
}

export const useAuthStore = create<AuthState>((set) => {
  const savedToken = localStorage.getItem('auth_token')
  const savedUser = localStorage.getItem('auth_user')
  const savedRoles = localStorage.getItem('auth_roles')

  return {
    user: savedUser ? JSON.parse(savedUser) : null,
    token: savedToken,
    roles: savedRoles ? JSON.parse(savedRoles) : [],

    login: async (email: string, password: string) => {
      const { api } = await import('@/lib/api')
      const response = await api.post('/login', { email, password })
      const { user, token, roles } = response.data

      localStorage.setItem('auth_token', token)
      localStorage.setItem('auth_user', JSON.stringify(user))
      localStorage.setItem('auth_roles', JSON.stringify(roles))

      set({ user, token, roles })
    },

    logout: () => {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('auth_user')
      localStorage.removeItem('auth_roles')
      set({ user: null, token: null, roles: [] })
    },

    setUser: (user, token, roles) => {
      if (token) {
        localStorage.setItem('auth_token', token)
        localStorage.setItem('auth_user', JSON.stringify(user))
        localStorage.setItem('auth_roles', JSON.stringify(roles))
      }
      set({ user, token, roles })
    },
  }
})
