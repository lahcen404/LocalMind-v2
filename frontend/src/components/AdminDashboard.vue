<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';

const router = useRouter();
const stats = ref({
  users_count: 0,
  questions_count: 0,
  responses_count: 0,
});
const loading = ref(true);
const error = ref('');

const fetchStats = async () => {
  loading.value = true;
  error.value = '';
  try {
    const { data } = await api.get('/admin/stats');
    stats.value = data;
  } catch (err) {
    if (err.response?.status === 403) {
      router.push('/');
      return;
    }
    error.value = err.response?.data?.message || 'Failed to load stats.';
  } finally {
    loading.value = false;
  }
};

onMounted(fetchStats);
</script>

<template>
  <div class="w-full max-w-4xl mx-auto py-8 px-4">
    <button
      @click="router.push('/')"
      class="mb-8 flex items-center gap-2 text-zinc-500 hover:text-white transition-colors group"
    >
      <i class="fa-solid fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
      <span class="text-[10px] font-black uppercase tracking-widest">Back to Feed</span>
    </button>

    <!-- Header -->
    <div class="mb-10">
      <div class="flex items-center gap-3 mb-2">
        <span class="bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">
          <i class="fa-solid fa-shield-halved mr-1"></i> Admin
        </span>
      </div>
      <h1 class="text-3xl font-black text-white italic tracking-tight">Dashboard</h1>
      <p class="text-zinc-500 text-sm mt-1">Platform statistics</p>
    </div>

    <div v-if="error" class="p-6 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-300 text-sm">
      {{ error }}
    </div>

    <div v-else-if="loading" class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div v-for="i in 3" :key="i" class="h-32 bg-zinc-900/50 border border-zinc-800 rounded-2xl animate-pulse"></div>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <!-- Users -->
      <div class="bg-zinc-900/50 border border-zinc-800 rounded-2xl p-6 relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
          <i class="fa-solid fa-users text-6xl text-blue-500"></i>
        </div>
        <p class="text-zinc-400 text-xs font-bold uppercase tracking-widest">Total Users</p>
        <p class="text-3xl font-black text-white mt-2">{{ stats.users_count }}</p>
        <p class="text-emerald-400 text-xs mt-2 flex items-center gap-1">
          <i class="fa-solid fa-arrow-trend-up"></i> Registered
        </p>
      </div>

      <!-- Questions -->
      <div class="bg-zinc-900/50 border border-zinc-800 rounded-2xl p-6 relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
          <i class="fa-regular fa-comments text-6xl text-amber-500"></i>
        </div>
        <p class="text-zinc-400 text-xs font-bold uppercase tracking-widest">Questions</p>
        <p class="text-3xl font-black text-white mt-2">{{ stats.questions_count }}</p>
        <p class="text-zinc-500 text-xs mt-2">Total posted</p>
      </div>

      <!-- Responses -->
      <div class="bg-zinc-900/50 border border-zinc-800 rounded-2xl p-6 relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
          <i class="fa-solid fa-reply-all text-6xl text-purple-500"></i>
        </div>
        <p class="text-zinc-400 text-xs font-bold uppercase tracking-widest">Responses</p>
        <p class="text-3xl font-black text-white mt-2">{{ stats.responses_count }}</p>
        <p class="text-zinc-500 text-xs mt-2">Community engagement</p>
      </div>
    </div>
  </div>
</template>
