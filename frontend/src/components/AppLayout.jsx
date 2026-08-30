import { useState } from 'react'
import { NavLink, Outlet } from 'react-router-dom'
import { ClipboardList, LayoutDashboard, LogOut, MapPin, Menu, UserRound, Users, X } from 'lucide-react'
import { useAuth } from '../context/AuthContext'

const links = {
  admin: [
    ['/admin', 'Dashboard', LayoutDashboard, true], ['/admin/work-locations', 'Work Locations', MapPin], ['/admin/tasks', 'Tasks', ClipboardList], ['/admin/volunteers', 'Volunteers', Users], ['/admin/assignments', 'Assignments', UserRound],
  ],
  volunteer: [
    ['/volunteer', 'Dashboard', LayoutDashboard, true], ['/volunteer/profile', 'My Profile', UserRound], ['/volunteer/assignments', 'My Assignments', Users], ['/volunteer/tasks', 'Tasks', ClipboardList], ['/volunteer/work-locations', 'Work Locations', MapPin],
  ],
}

export default function AppLayout() {
  const { user, logout } = useAuth()
  const [open, setOpen] = useState(false)
  return <div className="min-h-screen bg-slate-50">
    {open && <button aria-label="Close navigation" className="fixed inset-0 z-30 bg-slate-900/40 lg:hidden" onClick={() => setOpen(false)} />}
    <aside className={`fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-[#163b2d] text-white transition-transform lg:translate-x-0 ${open ? 'translate-x-0' : '-translate-x-full'}`}>
      <div className="flex h-20 items-center justify-between border-b border-white/10 px-6"><div><div className="text-lg font-bold">Volunteer Hub</div><div className="text-xs text-emerald-200">Coordination portal</div></div><button className="lg:hidden" onClick={() => setOpen(false)}><X size={20} /></button></div>
      <nav className="flex-1 space-y-1 p-4">{links[user.role].map(([to, label, Icon, end]) => <NavLink key={to} to={to} end={end} onClick={() => setOpen(false)} className={({ isActive }) => `flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition ${isActive ? 'bg-white text-emerald-900' : 'text-emerald-50 hover:bg-white/10'}`}><Icon size={18} />{label}</NavLink>)}</nav>
      <div className="border-t border-white/10 p-4"><button className="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-emerald-50 hover:bg-white/10" onClick={logout}><LogOut size={18} />Log out</button></div>
    </aside>
    <div className="lg:pl-64"><header className="sticky top-0 z-20 flex h-20 items-center justify-between border-b border-slate-200 bg-white/95 px-4 backdrop-blur sm:px-8"><button aria-label="Open navigation" className="rounded-lg border border-slate-200 p-2 lg:hidden" onClick={() => setOpen(true)}><Menu size={20} /></button><div className="ml-auto text-right"><p className="text-sm font-semibold text-slate-800">{user.name}</p><p className="text-xs capitalize text-slate-500">{user.role}</p></div></header><main className="p-4 sm:p-8"><Outlet /></main></div>
  </div>
}
