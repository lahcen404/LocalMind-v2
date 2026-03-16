<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';

const router = useRouter();
const profile = ref(null);
const loading = ref(true);
const error = ref('');

const fetchProfile = async () => {
  loading.value = true;
  error.value = '';
  try {
    const response = await api.get('/profile');
    let data = response.data;
    if (typeof data === 'string') {
      const jsonStart = data.indexOf('{');
      if (jsonStart !== -1) {
        try {
          data = JSON.parse(data.substring(jsonStart));
        } catch (_) {}
      }
    }
    const payload = data?.data ?? data;
    if (payload && typeof payload === 'object' && ('name' in payload || 'email' in payload || 'id' in payload)) {
      profile.value = payload;
    } else {
      profile.value = payload || null;
      if (!profile.value) error.value = 'Invalid profile response.';
    }
  } catch (err) {
    if (err.response?.status === 401) {
      router.push('/login');
      return;
    }
    error.value = err.response?.data?.message || 'Failed to load profile.';
  } finally {
    loading.value = false;
  }
};

onMounted(fetchProfile);
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

    <div v-if="loading" class="space-y-6">
      <div class="h-48 bg-zinc-900/40 border border-zinc-800 rounded-2xl animate-pulse"></div>
      <div class="grid grid-cols-3 gap-4">
        <div v-for="i in 3" :key="i" class="h-24 bg-zinc-900/40 border border-zinc-800 rounded-xl animate-pulse"></div>
      </div>
    </div>

    <div v-else-if="error" class="p-6 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-300">
      {{ error }}
    </div>

    <div v-else-if="profile" class="space-y-8">
      <!-- Profile card -->
      <div class="bg-zinc-900 border border-zinc-800 p-8 rounded-2xl">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
          <div
            class="w-20 h-20 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-black text-2xl shrink-0"
          >
            {{ profile.initial }}
          </div>
          <div class="min-w-0">
            <h1 class="text-2xl font-black text-white italic tracking-tight">{{ profile.name }}</h1>
            <p class="text-zinc-400 text-sm mt-1">{{ profile.email }}</p>
            <div class="flex items-center gap-3 mt-3">
              <span
                class="text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-lg border"
                :class="profile.role === 'admin' ? 'bg-rose-500/10 border-rose-500/30 text-rose-400' : 'bg-zinc-800 border-zinc-700 text-zinc-400'"
              >
                {{ profile.role }}
              </span>
              <span class="text-zinc-600 text-[10px] font-bold uppercase tracking-widest">
                Joined {{ profile.joined_at }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <router-link
          to="/"
          class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6 hover:border-indigo-500/30 transition-all group"
        >
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-indigo-500/20 border border-indigo-500/30 flex items-center justify-center text-indigo-400 group-hover:scale-105 transition-transform">
              <i class="fa-regular fa-comments text-xl"></i>
            </div>
            <div>
              <p class="text-2xl font-black text-white">{{ profile.stats?.questions_count ?? 0 }}</p>
              <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest">Questions</p>
            </div>
          </div>
        </router-link>
        <router-link
          to="/"
          class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6 hover:border-indigo-500/30 transition-all group"
        >
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center text-amber-400 group-hover:scale-105 transition-transform">
              <i class="fa-solid fa-reply text-xl"></i>
            </div>
            <div>
              <p class="text-2xl font-black text-white">{{ profile.stats?.responses_count ?? 0 }}</p>
              <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest">Responses</p>
            </div>
          </div>
        </router-link>
        <router-link
          to="/favorites"
          class="bg-zinc-900/50 border border-zinc-800 rounded-xl p-6 hover:border-red-500/30 transition-all group"
        >
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-red-500/20 border border-red-500/30 flex items-center justify-center text-red-400 group-hover:scale-105 transition-transform">
              <i class="fa-solid fa-heart text-xl"></i>
            </div>
            <div>
              <p class="text-2xl font-black text-white">{{ profile.stats?.favorites_count ?? 0 }}</p>
              <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest">Favorites</p>
            </div>
          </div>
        </router-link>
      </div>

      <!-- Recent questions -->
      <section v-if="profile.recent_questions?.length" class="space-y-4">
        <h2 class="text-lg font-black text-white uppercase tracking-tight flex items-center gap-2">
          <i class="fa-solid fa-list text-indigo-500"></i>
          Recent questions
        </h2>
        <div class="space-y-3">
          <router-link
            v-for="q in profile.recent_questions"
            :key="q.id"
            :to="`/questions/${q.id}`"
            class="block bg-zinc-900/40 border border-zinc-800 p-4 rounded-xl hover:border-indigo-500/30 transition-all"
          >
            <p class="font-bold text-white line-clamp-1">{{ q.title }}</p>
            <p class="text-zinc-500 text-[10px] mt-1">{{ q.created_at }} · {{ q.metrics?.responses_count ?? 0 }} responses</p>
          </router-link>
        </div>
      </section>

      <div v-else class="text-center py-12 rounded-2xl border-2 border-dashed border-zinc-800">
        <i class="fa-regular fa-comments text-zinc-700 text-3xl mb-3"></i>
        <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest">No questions yet</p>
        <router-link to="/questions/create" class="inline-block mt-3 text-indigo-400 hover:text-indigo-300 text-[10px] font-bold uppercase">
          Ask your first question
        </router-link>
      </div>
    </div>
  </div>
</template>
