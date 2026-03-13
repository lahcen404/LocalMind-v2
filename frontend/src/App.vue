<script setup>
import { onMounted, ref } from 'vue'
import Login from '@/components/Login.vue'
import QuestionFeed from '@/components/QuestionFeed.vue' // 1. Import your new component

const user = ref(null)

onMounted(() => {
  const savedUser = localStorage.getItem('lm_user')

  // validatioon
  if(savedUser && savedUser !== "undefined" && savedUser !== "null") {
    try {
      user.value = JSON.parse(savedUser)
    } catch (e) {
      console.error("Session corrupted:", e)
      localStorage.removeItem('lm_user')
    }
  }
})


const onLoginSuccess = (userData) => {
  console.log(" App.vue: Identity Verified!", userData.name)
  user.value = userData
}

// logout
const logout = () => {
  localStorage.removeItem('lm_token')
  localStorage.removeItem('lm_user')
  user.value = null
  console.log("🔌 Terminal Session Terminated.")
}
</script>

<template>
  <main class="min-h-screen bg-zinc-950 text-white font-sans selection:bg-indigo-500/30">
    
    <!-- NAVIGATION BAR (Visible only when logged in) -->
    <nav v-if="user" class="border-b border-zinc-900 bg-zinc-950/80 backdrop-blur-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-2 group cursor-pointer">
                <i class="fa-solid fa-brain text-indigo-500 text-xl group-hover:rotate-12 transition-transform"></i>
                <span class="font-black italic uppercase tracking-tighter text-lg">LocalMind</span>
            </div>

            <!-- User Meta & Logout -->
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-3">
                    <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest hidden sm:block">
                        Agent: {{ user.name }}
                    </span>
                    <button @click="logout" 
                        class="w-8 h-8 rounded-lg bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-500 hover:text-red-400 hover:border-red-500/30 transition-all active:scale-95"
                        title="Logout">
                        <i class="fa-solid fa-power-off text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENT  -->
    <div class="container mx-auto">
        <!-- 2. Show Login if user is null -->
        <div v-if="!user" class="min-h-[90vh] flex items-center justify-center p-6">
            <Login @login-success="onLoginSuccess" />
        </div>

        <!-- Questioons -->
        <div v-else class="animate-in fade-in duration-1000">
            <QuestionFeed />
        </div>
    </div>

  </main>
</template>

<style>
::-webkit-scrollbar { width: 8px; }
::-webkit-scrollbar-track { background: #09090b; }
::-webkit-scrollbar-thumb { background: #27272a; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #3f3f46; }
</style>