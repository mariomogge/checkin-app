<template>
  <div class="admin-view">
    <h2>Admin – Buchungsübersicht</h2>

    <div v-if="loading">Lade Buchungen...</div>
    <div v-else-if="bookings.length === 0">Keine Buchungen gefunden.</div>

    <table v-else>
      <thead>
        <tr>
          <th>User ID</th>
          <th>Desk ID</th>
          <th>Start</th>
          <th>Ende</th>
          <th>Checkout</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="b in bookings" :key="b.id">
          <td>{{ b.userId }}</td>
          <td>{{ b.deskId }}</td>
          <td>{{ b.start }}</td>
          <td>{{ b.end || '–' }}</td>
          <td>{{ b.checkout || '–' }}</td>
        </tr>
      </tbody>
    </table>

    <p v-if="error" class="error">{{ error }}</p>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../../store/auth'

const auth = useAuthStore()
const bookings = ref([])
const loading = ref(true)
const error = ref('')

const fetchBookings = async () => {
  if (auth.role !== 'admin') {
    error.value = 'Zugriff verweigert'
    loading.value = false
    return
  }

  try {
    const res = await axios.get('http://localhost:8080/admin/bookings')
    bookings.value = res.data
  } catch (e) {
    error.value = e.response?.data?.error || 'Fehler beim Laden'
  } finally {
    loading.value = false
  }
}

onMounted(fetchBookings)
</script>

<style scoped>
.admin-view {
  padding: 1rem;
  color: #fff;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
  background: #222;
  color: #fff;
}

th,
td {
  padding: 0.5rem;
  border: 1px solid #444;
  text-align: left;
}

th {
  background: #333;
}

.error {
  color: #cd5c5c;
  margin-top: 1rem;
}
</style>
