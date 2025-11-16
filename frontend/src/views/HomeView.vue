<template>
  <div class="home">
    <nav class="navbar">
      <div class="nav-content">
        <h1>{{ t('home.title') }}</h1>
        <div class="user-menu" v-if="user">
          <img :src="user.avatar || 'https://via.placeholder.com/40'" :alt="user.name" class="avatar" />
          <span>{{ user.name }}</span>
          <button @click="handleLogout" class="btn-logout">{{ t('nav.logout') }}</button>
        </div>
      </div>
    </nav>

    <div class="container">
      <div class="welcome-card">
        <h2>{{ t('home.welcome') }}</h2>
        <p v-if="user">{{ t('home.subtitle', { name: user.name }) }}</p>
        <p v-else>{{ t('home.loading') }}</p>
        
        <div class="cta-section">
          <button @click="goToAiPrompt" class="btn-ai">
            {{ t('home.tryAssistant') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { logout } from '../services/auth'

const { t } = useI18n()
const router = useRouter()
const user = ref(null)

onMounted(() => {
  const userStr = localStorage.getItem('user')
  if (userStr) {
    user.value = JSON.parse(userStr)
  } else {
    router.push('/login')
  }
})

const handleLogout = async () => {
  try {
    await logout()
    router.push('/login')
  } catch (error) {
    console.error('Erro ao fazer logout:', error)
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push('/login')
  }
}

const goToAiPrompt = () => {
  router.push('/ai-prompt')
}
</script>

<style scoped>
.home {
  width: 100%;
  min-height: 100vh;
  background: #f5f7fa;
  margin: 0;
  padding: 0;
}

.navbar {
  width: 100%;
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  padding: 16px 0;
  margin: 0;
}

.nav-content {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav-content h1 {
  margin: 0;
  color: #667eea;
  font-size: 24px;
}

.user-menu {
  display: flex;
  align-items: center;
  gap: 16px;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.user-menu span {
  color: #333;
  font-weight: 500;
}

.btn-logout {
  padding: 8px 20px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-logout:hover {
  background: #764ba2;
  transform: translateY(-2px);
}

.container {
  max-width: 1200px;
  margin: 40px auto;
  padding: 0 20px;
}

.welcome-card {
  background: white;
  padding: 40px;
  border-radius: 16px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.welcome-card h2 {
  color: #333;
  margin-bottom: 16px;
  font-size: 28px;
}

.welcome-card p {
  color: #666;
  font-size: 18px;
}

.cta-section {
  margin-top: 32px;
}

.btn-ai {
  padding: 16px 32px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 18px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-ai:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}
</style>
