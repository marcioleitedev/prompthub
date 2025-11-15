<template>
  <div class="ai-agents-page">
    <div class="container">
      <div class="header">
        <h1>🤖 Gerenciar Agentes de IA</h1>
        <p>Crie e gerencie seus agentes personalizados</p>
      </div>

      <!-- Lista de Agentes -->
      <div class="agents-grid">
        <div 
          v-for="agent in agents" 
          :key="agent.id" 
          class="agent-card"
          @click="selectAgent(agent)"
        >
          <div class="agent-header">
            <h3>{{ agent.name }}</h3>
            <span :class="['badge', agent.ai_provider]">
              {{ getProviderIcon(agent.ai_provider) }} {{ agent.ai_provider }}
            </span>
          </div>
          <p class="agent-description">{{ agent.description || 'Sem descrição' }}</p>
          <div class="agent-actions">
            <button @click.stop="useAgent(agent)" class="btn-use">
              💬 Usar
            </button>
            <button @click.stop="editAgent(agent)" class="btn-edit">
              ✏️ Editar
            </button>
            <button @click.stop="deleteAgent(agent.id)" class="btn-delete">
              🗑️
            </button>
          </div>
        </div>

        <!-- Card para criar novo agente -->
        <div class="agent-card create-card" @click="$emit('create-agent')">
          <div class="create-icon">➕</div>
          <h3>Criar Novo Agente</h3>
        </div>
      </div>

      <!-- Modal: Usar Agente -->
      <div v-if="selectedAgent" class="modal" @click.self="selectedAgent = null">
        <div class="modal-content">
          <div class="modal-header">
            <h2>{{ selectedAgent.name }}</h2>
            <button @click="selectedAgent = null" class="btn-close">✕</button>
          </div>

          <div class="form-group">
            <label>API Token - {{ selectedAgent.ai_provider }}</label>
            <div class="token-input-group">
              <input
                :type="showToken ? 'text' : 'password'"
                v-model="apiToken"
                placeholder="Cole seu API token aqui"
                class="token-input"
              />
              <button 
                type="button" 
                class="toggle-token"
                @click="showToken = !showToken"
              >
                {{ showToken ? '🙈' : '👁️' }}
              </button>
            </div>
          </div>

          <div class="form-group">
            <label>Seu Prompt:</label>
            <textarea
              v-model="agentPrompt"
              placeholder="Digite seu prompt..."
              rows="5"
              class="prompt-textarea"
            ></textarea>
          </div>

          <div class="form-group">
            <label>Arquivo/Dados (opcional):</label>
            <textarea
              v-model="fileContent"
              placeholder="Cole conteúdo de arquivo ou dados adicionais aqui..."
              rows="8"
              class="file-textarea"
            ></textarea>
          </div>

          <div class="form-group">
            <label>Dados Adicionais (opcional):</label>
            <textarea
              v-model="additionalData"
              placeholder="Informações extras para o agente..."
              rows="4"
              class="prompt-textarea"
            ></textarea>
          </div>

          <div class="modal-actions">
            <button @click="sendToAgent" class="btn-primary" :disabled="loading">
              {{ loading ? 'Enviando...' : '🚀 Enviar' }}
            </button>
          </div>

          <div v-if="error" class="error-message">{{ error }}</div>
          
          <div v-if="response" class="response-section">
            <h3>Resposta:</h3>
            <div class="response-content">
              <pre>{{ response }}</pre>
            </div>
            <button @click="copyResponse" class="btn-copy">
              {{ copied ? '✓ Copiado' : '📋 Copiar' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { API_BASE } from '../config'

const emit = defineEmits(['create-agent'])

const agents = ref([])
const selectedAgent = ref(null)
const apiToken = ref('')
const agentPrompt = ref('')
const fileContent = ref('')
const additionalData = ref('')
const response = ref('')
const error = ref('')
const loading = ref(false)
const showToken = ref(false)
const copied = ref(false)

onMounted(() => {
  loadAgents()
})

const loadAgents = async () => {
  try {
    const res = await fetch(`${API_BASE}/agents`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json'
      }
    })
    const data = await res.json()
    agents.value = data
  } catch (err) {
    console.error('Erro ao carregar agentes:', err)
  }
}

const selectAgent = (agent) => {
  selectedAgent.value = agent
  response.value = ''
  error.value = ''
  agentPrompt.value = ''
  fileContent.value = ''
  additionalData.value = ''
}

const useAgent = (agent) => {
  selectAgent(agent)
}

const editAgent = (agent) => {
  // Emitir evento para abrir formulário de edição
  emit('edit-agent', agent)
}

