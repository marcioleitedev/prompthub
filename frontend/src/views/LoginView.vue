<template>
  <div class="login-container">
    <div class="login-card">
      <div class="brand">
        <h1 class="brand-name">🚀 PromptHub</h1>
        <p class="brand-tagline">Hub de Assistentes Inteligentes</p>
      </div>
      
      <h2>{{ isRegistering ? t('login.createAccount') : t('login.title') }}</h2>
      
      <form @submit.prevent="handleLogin" v-if="!isRegistering">
        <div class="form-group">
          <label for="email">{{ t('login.email') }}</label>
          <input
            type="email"
            id="email"
            v-model="loginForm.email"
            required
            :placeholder="t('login.emailPlaceholder')"
          />
        </div>

        <div class="form-group">
          <label for="password">{{ t('login.password') }}</label>
          <input
            type="password"
            id="password"
            v-model="loginForm.password"
            required
            :placeholder="t('login.passwordPlaceholder')"
          />
        </div>

        <button type="submit" class="btn-primary" :disabled="loading">
          {{ loading ? t('login.entering') : t('login.enterButton') }}
        </button>

        <div class="divider">
          <span>{{ t('login.or') }}</span>
        </div>

        <button type="button" @click="loginWithGoogle" class="btn-google">
          <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            <path fill="none" d="M0 0h48v48H0z"/>
          </svg>
          {{ t('login.continueGoogle') }}
        </button>

        <p class="toggle-form">
          {{ t('login.noAccount') }}
          <a href="#" @click.prevent="isRegistering = true">{{ t('login.signUp') }}</a>
        </p>
      </form>

      <form @submit.prevent="handleRegister" v-else>
        <div class="form-group">
          <label for="name">{{ t('login.name') }}</label>
          <input
            type="text"
            id="name"
            v-model="registerForm.name"
            required
            :placeholder="t('login.namePlaceholder')"
          />
        </div>

        <div class="form-group">
          <label for="register-email">{{ t('login.email') }}</label>
          <input
            type="email"
            id="register-email"
            v-model="registerForm.email"
            required
            placeholder="seu@email.com"
          />
        </div>

        <div class="form-group">
          <label for="register-password">Senha</label>
          <input
            type="password"
            id="register-password"
            v-model="registerForm.password"
            required
            placeholder="••••••••"
            minlength="6"
          />
        </div>

        <div class="form-group">
          <label for="password-confirmation">Confirmar Senha</label>
          <input
            type="password"
            id="password-confirmation"
            v-model="registerForm.password_confirmation"
            required
            placeholder="••••••••"
            minlength="6"
          />
        </div>

        <button type="submit" class="btn-primary" :disabled="loading">
          {{ loading ? 'Cadastrando...' : 'Cadastrar' }}
        </button>

        <div class="divider">
          <span>ou</span>
        </div>

        <button type="button" @click="loginWithGoogle" class="btn-google">
          <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
            <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
            <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
            <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
            <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
            <path fill="none" d="M0 0h48v48H0z"/>
          </svg>
          Cadastrar com Google
        </button>

        <p class="toggle-form">
          Já tem uma conta? 
          <a href="#" @click.prevent="isRegistering = false">Faça login</a>
        </p>
      </form>

      <div v-if="error" class="error-message">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { login, register } from '../services/auth'
import { API_BASE } from '../config'

const { t } = useI18n()
const router = useRouter()
const isRegistering = ref(false)
const loading = ref(false)
const error = ref('')

const loginForm = ref({
  email: '',
  password: ''
})

const registerForm = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const handleLogin = async () => {
  try {
    loading.value = true
    error.value = ''
    const response = await login(loginForm.value)
    localStorage.setItem('token', response.token)
    localStorage.setItem('user', JSON.stringify(response.user))
    router.push('/')
  } catch (err) {
    error.value = err.response?.data?.error || 'Erro ao fazer login'
  } finally {
    loading.value = false
  }
}

const handleRegister = async () => {
  try {
    loading.value = true
    error.value = ''
    
    if (registerForm.value.password !== registerForm.value.password_confirmation) {
      error.value = 'As senhas não coincidem'
      return
    }
    
    const response = await register(registerForm.value)
    localStorage.setItem('token', response.token)
    localStorage.setItem('user', JSON.stringify(response.user))
    router.push('/')
  } catch (err) {
    error.value = err.response?.data?.errors 
      ? Object.values(err.response.data.errors).flat().join(', ')
      : 'Erro ao fazer cadastro'
  } finally {
    loading.value = false
  }
}

const loginWithGoogle = async () => {
  try {
    const response = await fetch(`${API_BASE}/auth/google`)
    const data = await response.json()
    if (data.url) {
      window.location.href = data.url
    }
  } catch (err) {
    error.value = 'Erro ao conectar com Google'
  }
}
</script>

<style scoped>
.login-container {
  width: 100vw;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
  margin: 0;
  position: relative;
  left: 50%;
  right: 50%;
  margin-left: -50vw;
  margin-right: -50vw;
}

.login-card {
  background: white;
  padding: 40px;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  width: 100%;
  max-width: 420px;
}

.brand {
  text-align: center;
  margin-bottom: 32px;
  padding-bottom: 24px;
  border-bottom: 2px solid #f0f0f0;
}

.brand-name {
  font-size: 36px;
  font-weight: 700;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 8px;
}

.brand-tagline {
  font-size: 14px;
  color: #666;
  margin: 0;
}

h1, h2 {
  text-align: center;
  color: #333;
  margin-bottom: 30px;
  font-size: 28px;
}

h2 {
  font-size: 24px;
  margin-bottom: 24px;
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 8px;
  color: #555;
  font-weight: 500;
  font-size: 14px;
}

input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e1e8ed;
  border-radius: 8px;
  font-size: 14px;
  transition: border-color 0.3s;
  box-sizing: border-box;
}

input:focus {
  outline: none;
  border-color: #667eea;
}

.btn-primary {
  width: 100%;
  padding: 14px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s, opacity 0.2s;
  margin-bottom: 16px;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.divider {
  position: relative;
  text-align: center;
  margin: 24px 0;
}

.divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  background: #e1e8ed;
}

.divider span {
  position: relative;
  background: white;
  padding: 0 16px;
  color: #999;
  font-size: 14px;
}

.btn-google {
  width: 100%;
  padding: 14px;
  background: white;
  color: #555;
  border: 2px solid #e1e8ed;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  transition: all 0.2s;
}

.btn-google:hover {
  background: #f8f9fa;
  border-color: #d1d9e0;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.toggle-form {
  text-align: center;
  margin-top: 24px;
  color: #666;
  font-size: 14px;
}

.toggle-form a {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
}

.toggle-form a:hover {
  text-decoration: underline;
}

.error-message {
  margin-top: 16px;
  padding: 12px;
  background: #fee;
  border: 1px solid #fcc;
  border-radius: 8px;
  color: #c33;
  font-size: 14px;
  text-align: center;
}
</style>
