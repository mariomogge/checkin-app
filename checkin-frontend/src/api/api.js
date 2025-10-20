import axios from 'axios'
import { useAuthStore } from '../store/auth'

const instance = axios.create({
  baseURL: 'http://localhost:8080',
  headers: {
    'Content-Type': 'application/json',
  }
})

instance.interceptors.request.use(config => {
  const auth = useAuthStore()
  if (auth.token) {
    config.headers.Authorization = `Bearer ${auth.token}`
  }
  return config
})

export default {
  login: (name, password) => instance.post('/login', { name, password }),
  checkIn: userId => instance.post('/checkin', { userId }),
  checkOut: userId => instance.post('/checkout', { userId }),
  book: (userId, deskId) => instance.post('/book', { userId, deskId }),
  getDesks: () => instance.get('/desks'),
  getBookings: () => instance.get('/admin/bookings'),
  getActiveBooking: userId => instance.get(`/booking/active/${userId}`)
}
