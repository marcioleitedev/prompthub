<template>
  <div class="ai-page">
    <div class="container">
      <!-- Header com Links de API -->
      <div class="header-section">
        <div class="header-top">
          <div>
            <h1>🚀 PromptHub</h1>
            <p>Hub de Assistentes Inteligentes</p>
          </div>
          <button @click="logout" class="btn-logout">
            🚪 Sair
          </button>
        </div>
        
        <div class="api-keys-box">
          <h3>🔑 Onde obter suas API Keys:</h3>
          <div class="api-links">
            <a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener" class="api-link openai">
              <span class="icon">🤖</span>
              <div class="link-info">
                <strong>OpenAI</strong>
                <small>platform.openai.com/api-keys</small>
              </div>
            </a>
            <a href="https://aistudio.google.com/app/apikey" target="_blank" rel="noopener" class="api-link gemini">
              <span class="icon">✨</span>
              <div class="link-info">
                <strong>Google Gemini</strong>
                <small>aistudio.google.com/app/apikey</small>
              </div>
            </a>

          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="tabs">
        <button 
          :class="['tab', { active: activeTab === 'agents' }]"
          @click="activeTab = 'agents'"
        >
          🤖 Meus Agentes
        </button>
        <button 
          :class="['tab', { active: activeTab === 'prompt' }]"
          @click="activeTab = 'prompt'"
        >
          💬 Prompt Direto
        </button>
      </div>

      <!-- Tab: Meus Agentes -->
      <div v-if="activeTab === 'agents'">
        <AgentsList 
          @create-agent="showAgentForm = true; editingAgent = null"
          @edit-agent="editAgent"
        />
      </div>

      <!-- Tab: Prompt Direto -->
      <div v-if="activeTab === 'prompt'">
        <div class="prompt-card">
          <!-- Conteúdo do prompt direto (mantém o código original) -->
          <div class="ai-selector">
            <label>Selecione a IA:</label>
            <div class="ai-options">
              <button
                v-for="ai in aiProviders"
                :key="ai.value"
                :class="['ai-button', { active: selectedAi === ai.value }]"
                @click="selectAi(ai.value)"
              >
                <span class="ai-icon">{{ ai.icon }}</span>
                <span class="ai-name">{{ ai.name }}</span>
              </button>
            </div>
          </div>

          <div class="form-group">
            <label>API Token</label>
            <div class="token-input-group">
              <input
                :type="showToken ? 'text' : 'password'"
                v-model="apiTokens[selectedAi]"
                :placeholder="`Cole seu token da ${aiProviders.find(a => a.value === selectedAi)?.name}`"
                class="token-input"
              />
              <button type="button" class="toggle-token" @click="showToken = !showToken">
                {{ showToken ? '🙈' : '👁️' }}
              </button>
            </div>
          </div>

          <div class="form-group">
            <label>Seu Prompt:</label>
            <textarea
              v-model="prompt"
              placeholder="Digite seu prompt aqui..."
              rows="8"
              class="prompt-textarea"
            ></textarea>
            <div class="char-count">{{ prompt.length }} caracteres</div>
          </div>

          <div class="actions">
            <button @click="sendPrompt" class="btn-primary" :disabled="loading || !prompt.trim() || !apiTokens[selectedAi]">
              {{ loading ? 'Enviando...' : '🚀 Enviar Prompt' }}
            </button>
            <button @click="prompt = ''; response = ''" class="btn-secondary" :disabled="loading">
              🗑️ Limpar
            </button>
          </div>

          <div v-if="error" class="error-message">{{ error }}</div>

          <div v-if="response" class="response-card">
            <div class="response-header">
              <h2>Resposta</h2>
              <button @click="copyResponse" class="btn-copy">
                {{ copied ? '✓ Copiado' : '📋 Copiar' }}
              </button>
            </div>
            <div class="response-content">
              <pre>{{ response }}</pre>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal: Formulário de Agente -->
      <AgentForm 
        v-if="showAgentForm"
        :agent="editingAgent"
        @close="showAgentForm = false; editingAgent = null"
        @saved="handleAgentSaved"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AgentsList from '../components/AgentsList.vue'
import AgentForm from '../components/AgentForm.vue'
import { API_BASE } from '../config'

const activeTab = ref('agents')
const showAgentForm = ref(false)
const editingAgent = ref(null)

const aiProviders = [
  { value: 'openai', name: 'OpenAI', icon: '🤖' },
  { value: 'gemini', name: 'Gemini', icon: '✨' }
]

const selectedAi = ref('openai')
const apiTokens = ref({
  openai: '',
  gemini: ''
})
const prompt = ref('')
const response = ref('')
const loading = ref(false)
const error = ref('')
const showToken = ref(false)
const copied = ref(false)

onMounted(() => {
  const savedTokens = localStorage.getItem('ai_tokens')
  if (savedTokens) {
    apiTokens.value = JSON.parse(savedTokens)
  }
})

const selectAi = (ai) => {
  selectedAi.value = ai
  error.value = ''
}

const sendPrompt = async () => {
  if (!prompt.value.trim() || !apiTokens.value[selectedAi.value]) {
    error.value = 'Preencha todos os campos'
    return
  }

  const token = localStorage.getItem('token')
  if (!token) {
    error.value = 'Você precisa estar logado. Redirecionando...'
    setTimeout(() => {
      window.location.href = '/login'
    }, 2000)
    return
  }

  loading.value = true
  error.value = ''
  response.value = ''

  try {
    localStorage.setItem('ai_tokens', JSON.stringify(apiTokens.value))

    const res = await fetch(`${API_BASE}/ai/prompt`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': `Bearer ${token}`
      },
      body: JSON.stringify({
        provider: selectedAi.value,
        api_token: apiTokens.value[selectedAi.value],
        prompt: prompt.value,
        temperature: 0.7,
        max_tokens: 1000
      })
    })

    const data = await res.json()
    
    if (res.status === 401) {
      error.value = 'Sessão expirada. Faça login novamente.'
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      setTimeout(() => {
        window.location.href = '/login'
      }, 2000)
      return
    }
    
    if (!res.ok) throw new Error(data.error || 'Erro ao enviar prompt')
    response.value = data.response
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