const deleteAgent = async (id) => {
  if (!confirm('Tem certeza que deseja deletar este agente?')) return

  try {
    await fetch(`${API_BASE}/agents/${id}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json'
      }
    })
    loadAgents()
  } catch (err) {
    alert('Erro ao deletar agente')
  }
}

const sendToAgent = async () => {
  if (!agentPrompt.value.trim()) {
    error.value = 'Digite um prompt'
    return
  }

  if (!apiToken.value) {
    error.value = 'Digite o API token'
    return
  }

  loading.value = true
  error.value = ''
  response.value = ''

  try {
    const res = await fetch(`${API_BASE}/agents/${selectedAgent.value.id}/prompt`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        api_token: apiToken.value,
        prompt: agentPrompt.value,
        file_content: fileContent.value,
        additional_data: additionalData.value
      })
    })

    const data = await res.json()

    if (!res.ok) {
      throw new Error(data.error || 'Erro ao enviar prompt')
    }

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

const getProviderIcon = (provider) => {
  const icons = {
    openai: '🤖',
    gemini: '✨',
    perplexity: '🔍'
  }
  return icons[provider] || '🤖'
}
</script>

<style scoped>
.ai-agents-page {
  width: 100%;
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 40px 20px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.header {
  text-align: center;
  color: white;
  margin-bottom: 40px;
}

.header h1 {
  font-size: 42px;
  margin-bottom: 12px;
}

.header p {
  font-size: 18px;
  opacity: 0.9;
}

.agents-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 24px;
  margin-bottom: 40px;
}

.agent-card {
  background: white;
  border-radius: 16px;
  padding: 24px;
  cursor: pointer;
  transition: all 0.3s;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

.agent-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.agent-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 12px;
}

.agent-header h3 {
  color: #333;
  margin: 0;
  font-size: 20px;
}

.badge {
  padding: 4px 12px;
  border-radius: 12px;
  font-size: 12px;
  font-weight: 600;
}

.badge.openai { background: #10a37f; color: white; }
.badge.gemini { background: #4285f4; color: white; }
.badge.perplexity { background: #8b5cf6; color: white; }

.agent-description {
  color: #666;
  font-size: 14px;
  margin: 12px 0;
  line-height: 1.5;
}

.agent-actions {
  display: flex;
  gap: 8px;
  margin-top: 16px;
}

.btn-use, .btn-edit, .btn-delete {
  flex: 1;
  padding: 8px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.3s;
}

.btn-use {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-edit {
  background: #f8f9fa;
  color: #667eea;
}

.btn-delete {
  background: #fee;
  color: #c33;
}

.btn-use:hover { opacity: 0.9; }
.btn-edit:hover { background: #e9ecef; }
.btn-delete:hover { background: #fdd; }

.create-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 200px;
  border: 2px dashed #667eea;
  background: rgba(255, 255, 255, 0.9);
}

.create-icon {
  font-size: 48px;
  margin-bottom: 12px;
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  background: white;
  border-radius: 16px;
  padding: 32px;
  max-width: 800px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 2px solid #e1e8ed;
}

.modal-header h2 {
  margin: 0;
  color: #333;
}

.btn-close {
  padding: 8px 12px;
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #999;
}

.btn-close:hover {
  color: #333;
}

.form-group {
  margin-bottom: 20px;
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
.prompt-textarea,
.file-textarea {
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

.prompt-textarea {
  resize: vertical;
}

.file-textarea {
  resize: vertical;
  font-family: 'Courier New', monospace;
  font-size: 13px;
}

.token-input:focus,
.prompt-textarea:focus,
.file-textarea:focus {
  outline: none;
  border-color: #667eea;
}

.modal-actions {
  display: flex;
  gap: 12px;
  margin-top: 24px;
}

.btn-primary {
  flex: 1;
  padding: 14px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-primary:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.error-message {
  margin-top: 16px;
  padding: 12px;
  background: #fee;
  border: 1px solid #fcc;
  border-radius: 8px;
  color: #c33;
}

.response-section {
  margin-top: 24px;
  padding-top: 24px;
  border-top: 2px solid #e1e8ed;
}

.response-section h3 {
  margin-bottom: 12px;
  color: #333;
}

.response-content {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 20px;
  max-height: 400px;
  overflow-y: auto;
  margin-bottom: 12px;
}

.response-content pre {
  margin: 0;
  white-space: pre-wrap;
  word-wrap: break-word;
  color: #333;
  font-size: 14px;
  line-height: 1.6;
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
</style>
