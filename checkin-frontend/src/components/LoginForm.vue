<template>
  <form @submit.prevent="login">
    <input v-model="name" placeholder="Name" />
    <input v-model="password" type="password" placeholder="Password" />
    <button>Login</button>
  </form>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../store/auth'
import api from '../api/api'
import { useRouter } from 'vue-router'

const name = ref('')
const password = ref('')
const auth = useAuthStore()
const router = useRouter()

const login = async () => {
  try {
    const response = await api.login(name.value, password.value)
    auth.setToken(response.data.token)
    router.push('/dashboard')
  } catch (e) {
    e.value = 'Login fehlgeschlagen'
    console.error(e)
  }
}
</script>
