<script setup>
import { ref, computed, onMounted, watch, inject } from 'vue';
import { useRouter } from 'vue-router';
import api from "@/services/api";

const props = defineProps({
    id: {
        type: [String, Number],
        required: true
    }
});

const router = useRouter();
const user = inject('user');
const question = ref({});
const error = ref('');
const loading = ref('');
const editing = ref(false);
const editForm = ref({ title: '', content: '', location: '' });
const saving = ref(false);
const deleteError = ref('');

const isOwner = computed(() => {
    const q = question.value;
    if (q?.is_owner === true) return true;
    if (!user?.value || !q?.author) return false;
    return Number(q.author.id) === Number(user.value.id);
});

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

const startEdit = () => {
    editForm.value = {
        title: question.value.title || '',
        content: question.value.content || '',
        location: question.value.location || ''
    };
    editing.value = true;
};

const cancelEdit = () => {
    editing.value = false;
};

const saveEdit = async () => {
    saving.value = true;
    deleteError.value = '';
    try {
        const { data } = await api.put(`/questions/${props.id}`, editForm.value);
        question.value = data.data ?? data;
        editing.value = false;
    } catch (err) {
        deleteError.value = err.response?.data?.message || err.response?.data?.errors ? JSON.stringify(err.response.data.errors) : 'Failed to update question.';
    } finally {
        saving.value = false;
    }
};

const deleteQuestion = async () => {
    if (!confirm('Delete this question permanently? This cannot be undone.')) return;
    deleteError.value = '';
    try {
        await api.delete(`/questions/${props.id}`);
        router.push('/');
    } catch (err) {
        deleteError.value = err.response?.data?.message || 'Failed to delete question.';
    }
};

const newResponseContent = ref('');
const responseError = ref('');
const submittingResponse = ref(false);

const addResponse = async () => {
    const content = newResponseContent.value?.trim() || '';
    if (!content || content.length < 5) {
        responseError.value = 'Content must be at least 5 characters.';
        return;
    }
    responseError.value = '';
    submittingResponse.value = true;
    try {
        const { data } = await api.post(`/questions/${props.id}/responses`, { content });
        const created = data.data ?? data;
        if (!question.value.responses) question.value.responses = [];
        question.value.responses.unshift(created);
        if (question.value.metrics) question.value.metrics.responses_count = (question.value.metrics.responses_count || 0) + 1;
        newResponseContent.value = '';
    } catch (err) {
        const msg = err.response?.data?.message || (err.response?.data?.errors?.content ? err.response.data.errors.content[0] : 'Failed to post response.');
        responseError.value = msg;
    } finally {
        submittingResponse.value = false;
    }
};

const editingResponseId = ref(null);
const editResponseContent = ref('');

const startEditResponse = (res) => {
    editingResponseId.value = res.id;
    editResponseContent.value = res.content || '';
};

const cancelEditResponse = () => {
    editingResponseId.value = null;
    editResponseContent.value = '';
};

const saveResponse = async (res) => {
    const content = editResponseContent.value?.trim() || '';
    if (content.length < 5) return;
    try {
        const { data } = await api.put(`/responses/${res.id}`, { content });
        const updated = data.data ?? data;
        const idx = question.value.responses?.findIndex((r) => r.id === res.id);
        if (idx !== undefined && idx >= 0) question.value.responses[idx] = updated;
        cancelEditResponse();
    } catch (_) {}
};

