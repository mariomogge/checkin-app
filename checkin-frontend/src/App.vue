<template>
  <div id="app">
    <header v-if="auth.token">
      <nav >
        <router-link to="/dashboard">Dashboard</router-link>
        <router-link v-if="auth.role === 'admin'" to="/admin">Admin</router-link>
        <button @click="logout">Logout</button>
      </nav>
    </header>

    <main>
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useAuthStore } from './store/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

const logout = () => {
  auth.logout()
  router.push('/login')
}
</script>

<style scoped>
header {
  background-color: #eee;
  padding: 1rem;
  margin-bottom: 1rem;
}

nav {
  display: flex;
  gap: 1rem;
  align-items: center;
  background-color: #222;
}

button {
  background: transparent;
  border: none;
  color: var(--highlight);
  cursor: pointer;
}
</style>
