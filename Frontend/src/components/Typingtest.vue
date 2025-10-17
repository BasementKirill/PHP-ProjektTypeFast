<template>
  <div class="wrap">
    <h2>Typing Test (30s)</h2>
    <p class="prompt">{{ sample }}</p>

    <textarea v-model="input" :disabled="done" @input="onInput" placeholder="Tippe hier..." />

    <div class="info">
      <span>Zeit: {{ timeLeft }}s</span>
      <span>WPM: {{ wpm }}</span>
      <span>Accuracy: {{ accuracy }}%</span>
    </div>

    <div class="actions">
      <button @click="start" :disabled="running">Start</button>
      <button @click="reset">Reset</button>
      <button @click="save" :disabled="!done || saving">Speichern</button>
    </div>

    <p v-if="msg" class="msg">{{ msg }}</p>
  </div>
</template>

<script setup>
import { ref, computed, onUnmounted } from 'vue'
import api from '../services/api.js'

const sample = 'The quick brown fox jumps over the lazy dog'
const input = ref('')
const timeLeft = ref(30)
const running = ref(false)
const done = ref(false)
const timer = ref(null)
const msg = ref('')
const saving = ref(false)

const wordsTyped = computed(() => input.value.trim().split(/\s+/).filter(Boolean).length)
const wpm = computed(() => {
  const used = 30 - timeLeft.value
  if (used <= 0) return 0
  return Math.round((wordsTyped.value / used) * 60)
})
const accuracy = computed(() => {
  const length = Math.max(sample.length, 1)
  let correct = 0
  for (let i = 0; i < Math.min(input.value.length, sample.length); i++) {
    if (input.value[i] === sample[i]) correct++
  }
  return Math.round((correct / length) * 100)
})

function onInput() {
  if (!running.value) start()
}

function start() {
  if (running.value) return
  running.value = true
  done.value = false
  msg.value = ''
  timer.value = setInterval(() => {
    timeLeft.value--
    if (timeLeft.value <= 0) finish()
  }, 1000)
}

function finish() {
  clearInterval(timer.value)
  timer.value = null
  running.value = false
  done.value = true
}

function reset() {
  clearInterval(timer.value)
  timer.value = null
  input.value = ''
  timeLeft.value = 30
  running.value = false
  done.value = false
  msg.value = ''
}

async function save() {
  saving.value = true
  msg.value = '...'
  try {
    const res = await api.saveResult(wpm.value, accuracy.value)
    msg.value = res.data.status
  } catch (e) {
    msg.value = e?.response?.data?.message || 'Speichern fehlgeschlagen'
  } finally {
    saving.value = false
  }
}

onUnmounted(() => clearInterval(timer.value))
</script>

<style>
.wrap { max-width: 720px; margin: 20px auto; color: #ddd; }
.prompt { background:#111; padding:10px; border-radius:8px; border:1px solid #222; }
textarea { width:100%; height:120px; margin:10px 0; padding:10px; border-radius:8px; background:#0f0f0f; color:#eaeaea; border:1px solid #333; }
.info { display:flex; gap:14px; margin:8px 0; }
.actions { display:flex; gap:8px; }
button { padding:8px 12px; border-radius:8px; border:1px solid #333; background:#171717; color:#ddd; cursor:pointer; }
.msg { margin-top: 10px; color:#9ad; }
</style>
