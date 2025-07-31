<template>
  <div class="news-card">
    <h3 class="news-title">
      <router-link :to="`/news/${news.slug}`">
        {{ news.title }}
      </router-link>
    </h3>
    
    <p class="news-excerpt">{{ news.content }}</p>
    
    <div class="news-meta">
      <time class="news-date">
        {{ formatDate(news.published_at) }}
      </time>
      <router-link :to="`/news/${news.slug}`" class="read-more">
        Читать далее →
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NewsCard',
  props: {
    news: {
      type: Object,
      required: true
    }
  },
  setup() {
    /**
     * Форматирование даты для отображения
     */
    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }
    
    return {
      formatDate
    }
  }
}
</script>

<style scoped>
.news-card {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}

.news-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.news-title {
  margin-bottom: 1rem;
  font-size: 1.25rem;
  line-height: 1.3;
}

.news-title a {
  color: #2c3e50;
  text-decoration: none;
  transition: color 0.3s;
}

.news-title a:hover {
  color: #3498db;
}

.news-excerpt {
  color: #7f8c8d;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.news-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.875rem;
}

.news-date {
  color: #95a5a6;
}

.read-more {
  color: #3498db;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s;
}

.read-more:hover {
  color: #2980b9;
}
</style>