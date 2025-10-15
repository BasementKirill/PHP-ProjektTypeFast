import axios from 'axios'

const api = axios.create({
  baseURL: '/api/',
  withCredentials: true
})

export default {
  login: (username, password) => api.post('auth.php?action=login', { username, password }),
  register: (username, password) => api.post('auth.php?action=register', { username, password }),
  me: () => api.get('auth.php?action=me'),
  logout: () => api.post('auth.php?action=logout')
}
