<template>
  <div class="wrap">
    <h2>Leaderboard</h2>
    <button @click="load" :disabled="loading">Neu laden</button>
    <table v-if="items.length">
      <thead>
        <tr><th>User</th><th>WPM</th><th>Accuracy</th><th>Datum</th></tr>
      </thead>
      <tbody>
        <tr v-for="it in items" :key="it.id">
          <td>{{ it.username }}</td>
          <td>{{ it.wpm }}</td>
          <td>{{ it.accuracy }}%</td>
          <td>{{ new Date(it.created_at).toLocaleString() }}</td>
        </tr>
      </tbody>
    </table>
    <p v-else class="msg">Keine Einträge</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api.js'

const items = ref([])
const loading = ref(false)

async function load() {
  loading.value = true
  try {
    const res = await api.leaderboard(10)
    items.value = res.data.items || []
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<style>
.wrap { max-width: 720px; margin: 20px auto; color:#ddd; }
table { width:100%; border-collapse: collapse; margin-top:10px; }
th, td { padding:8px; border-bottom:1px solid #222; text-align:left; }
.msg { color:#9ad; }
</style>
