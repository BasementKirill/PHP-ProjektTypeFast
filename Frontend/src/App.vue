<template>
  <div>
    <NavBar v-if="auth.user" :tab="tab" :username="auth.user.username" @change-tab="setTab" @logout="doLogout" />

    <div v-if="!auth.user">
      <LoginForm @success="onLoginSuccess" />
    </div>

    <div v-else>
      <section v-if="tab==='test'" class="section"><Typingtest /></section>
      <section v-if="tab==='features'" class="section"><Leaderboard /></section>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import NavBar from './components/NavBar.vue'
import LoginForm from './components/LoginForm.vue'
import Typingtest from './components/Typingtest.vue'
import Leaderboard from './components/Leaderboard.vue'
import api from './services/api.js'

const auth = reactive({ user: null })
const tab = ref('test')

const onLoginSuccess = (user) => {
  auth.user = user
}

const setTab = (t) => { tab.value = t }

const doLogout = async () => {
  await api.logout()
  auth.user = null
}

onMounted(async () => {
  try {
    const res = await api.me()
    auth.user = res.data.user
  } catch (_) {
    auth.user = null
  }
})
</script>

<style>
.section { padding: 20px; color: #ddd; }
</style>
  