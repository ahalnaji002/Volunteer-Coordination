import { useState } from 'react'
import { Navigate, useNavigate } from 'react-router-dom'
import { HeartHandshake } from 'lucide-react'
import { useAuth } from '../context/AuthContext'
import { Alert, FieldError } from '../components/Feedback'
import { errorDetails } from '../services/api'
import { useTransientFeedback } from '../hooks/useTransientFeedback'

export default function LoginPage() {
  const { user, login } = useAuth()
  const navigate = useNavigate()
  const [form, setForm] = useState({ email: '', password: '' })
  const [feedback, setFeedback] = useTransientFeedback({ message: '', errors: {} })
  const [submitting, setSubmitting] = useState(false)
  if (user) return <Navigate to={user.role === 'admin' ? '/admin' : '/volunteer'} replace />
  const submit = async (event) => {
    event.preventDefault(); setSubmitting(true); setFeedback({ message: '', errors: {} })
    try { const authenticated = await login(form); navigate(authenticated.role === 'admin' ? '/admin' : '/volunteer', { replace: true }) }
    catch (error) { setFeedback(errorDetails(error)) }
    finally { setSubmitting(false) }
  }
  return <main className="grid min-h-screen bg-[#f3f7f4] lg:grid-cols-2">
    <section className="hidden bg-[#163b2d] p-12 text-white lg:flex lg:flex-col lg:justify-between"><div className="flex items-center gap-3 text-lg font-bold"><span className="rounded-xl bg-white/10 p-2"><HeartHandshake /></span>Volunteer Coordination</div><div className="max-w-lg"><p className="mb-4 text-sm font-semibold uppercase tracking-[0.22em] text-emerald-300">Work together, clearly</p><h1 className="text-5xl font-bold leading-tight">Coordinate people, tasks, and places in one simple portal.</h1><p className="mt-6 text-lg leading-relaxed text-emerald-100">A focused workspace for administrators and volunteers.</p></div><p className="text-sm text-emerald-200">Volunteer Coordination training project</p></section>
    <section className="flex items-center justify-center p-6"><div className="w-full max-w-md"><div className="mb-8 lg:hidden"><div className="flex items-center gap-2 text-lg font-bold text-emerald-900"><HeartHandshake />Volunteer Coordination</div></div><div className="card p-6 sm:p-8"><h2 className="text-2xl font-bold text-slate-900">Welcome back</h2><p className="mt-2 text-sm text-slate-500">Sign in with your administrator or volunteer account.</p><form className="mt-7 space-y-5" onSubmit={submit}><Alert>{feedback.message}</Alert><div><label className="label" htmlFor="email">Email address</label><input className="field" id="email" type="email" autoComplete="email" required value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} /><FieldError errors={feedback.errors} name="email" /></div><div><label className="label" htmlFor="password">Password</label><input className="field" id="password" type="password" autoComplete="current-password" required value={form.password} onChange={(e) => setForm({ ...form, password: e.target.value })} /><FieldError errors={feedback.errors} name="password" /></div><button className="btn-primary w-full" disabled={submitting}>{submitting ? 'Signing in…' : 'Sign in'}</button></form></div></div></section>
  </main>
}
