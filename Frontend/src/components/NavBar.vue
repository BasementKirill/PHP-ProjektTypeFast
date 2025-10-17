<template>
  <nav class="bar">
    <div class="left">
      <button :class="{active: tab==='test'}" @click="$emit('change-tab', 'test')">Test</button>
      <button :class="{active: tab==='features'}" @click="$emit('change-tab', 'features')">Features</button>
    </div>
    <div class="right">
      <span class="user">{{ username }}</span>
      <button class="logout" @click="$emit('logout')">Logout</button>
    </div>
  </nav>
</template>

<script setup>
defineProps({
  tab: { type: String, default: 'test' },
  username: { type: String, default: '' }
})
</script>

<style>
.bar { display:flex; align-items:center; justify-content:space-between; padding:10px 14px; background:#0f0f0f; border-bottom:1px solid #222; position:sticky; top:0; z-index:10; }
.left, .right { display:flex; gap:8px; align-items:center; }
button { padding:8px 10px; border-radius:8px; border:1px solid #333; background:#171717; color:#ddd; cursor:pointer; }
button.active { background:#2b7cff; color:#fff; border-color:#2b7cff; }
button.logout { background:#242424; }
.user { color:#9ad; font-size:14px; }
</style>
