export function Alert({ type = 'error', children }) {
  if (!children) return null
  return <div role="alert" className={`rounded-lg border px-4 py-3 text-sm ${type === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-800'}`}>{children}</div>
}

export function Loading({ label = 'Loading…' }) {
  return <div className="flex min-h-48 items-center justify-center gap-3 text-sm text-slate-500"><span className="h-5 w-5 animate-spin rounded-full border-2 border-emerald-700 border-t-transparent" />{label}</div>
}

export function FieldError({ errors, name }) {
  return errors?.[name]?.map((message) => <p key={message} className="mt-1 text-xs text-red-600">{message}</p>) || null
}
