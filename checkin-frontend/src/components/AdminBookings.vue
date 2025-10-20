<template>
  <div>
    <h2>All Bookings</h2>
    <ul>
      <li v-for="b in bookings" :key="b.userId + b.deskId">
        User {{ b.userId }} → Desk {{ b.deskId }}<br />
        {{ b.start }} - {{ b.end || '...' }}<br />
        Checked out: {{ b.checkout || 'no' }}
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const bookings = ref([])

onMounted(async () => {
  const res = await axios.get('http://localhost:8080/bookings/all')
  bookings.value = res.data
})
</script>
