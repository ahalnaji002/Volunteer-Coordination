import { AlertTriangle, X } from 'lucide-react'

export default function ConfirmModal({ open, title, message, confirmLabel = 'Confirm', busy = false, onCancel, onConfirm }) {
  if (!open) return null

  return <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4">
    <div role="alertdialog" aria-modal="true" aria-labelledby="confirm-title" aria-describedby="confirm-message" className="card w-full max-w-md">
      <div className="flex items-start justify-between gap-4 border-b border-slate-200 px-6 py-4">
        <div className="flex items-center gap-3"><span className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-50 text-red-700"><AlertTriangle size={20} /></span><h2 id="confirm-title" className="text-lg font-semibold text-slate-900">{title}</h2></div>
        <button type="button" aria-label="Close confirmation" className="rounded p-1 text-slate-500 hover:bg-slate-100" disabled={busy} onClick={onCancel}><X size={20} /></button>
      </div>
      <div className="px-6 py-5"><p id="confirm-message" className="text-sm leading-6 text-slate-600">{message}</p></div>
      <div className="flex justify-end gap-3 border-t border-slate-200 px-6 py-4"><button type="button" className="btn-secondary" disabled={busy} onClick={onCancel}>Cancel</button><button type="button" className="inline-flex items-center justify-center rounded-lg bg-red-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-800 disabled:cursor-not-allowed disabled:opacity-60" disabled={busy} onClick={onConfirm}>{busy ? 'Deleting…' : confirmLabel}</button></div>
    </div>
  </div>
}
