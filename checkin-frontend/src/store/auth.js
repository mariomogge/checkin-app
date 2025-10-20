import axios from 'axios'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || '',
    userId: null,
    role: '',
  }),
  actions: {
    async login(name, password) {
      const res = await axios.post('http://localhost:8080/login', { name, password })
      this.token = res.data.token
      localStorage.setItem('token', this.token)

      // 🔍 Token decodieren, um userId und Rolle zu speichern
      const payload = JSON.parse(atob(this.token.split('.')[1]))
      this.userId = payload.sub || payload.userId
      this.role = payload.role

      axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
    },
    logout() {
      this.token = ''
      this.userId = null
      this.role = ''
      localStorage.removeItem('token')
      delete axios.defaults.headers.common['Authorization']
    },
  },
})
