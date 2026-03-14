<script setup>
import { ref, onMounted, watch, inject } from "vue"; 
import { useRouter } from "vue-router";
import api from "@/services/api";

const router = useRouter();

const user = inject('user');

const questions = ref([]);
const error = ref("");
const loading = ref(false);

const keyword = ref("");
const location = ref("");

const isOwner = (q) => {
  if (q?.is_owner === true) return true;
  if (!user?.value || !q?.author) return false;
  return Number(q.author.id) === Number(user.value.id);
};
const isAdmin = () => user?.value?.role === 'admin';
const canDelete = (q) => isOwner(q) || isAdmin();

const toggleFavorite = async (q, e) => {
  e?.stopPropagation?.();
  if (!q.id) return;
  try {
    const response = await api.post(`/questions/${q.id}/favorite`);

    let rawData = response.data;
    if (typeof rawData === "string") {
      const jsonStart = rawData.indexOf("{");
      if (jsonStart !== -1) {
        try {
          rawData = JSON.parse(rawData.substring(jsonStart));
        } catch (e) {
          console.error("Error cleaning favorite JSON:", e);
        }
      }
    }

    // Uppdate the reactive state
    q.is_favorited = rawData.is_favorited;
    if (q.metrics) q.metrics.favorites_count = rawData.favorites_count;
  } catch (_) {}
};

const goToEdit = (q, e) => {
  e?.stopPropagation?.();
  router.push(`/questions/${q.id}`);
};

const deleteQuestion = async (q, e) => {
  e?.stopPropagation?.();
  if (!q.id) return;
  if (!confirm("Delete this question permanently? This cannot be undone.")) return;
  try {
    await api.delete(`/questions/${q.id}`);
    questions.value = questions.value.filter((x) => x.id !== q.id);
  } catch (_) {}
};

const fetchQuestions = async () => {
  error.value = "";
  loading.value = true;

  try {
    const response = await api.get("/questions", {
      params: {
        keyword: keyword.value,
        location: location.value,
      },
    });

    let rawData = response.data;
    if (typeof rawData === "string") {
      const jsonStart = rawData.indexOf("{");
      if (jsonStart !== -1) {
        try {
          rawData = JSON.parse(rawData.substring(jsonStart));
        } catch (e) {
          console.error(" Error cleaning feed JSON:", e);
        }
      }
    }

    questions.value = rawData.data || [];
    console.log("Dataaa jaat");
  } catch (err) {
    console.log("error in fetching daata");
    error.value = "Error in feeetching data !!";
  } finally {
    loading.value = false;
  }
};

// reeefresh when user stops typing
let timeout = null;
watch([keyword, location], () => {
  clearTimeout(timeout);
  timeout = setTimeout(fetchQuestions, 500); // waait 500ms before calling API
});

onMounted(fetchQuestions);
</script>

