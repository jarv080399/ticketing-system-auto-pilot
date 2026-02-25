<template>
    <div class="space-y-8 animate-fade-in pb-12">

        <!-- ─── Page Header ─── -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black text-text-main tracking-tight">
                    Knowledge <span class="text-primary">Base</span>
                </h1>
                <p class="text-text-dim font-medium mt-1">Manage articles, FAQs, and documentation for users and staff.</p>
            </div>
            <div class="flex gap-4">
                <router-link
                    to="/agent/kb/new"
                    class="px-8 py-3 bg-primary hover:bg-primary-dark text-white font-black rounded-xl shadow-xl shadow-primary/20 hover-lift active:scale-95 transition-all flex items-center gap-2"
                >
                    <span class="text-xl">+</span> New Article
                </router-link>
            </div>
        </div>

        <!-- ─── Filters ─── -->
        <div class="glass-card p-6 rounded-2xl flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-[300px] relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-text-dim">🔍</span>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search articles..."
                    class="w-full bg-surface-light border border-glass-border rounded-xl py-3 pl-12 pr-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all"
                    @keyup.enter="fetchArticles(1)"
                />
            </div>
            <select
                v-model="filterStatus"
                @change="fetchArticles(1)"
                class="bg-surface-light border border-glass-border rounded-xl px-4 py-3 text-sm text-text-main focus:outline-none"
            >
                <option value="">All Statuses</option>
                <option value="published">Published</option>
                <option value="draft">Drafts</option>
            </select>
            <select
                v-model="filterVisibility"
                @change="fetchArticles(1)"
                class="bg-surface-light border border-glass-border rounded-xl px-4 py-3 text-sm text-text-main focus:outline-none"
            >
                <option value="">All Visibility</option>
                <option value="public">🌐 Public</option>
                <option value="internal">🔒 Internal</option>
            </select>
        </div>

        <!-- ─── Loading ─── -->
        <div v-if="loading" class="flex items-center justify-center py-24">
            <div class="flex flex-col items-center gap-4">
                <div class="animate-spin rounded-full h-10 w-10 border-b-4 border-primary"></div>
                <p class="text-sm font-bold text-text-dim animate-pulse uppercase tracking-widest">Loading DB...</p>
            </div>
        </div>

        <template v-else>
            <!-- ─── Table ─── -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-light/50 border-b border-glass-border">
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Title</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Category</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Status</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Stats</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-glass-border">
                        <tr
                            v-for="article in articles"
                            :key="article.id"
                            class="hover:bg-white/5 transition-colors group cursor-default"
                        >
                            <!-- Article Info -->
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-surface-light flex items-center justify-center text-xl flex-shrink-0">
                                        {{ article.category?.icon || '📄' }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-text-main tracking-tight">{{ article.title }}</p>
                                        <p class="text-xs text-text-dim mt-0.5 truncate max-w-sm">
                                            By {{ article.author?.name || 'Unknown' }} • Updated {{ formatDate(article.updated_at) }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-6 py-5">
                                <span
                                    v-if="article.category"
                                    class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border bg-primary/10 text-primary border-primary/20 block w-max"
                                >
                                    {{ article.category.name }}
                                </span>
                                <span v-else class="text-xs text-text-dim italic">—</span>
                            </td>

                            <!-- Status & Visibility -->
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-1 items-start">
                                    <span
                                        :class="article.status === 'published'
                                            ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20'
                                            : 'bg-amber-500/10 text-amber-500 border-amber-500/20'"
                                        class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border"
                                    >
                                        {{ article.status }}
                                    </span>
                                    <span
                                        :class="article.visibility === 'public'
                                            ? 'text-blue-400'
                                            : 'text-red-400'"
                                        class="text-[10px] font-bold flex items-center gap-1"
                                    >
                                        <span v-if="article.visibility === 'public'">🌐 Public</span>
                                        <span v-else>🔒 Internal</span>
                                    </span>
                                </div>
                            </td>

                            <!-- Stats -->
                            <td class="px-6 py-5 flex items-center gap-4">
                                <div class="text-center" title="Views">
                                    <span class="block text-xs font-bold text-text-main">{{ article.view_count }}</span>
                                    <span class="text-[9px] uppercase tracking-widest text-text-dim">Views</span>
                                </div>
                                <div class="text-center" title="Helpful">
                                    <span class="block text-xs font-bold text-emerald-500">{{ article.helpful_yes }} <span class="text-text-main/20">/</span> <span class="text-red-500">{{ article.helpful_no }}</span></span>
                                    <span class="text-[9px] uppercase tracking-widest text-text-dim">Votes</span>
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-100 transition-opacity">
                                    <router-link
                                        :to="`/agent/kb/edit/${article.slug}`"
                                        title="Edit"
                                        class="p-2 text-text-dim hover:text-primary transition-colors inline-block"
                                    >✏️</router-link>
                                    <a
                                        v-if="article.visibility === 'public' && article.status === 'published'"
                                        :href="`/kb/${article.slug}`"
                                        target="_blank"
                                        title="View Public Link"
                                        class="p-2 text-text-dim hover:text-emerald-400 transition-colors inline-block"
                                    >🔗</a>
                                    <button
                                        @click.stop="confirmDelete(article)"
                                        title="Delete"
                                        class="p-2 text-text-dim hover:text-red-400 transition-colors"
                                    >🗑️</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Empty State inside table -->
                <div v-if="articles.length === 0" class="py-20 text-center">
                    <p class="text-text-dim italic">
                        No knowledge base articles found. Click "New Article" to create one.
                    </p>
                </div>
            </div>

            <!-- Pagination Controls -->
            <div v-if="meta && meta.last_page > 1" class="flex justify-between items-center mt-6">
                <button
                    @click="fetchArticles(meta.current_page - 1)"
                    :disabled="meta.current_page === 1"
                    class="px-4 py-2 bg-surface-light border border-glass-border rounded-xl text-xs font-bold text-text-main disabled:opacity-50 hover:bg-white/5 transition-colors"
                >Previous</button>
                <span class="text-xs text-text-dim">Page {{ meta.current_page }} of {{ meta.last_page }}</span>
                <button
                    @click="fetchArticles(meta.current_page + 1)"
                    :disabled="meta.current_page === meta.last_page"
                    class="px-4 py-2 bg-surface-light border border-glass-border rounded-xl text-xs font-bold text-text-main disabled:opacity-50 hover:bg-white/5 transition-colors"
                >Next</button>
            </div>

        </template>

        <!-- ─── Delete Confirm Modal ─── -->
        <Transition name="modal">
            <div v-if="showDeleteConfirm" class="fixed inset-0 bg-background/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="glass-card rounded-2xl w-full max-w-sm p-8 shadow-[0_0_60px_rgba(0,0,0,0.4)] border border-red-500/20">
                    <h2 class="text-xl font-black text-text-main mb-2">Delete Article?</h2>
                    <p class="text-sm text-text-dim mb-6">
                        You are about to permanently delete
                        <span class="text-text-main font-bold">"{{ deletingArticle?.title }}"</span>.
                        This cannot be undone.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button
                            @click="showDeleteConfirm = false; deletingArticle = null"
                            class="px-5 py-2.5 text-sm text-text-dim font-bold hover:text-text-main hover:bg-white/5 rounded-xl transition-all"
                        >
                            Cancel
                        </button>
                        <button
                            @click="executeDelete"
                            :disabled="deleting"
                            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-black rounded-xl transition-all flex items-center gap-2 disabled:opacity-50"
                        >
                            <svg v-if="deleting" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ deleting ? 'Deleting...' : 'Yes, Delete' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '@/plugins/axios';
import { useToast } from 'vue-toastification';

const toast = useToast();

const articles = ref([]);
const meta = ref(null);
const loading = ref(true);
const deleting = ref(false);

const searchQuery = ref('');
const filterStatus = ref('');
const filterVisibility = ref('');

const showDeleteConfirm = ref(false);
const deletingArticle = ref(null);

const fetchArticles = async (page = 1) => {
    loading.value = true;
    try {
        const params = {
            page,
            q: searchQuery.value,
            status: filterStatus.value,
            visibility: filterVisibility.value
        };
        const { data } = await axios.get('/kb/articles', { params });
        articles.value = data.data;
        meta.value = data.meta;
    } catch (err) {
        toast.error('Failed to load articles');
    } finally {
        loading.value = false;
    }
};

const confirmDelete = (article) => {
    deletingArticle.value = article;
    showDeleteConfirm.value = true;
};

const executeDelete = async () => {
    deleting.value = true;
    try {
        await axios.delete(`/kb/articles/${deletingArticle.value.slug}`);
        toast.info('Article deleted.');
        fetchArticles(meta.value?.current_page || 1);
    } catch {
        toast.error('Failed to delete article.');
    } finally {
        deleting.value = false;
        showDeleteConfirm.value = false;
        deletingArticle.value = null;
    }
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};

onMounted(() => {
    fetchArticles();
});
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>
