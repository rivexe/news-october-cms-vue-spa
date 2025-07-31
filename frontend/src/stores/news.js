import { defineStore } from 'pinia'
import api from '../services/api'

/**
 * Store для управления новостями
 */
export const useNewsStore = defineStore('news', {
  state: () => ({
    newsList: [],
    currentNews: null,
    loading: false,
    error: null
  }),

  actions: {
    /**
     * Получение списка новостей
     */
    async fetchNews(isAdmin = false) {
      this.loading = true
      this.error = null
      
      try {
        const endpoint = isAdmin ? '/admin/news' : '/news'
        const response = await api.get(endpoint)
        this.newsList = response.data.data
      } catch (error) {
        this.error = 'Ошибка при загрузке новостей'
        console.error('Fetch news error:', error)
      } finally {
        this.loading = false
      }
    },

    /**
     * Получение новости по slug
     */
    async fetchNewsBySlug(slug) {
      this.loading = true
      this.error = null
      
      try {
        const response = await api.get(`/news/${slug}`)
        this.currentNews = response.data.data
      } catch (error) {
        this.error = 'Новость не найдена'
        console.error('Fetch news by slug error:', error)
      } finally {
        this.loading = false
      }
    },

    /**
     * Создание новости
     */
    async createNews(newsData) {
      try {
        const response = await api.post('/news', newsData)
        await this.fetchNews(true)
        return { success: true, data: response.data.data }
      } catch (error) {
        return { 
          success: false, 
          error: error.response?.data?.message || 'Ошибка при создании новости' 
        }
      }
    },
    /**
     * Обновление новости
     */
    async updateNews(id, newsData) {
      try {
        const response = await api.put(`/news/${id}`, newsData)
        await this.fetchNews(true)
        return { success: true, data: response.data.data }
      } catch (error) {
        return { 
          success: false, 
          error: error.response?.data?.message || 'Ошибка при обновлении новости' 
        }
      }
    },

    /**
     * Удаление новости
     */
    async deleteNews(id) {
      try {
        await api.delete(`/news/${id}`)
        await this.fetchNews(true)
        return { success: true }
      } catch (error) {
        return { 
          success: false, 
          error: error.response?.data?.message || 'Ошибка при удалении новости' 
        }
      }
    }
  }
})