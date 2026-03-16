<script setup>
import { ref } from 'vue';
import { RouterLink, useRouter } from 'vue-router';
import api from '@/services/api';

const router = useRouter();

const title = ref('')
const content = ref('')
const location = ref('')
const error = ref('')
const loading = ref(false)

const handlePost = async () => {
    error.value = '';

    if(title.value.length < 5 || location.value.length < 10){
        error.value = "Tiitle must be 5+ chars and Content 10+ chars.";
        return;
    }

    loading.value = true;
    error.value = '';

    try{
        await api.post('/questions', {
            title: title.value,
            content: content.value,
            location: location.value
        });
        console.log('Question added successfully !!')
        router.push('/')
    }catch(err){
        error.value = err.response?.data?.message || "error in addiding a question !!";
    }finally{
        loading.value = false;
    }
}



</script>

<template>
    <div class="w-full max-w-2xl mx-auto py-10 px-4">
        
        <!-- Back Link -->
        <button @click="router.push('/')" class="mb-6 text-zinc-500 hover:text-white text-[10px] font-black uppercase tracking-widest transition-colors flex items-center gap-2">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            Cancel Broadcast
        </button>

        <div class="bg-zinc-900 border border-zinc-800 p-8 md:p-12 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <!-- Glow Effect -->
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none group-hover:bg-indigo-500/10 transition-colors duration-700"></div>

            <div class="mb-10 relative z-10">
                <h1 class="text-3xl font-black text-white italic uppercase tracking-tighter flex items-center gap-3">
                    <i class="fa-solid fa-pen-to-square text-indigo-500"></i>
                    Broadcast Inquiry
                </h1>
                <p class="text-zinc-500 text-[10px] mt-2 uppercase tracking-[0.2em] font-bold">Initiate new community intelligence request</p>
            </div>

            <form @submit.prevent="handlePost" class="space-y-6 relative z-10">
                <!-- Title -->
                <div>
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Question Headline</label>
                    <input v-model="title" type="text" required placeholder="e.g., Best fiber provider in Sector 4?"
                        class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-5 py-4 text-white focus:border-indigo-500 outline-none transition-all placeholder:text-zinc-800">
                </div>

                <!-- Location -->
                <div>
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Target Location</label>
                    <div class="relative group">
                        <i class="fa-solid fa-location-dot absolute left-5 top-1/2 -translate-y-1/2 text-zinc-700 group-focus-within:text-indigo-500 transition-colors"></i>
                        <input v-model="location" type="text" required placeholder="Marrakesh, Gueliz..."
                            class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl pl-12 pr-5 py-4 text-white focus:border-indigo-500 outline-none transition-all placeholder:text-zinc-800">
                    </div>
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-2 ml-1">Details & Context</label>
                    <textarea v-model="content" rows="6" required placeholder="Describe what information you need..."
                        class="w-full bg-zinc-950 border border-zinc-800 rounded-2xl px-5 py-4 text-white focus:border-indigo-500 outline-none transition-all resize-none leading-relaxed"></textarea>
                </div>

                <!-- Error Box -->
                <div v-if="error" class="bg-red-500/10 border border-red-500/20 p-4 rounded-xl text-red-400 text-[10px] font-bold uppercase text-center animate-pulse">
                    {{ error }}
                </div>

                <!-- Submit -->
                <button type="submit" :disabled="loading" 
                    class="w-full bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white font-black py-5 rounded-2xl shadow-xl shadow-indigo-900/20 transition-all active:scale-95 uppercase tracking-widest text-xs flex items-center justify-center gap-3">
                    <span v-if="loading">Transmitting to Network...</span>
                    <span v-else>Add Question</span>
                    <i class="fa-solid fa-paper-plane text-[10px]"></i>
                </button>
            </form>
        </div>
    </div>
</template>