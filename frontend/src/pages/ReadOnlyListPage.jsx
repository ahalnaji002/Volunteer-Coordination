import { useEffect, useState } from 'react'
import api, { errorDetails } from '../services/api'
import PageHeader from '../components/PageHeader'
import { Alert, Loading } from '../components/Feedback'
import { useTransientFeedback } from '../hooks/useTransientFeedback'

const definitions = {
  tasks: { title: 'Tasks', description: 'Browse the tasks available to volunteers.', columns: [['name', 'Name'], ['description', 'Description']] },
  'work-locations': { title: 'Work Locations', description: 'Browse the locations where volunteer work takes place.', columns: [['name', 'Name'], ['address', 'Address']] },
  'my-assignments': { title: 'My Assignments', description: 'Review the tasks and locations assigned to you.', columns: [['task.name', 'Task'], ['work_location.name', 'Work location'], ['assignment_date', 'Date'], ['status', 'Status']] },
}
const getValue = (item, path) => path.split('.').reduce((value, key) => value?.[key], item) ?? '—'

export default function ReadOnlyListPage({ resource }) {
  const definition = definitions[resource]
  const [items, setItems] = useState([]); const [loading, setLoading] = useState(true); const [error, setError] = useTransientFeedback('')
  useEffect(() => { setLoading(true); api.get(`/${resource}`).then(({ data }) => setItems(data.data)).catch((requestError) => setError(errorDetails(requestError).message)).finally(() => setLoading(false)) }, [resource, setError])
  return <><PageHeader title={definition.title} description={definition.description} />{error && <div className="mb-4"><Alert>{error}</Alert></div>}{loading ? <Loading /> : <div className="card overflow-hidden"><div className="overflow-x-auto"><table className="w-full text-left text-sm"><thead className="bg-slate-50 text-xs uppercase tracking-wide text-slate-500"><tr>{definition.columns.map(([, label]) => <th className="px-5 py-3" key={label}>{label}</th>)}</tr></thead><tbody className="divide-y divide-slate-100">{items.map((item) => <tr key={item.id}>{definition.columns.map(([key]) => <td className="px-5 py-4 text-slate-700" key={key}>{key === 'status' ? <span className="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold capitalize text-amber-700">{getValue(item, key)}</span> : getValue(item, key)}</td>)}</tr>)}{!items.length && <tr><td colSpan={definition.columns.length} className="px-5 py-12 text-center text-slate-500">Nothing to show yet.</td></tr>}</tbody></table></div></div>}</>
}
