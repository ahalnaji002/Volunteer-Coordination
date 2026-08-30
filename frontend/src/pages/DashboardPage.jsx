import { useEffect, useState } from 'react'
import { ClipboardList, MapPin, UserRound, Users } from 'lucide-react'
import { useAuth } from '../context/AuthContext'
import api from '../services/api'
import PageHeader from '../components/PageHeader'
import { Alert } from '../components/Feedback'
import { useTransientFeedback } from '../hooks/useTransientFeedback'

export default function DashboardPage() {
  const { user } = useAuth()
  const [counts, setCounts] = useState([])
  const [error, setError] = useTransientFeedback('')
  useEffect(() => {
    const endpoints = user.role === 'admin' ? [['Work locations', '/work-locations', MapPin], ['Tasks', '/tasks', ClipboardList], ['Volunteers', '/volunteers', Users], ['Assignments', '/assignments', UserRound]] : [['My assignments', '/my-assignments', UserRound], ['Available tasks', '/tasks', ClipboardList], ['Work locations', '/work-locations', MapPin]]
    Promise.all(endpoints.map(async ([label, url, Icon]) => { const response = await api.get(url); return { label, count: response.data.data.length, Icon } })).then(setCounts).catch(() => setError('Some dashboard totals could not be loaded.'))
  }, [setError, user.role])
  return <><PageHeader title={`Welcome, ${user.name.split(' ')[0]}`} description={user.role === 'admin' ? 'A current overview of volunteer coordination records.' : 'Your volunteer workspace and current assignments.'} /><Alert>{error}</Alert><div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">{counts.map(({ label, count, Icon }) => <div className="card p-5" key={label}><div className="mb-5 flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700"><Icon size={20} /></div><p className="text-3xl font-bold text-slate-900">{count}</p><p className="mt-1 text-sm text-slate-500">{label}</p></div>)}</div><div className="card mt-6 p-6"><h2 className="font-semibold text-slate-900">Your access</h2><p className="mt-2 text-sm leading-6 text-slate-600">{user.role === 'admin' ? 'Manage work locations, tasks, volunteers, and their assignments from the navigation.' : 'Review shared tasks and locations, keep your profile up to date, and see assignments allocated to you.'}</p></div></>
}
