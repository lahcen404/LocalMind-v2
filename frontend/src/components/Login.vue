<script setup>
import { ref, inject } from 'vue';
import { RouterLink } from 'vue-router';
import api from '@/services/api';

const onAuthSuccess = inject('onAuthSuccess');

const email = ref('');
const password = ref('');
const loading = ref(false);
const error = ref('');


const handleLogin = async () => {
    error.value = '';
    loading.value = true;
    
    try {
        const { data } = await api.post('/login', {
            email: email.value.trim(),
            password: password.value,
            device_name: 'lahcen_device'
        });

        if (data?.user && data?.token) {
            const userData = data.user;
            const token = data.token;

            console.log(" success login API ! Utilisateur :", userData.name);

            
            localStorage.setItem('lm_token', token);
            localStorage.setItem('lm_user', JSON.stringify(userData));

            if (onAuthSuccess) onAuthSuccess(userData);
        } else {
            error.value = "Server returned an unexpected login response.";
        }
        
    } catch (err) {
        if (err.response?.status === 401) {
            error.value = "Incorrect credentials. Please check your email and password.";
        } else if (err.response?.status === 422) {
            const validationErrors = err.response?.data?.errors;
            const firstError = validationErrors ? Object.values(validationErrors)[0] : null;
            error.value = Array.isArray(firstError) ? firstError[0] : 'Please fill in all required fields correctly.';
        } else {
            error.value = err.response?.data?.message || 'Identifiants invalides ou erreur réseau.';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="w-full max-w-sm bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
        
        <div class="mb-10 text-center relative z-10">
            <div class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl shadow-indigo-500/20 rotate-3">
                <i class="fa-solid fa-key text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-black text-white italic uppercase tracking-tighter leading-none">Terminal Access</h2>
            <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mt-2">LocalMind Intelligence Network</p>
        </div>

        <form @submit.prevent="handleLogin" class="space-y-6 relative z-10">
            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2 ml-1">Identity (Email)</label>
                <input v-model="email" type="email" required placeholder="name@example.com"
                    class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3.5 text-white text-sm focus:border-indigo-500 outline-none transition-all placeholder:text-zinc-800">
            </div>

            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2 ml-1">Security Key (Password)</label>
                <input v-model="password" type="password" required placeholder="••••••••"
                    class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3.5 text-white text-sm focus:border-indigo-500 outline-none transition-all placeholder:text-zinc-800">
            </div>

            <div v-if="error" class="bg-red-500/10 border border-red-500/20 p-3 rounded-lg flex items-center gap-2 animate-pulse">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-[10px]"></i>
                <p class="text-red-200 text-[10px] font-bold uppercase tracking-widest">{{ error }}</p>
            </div>

            <button type="submit" :disabled="loading" 
                class="w-full bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-95 uppercase tracking-widest text-[10px]">
                <span v-if="loading">Processing Identity...</span>
                <span v-else>Login</span>
            </button>
        </form>

        <div class="mt-6 text-center relative z-10">
            <RouterLink
                to="/register"
                class="text-zinc-400 hover:text-white text-[10px] font-black uppercase tracking-widest transition-colors"
            >
                Need an account? Register
            </RouterLink>
        </div>
    </div>
</template>

