<template>
  <div class="news-detail">
    <div v-if="newsStore.loading" class="loading">
      Загрузка...
    </div>
    
    <div v-else-if="newsStore.error" class="error">
      {{ newsStore.error }}
      <router-link to="/news" class="back-link">Вернуться к новостям</router-link>
    </div>
    
    <article v-else-if="newsStore.currentNews" class="news-article">
      <header class="news-header">
        <h1>{{ newsStore.currentNews.title }}</h1>
        <time class="news-date">
          {{ formatDate(newsStore.currentNews.published_at) }}
        </time>
      </header>
      
      <div class="news-content" v-html="newsStore.currentNews.content"></div>
      
      <router-link to="/news" class="back-link">Вернуться к новостям</router-link>
    </article>
  </div>
</template>

<script>
import { onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useNewsStore } from '../stores/news'

export default {
  name: 'NewsDetail',
  props: {
    slug: {
      type: String,
      required: true
    }
  },
  setup(props) {
    const route = useRoute()
    const newsStore = useNewsStore()
    
    /**
     * Форматирование даты для отображения
     */
    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
    
    onMounted(() => {
      newsStore.fetchNewsBySlug(props.slug)
    })
    
    watch(() => route.params.slug, (newSlug) => {
      if (newSlug) {
        newsStore.fetchNewsBySlug(newSlug)
      }
    })
    
    return {
      newsStore,
      formatDate
    }
  }
}
</script>

<style scoped>
.news-article {
  max-width: 800px;
  margin: 0 auto;
}

.news-header {
  margin-bottom: 2rem;
  border-bottom: 2px solid #ecf0f1;
  padding-bottom: 1rem;
}

.news-header h1 {
  font-size: 2.5rem;
  color: #2c3e50;
  margin-bottom: 0.5rem;
  line-height: 1.2;
}

.news-date {
  color: #7f8c8d;
  font-size: 1rem;
}

.news-content {
  font-size: 1.1rem;
  line-height: 1.8;
  margin-bottom: 3rem;
}

.news-content :deep(p) {
  margin-bottom: 1rem;
}

.news-content :deep(h2),
.news-content :deep(h3) {
  margin: 2rem 0 1rem;
  color: #34495e;
}

.back-link {
  display: inline-block;
  color: #3498db;
  text-decoration: none;
  font-weight: 500;
  padding: 0.5rem 0;
  border-bottom: 1px solid transparent;
  transition: border-color 0.3s;
}

.back-link:hover {
  border-bottom-color: #3498db;
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