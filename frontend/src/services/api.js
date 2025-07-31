import axios from 'axios'
import { useAuthStore } from '../stores/auth'

/**
 * Настроенный экземпляр axios для работы с API
 */
const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json'
  }
})

/**
 * Интерцептор для добавления токена авторизации
 */
api.interceptors.request.use((config) => {
  const authStore = useAuthStore()
  if (authStore.token) {
    config.headers.Authorization = `Bearer ${authStore.token}`
  }
  return config
})

/**
 * Интерцептор для обработки ошибок авторизации
 */
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      const authStore = useAuthStore()
      authStore.logout()
    }
    return Promise.reject(error)
  }
)

export default api