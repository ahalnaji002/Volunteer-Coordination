import { Navigate, Outlet } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'
import { Loading } from './Feedback'

export function ProtectedRoute() {
  const { user, loading } = useAuth()
  if (loading) return <Loading label="Restoring your session…" />
  return user ? <Outlet /> : <Navigate to="/login" replace />
}

export function RoleRoute({ role }) {
  const { user } = useAuth()
  if (user?.role !== role) return <Navigate to={user?.role === 'admin' ? '/admin' : '/volunteer'} replace />
  return <Outlet />
}
