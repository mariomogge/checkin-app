import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    token: localStorage.getItem('token') || '',
    role: '',
    userId: null,
  }),
  actions: {
    setToken(token) {
      this.token = token
      localStorage.setItem('token', token)

      const payload = JSON.parse(atob(token.split('.')[1]))
      this.userId = payload.sub
      this.role = payload.role
    },
    logout() {
      this.token = ''
      this.userId = null
      this.role = ''
      localStorage.removeItem('token')
    }
  },
})
