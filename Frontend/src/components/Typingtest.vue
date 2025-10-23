<template>
  <div class="wrap">
    <h2>Typing Test (30s)</h2>
    <p class="prompt">{{ sample }}</p>

    <textarea v-model="input" :disabled="done" @input="onInput" placeholder="Tippe hier..." />

    <div class="info">
      <span>Zeit: {{ timeLeft }}s</span>
      <span>WPM: {{ shownWpm }}</span>
      <span>Accuracy: {{ shownAccuracy }}%</span>
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
const liveWpm = computed(() => {
  const used = 30 - timeLeft.value
  if (used <= 0) return 0
  return Math.round((wordsTyped.value / used) * 60)
})
const liveAccuracy = computed(() => {
  const length = Math.max(sample.length, 1)
  let correct = 0
  for (let i = 0; i < Math.min(input.value.length, sample.length); i++) {
    if (input.value[i] === sample[i]) correct++
  }
  return Math.round((correct / length) * 100)
})

const finalWpm = ref(0)
const finalAccuracy = ref(0)
const shownWpm = computed(() => done.value ? finalWpm.value : liveWpm.value)
const shownAccuracy = computed(() => done.value ? finalAccuracy.value : liveAccuracy.value)

function onInput() {
  if (!running.value && !done.value) start()
  // Wenn der gesamte Text exakt (bis auf trailing Spaces) getippt wurde → beenden
  if (input.value.trimEnd() === sample) finish()
}

function start() {
  if (running.value || done.value) return
  running.value = true
  msg.value = ''
  timer.value = setInterval(() => {
    timeLeft.value--
    if (timeLeft.value <= 0) finish()
  }, 1000)
}

function finish() {
  if (done.value) return
  clearInterval(timer.value)
  timer.value = null
  running.value = false
  done.value = true
  finalWpm.value = liveWpm.value
  finalAccuracy.value = liveAccuracy.value
}

function reset() {
  clearInterval(timer.value)
  timer.value = null
  input.value = ''
  timeLeft.value = 30
  running.value = false
  done.value = false
  msg.value = ''
  finalWpm.value = 0
  finalAccuracy.value = 0
}

async function save() {
  if (!done.value) return
  saving.value = true
  msg.value = '...'
  try {
    const res = await api.saveResult(finalWpm.value, finalAccuracy.value)
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
