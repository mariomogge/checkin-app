<template>
  <div class="booking-form">
    <h3>Arbeitsplatz buchen</h3>

    <div v-if="loading">Lade verfügbare Plätze...</div>

    <div v-else>
      <label for="desk-select">Platz auswählen:</label>
      <select v-model="selectedDeskId" id="desk-select">
        <option disabled value="">-- Bitte auswählen --</option>
        <option v-for="desk in desks" :key="desk.id" :value="desk.id">
          {{ desk.location }}
        </option>
      </select>

      <button @click="bookDesk" :disabled="!selectedDeskId">Buchen</button>
    </div>

    <p v-if="message" :class="{ error: isError, success: !isError }">
      {{ message }}
    </p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../store/auth'

const desks = ref([])
const selectedDeskId = ref('')
const loading = ref(false)
const message = ref('')
const isError = ref(false)

const auth = useAuthStore()

const fetchDesks = async () => {
  loading.value = true
  try {
    const res = await axios.get('http://localhost:8080/desks/available')
    desks.value = res.data
  } catch (e) {
    message.value = 'Fehler beim Laden der Plätze.'
    isError.value = true
  } finally {
    loading.value = false
  }
}

const bookDesk = async () => {
  message.value = ''
  isError.value = false
  try {
    await axios.post('http://localhost:8080/book', {
      userId: auth.userId,
      deskId: selectedDeskId.value
    })
    message.value = 'Platz erfolgreich gebucht!'
  } catch (e) {
    isError.value = true
    message.value = e.response?.data?.error || 'Buchung fehlgeschlagen'
  }
}

onMounted(fetchDesks)
</script>

<style scoped>
.booking-form {
  padding: 1rem;
  background: #1f1f1f;
  color: #fff;
  border-radius: 6px;
}

select,
button {
  margin-top: 0.5rem;
  padding: 0.4rem 0.6rem;
  border-radius: 4px;
  border: none;
  font-size: 1rem;
}

select {
  background-color: #333;
  color: #fff;
}

button {
  background-color: #cbff00;
  color: #000;
  cursor: pointer;
  margin-left: 0.5rem;
}

button:disabled {
  background-color: #888;
  cursor: not-allowed;
}

.success {
  color: #cbff00;
  margin-top: 0.5rem;
}

.error {
  color: #cd5c5c;
  margin-top: 0.5rem;
}
</style>
