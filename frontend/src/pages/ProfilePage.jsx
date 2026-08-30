import { useEffect, useState } from 'react'
import api, { errorDetails } from '../services/api'
import PageHeader from '../components/PageHeader'
import { Alert, FieldError, Loading } from '../components/Feedback'
import { useAuth } from '../context/AuthContext'
import { useTransientFeedback } from '../hooks/useTransientFeedback'

export default function ProfilePage() {
  const { refreshUser } = useAuth()
  const [form, setForm] = useState(null); const [feedback, setFeedback] = useTransientFeedback({ type: '', message: '', errors: {} }); const [saving, setSaving] = useState(false)
  useEffect(() => { api.get('/me').then(({ data }) => { const { name, email, phone } = data.data; setForm({ name, email, phone }) }).catch((error) => setFeedback({ type: 'error', ...errorDetails(error) })) }, [setFeedback])
  const submit = async (event) => { event.preventDefault(); setSaving(true); setFeedback({ type: '', message: '', errors: {} }); try { const { data } = await api.patch('/me', form); setFeedback({ type: 'success', message: data.message, errors: {} }); await refreshUser() } catch (error) { setFeedback({ type: 'error', ...errorDetails(error) }) } finally { setSaving(false) } }
  return <><PageHeader title="My Profile" description="Keep your personal and contact information current." />{!form ? (feedback.message ? <Alert>{feedback.message}</Alert> : <Loading />) : <div className="card max-w-2xl p-6"><form className="space-y-5" onSubmit={submit}><Alert type={feedback.type}>{feedback.message}</Alert>{[['name', 'Name', 'text'], ['email', 'Email address', 'email'], ['phone', 'Phone', 'tel']].map(([name, label, type]) => <div key={name}><label className="label" htmlFor={name}>{label}</label><input className="field" id={name} type={type} required value={form[name] || ''} onChange={(e) => setForm({ ...form, [name]: e.target.value })} /><FieldError errors={feedback.errors} name={name} /></div>)}<button className="btn-primary" disabled={saving}>{saving ? 'Saving…' : 'Save changes'}</button></form></div>}</>
}
