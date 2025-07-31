<template>
  <div class="news-form">
    <h2>{{ news ? 'Редактировать новость' : 'Создать новость' }}</h2>
    
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="title">Заголовок:</label>
        <input
          id="title"
          v-model="form.title"
          type="text"
          required
          class="form-input"
          :disabled="loading"
        />
      </div>
      
      <div class="form-group">
        <label for="slug">Slug:</label>
        <input
          id="slug"
          v-model="form.slug"
          type="text"
          class="form-input"
          :disabled="loading"
          placeholder="Автоматически из заголовка"
        />
      </div>
      
      <div class="form-group">
        <label for="content">Содержание:</label>
        <textarea
          id="content"
          v-model="form.content"
          required
          class="form-textarea"
          :disabled="loading"
          rows="10"
        ></textarea>
      </div>
      
      <div class="form-row">
        <div class="form-group">
          <label for="published_at">Дата публикации:</label>
          <input
            id="published_at"
            v-model="form.published_at"
            type="datetime-local"
            class="form-input"
            :disabled="loading"
          />
        </div>
        
        <div class="form-group">
          <label class="checkbox-label">
            <input
              v-model="form.is_published"
              type="checkbox"
              :disabled="loading"
            />
            Опубликовано
          </label>
        </div>
      </div>
      
      <div class="form-actions">
        <button type="button" @click="$emit('cancel')" class="btn-cancel" :disabled="loading">
          Отмена
        </button>
        <button type="submit" class="btn-save" :disabled="loading">
          {{ loading ? 'Сохранение...' : 'Сохранить' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { ref, onMounted, watch } from 'vue'

export default {
  name: 'NewsForm',
  props: {
    news: {
      type: Object,
      default: null
    }
  },
  emits: ['save', 'cancel'],
  setup(props, { emit }) {
    const loading = ref(false)
    
    const form = ref({
      title: '',
      slug: '',
      content: '',
      published_at: '',
      is_published: false
    })
    
    /**
     * Заполнение формы данными новости при редактировании
     */
    const fillForm = () => {
      if (props.news) {
        form.value = {
          title: props.news.title || '',
          slug: props.news.slug || '',
          content: props.news.content || '',
          published_at: props.news.published_at ? 
            new Date(props.news.published_at).toISOString().slice(0, 16) : '',
          is_published: props.news.is_published || false
        }
      }
    }
    
    /**
     * Автоматическая генерация slug из заголовка
     */
    watch(() => form.value.title, (newTitle) => {
      if (newTitle && !form.value.slug) {
        form.value.slug = newTitle
          .toLowerCase()
          .replace(/[^a-zA-Zа-яА-Я0-9\s]/g, '')
          .replace(/\s+/g, '-')
          .trim()
      }
    })
    
    /**
     * Обработка отправки формы
     */
    const handleSubmit = async () => {
      loading.value = true
      
      const formData = { ...form.value }
      
      // Преобразование даты в правильный формат
      if (formData.published_at) {
        formData.published_at = new Date(formData.published_at).toISOString()
      }
      
      emit('save', formData)
      loading.value = false
    }
    
    onMounted(() => {
      fillForm()
    })
    
    return {
      form,
      loading,
      handleSubmit
    }
  }
}
</script>

<style scoped>
.news-form {
  padding: 2rem;
}

.news-form h2 {
  margin-bottom: 2rem;
  color: #2c3e50;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #34495e;
}

.form-input,
.form-textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #3498db;
}

.form-textarea {
  resize: vertical;
  font-family: inherit;
}

.form-row {
  display: flex;
  gap: 1rem;
  align-items: end;
}

.form-row .form-group {
  flex: 1;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
  width: auto;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
  border-top: 1px solid #ecf0f1;
  padding-top: 1.5rem;
}

.btn-cancel,
.btn-save {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
  transition: background 0.3s;
}

.btn-cancel {
  background: #95a5a6;
  color: white;
}

.btn-cancel:hover:not(:disabled) {
  background: #7f8c8d;
}

.btn-save {
  background: #27ae60;
  color: white;
}

.btn-save:hover:not(:disabled) {
  background: #219a52;
}

.btn-cancel:disabled,
.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>