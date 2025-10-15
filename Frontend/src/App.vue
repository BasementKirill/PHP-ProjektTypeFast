<template>
  <div>
    <h1>TypeFast</h1>
    <p>Status: {{ statusText }}</p>
    <LoginForm />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import LoginForm from './components/LoginForm.vue'
import api from './services/api.js'

const statusText = ref('checking...')

onMounted(async () => {
  try {
    const res = await api.me()
    statusText.value = res.data.status
  } catch (e) {
    statusText.value = 'unauthenticated'
  }
})
</script>

<style>
h1 { color: #4CAF50; font-family: Arial; }
</style>
  