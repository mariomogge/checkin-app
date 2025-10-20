<template>
  <div>
    <select v-model="selectedDeskId">
      <option disabled value="">Wähle Arbeitsplatz</option>
      <option v-for="desk in desks" :key="desk.id" :value="desk.id">
        {{ desk.location }}
      </option>
    </select>

    <button @click="bookDesk">Buchen</button>
    <p v-if="error" class="error">{{ error }}</p>
    <p v-if="success" class="success">{{ success }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../store/auth'
import api from '../api/api'

const auth = useAuthStore()
const desks = ref([])
const selectedDeskId = ref('')
const error = ref('')
const success = ref('')

const loadDesks = async () => {
  try {
    const res = await api.getDesks()
    desks.value = res.data
  } catch (e) {
    error.value = 'Fehler beim Laden der Arbeitsplätze'
  }
}

const bookDesk = async () => {
  error.value = ''
  success.value = ''
  try {
    await api.book(auth.userId, selectedDeskId.value)
    success.value = 'Buchung erfolgreich!'
  } catch (e) {
    error.value = e.response?.data?.error || 'Buchung fehlgeschlagen'
  }
}

onMounted(loadDesks)
</script>

<style scoped>
.error {
  color: #cd5c5c;
}
.success {
  color: #cbff00;
}
</style>
