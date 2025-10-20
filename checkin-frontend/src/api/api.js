import axios from 'axios'

const instance = axios.create({
  baseURL: 'http://localhost:8080',
  headers: {
    'Content-Type': 'application/json',
  }
})

instance.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default {
  login: (name, password) => instance.post('/login', { name, password }),
  checkIn: userId => instance.post('/check-in', { userId }),
  checkOut: userId => instance.post('/check-out', { userId }),
  book: (userId, deskId) => instance.post('/book', { userId, deskId }),
  getDesks: () => instance.get('/desks'),
  getUser: (id) => instance.get(`/user/${id}`),
  getAdminBookings: () => instance.get('/admin/bookings'),
  getActiveBooking: userId => instance.get(`/bookings/active/${userId}`)
}
