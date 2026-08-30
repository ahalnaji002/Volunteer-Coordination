import axios from 'axios'

export const TOKEN_KEY = 'volunteer_coordination_token'

const api = axios.create({ baseURL: import.meta.env.VITE_API_URL || '/api', headers: { Accept: 'application/json', 'Content-Type': 'application/json' } })

api.interceptors.request.use((config) => {
  const token = localStorage.getItem(TOKEN_KEY)
  if (token) config.headers.Authorization = `Bearer ${token}`
  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem(TOKEN_KEY)
      window.dispatchEvent(new Event('auth:unauthorized'))
    }
    return Promise.reject(error)
  },
)

export const errorDetails = (error) => ({
  message: error.response?.data?.message || (error.request ? 'Unable to reach the API. Make sure Laravel is running.' : error.message) || 'Something went wrong.',
  errors: error.response?.data?.errors || {},
  status: error.response?.status,
})

export default api
