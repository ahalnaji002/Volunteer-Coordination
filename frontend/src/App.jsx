import { Navigate, Route, Routes } from 'react-router-dom'
import AppLayout from './components/AppLayout'
import { ProtectedRoute, RoleRoute } from './components/Routes'
import LoginPage from './pages/LoginPage'
import DashboardPage from './pages/DashboardPage'
import ResourcePage from './pages/ResourcePage'
import ReadOnlyListPage from './pages/ReadOnlyListPage'
import ProfilePage from './pages/ProfilePage'

export default function App() {
  return <Routes>
    <Route path="/login" element={<LoginPage />} />
    <Route element={<ProtectedRoute />}><Route element={<AppLayout />}>
      <Route element={<RoleRoute role="admin" />}>
        <Route path="/admin" element={<DashboardPage />} />
        <Route path="/admin/work-locations" element={<ResourcePage resource="work-locations" />} />
        <Route path="/admin/tasks" element={<ResourcePage resource="tasks" />} />
        <Route path="/admin/volunteers" element={<ResourcePage resource="volunteers" />} />
        <Route path="/admin/assignments" element={<ResourcePage resource="assignments" />} />
      </Route>
      <Route element={<RoleRoute role="volunteer" />}>
        <Route path="/volunteer" element={<DashboardPage />} />
        <Route path="/volunteer/profile" element={<ProfilePage />} />
        <Route path="/volunteer/assignments" element={<ReadOnlyListPage resource="my-assignments" />} />
        <Route path="/volunteer/tasks" element={<ReadOnlyListPage resource="tasks" />} />
        <Route path="/volunteer/work-locations" element={<ReadOnlyListPage resource="work-locations" />} />
      </Route>
    </Route></Route>
    <Route path="*" element={<Navigate to="/login" replace />} />
  </Routes>
}
