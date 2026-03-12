<script setup>
import { onMounted, ref } from 'vue'
import Login from '@/components/Login.vue'

const user = ref(null)

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

const logout = () => {
  localStorage.removeItem('lm_token')
  localStorage.removeItem('lm_user')
  user.value = null
}
</script>

<template>
  <main class="min-h-screen bg-zinc-950 text-white flex flex-col items-center justify-center p-6 font-sans">
    
    <!-- if user is not logged in -->
    <Login v-if="!user" @login-success="onLoginSuccess" />

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
</template>

<style scoped>
h1 {
  background: linear-gradient(to bottom right, #ffffff, #71717a);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
</style>