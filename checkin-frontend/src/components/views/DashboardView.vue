<template>
  <main>
    <h1>Dashboard</h1>

    <section>
      <p>Status: <strong>{{ checkedIn ? 'Eingecheckt' : 'Ausgecheckt' }}</strong></p>

      <button v-if="!checkedIn" @click="checkIn">Check-In</button>
      <button v-else @click="checkOut">Check-Out</button>
    </section>

    <section v-if="checkedIn">
      <DeskList />
      <h2>Arbeitsplatz buchen</h2>
      <BookingForm />
    </section>

    <section v-if="booking">
      <h2>Aktive Buchung</h2>
      <p><strong>Desk:</strong> {{ booking.deskId }}</p>
      <p><strong>Start:</strong> {{ booking.startTime }}</p>
      <p><strong>Ende:</strong> {{ booking.endTime || 'Offen' }}</p>
      <p><strong>Checkout:</strong> {{ booking.checkoutTime || 'Noch aktiv' }}</p>
    </section>
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../../store/auth'
import BookingForm from '../BookingForm.vue'
import DeskList from '../DeskList.vue'

const auth = useAuthStore()
const checkedIn = ref(false)
const booking = ref(null)

const fetchUserStatus = async () => {
  try {
    const res = await axios.get(`http://localhost:8080/user/${auth.userId}`)
    checkedIn.value = res.data.checked_in === 1
  } catch (e) {
    console.error('Status-Fehler', e)
  }

  try {
    const res = await axios.get(`http://localhost:8080/bookings/active/${auth.userId}`)
    booking.value = res.data
  } catch (e) {
    console.log('Keine aktive Buchung')
  }
}

const checkIn = async () => {
  try {
    await axios.post('http://localhost:8080/check-in', {
      userId: auth.userId
    })
    checkedIn.value = true
    fetchUserStatus()
  } catch (e) {
    const msg = e.response?.data?.error || e.message
    if (msg === 'User already check in') {
      checkedIn.value = true
    }
    alert(msg)
  }
}

const checkOut = async () => {
  try {
    await axios.post('http://localhost:8080/check-out', {
      userId: auth.userId
    })
    checkedIn.value = false
    fetchUserStatus()
  } catch (e) {
    alert(e.response?.data?.error || 'Check-Out fehlgeschlagen')
  }
}

onMounted(fetchUserStatus)
</script>
