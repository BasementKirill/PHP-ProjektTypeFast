<template>
  <div class="card">
    <div class="tabs">
      <button :class="{active: mode==='login'}" @click="mode='login'">Login</button>
      <button :class="{active: mode==='register'}" @click="mode='register'">Registrieren</button>
    </div>

    <form @submit.prevent="submit">
      <input v-model="username" type="text" placeholder="Benutzername" required />
      <input v-model="password" type="password" placeholder="Passwort" required />
      <button type="submit">{{ mode==='login' ? 'Login' : 'Konto erstellen' }}</button>
    </form>

    <p v-if="message" class="msg">{{ message }}</p>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import api from '../services/api.js'

const mode = ref('login')
const username = ref('')
const password = ref('')
const message = ref('')

const submit = async () => {
  message.value = '...'
  try {
    const fn = mode.value === 'login' ? api.login : api.register
    const res = await fn(username.value, password.value)
    message.value = res.data.status || 'ok'
  } catch (e) {
    message.value = e?.response?.data?.message || 'Fehlgeschlagen'
  }
}
</script>

<style>
.card { max-width: 360px; margin: 24px auto; padding: 20px; border-radius: 12px; box-shadow: 0 6px 20px rgba(0,0,0,.12); background: #111; color: #eaeaea; }
.tabs { display: flex; gap: 8px; margin-bottom: 12px; }
.tabs button { flex: 1; padding: 10px; border-radius: 8px; border: 1px solid #333; background: #1a1a1a; color: #bbb; cursor: pointer; }
.tabs button.active { background: #2b7cff; color: #fff; border-color: #2b7cff; }
form { display: grid; gap: 10px; }
input { padding: 10px 12px; border-radius: 8px; border: 1px solid #333; background: #0f0f0f; color: #eaeaea; }
button[type="submit"] { padding: 10px 12px; border-radius: 8px; border: none; background: #2b7cff; color: #fff; cursor: pointer; }
.msg { margin-top: 10px; color: #9ad; }
</style>
  