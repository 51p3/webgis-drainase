import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom'
import { QueryClientProvider } from '@tanstack/react-query'
import { queryClient } from './lib/query-client'
import { useAuthStore } from './stores/auth-store'
import Layout from './components/Layout'
import Login from './pages/Login'
import Dashboard from './pages/Dashboard'
import DrainageList from './pages/Drainage/DrainageList'
import DrainageForm from './pages/Drainage/DrainageForm'
import DrainageDetail from './pages/Drainage/DrainageDetail'
import FloodList from './pages/Flood/FloodList'
import FloodForm from './pages/Flood/FloodForm'
import FloodDetail from './pages/Flood/FloodDetail'
import MapView from './pages/Map/MapView'
import NewsList from './pages/News/NewsList'
import NewsForm from './pages/News/NewsForm'
import UserList from './pages/Users/UserList'
import Settings from './pages/Settings/Settings'

function ProtectedRoute({ children }: { children: React.ReactNode }) {
  const { token } = useAuthStore()
  return token ? <>{children}</> : <Navigate to="/login" />
}

export default function App() {
  const { token } = useAuthStore()

  return (
    <QueryClientProvider client={queryClient}>
      <Router>
        <Routes>
          {!token ? (
            <Route path="/login" element={<Login />} />
          ) : (
            <Route element={<Layout />}>
              <Route path="/" element={<Dashboard />} />
              <Route path="/map" element={<MapView />} />
              
              {/* Drainage Routes */}
              <Route path="/drainages" element={<DrainageList />} />
              <Route path="/drainages/new" element={<DrainageForm />} />
              <Route path="/drainages/:id" element={<DrainageDetail />} />
              <Route path="/drainages/:id/edit" element={<DrainageForm />} />
              
              {/* Flood Routes */}
              <Route path="/floods" element={<FloodList />} />
              <Route path="/floods/new" element={<FloodForm />} />
              <Route path="/floods/:id" element={<FloodDetail />} />
              <Route path="/floods/:id/edit" element={<FloodForm />} />
              
              {/* News Routes */}
              <Route path="/news" element={<NewsList />} />
              <Route path="/news/new" element={<NewsForm />} />
              <Route path="/news/:id/edit" element={<NewsForm />} />
              
              {/* Admin Routes */}
              <Route path="/users" element={<UserList />} />
              <Route path="/settings" element={<Settings />} />
            </Route>
          )}
          <Route path="*" element={<Navigate to={token ? '/' : '/login'} />} />
        </Routes>
      </Router>
    </QueryClientProvider>
  )
}
