<template>
  <div class="callback-container">
    <div class="spinner"></div>
    <p>{{ message }}</p>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { API_BASE } from '../config'

const router = useRouter()
const route = useRoute()
const message = ref('Processando autenticação...')

onMounted(() => {
  console.log('AuthCallback mounted')
  const token = route.query.token
  const error = route.query.error
  
  console.log('Token:', token)
  console.log('Error:', error)

  if (error) {
    message.value = 'Erro na autenticação. Redirecionando...'
    setTimeout(() => {
      router.push('/login')
    }, 2000)
    return
  }

  if (token) {
    localStorage.setItem('token', token)
    message.value = 'Login realizado com sucesso! Redirecionando...'
    
    // Fetch user data
    fetch(`${API_BASE}/me`, {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    })
    .then(res => {
      console.log('Response status:', res.status)
      return res.json()
    })
    .then(user => {
      console.log('User data:', user)
      localStorage.setItem('user', JSON.stringify(user))
      setTimeout(() => {
        router.push('/')
      }, 1000)
    })
    .catch(err => {
      console.error('Error fetching user:', err)
      setTimeout(() => {
        router.push('/')
      }, 1000)
    })
  } else {
    message.value = 'Token não encontrado. Redirecionando...'
    setTimeout(() => {
      router.push('/login')
    }, 2000)
  }
})
</script>

<style scoped>
.callback-container {
  width: 100vw;
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  margin: 0;
  padding: 0;
}

.spinner {
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top: 4px solid white;
  width: 50px;
  height: 50px;
  animation: spin 1s linear infinite;
  margin-bottom: 20px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

p {
  color: white;
  font-size: 18px;
  font-weight: 500;
}
</style>
