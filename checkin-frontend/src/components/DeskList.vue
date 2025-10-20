<template>
  <div>
    <h2>Available Desks</h2>
    <ul>
      <li v-for="desk in desks" :key="desk.id">
        {{ desk.location }}
        <button @click="selectDesk(desk)">Book</button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const desks = ref([])

onMounted(async () => {
  const res = await axios.get('http://localhost:8080/desks')
  desks.value = res.data
})

const selectDesk = (desk) => {
  alert(`Selected Desk ID ${desk.id}`) // später Übergabe an BookingForm
}
</script>