const copyResponse = async () => {
  try {
    await navigator.clipboard.writeText(response.value)
    copied.value = true
    setTimeout(() => copied.value = false, 2000)
  } catch (err) {
    console.error('Erro ao copiar:', err)
  }
}

const editAgent = (agent) => {
  editingAgent.value = agent
  showAgentForm.value = true
}

const handleAgentSaved = () => {
  // Recarrega a lista de agentes
  activeTab.value = 'agents'
}

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  window.location.href = '/login'
}
</script>

<style scoped>
.ai-page {
  width: 100%;
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 40px 20px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.header-section {
  text-align: center;
  color: white;
  margin-bottom: 40px;
}

.header-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.header-top > div {
  flex: 1;
  text-align: center;
}

.btn-logout {
  padding: 10px 20px;
  background: rgba(255, 255, 255, 0.2);
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 8px;
  color: white;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  white-space: nowrap;
}

.btn-logout:hover {
  background: rgba(255, 255, 255, 0.3);
  border-color: white;
  transform: translateY(-2px);
}

.header-section h1 {
  font-size: 42px;
  margin-bottom: 12px;
}

.header-section p {
  font-size: 18px;
  opacity: 0.9;
}

.api-keys-box {
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 24px;
  margin-top: 24px;
}

.api-keys-box h3 {
  color: white;
  margin-bottom: 16px;
  font-size: 18px;
}

.api-links {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 16px;
}

.api-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: white;
  border-radius: 12px;
  text-decoration: none;
  transition: all 0.3s;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.api-link:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.api-link .icon {
  font-size: 32px;
}

.link-info {
  flex: 1;
}

.link-info strong {
  display: block;
  color: #333;
  font-size: 16px;
  margin-bottom: 4px;
}

.link-info small {
  color: #666;
  font-size: 12px;
}

.api-link.openai { border-left: 4px solid #10a37f; }
.api-link.gemini { border-left: 4px solid #4285f4; }
.api-link.perplexity { border-left: 4px solid #8b5cf6; }

.tabs {
  display: flex;
  gap: 12px;
  margin-bottom: 24px;
}

.tab {
  flex: 1;
  padding: 16px 24px;
  background: rgba(255, 255, 255, 0.2);
  border: none;
  border-radius: 12px;
  color: white;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.tab:hover {
  background: rgba(255, 255, 255, 0.3);
}

.tab.active {
  background: white;
  color: #667eea;
}

.prompt-card {
  background: white;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.ai-selector {
  margin-bottom: 28px;
}

.ai-selector label {
  display: block;
  font-weight: 600;
  color: #333;
  margin-bottom: 16px;
}

.ai-options {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 12px;
}

.ai-button {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 20px;
  border: 2px solid #e1e8ed;
  border-radius: 12px;
  background: white;
  cursor: pointer;
  transition: all 0.3s;
  font-weight: 600;
  color: #666;
}

.ai-button:hover {
  border-color: #667eea;
}

.ai-button.active {
  border-color: #667eea;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.ai-icon {
  font-size: 32px;
}

.form-group {
  margin-bottom: 24px;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #333;
  margin-bottom: 8px;
}

.token-input-group {
  display: flex;
  gap: 8px;
}

.token-input,
.prompt-textarea {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e1e8ed;
  border-radius: 8px;
  font-size: 14px;
  font-family: inherit;
  transition: border-color 0.3s;
}

.token-input {
  flex: 1;
}

.toggle-token {
  padding: 12px 16px;
  border: 2px solid #e1e8ed;
  border-radius: 8px;
  background: white;
  cursor: pointer;
  font-size: 18px;
}

.toggle-token:hover {
  border-color: #667eea;
}

.token-input:focus,
.prompt-textarea:focus {
  outline: none;
  border-color: #667eea;
}

.prompt-textarea {
  resize: vertical;
}

.char-count {
  text-align: right;
  color: #999;
  font-size: 12px;
  margin-top: 4px;
}

.actions {
  display: flex;
  gap: 12px;
}

.btn-primary,
.btn-secondary {
  flex: 1;
  padding: 14px 24px;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-secondary {
  background: white;
  color: #666;
  border: 2px solid #e1e8ed;
}

.btn-secondary:hover:not(:disabled) {
  border-color: #667eea;
  color: #667eea;
}

.error-message {
  margin-top: 16px;
  padding: 12px;
  background: #fee;
  border: 1px solid #fcc;
  border-radius: 8px;
  color: #c33;
}

.response-card {
  margin-top: 24px;
  padding-top: 24px;
  border-top: 2px solid #e1e8ed;
}

.response-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.response-header h2 {
  margin: 0;
  color: #333;
}

.btn-copy {
  padding: 8px 16px;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

.btn-copy:hover {
  background: #764ba2;
}

.response-content {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 20px;
  max-height: 500px;
  overflow-y: auto;
}

.response-content pre {
  margin: 0;
  white-space: pre-wrap;
  word-wrap: break-word;
  color: #333;
  font-size: 14px;
  line-height: 1.6;
}
</style>
