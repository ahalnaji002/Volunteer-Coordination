import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react'
import api, { TOKEN_KEY } from '../services/api'

const AuthContext = createContext(null)

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null)
  const [loading, setLoading] = useState(Boolean(localStorage.getItem(TOKEN_KEY)))

  const clearSession = useCallback(() => { localStorage.removeItem(TOKEN_KEY); setUser(null); setLoading(false) }, [])
  const fetchUser = useCallback(async () => { const { data } = await api.get('/user'); setUser(data.data); return data.data }, [])

  useEffect(() => {
    const token = localStorage.getItem(TOKEN_KEY)
    if (token) fetchUser().catch(clearSession).finally(() => setLoading(false))
    const unauthorized = () => clearSession()
    window.addEventListener('auth:unauthorized', unauthorized)
    return () => window.removeEventListener('auth:unauthorized', unauthorized)
  }, [clearSession, fetchUser])

  const login = useCallback(async (credentials) => {
    const { data } = await api.post('/login', credentials)
    localStorage.setItem(TOKEN_KEY, data.data.token)
    const authenticatedUser = await fetchUser()
    return authenticatedUser
  }, [fetchUser])
  const logout = useCallback(async () => { try { await api.post('/logout') } finally { clearSession() } }, [clearSession])
  const value = useMemo(() => ({ user, loading, login, logout, refreshUser: fetchUser }), [user, loading, login, logout, fetchUser])
  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export const useAuth = () => useContext(AuthContext)
