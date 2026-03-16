<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from "@/services/api";

const router = useRouter();

const questions = ref([]);
const error = ref('');
const loading = ref(true);
const pagination = ref({ current_page: 1, last_page: 1 });

const toggleFavorite = async (q, e) => {
    e?.stopPropagation?.();
    if (!q.id) return;
    try {
        const { data } = await api.post(`/questions/${q.id}/favorite`);
        q.is_favorited = data.is_favorited;
        if (q.metrics) q.metrics.favorites_count = data.favorites_count;
        if (!data.is_favorited) {
            questions.value = questions.value.filter((x) => x.id !== q.id);
        }
    } catch (_) {}
};

const fetchFavorites = async (page = 1) => {
    error.value = '';
    loading.value = true;
    try {
        const { data } = await api.get('/favorites', { params: { page } });
        questions.value = data.data ?? [];
        const meta = data.meta ?? data;
        pagination.value = {
            current_page: meta.current_page ?? 1,
            last_page: meta.last_page ?? 1,
        };
    } catch (err) {
        error.value = "Error loading favorites.";
    } finally {
        loading.value = false;
    }
};

onMounted(() => fetchFavorites());
</script>

<template>
    <div class="w-full max-w-4xl mx-auto py-8 px-4">
        <div class="flex items-center justify-between mb-10">
            <div>
                <h2 class="text-3xl font-black text-white italic uppercase tracking-tighter">Favorites</h2>
                <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-1">Your saved questions</p>
            </div>
            <button
                @click="fetchFavorites(pagination.current_page)"
                class="p-3 bg-zinc-900 border border-zinc-800 rounded-xl text-zinc-400 hover:text-indigo-400 transition-all active:scale-95"
            >
                <i class="fa-solid fa-rotate-right" :class="{ 'animate-spin': loading }"></i>
            </button>
        </div>

        <div v-if="loading" class="space-y-6">
            <div v-for="i in 3" :key="i" class="h-40 bg-zinc-900/40 border border-zinc-800 rounded-[2rem] animate-pulse"></div>
        </div>

        <div v-else-if="error" class="p-8 bg-red-500/10 border border-red-500/20 rounded-[2rem] text-center">
            <i class="fa-solid fa-triangle-exclamation text-red-500 text-3xl mb-3"></i>
            <p class="text-red-200 text-xs font-bold uppercase tracking-widest">{{ error }}</p>
        </div>

        <div v-else class="space-y-6">
            <div
                v-for="q in questions"
                :key="q.id"
                @click="router.push(`/questions/${q.id}`)"
                class="bg-zinc-900/40 border border-zinc-800 p-6 rounded-[2rem] hover:border-indigo-500/30 transition-all group relative overflow-hidden shadow-xl cursor-pointer"
            >
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-[10px] font-black text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 uppercase tracking-widest">
                        <i class="fa-solid fa-location-dot mr-1"></i>
                        {{ q.location }}
                    </span>
                    <span class="text-zinc-600 text-[10px] uppercase font-bold tracking-widest">{{ q.created_at }}</span>
                </div>

                <h3 class="text-xl font-bold text-white mb-2 group-hover:text-indigo-400 transition-colors leading-tight">
                    {{ q.title }}
                </h3>
                <p class="text-zinc-400 text-sm line-clamp-2 leading-relaxed mb-8">
                    {{ q.content }}
                </p>

                <div class="flex items-center justify-between border-t border-zinc-800/50 pt-4 mt-auto">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-zinc-800 rounded-lg flex items-center justify-center text-zinc-500 font-bold text-xs border border-zinc-700">
                            {{ q.author?.initial || '?' }}
                        </div>
                        <span class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">{{ q.author?.name }}</span>
                    </div>

                    <div class="flex items-center gap-4 text-zinc-600">
                        <div class="flex items-center gap-1.5">
                            <i class="fa-regular fa-comment text-xs"></i>
                            <span class="text-[10px] font-bold">{{ q.metrics?.responses_count || 0 }}</span>
                        </div>
                        <button
                            @click="toggleFavorite(q, $event)"
                            class="flex items-center gap-1.5 text-red-400 hover:opacity-80 transition-opacity"
                            title="Remove from favorites"
                        >
                            <i class="fa-solid fa-heart text-xs"></i>
                            <span class="text-[10px] font-bold">{{ q.metrics?.favorites_count ?? 0 }}</span>
                        </button>
                        <i class="fa-solid fa-chevron-right text-[10px] group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </div>

            <div v-if="questions.length === 0" class="text-center py-20 border-2 border-dashed border-zinc-800 rounded-[2.5rem]">
                <i class="fa-regular fa-heart text-zinc-800 text-4xl mb-4"></i>
                <p class="text-zinc-600 uppercase text-[10px] font-black tracking-widest">No favorites yet. Save questions from the feed.</p>
                <button
                    @click="router.push('/')"
                    class="mt-4 text-indigo-400 text-xs font-bold uppercase tracking-widest hover:underline"
                >
                    Go to Feed
                </button>
            </div>
        </div>
    </div>
</template>
