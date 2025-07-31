<template>
  <div id="app">
    <nav class="navbar">
      <div class="nav-container">
        <router-link to="/" class="nav-brand">Новости</router-link>
        <div class="nav-menu">
          <router-link to="/news" class="nav-link">Все новости</router-link>
          <router-link v-if="!authStore.isAuthenticated" to="/admin/login" class="nav-link">
            Войти
          </router-link>
          <div v-else class="nav-admin">
            <router-link to="/admin/news" class="nav-link">Админ панель</router-link>
            <button @click="handleLogout" class="nav-link btn-logout">Выйти</button>
          </div>
        </div>
      </div>
    </nav>
    
    <main class="main-content">
      <router-view />
    </main>
  </div>
</template>

<script>
import { useAuthStore } from './stores/auth'
import { useRouter } from 'vue-router' 

export default {
  name: 'App',
  setup() {
    const authStore = useAuthStore()
    const router = useRouter()

      /**
     * Обработка выхода из системы
     */
    const handleLogout = async () => {
      authStore.logout()
      await router.push('/admin/login')
    }
    
    return {
      authStore,
      handleLogout
    }
  }
}
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: Arial, sans-serif;
  line-height: 1.6;
  color: #333;
}

.navbar {
  background: #2c3e50;
  color: white;
  padding: 1rem 0;
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav-brand {
  font-size: 1.5rem;
  font-weight: bold;
  color: white;
  text-decoration: none;
}

.nav-menu {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.nav-link {
  color: white;
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  transition: background 0.3s;
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.1);
}

.btn-logout {
  background: transparent;
  border: none;
  cursor: pointer;
}

.main-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem 1rem;
}

.router-link-active {
  background: rgba(255, 255, 255, 0.2);
}
</style>