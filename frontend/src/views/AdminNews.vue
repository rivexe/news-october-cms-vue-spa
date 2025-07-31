<template>
  <div class="admin-news">
    <div class="admin-header">
      <h1>Управление новостями</h1>
      <button @click="showCreateForm = true" class="btn-primary">
        Добавить новость
      </button>
    </div>
    
    <div v-if="newsStore.loading" class="loading">
      Загрузка...
    </div>
    
    <div v-else-if="newsStore.error" class="error">
      {{ newsStore.error }}
    </div>
    
    <div v-else class="news-table-container">
      <table class="news-table">
        <thead>
          <tr>
            <th>Заголовок</th>
            <th>Slug</th>
            <th>Опубликовано</th>
            <th>Дата публикации</th>
            <th>Действия</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="news in newsStore.newsList" :key="news.id">
            <td>{{ news.title }}</td>
            <td>{{ news.slug }}</td>
            <td>
              <span class="status" :class="{ published: news.is_published }">
                {{ news.is_published ? 'Да' : 'Нет' }}
              </span>
            </td>
            <td>{{ formatDate(news.published_at) }}</td>
            <td class="actions">
              <button @click="editNews(news)" class="btn-edit">
                Редактировать
              </button>
              <button @click="deleteNews(news)" class="btn-delete">
                Удалить
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <!-- Модальное окно для создания/редактирования -->
    <div v-if="showCreateForm || editingNews" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <NewsForm
          :news="editingNews"
          @save="handleSave"
          @cancel="closeModal"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useNewsStore } from '../stores/news'
import NewsForm from '../components/NewsForm.vue'

export default {
  name: 'AdminNews',
  components: {
    NewsForm
  },
  setup() {
    const newsStore = useNewsStore()
    const showCreateForm = ref(false)
    const editingNews = ref(null)
    
    /**
     * Форматирование даты для отображения
     */
    const formatDate = (dateString) => {
      if (!dateString) return 'Не установлена'
      const date = new Date(dateString)
      return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
    
    /**
     * Открытие формы редактирования
     */
    const editNews = (news) => {
      editingNews.value = { ...news }
    }
    
    /**
     * Удаление новости с подтверждением
     */
    const deleteNews = async (news) => {
      if (confirm(`Вы уверены, что хотите удалить новость "${news.title}"?`)) {
        const result = await newsStore.deleteNews(news.id)
        if (!result.success) {
          alert(result.error)
        }
      }
    }
        
    /**
     * Обработка сохранения новости
     */
    const handleSave = async (newsData) => {
      let result
      
      if (editingNews.value) {
        result = await newsStore.updateNews(editingNews.value.id, newsData)
      } else {
        result = await newsStore.createNews(newsData)
      }
      
      if (result.success) {
        closeModal()
        await newsStore.fetchNews(true)
      } else {
        alert(result.error)
      }
    }
    
    /**
     * Закрытие модального окна
     */
    const closeModal = () => {
      showCreateForm.value = false
      editingNews.value = null
    }
    
    onMounted(() => {
      newsStore.fetchNews(true)
    })
    
    return {
      newsStore,
      showCreateForm,
      editingNews,
      formatDate,
      editNews,
      deleteNews,
      handleSave,
      closeModal
    }
  }
}
</script>

<style scoped>
.admin-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.admin-header h1 {
  color: #2c3e50;
}

.btn-primary {
  background: #27ae60;
  color: white;
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  transition: background 0.3s;
}

.btn-primary:hover {
  background: #219a52;
}

.news-table-container {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.news-table {
  width: 100%;
  border-collapse: collapse;
}

.news-table th,
.news-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #ecf0f1;
}

.news-table th {
  background: #f8f9fa;
  font-weight: 600;
  color: #2c3e50;
}

.status {
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.875rem;
  background: #e74c3c;
  color: white;
}

.status.published {
  background: #27ae60;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.btn-edit,
.btn-delete {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.875rem;
  transition: background 0.3s;
}

.btn-edit {
  background: #3498db;
  color: white;
}

.btn-edit:hover {
  background: #2980b9;
}

.btn-delete {
  background: #e74c3c;
  color: white;
}

.btn-delete:hover {
  background: #c0392b;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
}

.loading, .error {
  text-align: center;
  padding: 2rem;
  font-size: 1.1rem;
}

.error {
  color: #e74c3c;
}
</style>