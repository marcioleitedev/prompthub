<template>
  <div class="agent-form-modal" @click.self="$emit('close')">
    <div class="form-container">
      <div class="form-header">
        <h2>{{ agent ? 'Editar Agente' : 'Criar Novo Agente' }}</h2>
        <button @click="$emit('close')" class="btn-close">✕</button>
      </div>

      <form @submit.prevent="saveAgent">
        <div class="form-group">
          <label for="name">Nome do Agente *</label>
          <input
            type="text"
            id="name"
            v-model="form.name"
            placeholder="Ex: Assistente de Código"
            required
          />
        </div>

        <div class="form-group">
          <label for="description">Descrição</label>
          <textarea
            id="description"
            v-model="form.description"
            placeholder="Descreva a função deste agente..."
            rows="3"
          ></textarea>
        </div>

        <div class="form-group">
          <label>Provedor de IA *</label>
          <div class="provider-options">
            <button
              type="button"
              v-for="provider in providers"
              :key="provider.value"
              :class="['provider-btn', { active: form.ai_provider === provider.value }]"
              @click="form.ai_provider = provider.value"
            >
              <span class="icon">{{ provider.icon }}</span>
              <span>{{ provider.name }}</span>
            </button>
          </div>
        </div>

        <div class="form-group">
          <label for="ai_model">Modelo (opcional)</label>
          <input
            type="text"
            id="ai_model"
            v-model="form.ai_model"
            :placeholder="getModelPlaceholder(form.ai_provider)"
          />
        </div>

        <div class="form-group">
          <label for="system_prompt">Prompt do Sistema</label>
          <textarea
            id="system_prompt"
            v-model="form.system_prompt"
            placeholder="Ex: Você é um assistente especializado em programação Python..."
            rows="4"
          ></textarea>
          <small>Define o comportamento e personalidade do agente</small>
        </div>

        <div class="form-group">
          <label for="instructions">Instruções Específicas</label>
          <textarea
            id="instructions"
            v-model="form.instructions"
            placeholder="Ex: Sempre forneça exemplos de código. Explique conceitos complexos de forma simples..."
            rows="4"
          ></textarea>
          <small>Orientações sobre como o agente deve responder</small>
        </div>

        <div class="advanced-section">
          <h3>⚙️ Configurações Avançadas</h3>
          
          <div class="form-row">
            <div class="form-group">
              <label for="temperature">Temperatura (Criatividade)</label>
              <input
                type="range"
                id="temperature"
                v-model.number="form.configuration.temperature"
                min="0"
                max="1"
                step="0.1"
              />
              <span class="value-label">{{ form.configuration.temperature }}</span>
            </div>

            <div class="form-group">
              <label for="max_tokens">Máximo de Tokens</label>
              <input
                type="number"
                id="max_tokens"
                v-model.number="form.configuration.max_tokens"
                min="100"
                max="4000"
                step="100"
              />
            </div>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" @click="$emit('close')" class="btn-secondary">
            Cancelar
          </button>
          <button type="submit" class="btn-primary" :disabled="loading">
            {{ loading ? 'Salvando...' : (agent ? 'Atualizar' : 'Criar Agente') }}
          </button>
        </div>

        <div v-if="error" class="error-message">{{ error }}</div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { API_BASE } from '../config'

const props = defineProps({
  agent: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'saved'])

const providers = [
  { value: 'openai', name: 'OpenAI', icon: '🤖' },
  { value: 'gemini', name: 'Gemini', icon: '✨' }
]

const form = reactive({
  name: '',
  description: '',
  ai_provider: 'openai',
  ai_model: '',
  system_prompt: '',
  instructions: '',
  configuration: {
    temperature: 0.7,
    max_tokens: 1000
  }
})

const loading = ref(false)
const error = ref('')

// Populate form if editing
if (props.agent) {
  Object.assign(form, {
    ...props.agent,
    configuration: props.agent.configuration || { temperature: 0.7, max_tokens: 1000 }
  })
}

const getModelPlaceholder = (provider) => {
  const placeholders = {
    openai: 'Ex: gpt-3.5-turbo, gpt-4',
    gemini: 'Ex: gemini-2.0-flash-exp'
  }
  return placeholders[provider] || 'Nome do modelo'
}

const saveAgent = async () => {
  if (!form.name || !form.ai_provider) {
    error.value = 'Preencha os campos obrigatórios'
    return
  }

  loading.value = true
  error.value = ''

  try {
    const url = props.agent
      ? `${API_BASE}/agents/${props.agent.id}`
      : `${API_BASE}/agents`
    
    const method = props.agent ? 'PUT' : 'POST'

    console.log('Salvando agente:', { url, method, form })

    const res = await fetch(url, {
      method,
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json'
      },
      body: JSON.stringify(form)
    })

    const data = await res.json()
    console.log('Resposta do servidor:', { status: res.status, data })

    if (!res.ok) {
      throw new Error(data.error || data.message || 'Erro ao salvar agente')
    }

    emit('saved', data.agent)
    emit('close')
  } catch (err) {
    console.error('Erro ao salvar agente:', err)
    error.value = err.message
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.agent-form-modal {
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

.form-container {
  background: white;
  border-radius: 16px;
  padding: 32px;
  max-width: 800px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
}

.form-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 2px solid #e1e8ed;
}

.form-header h2 {
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
  font-size: 14px;
}

.form-group input[type="text"],
.form-group input[type="number"],
.form-group textarea {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e1e8ed;
  border-radius: 8px;
  font-size: 14px;
  font-family: inherit;
  transition: border-color 0.3s;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #667eea;
}

.form-group textarea {
  resize: vertical;
}

.form-group small {
  display: block;
  margin-top: 4px;
  color: #666;
  font-size: 12px;
}

.provider-options {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.provider-btn {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 16px;
  border: 2px solid #e1e8ed;
  border-radius: 12px;
  background: white;
  cursor: pointer;
  transition: all 0.3s;
  font-weight: 600;
  color: #666;
}

.provider-btn:hover {
  border-color: #667eea;
}

.provider-btn.active {
  border-color: #667eea;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.provider-btn .icon {
  font-size: 28px;
}

.advanced-section {
  margin-top: 32px;
  padding-top: 24px;
  border-top: 2px solid #e1e8ed;
}

.advanced-section h3 {
  color: #333;
  margin-bottom: 20px;
  font-size: 18px;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

input[type="range"] {
  width: 100%;
}

.value-label {
  display: inline-block;
  margin-left: 8px;
  font-weight: 600;
  color: #667eea;
}

.form-actions {
  display: flex;
  gap: 12px;
  margin-top: 32px;
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

.btn-secondary:hover {
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

@media (max-width: 768px) {
  .provider-options {
    grid-template-columns: 1fr;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
