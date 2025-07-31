<template>
  <div class="news-list">
    <h1>Новости</h1>
    
    <div v-if="newsStore.loading" class="loading">
      Загрузка...
    </div>
    
    <div v-else-if="newsStore.error" class="error">
      {{ newsStore.error }}
    </div>
    
    <div v-else class="news-grid">
      <NewsCard 
        v-for="news in newsStore.newsList" 
        :key="news.id" 
        :news="news" 
      />
    </div>
  </div>
</template>

<script>
import { onMounted } from 'vue'
import { useNewsStore } from '../stores/news'
import NewsCard from '../components/NewsCard.vue'

export default {
  name: 'NewsList',
  components: {
    NewsCard
  },
  setup() {
    const newsStore = useNewsStore()
    
    onMounted(() => {
      newsStore.fetchNews()
    })
    
    return {
      newsStore
    }
  }
}
</script>

<style scoped>
.news-list h1 {
  margin-bottom: 2rem;
  color: #2c3e50;
}

.news-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 2rem;
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