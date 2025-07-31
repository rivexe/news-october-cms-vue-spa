import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import NewsList from '../views/NewsList.vue'
import NewsDetail from '../views/NewsDetail.vue'
import AdminLogin from '../views/AdminLogin.vue'
import AdminNews from '../views/AdminNews.vue'

const routes = [
  {
    path: '/',
    redirect: '/news'
  },
  {
    path: '/news',
    name: 'NewsList',
    component: NewsList
  },
  {
    path: '/news/:slug',
    name: 'NewsDetail',
    component: NewsDetail,
    props: true
  },
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: AdminLogin
  },
  {
    path: '/admin/news',
    name: 'AdminNews',
    component: AdminNews,
    meta: { requiresAuth: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

/**
 * Проверка авторизации перед переходом на защищенные маршруты
 */
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next('/admin/login')
  } else {
    next()
  }
})

export default router