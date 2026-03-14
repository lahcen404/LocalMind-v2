<script setup>
import { onMounted, ref, provide } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const user = ref(null)

onMounted(() => {
  const savedUser = localStorage.getItem('lm_user')
  if (savedUser && savedUser !== 'undefined' && savedUser !== 'null') {
    try {
      user.value = JSON.parse(savedUser)
    } catch (e) {
      console.error('Session corrompue:', e)
      localStorage.removeItem('lm_user')
    }
  }
})

const onAuthSuccess = (userData) => {
  user.value = userData
  router.push('/')
}

// get user and auth success handler available to all child components
provide('user', user)
provide('onAuthSuccess', onAuthSuccess)

const logout = () => {
  localStorage.removeItem('lm_token')
  localStorage.removeItem('lm_user')
  user.value = null
  router.push('/login')
}

const goToFeed = () => router.push('/')
</script>

<template>
  <main class="min-h-screen bg-zinc-950 text-white font-sans selection:bg-indigo-500/30 flex flex-col">
    
    <!-- barre de navigation -->
    <nav class="border-b border-zinc-900 bg-zinc-950/80 backdrop-blur-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <!-- Logo -->
            <div @click="goToFeed" class="flex items-center gap-2 group cursor-pointer">
                <i class="fa-solid fa-brain text-indigo-500 text-xl group-hover:rotate-12 transition-transform"></i>
                <span class="font-black italic uppercase tracking-tighter text-lg">LocalMind</span>
            </div>

            <!-- naav -->
            <div class="flex items-center gap-6">
                <!--  -->
                <template v-if="user">
                    <router-link
                        to="/favorites"
                        class="flex items-center gap-2 text-zinc-500 hover:text-red-400 transition-colors"
                        active-class="text-red-400"
                    >
                        <i class="fa-regular fa-heart text-sm"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest hidden sm:inline">Favoris</span>
                    </router-link>
                    
                    <div class="flex items-center gap-3">
                        <span class="text-[10px] font-black text-zinc-500 uppercase tracking-widest hidden sm:block">
                            Agent: {{ user.name }}
                        </span>
                        <button @click="logout" 
                            class="w-8 h-8 rounded-lg bg-zinc-900 border border-zinc-800 flex items-center justify-center text-zinc-500 hover:text-red-400 hover:border-red-500/30 transition-all active:scale-95"
                            title="Déconnexion">
                            <i class="fa-solid fa-power-off text-xs"></i>
                        </button>
                    </div>
                </template>

                <template v-else>
                    <router-link to="/login" class="text-zinc-400 hover:text-white text-[10px] font-black uppercase tracking-widest transition-colors">
                        Connexion
                    </router-link>
                    <router-link to="/register" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-500/20 transition-all hover:scale-105">
                        S'inscrire
                    </router-link>
                </template>
            </div>
        </div>
    </nav>

    <!-- CONTENU -->
    <div
      class="container mx-auto flex-grow"
      :class="{ 'flex items-center justify-center py-12': !user }"
    >
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </div>

    <!-- foooter -->
    <footer class="border-t border-zinc-900 bg-zinc-950 py-10 mt-auto">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-2 opacity-50">
                <i class="fa-solid fa-brain text-xs"></i>
                <span class="font-black italic uppercase tracking-tighter text-xs">LocalMind</span>
            </div>
            
            <p class="text-zinc-600 text-[10px] font-bold uppercase tracking-[0.3em]">
                &copy; {{ new Date().getFullYear() }} Réseau d'Intelligence Communautaire
            </p>

            <div class="flex items-center gap-4 text-zinc-500">
                <a href="#" class="hover:text-indigo-400 transition-colors"><i class="fa-brands fa-github"></i></a>
                <a href="#" class="hover:text-indigo-400 transition-colors"><i class="fa-brands fa-x-twitter"></i></a>
            </div>
        </div>
    </footer>

  </main>
</template>

<style>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

::-webkit-scrollbar { width: 8px; }
::-webkit-scrollbar-track { background: #09090b; }
::-webkit-scrollbar-thumb { background: #27272a; border-radius: 10px; }
::-webkit-scrollbar-thumb:hover { background: #3f3f46; }
</style>