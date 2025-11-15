import axios from 'axios'
import { API_BASE } from '../config'

const API_URL = API_BASE

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Add token to requests if available
api.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export const login = async (credentials) => {
  const response = await api.post('/login', credentials)
  return response.data
}

export const register = async (userData) => {
  const response = await api.post('/register', userData)
  return response.data
}

export const logout = async () => {
  try {
    await api.post('/logout')
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }
}

export const getCurrentUser = async () => {
  const response = await api.get('/me')
  return response.data
}

export default api
