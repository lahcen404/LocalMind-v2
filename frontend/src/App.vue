<script setup>
import { ref } from 'vue'
const status = ref("Terminal Active")
</script>

<template>
  <main class="min-h-screen bg-zinc-950 text-white flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-zinc-900 border border-zinc-800 p-8 rounded-3xl shadow-2xl text-center">
      <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-indigo-500/20 shadow-xl rotate-3">
         <i class="fa-solid fa-brain text-white text-3xl"></i>
      </div>
      <h1 class="text-3xl font-black tracking-tighter mb-2 italic uppercase">LocalMind</h1>
      <p class="text-zinc-500 text-xs uppercase tracking-widest font-bold mb-8">Intelligence Network</p>
      <div class="flex items-center justify-center gap-2 px-4 py-2 bg-zinc-950 rounded-full border border-zinc-800 w-fit mx-auto">
        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
        <span class="text-xs font-mono text-emerald-500 uppercase">{{ status }}</span>
      </div>
    </div>
  </main>
</template>
