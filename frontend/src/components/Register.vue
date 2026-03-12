<script setup>
import { ref } from 'vue';
import api from '@/services/api';

const emit = defineEmits(['register-success', 'switch-to-login']);

const name = ref('');
const email = ref('');
const password = ref('');
const confirmPassword = ref('');
const loading = ref(false);
const error = ref('');

const getDeviceName = () => {
    const platform = navigator.platform || 'unknown-device';
    const browser = navigator.userAgent.includes('Firefox') ? 'firefox' : 'browser';
    return `${platform}-${browser}`.toLowerCase();
};

const handleRegister = async () => {
    error.value = '';

    if (password.value !== confirmPassword.value) {
        error.value = 'Passwords do not match.';
        return;
    }

    loading.value = true;

    try {
        const { data } = await api.post('/register', {
            name: name.value.trim(),
            email: email.value.trim(),
            password: password.value,
            device_name: getDeviceName(),
            role: 'member'
        });

        if (data?.user && data?.token) {
            const userData = data.user;
            const token = data.token;

            localStorage.setItem('lm_token', token);
            localStorage.setItem('lm_user', JSON.stringify(userData));

            emit('register-success', userData);
        } else {
            error.value = 'Server returned an unexpected register response.';
        }
    } catch (err) {
        if (err.response?.status === 422) {
            const validationErrors = err.response?.data?.errors;
            const firstError = validationErrors ? Object.values(validationErrors)[0] : null;
            error.value = Array.isArray(firstError) ? firstError[0] : 'Please fill in all required fields correctly.';
        } else {
            error.value = err.response?.data?.message || 'Registration failed. Please try again.';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="w-full max-w-sm bg-zinc-900 border border-zinc-800 p-8 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
        <div class="mb-10 text-center relative z-10">
            <div class="w-16 h-16 bg-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl shadow-emerald-500/20 rotate-3">
                <i class="fa-solid fa-user-plus text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-black text-white italic uppercase tracking-tighter leading-none">Create Access</h2>
            <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest mt-2">Join LocalMind Intelligence Network</p>
        </div>

        <form @submit.prevent="handleRegister" class="space-y-5 relative z-10">
            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2 ml-1">Name</label>
                <input v-model="name" type="text" required placeholder="Your full name"
                    class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3.5 text-white text-sm focus:border-emerald-500 outline-none transition-all placeholder:text-zinc-800">
            </div>

            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2 ml-1">Identity (Email)</label>
                <input v-model="email" type="email" required placeholder="name@example.com"
                    class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3.5 text-white text-sm focus:border-emerald-500 outline-none transition-all placeholder:text-zinc-800">
            </div>

            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2 ml-1">Security Key (Password)</label>
                <input v-model="password" type="password" required placeholder="••••••••"
                    class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3.5 text-white text-sm focus:border-emerald-500 outline-none transition-all placeholder:text-zinc-800">
            </div>

            <div>
                <label class="block text-[10px] font-black text-zinc-500 uppercase tracking-widest mb-2 ml-1">Confirm Password</label>
                <input v-model="confirmPassword" type="password" required placeholder="••••••••"
                    class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3.5 text-white text-sm focus:border-emerald-500 outline-none transition-all placeholder:text-zinc-800">
            </div>

            <div v-if="error" class="bg-red-500/10 border border-red-500/20 p-3 rounded-lg flex items-center gap-2 animate-pulse">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-[10px]"></i>
                <p class="text-red-200 text-[10px] font-bold uppercase tracking-widest">{{ error }}</p>
            </div>

            <button type="submit" :disabled="loading"
                class="w-full bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white font-black py-4 rounded-xl shadow-lg transition-all active:scale-95 uppercase tracking-widest text-[10px]">
                <span v-if="loading">Creating Identity...</span>
                <span v-else>Create Account</span>
            </button>
        </form>

        <div class="mt-6 text-center relative z-10">
            <button
                type="button"
                @click="emit('switch-to-login')"
                class="text-zinc-400 hover:text-white text-[10px] font-black uppercase tracking-widest transition-colors"
            >
                Already have an account? Login
            </button>
        </div>
    </div>
</template>
