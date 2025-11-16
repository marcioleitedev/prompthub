import { ref, computed } from 'vue'

const translations = {
  pt: {
    footer: {
      developedBy: 'Desenvolvido por'
    },
    nav: {
      logout: 'Sair'
    },
    home: {
      title: 'PromptHub',
      welcome: 'Bem-vindo ao PromptHub!',
      subtitle: 'Ola, {name}! Voce esta autenticado com sucesso.',
      loading: 'Carregando...',
      tryAssistant: 'Experimentar Assistente de IA'
    },
    login: {
      title: 'Login',
      createAccount: 'Criar Conta',
      brandName: 'PromptHub',
      brandTagline: 'Hub de Assistentes Inteligentes',
      email: 'Email',
      emailPlaceholder: 'seu@email.com',
      password: 'Senha',
      passwordPlaceholder: '********',
      confirmPassword: 'Confirmar Senha',
      name: 'Nome',
      namePlaceholder: 'Seu nome completo',
      enterButton: 'Entrar',
      registerButton: 'Cadastrar',
      entering: 'Entrando...',
      registering: 'Cadastrando...',
      or: 'ou',
      continueGoogle: 'Continuar com Google',
      registerGoogle: 'Cadastrar com Google',
      noAccount: 'Nao tem uma conta?',
      hasAccount: 'Ja tem uma conta?',
      signUp: 'Cadastre-se',
      signIn: 'Faca login',
      errorGoogle: 'Erro ao conectar com Google',
      passwordMismatch: 'As senhas nao coincidem'
    }
  },
  en: {
    footer: {
      developedBy: 'Developed by'
    },
    nav: {
      logout: 'Logout'
    },
    home: {
      title: 'PromptHub',
      welcome: 'Welcome to PromptHub!',
      subtitle: 'Hello, {name}! You are successfully authenticated.',
      loading: 'Loading...',
      tryAssistant: 'Try AI Assistant'
    },
    login: {
      title: 'Login',
      createAccount: 'Create Account',
      brandName: 'PromptHub',
      brandTagline: 'Intelligent Assistants Hub',
      email: 'Email',
      emailPlaceholder: 'your@email.com',
      password: 'Password',
      passwordPlaceholder: '********',
      confirmPassword: 'Confirm Password',
      name: 'Name',
      namePlaceholder: 'Your full name',
      enterButton: 'Sign In',
      registerButton: 'Register',
      entering: 'Signing in...',
      registering: 'Registering...',
      or: 'or',
      continueGoogle: 'Continue with Google',
      registerGoogle: 'Sign up with Google',
      noAccount: "Don't have an account?",
      hasAccount: 'Already have an account?',
      signUp: 'Sign up',
      signIn: 'Sign in',
      errorGoogle: 'Error connecting to Google',
      passwordMismatch: 'Passwords do not match'
    }
  }
}

let savedLang = 'pt'
try {
  const stored = localStorage.getItem('lang')
  if (stored === 'pt' || stored === 'en') {
    savedLang = stored
  }
} catch (e) {
  console.warn('LocalStorage not available')
}

const currentLang = ref(savedLang)

export function useLanguage() {
  const t = (key, params = {}) => {
    const keys = key.split('.')
    let value = translations[currentLang.value]
    
    for (const k of keys) {
      if (value && typeof value === 'object') {
        value = value[k]
      } else {
        return key
      }
    }
    
    if (typeof value === 'string') {
      return value.replace(/\{(\w+)\}/g, (match, param) => params[param] || match)
    }
    
    return key
  }

  const setLanguage = (lang) => {
    if (lang === 'pt' || lang === 'en') {
      currentLang.value = lang
      try {
        localStorage.setItem('lang', lang)
      } catch (e) {
        console.warn('Cannot save to localStorage')
      }
    }
  }

  const locale = computed({
    get: () => currentLang.value,
    set: (val) => setLanguage(val)
  })

  return {
    t,
    locale,
    setLanguage
  }
}