<template>
  <div class="w-full max-w-4xl mx-auto py-8 px-4">
    <!-- Feed Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2
          class="text-3xl font-black text-white italic uppercase tracking-tighter"
        >
          Community Feed
        </h2>
        <p
          class="text-zinc-500 text-[10px] font-bold uppercase tracking-[0.3em] mt-1"
        >
          Real-time intelligence stream
        </p>
      </div>

      <div class="flex items-center gap-3">
        <!-- Section visible uniquement si l'utilisateur est injecté (connecté) -->
        <template v-if="user">
          <button
            @click="router.push('/favorites')"
            class="bg-zinc-900 hover:bg-zinc-800 text-rose-400 px-4 py-2.5 rounded-xl font-black uppercase text-[10px] tracking-widest transition-all active:scale-95 border border-zinc-800 flex items-center gap-2"
          >
            <i class="fa-solid fa-heart"></i>
            <span class="hidden sm:inline">Saved Intel</span>
          </button>
          
          <button
            @click="router.push('/questions/create')"
            class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2.5 rounded-xl font-black uppercase text-[10px] tracking-widest transition-all active:scale-95 shadow-lg shadow-indigo-900/20"
          >
            New Question
          </button>
        </template>

        <button
          @click="fetchQuestions"
          class="p-2.5 bg-zinc-900 border border-zinc-800 rounded-xl text-zinc-400 hover:text-indigo-400 transition-all active:scale-95"
        >
          <i
            class="fa-solid fa-rotate-right"
            :class="{ 'animate-spin': loading }"
          ></i>
        </button>
      </div>
    </div>

    <!-- Search & Filter Bar -->
    <div
      class="bg-zinc-900 border border-zinc-800 p-4 rounded-3xl mb-10 flex flex-col md:flex-row gap-4 shadow-xl"
    >
      <div class="flex-grow relative">
        <i
          class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600"
        ></i>
        <input
          v-model="keyword"
          type="text"
          placeholder="Search keywords..."
          class="w-full bg-zinc-950 border border-zinc-800 rounded-xl py-3 pl-11 pr-4 text-sm text-white focus:border-indigo-500 outline-none transition-all placeholder:text-zinc-800"
        />
      </div>
      <div class="md:w-1/3 relative">
        <i
          class="fa-solid fa-location-dot absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600"
        ></i>
        <input
          v-model="location"
          type="text"
          placeholder="Location..."
          class="w-full bg-zinc-950 border border-zinc-800 rounded-xl py-3 pl-11 pr-4 text-sm text-white focus:border-indigo-500 outline-none transition-all placeholder:text-zinc-800"
        />
      </div>
    </div>

    <!-- Loading Skeletons -->
    <div v-if="loading && questions.length === 0" class="space-y-6">
      <div
        v-for="i in 3"
        :key="i"
        class="h-40 bg-zinc-900/40 border border-zinc-800 rounded-[2rem] animate-pulse"
      ></div>
    </div>

    <!-- Error Message -->
    <div
      v-else-if="error"
      class="p-8 bg-red-500/10 border border-red-500/20 rounded-[2rem] text-center"
    >
      <i
        class="fa-solid fa-triangle-exclamation text-red-500 text-3xl mb-3"
      ></i>
      <p class="text-red-200 text-xs font-bold uppercase tracking-widest">
        {{ error }}
      </p>
    </div>

    <!-- Questions Feed -->
    <div v-else class="space-y-6">
      <div
        v-for="q in questions"
        :key="q.id"
        @click="router.push(`/questions/${q.id}`)"
        class="bg-zinc-900/40 border border-zinc-800 p-6 rounded-[2rem] hover:border-indigo-500/30 transition-all group relative overflow-hidden shadow-xl cursor-pointer"
      >
        <!-- Metadata Bar -->
        <div class="flex items-center gap-3 mb-4">
          <span
            class="text-[10px] font-black text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20 uppercase tracking-widest"
          >
            <i class="fa-solid fa-location-dot mr-1"></i>
            {{ q.location }}
          </span>
          <span
            class="text-zinc-600 text-[10px] uppercase font-bold tracking-widest"
            >{{ q.created_at }}</span
          >
        </div>

        <!-- Main Content -->
        <h3
          class="text-xl font-bold text-white mb-2 group-hover:text-indigo-400 transition-colors leading-tight"
        >
          {{ q.title }}
        </h3>
        <p class="text-zinc-400 text-sm line-clamp-2 leading-relaxed mb-8">
          {{ q.content }}
        </p>

        <!-- Card Footer -->
        <div
          class="flex flex-wrap items-center justify-between gap-3 border-t border-zinc-800/50 pt-4 mt-auto"
        >
          <!-- Author -->
          <div class="flex items-center gap-3">
            <div
              class="w-8 h-8 bg-zinc-800 rounded-lg flex items-center justify-center text-zinc-500 font-bold text-xs border border-zinc-700 shrink-0"
            >
              {{ q.author?.initial || "?" }}
            </div>
            <span
              class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest"
              >{{ q.author?.name }}</span
            >
          </div>

          <!-- Right side: actions + metrics -->
          <div class="flex items-center gap-3 flex-wrap">
          <!-- Owner: Edit. Owner or Admin: Delete -->
          <div
            v-if="canDelete(q)"
            class="flex items-center gap-2 pointer-events-auto mr-1"
          >
            <button
              v-if="isOwner(q)"
              @click="goToEdit(q, $event)"
              class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-zinc-800/80 border border-zinc-700 text-zinc-400 hover:text-indigo-400 hover:border-indigo-500/40 hover:bg-indigo-500/10 text-[10px] font-bold uppercase tracking-widest transition-all active:scale-95"
              title="Edit question"
            >
              <i class="fa-solid fa-pen text-[10px]"></i>
              <span>Edit</span>
            </button>
            <button
              @click="deleteQuestion(q, $event)"
              class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-zinc-800/80 border border-zinc-700 text-zinc-400 hover:text-red-400 hover:border-red-500/40 hover:bg-red-500/10 text-[10px] font-bold uppercase tracking-widest transition-all active:scale-95"
              title="Delete question"
            >
              <i class="fa-solid fa-trash text-[10px]"></i>
              <span>Delete</span>
            </button>
          </div>

            <!-- Metrics -->
            <div class="flex items-center gap-4 text-zinc-600">
              <div class="flex items-center gap-1.5">
                <i class="fa-regular fa-comment text-xs"></i>
                <span class="text-[10px] font-bold">{{
                  q.metrics?.responses_count || 0
                }}</span>
              </div>

              <button
                @click="toggleFavorite(q, $event)"
                class="flex items-center gap-1.5 hover:text-red-400 transition-colors pointer-events-auto"
                :class="q.is_favorited ? 'text-red-400' : ''"
              >
                <i
                  :class="
                    q.is_favorited ? 'fa-solid fa-heart' : 'fa-regular fa-heart'
                  "
                  class="text-xs"
                ></i>
                <span class="text-[10px] font-bold">{{
                  q.metrics?.favorites_count ?? 0
                }}</span>
              </button>

              <i
                class="fa-solid fa-chevron-right text-[10px] group-hover:translate-x-1 transition-transform text-zinc-600"
              ></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty Feed -->
      <div
        v-if="questions.length === 0"
        class="text-center py-20 border-2 border-dashed border-zinc-800 rounded-[2.5rem]"
      >
        <i class="fa-solid fa-satellite-dish text-zinc-800 text-4xl mb-4"></i>
        <p
          class="text-zinc-600 uppercase text-[10px] font-black tracking-widest"
        >
          Sector clear. No active inquiries found.
        </p>
      </div>
    </div>
  </div>
</template>