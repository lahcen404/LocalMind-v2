<script setup>
import { onMounted, ref } from 'vue'
import Login from '@/components/Login.vue'
import Register from '@/components/Register.vue'

const user = ref(null)
const authMode = ref('login')
const currentYear = new Date().getFullYear()

onMounted(() => {
  const savedUser = localStorage.getItem('lm_user')

  // verify that savedUser is not null, undefined, or the string "null" before parsing
  if(savedUser && savedUser !== "undefined" && savedUser !== "null") {
    try {
      user.value = JSON.parse(savedUser)
    } catch (e) {
      console.error("Erreur de session corrompue :", e)
      localStorage.removeItem('lm_user')
    }
  }
})


const onLoginSuccess = (userData) => {
  console.log("connected :", userData)
  
  if (userData) {
    user.value = userData
  } else {
    console.error("error : data is null")
  }
}

const switchToRegister = () => {
  authMode.value = 'register'
}

const switchToLogin = () => {
  authMode.value = 'login'
}

const logout = () => {
  localStorage.removeItem('lm_token')
  localStorage.removeItem('lm_user')
  user.value = null
}
</script>

<template>
  <div class="min-h-screen bg-zinc-950 text-white flex flex-col font-sans">
    <header class="border-b border-zinc-800/80 bg-zinc-950/90 backdrop-blur">
      <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 bg-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-500/20">
            <i class="fa-solid fa-brain text-white text-lg"></i>
          </div>
          <div>
            <p class="text-white font-black italic uppercase tracking-tight leading-none">LocalMind</p>
            <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-[0.35em] mt-1">Community Intelligence Network</p>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <template v-if="!user">
            <button
              @click="switchToLogin"
              :class="authMode === 'login' ? 'bg-indigo-600 text-white border-indigo-500/40' : 'bg-zinc-900 text-zinc-400 border-zinc-800'"
              class="px-4 py-2 rounded-full border text-[10px] font-black uppercase tracking-widest transition-all"
            >
              Login
            </button>
            <button
              @click="switchToRegister"
              :class="authMode === 'register' ? 'bg-emerald-600 text-white border-emerald-500/40' : 'bg-zinc-900 text-zinc-400 border-zinc-800'"
              class="px-4 py-2 rounded-full border text-[10px] font-black uppercase tracking-widest transition-all"
            >
              Register
            </button>
          </template>

          <div v-else class="text-right">
            <p class="text-white text-sm font-bold">{{ user.name }}</p>
            <p class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.3em]">{{ user.role }}</p>
          </div>
        </div>
      </div>
    </header>

    <main class="flex-1 flex items-center justify-center px-6 py-10">
      <!-- if user is not logged in -->
      <Login
        v-if="!user && authMode === 'login'"
        @login-success="onLoginSuccess"
        @switch-to-register="switchToRegister"
      />
      <Register
        v-else-if="!user"
        @register-success="onLoginSuccess"
        @switch-to-login="switchToLogin"
      />

      <!-- if user is logged in -->
      <div v-else class="text-center animate-in fade-in zoom-in duration-500">
        
        <!-- User Avatar Icon -->
        <div class="w-24 h-24 bg-indigo-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-2xl border border-indigo-400/30 rotate-3 transition-transform hover:rotate-0">
          <span class="text-4xl font-black text-white">
            {{ user.name ? user.name.charAt(0).toUpperCase() : '?' }}
          </span>
        </div>
        
        <h1 class="text-4xl font-black italic uppercase mb-2 tracking-tighter">
          Welcome, {{ user.name }}
        </h1>
        
        <p class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.4em] mb-10">
          LocalMind Identity: {{ user.role }}
        </p>
        
        <!-- Logout Button -->
        <button @click="logout" 
          class="text-zinc-500 hover:text-red-400 text-[10px] font-black uppercase tracking-widest border border-zinc-800 px-8 py-3 rounded-full transition-all hover:bg-red-500/10 hover:border-red-500/30 active:scale-95">
          Log Out
        </button>
      </div>
    </main>

    <footer class="border-t border-zinc-800/80">
      <div class="max-w-6xl mx-auto px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-3 text-center md:text-left">
        <p class="text-zinc-500 text-[10px] font-black uppercase tracking-[0.35em]">
          LocalMind Platform
        </p>
        <p class="text-zinc-600 text-[10px] font-bold uppercase tracking-[0.3em]">
          Secure Local Help • {{ currentYear }}
        </p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
h1 {
  background: linear-gradient(to bottom right, #ffffff, #71717a);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
</style>