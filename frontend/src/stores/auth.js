import { defineStore } from 'pinia'
import api from '../services/api'

/**
 * Store для управления аутентификацией
 */
export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token')
  }),

  getters: {
    isAuthenticated: (state) => !!state.token
  },

  actions: {
    /**
     * Авторизация пользователя
     */
    async login(credentials) {
      try {
        const response = await api.post('/auth/login', credentials)
        const { token, user } = response.data
        
        this.token = token
        this.user = user
        localStorage.setItem('token', token)
        
        return { success: true }
      } catch (error) {
        return { 
          success: false, 
          error: error.response?.data?.message || 'Ошибка авторизации' 
        }
      }
    },

    /**
     * Выход из системы
     */
    logout() {
      this.user = null
      this.token = null
      localStorage.removeItem('token')
      
      // на страницу входа
      if (window.location.pathname.startsWith('/admin') && window.location.pathname !== '/admin/login') {
        window.location.href = '/admin/login'
      }
    },

    /**
     * Инициализация при загрузке приложения
     */
    async checkAuth() {
      if (this.token) {
        try {
          const response = await api.get('/auth/me')
          this.user = response.data.user
        } catch (error) {
          this.logout()
        }
      }
    }
  }
})