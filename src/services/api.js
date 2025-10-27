import axios from 'axios'

const api = axios.create({
  baseURL: '/api/',
  withCredentials: true
})

export default {
  login: (username, password) => api.post('auth.php?action=login', { username, password }),
  register: (username, password) => api.post('auth.php?action=register', { username, password }),
  me: () => api.get('auth.php?action=me'),
  logout: () => api.post('auth.php?action=logout'),
  saveResult: (wpm, accuracy) => api.post('results.php', { wpm, accuracy }),
  myResults: () => api.get('results.php'),
  leaderboard: (limit = 10) => api.get(`leaderboard.php?limit=${limit}`)
}
