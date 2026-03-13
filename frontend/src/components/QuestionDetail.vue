<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import api from "@/services/api";

const props = defineProps({
    id: {
        type: [String, Number],
        required: true
    }
});

const router = useRouter();
const question = ref({});
const error = ref('');
const loading = ref('');

const fetchQuestion = async () => {
    error.value = '';
    loading.value = true;
    try {
        const { data } = await api.get(`/questions/${props.id}`);
        question.value = data.data ?? data;
    } catch (err) {
        error.value = "Error fetching question.";
    } finally {
        loading.value = false;
    }
};

const toggleFavorite = async () => {
    if (!question.value?.id) return;
    try {
        const { data } = await api.post(`/questions/${question.value.id}/favorite`);
        question.value.is_favorited = data.is_favorited;
        if (question.value.metrics) question.value.metrics.favorites_count = data.favorites_count;
    } catch (_) {}
};

onMounted(fetchQuestion);
watch(() => props.id, fetchQuestion);
</script>


<template>
    <div class="w-full max-w-4xl mx-auto py-8 px-4 animate-in fade-in slide-in-from-bottom-4 duration-700">
        
        <!-- Navigation -->
        <button @click="router.push('/')" class="mb-8 flex items-center gap-2 text-zinc-500 hover:text-white transition-colors group">
            <i class="fa-solid fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
            <span class="text-[10px] font-black uppercase tracking-widest">Return to Feed</span>
        </button>

        <div v-if="loading" class="space-y-6">
            <div class="h-64 bg-zinc-900/40 border border-zinc-800 rounded-[2.5rem] animate-pulse"></div>
        </div>

        <div v-else-if="question" class="space-y-10">
            <!-- 1. MAIN CONTENT CARD -->
            <article class="bg-zinc-900 border border-zinc-800 p-8 md:p-12 rounded-[2.5rem] shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl"></div>
                
                <div class="flex items-center gap-3 mb-6 relative z-10">
                    <span class="bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">
                        <i class="fa-solid fa-location-dot mr-1"></i>
                        {{ question.location }}
                    </span>
                    <span class="text-zinc-600 text-[10px] font-bold uppercase tracking-widest">
                        Broadcast {{ question.created_at }}
                    </span>
                </div>

                <h1 class="text-3xl md:text-5xl font-black text-white italic tracking-tighter mb-8 leading-tight relative z-10">
                    {{ question.title }}
                </h1>

                <div class="text-zinc-400 text-lg leading-relaxed mb-10 pb-10 border-b border-zinc-800/50 relative z-10">
                    {{ question.content }}
                </div>

                <div class="flex items-center justify-between relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-600 rounded-2xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                            {{ question.author?.initial }}
                        </div>
                        <div>
                            <p class="text-white font-bold">{{ question.author?.name }}</p>
                            <p class="text-zinc-500 text-[10px] uppercase tracking-widest font-black">Community Source</p>
                        </div>
                    </div>
                    <button
                        @click="toggleFavorite"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl border transition-colors"
                        :class="question.is_favorited ? 'bg-red-500/10 border-red-500/30 text-red-400' : 'bg-zinc-800/50 border-zinc-700 text-zinc-400 hover:text-red-400 hover:border-red-500/30'"
                        :title="question.is_favorited ? 'Remove from favorites' : 'Add to favorites'"
                    >
                        <i :class="question.is_favorited ? 'fa-solid fa-heart' : 'fa-regular fa-heart'"></i>
                        <span class="text-[10px] font-bold">{{ question.metrics?.favorites_count ?? 0 }}</span>
                    </button>
                </div>
            </article>

            <!-- 2. RESPONSES SECTION -->
            <section class="space-y-6">
                <div class="flex items-center gap-3 border-b border-zinc-800 pb-4 ml-4">
                    <i class="fa-solid fa-comments text-indigo-500 text-sm"></i>
                    <h3 class="text-white font-black italic uppercase tracking-widest text-xs">
                        Intelligence Stream ({{ question.metrics?.responses_count }})
                    </h3>
                </div>

                <div v-if="question.responses?.length" class="grid gap-4">
                    <div v-for="res in question.responses" :key="res.id" 
                        class="bg-zinc-900/40 border border-zinc-800 p-6 rounded-3xl hover:border-zinc-700 transition-all flex gap-5">
                        
                        <div class="w-10 h-10 bg-zinc-800 rounded-xl flex items-center justify-center text-zinc-500 font-bold border border-zinc-700 flex-shrink-0">
                            {{ res.author?.initial }}
                        </div>

                        <div class="flex-grow">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-white font-bold text-sm">{{ res.author?.name }}</span>
                                <span class="text-zinc-600 text-[10px] font-bold uppercase">{{ res.created_at }}</span>
                            </div>
                            <p class="text-zinc-400 text-sm leading-relaxed">{{ res.content }}</p>
                        </div>
                    </div>
                </div>

                <div v-else class="text-center py-16 bg-zinc-900/20 rounded-3xl border-2 border-dashed border-zinc-800 mx-4">
                    <i class="fa-solid fa-satellite-dish text-zinc-800 text-4xl mb-4"></i>
                    <p class="text-zinc-600 italic text-xs uppercase font-black tracking-widest">No intelligence received yet.</p>
                </div>
            </section>
        </div>
    </div>
</template>