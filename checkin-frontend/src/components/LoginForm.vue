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
import { useRouter } from 'vue-router'

const name = ref('')
const password = ref('')
const auth = useAuthStore()
const router = useRouter()

const login = async () => {
  try {
    await auth.login(name.value, password.value)
    router.push(auth.role === 'admin' ? '/admin' : '/dashboard')
  } catch (e) {
    alert('Login failed')
  }
}
</script>