const deleteResponse = async (res) => {
    if (!confirm('Delete this response?')) return;
    try {
        await api.delete(`/responses/${res.id}`);
        question.value.responses = question.value.responses.filter((r) => r.id !== res.id);
        if (question.value.metrics) question.value.metrics.responses_count = Math.max(0, (question.value.metrics.responses_count || 0) - 1);
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
                
                <div class="flex items-center justify-between gap-3 mb-6 relative z-10 flex-wrap">
                    <div class="flex items-center gap-3">
                        <span class="bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">
                            <i class="fa-solid fa-location-dot mr-1"></i>
                            {{ question.location }}
                        </span>
                        <span class="text-zinc-600 text-[10px] font-bold uppercase tracking-widest">
                            Broadcast {{ question.created_at }}
                        </span>
                    </div>
                    <!-- Owner actions: Edit & Delete -->
                    <div v-if="isOwner" class="flex items-center gap-2">
                        <button
                            v-if="!editing"
                            @click="startEdit"
                            class="px-3 py-1.5 rounded-lg border border-zinc-700 text-zinc-400 hover:text-indigo-400 hover:border-indigo-500/50 text-[10px] font-bold uppercase tracking-widest transition-colors"
                        >
                            <i class="fa-solid fa-pen mr-1"></i> Edit
                        </button>
                        <button
                            v-if="!editing"
                            @click="deleteQuestion"
                            class="px-3 py-1.5 rounded-lg border border-zinc-700 text-zinc-400 hover:text-red-400 hover:border-red-500/50 text-[10px] font-bold uppercase tracking-widest transition-colors"
                        >
                            <i class="fa-solid fa-trash mr-1"></i> Delete
                        </button>
                    </div>
                </div>

                <div v-if="deleteError && !editing" class="mb-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-300 text-xs relative z-10">
                    {{ deleteError }}
                </div>

                <!-- Edit form (owner only) -->
                <div v-if="editing" class="mb-8 p-6 bg-zinc-950/80 border border-indigo-500/30 rounded-2xl relative z-10 space-y-4">
                    <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest">Edit question</p>
                    <div v-if="deleteError" class="text-red-400 text-xs">{{ deleteError }}</div>
                    <input v-model="editForm.title" type="text" placeholder="Title" class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white text-sm focus:border-indigo-500 outline-none" />
                    <textarea v-model="editForm.content" rows="4" placeholder="Content" class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white text-sm focus:border-indigo-500 outline-none resize-y"></textarea>
                    <input v-model="editForm.location" type="text" placeholder="Location" class="w-full bg-zinc-900 border border-zinc-700 rounded-xl px-4 py-3 text-white text-sm focus:border-indigo-500 outline-none" />
                    <div class="flex gap-3">
                        <button @click="saveEdit" :disabled="saving" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 rounded-xl text-white text-[10px] font-bold uppercase tracking-widest">
                            {{ saving ? 'Saving...' : 'Save' }}
                        </button>
                        <button @click="cancelEdit" :disabled="saving" class="px-4 py-2 bg-zinc-800 hover:bg-zinc-700 rounded-xl text-zinc-300 text-[10px] font-bold uppercase tracking-widest">
                            Cancel
                        </button>
                    </div>
                </div>

                <h1 v-else class="text-3xl md:text-5xl font-black text-white italic tracking-tighter mb-8 leading-tight relative z-10">
                    {{ question.title }}
                </h1>

                <div v-if="!editing" class="text-zinc-400 text-lg leading-relaxed mb-10 pb-10 border-b border-zinc-800/50 relative z-10">
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

            <!-- 2. RESPONSES SECTION (CRUD: Create, Read, Update, Delete) -->
            <section class="space-y-6">
                <div class="flex items-center gap-3 border-b border-zinc-800 pb-4 ml-4">
                    <i class="fa-solid fa-comments text-indigo-500 text-sm"></i>
                    <h3 class="text-white font-black italic uppercase tracking-widest text-xs">
                        Intelligence Stream ({{ question.metrics?.responses_count }})
                    </h3>
                </div>

                <!-- Create response (only when logged in) -->
                <div v-if="user" class="bg-zinc-900/60 border border-zinc-800 p-5 rounded-2xl">
                    <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-3">Add your response</p>
                    <textarea
                        v-model="newResponseContent"
                        placeholder="Share your intelligence (min 5 characters)..."
                        rows="3"
                        class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-white text-sm focus:border-indigo-500 outline-none resize-y placeholder:text-zinc-600"
                    ></textarea>
                    <div v-if="responseError" class="mt-2 text-red-400 text-xs">{{ responseError }}</div>
                    <div class="mt-3 flex items-center justify-between">
                        <span class="text-zinc-600 text-[10px]">{{ newResponseContent.length }} / 2000</span>
                        <button
                            @click="addResponse"
                            :disabled="submittingResponse || !newResponseContent?.trim() || newResponseContent.trim().length < 5"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed rounded-xl text-white text-[10px] font-bold uppercase tracking-widest transition-all"
                        >
                            {{ submittingResponse ? 'Sending...' : 'Post response' }}
                        </button>
                    </div>
                </div>

                <!-- List of responses (Read). Each card: Edit/Delete only if you own it (Update/Delete). -->
                <div v-if="question.responses?.length" class="grid gap-4">
                    <div v-for="res in question.responses" :key="res.id"
                        class="bg-zinc-900/40 border border-zinc-800 p-6 rounded-3xl hover:border-zinc-700 transition-all flex gap-5">
                        <div class="w-10 h-10 bg-zinc-800 rounded-xl flex items-center justify-center text-zinc-500 font-bold border border-zinc-700 shrink-0">
                            {{ res.author?.initial }}
                        </div>
                        <div class="grow min-w-0">
                            <div class="flex items-center justify-between gap-2 flex-wrap mb-2">
                                <span class="text-white font-bold text-sm">{{ res.author?.name }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-zinc-600 text-[10px] font-bold uppercase">{{ res.created_at }}</span>
                                    <!-- Edit / Delete: only for the author of this response -->
                                    <template v-if="res.is_owner">
                                        <button
                                            v-if="editingResponseId !== res.id"
                                            @click="startEditResponse(res)"
                                            class="p-1.5 rounded-lg text-zinc-500 hover:text-indigo-400 hover:bg-indigo-500/10 transition-colors"
                                            title="Edit response"
                                        >
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                        </button>
                                        <button
                                            v-if="editingResponseId !== res.id"
                                            @click="deleteResponse(res)"
                                            class="p-1.5 rounded-lg text-zinc-500 hover:text-red-400 hover:bg-red-500/10 transition-colors"
                                            title="Delete response"
                                        >
                                            <i class="fa-solid fa-trash text-[10px]"></i>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            <!-- Inline edit mode -->
                            <div v-if="editingResponseId === res.id" class="space-y-3">
                                <textarea
                                    v-model="editResponseContent"
                                    rows="3"
                                    class="w-full bg-zinc-950 border border-zinc-700 rounded-xl px-4 py-3 text-white text-sm focus:border-indigo-500 outline-none resize-y"
                                ></textarea>
                                <div class="flex gap-2">
                                    <button
                                        @click="saveResponse(res)"
                                        :disabled="!editResponseContent?.trim() || editResponseContent.trim().length < 5"
                                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 rounded-lg text-white text-[10px] font-bold uppercase"
                                    >
                                        Save
                                    </button>
                                    <button
                                        @click="cancelEditResponse"
                                        class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 rounded-lg text-zinc-300 text-[10px] font-bold uppercase"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </div>
                            <p v-else class="text-zinc-400 text-sm leading-relaxed">{{ res.content }}</p>
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