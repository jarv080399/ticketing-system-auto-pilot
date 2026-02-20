<template>
    <div v-if="loading" class="w-full space-y-12 py-12">
        <div class="h-12 w-64 bg-surface-light animate-pulse rounded-xl"></div>
        <div class="h-64 glass-card animate-pulse rounded-xl"></div>
    </div>

    <div v-else-if="article" class="w-full space-y-12 pb-24">
        <!-- Header Actions -->
        <div class="flex items-center justify-between">
            <router-link to="/kb" class="flex items-center gap-2 text-text-dim hover:text-primary transition-colors group">
                <div class="p-2 rounded-lg group-hover:bg-primary/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </div>
                <span class="font-bold text-sm tracking-tight">Back to Library</span>
            </router-link>

            <div class="flex gap-3">
                 <button v-if="authStore.user?.role !== 'user'" @click="editArticle" class="px-6 py-2.5 bg-primary/20 text-primary rounded-xl text-xs font-black uppercase tracking-widest hover:bg-primary/30 transition-all">Edit Article</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
            <!-- Sidebar (TOC) -->
            <div class="lg:col-span-1 hidden lg:block space-y-8">
                <div class="sticky top-8">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-4">In this article</p>
                    <nav class="space-y-2 border-l-2 border-glass-border pl-4">
                        <a v-for="heading in tableOfContents" :key="heading.id" :href="'#' + heading.id" 
                           class="block text-sm font-medium text-text-dim hover:text-primary transition-colors py-1 truncate"
                           :class="{'pl-4 text-xs': heading.level > 2}">
                            {{ heading.text }}
                        </a>
                        <p v-if="!tableOfContents.length" class="text-xs italic text-text-dim">No headings found</p>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-12">
                <div class="glass-card p-12 rounded-xl relative overflow-hidden">
                    <div class="space-y-4 mb-10 pb-8 border-b border-glass-border">
                        <div class="flex items-center gap-3 flex-wrap">
                            <span v-if="article.visibility === 'internal'" class="px-3 py-1 bg-amber-500/20 text-amber-500 rounded-lg text-[10px] font-black uppercase tracking-widest">Internal Only</span>
                            <span v-if="article.category" class="text-xs font-black uppercase tracking-widest text-primary">{{ article.category.name }}</span>
                            <span v-if="article.category" class="w-1.5 h-1.5 rounded-full bg-glass-border"></span>
                            <span class="text-xs text-text-dim font-medium">Updated {{ formatTimeAgo(article.updated_at) }}</span>
                            <span class="w-1.5 h-1.5 rounded-full bg-glass-border"></span>
                            <span class="text-xs text-text-dim font-medium">{{ article.view_count }} views</span>
                        </div>
                        <h1 class="text-4xl font-black text-text-main tracking-tight leading-tight">{{ article.title }}</h1>
                        
                        <div v-if="article.tags?.length" class="flex flex-wrap gap-2 pt-2">
                            <span v-for="tag in article.tags" :key="tag.id" class="px-3 py-1 bg-surface-light border border-glass-border rounded-lg text-[10px] font-bold text-text-dim uppercase tracking-wider">
                                #{{ tag.name }}
                            </span>
                        </div>
                    </div>

                    <!-- Article Body -->
                    <!-- We render HTML directly. TipTap generates clean HTML. -->
                    <div class="prose prose-invert max-w-none prose-headings:font-black prose-a:text-primary hover:prose-a:text-primary-light prose-img:rounded-xl" v-html="processedContent">
                    </div>
                </div>

                <!-- Feedback Section -->
                <div class="glass-card p-8 rounded-xl text-center space-y-6 bg-linear-to-r from-primary/5 to-secondary/5">
                    <h3 class="text-lg font-black text-text-main">Was this article helpful?</h3>
                    <div v-if="!feedbackSubmitted" class="flex items-center justify-center gap-4">
                        <button @click="submitFeedback(true)" class="px-8 py-3 bg-white/5 border border-glass-border hover:bg-emerald-500/20 hover:text-emerald-400 hover:border-emerald-500/50 rounded-xl font-black transition-all flex items-center gap-2">
                            👍 Yes
                        </button>
                        <button @click="submitFeedback(false)" class="px-8 py-3 bg-white/5 border border-glass-border hover:bg-rose-500/20 hover:text-rose-400 hover:border-rose-500/50 rounded-xl font-black transition-all flex items-center gap-2">
                            👎 No
                        </button>
                    </div>
                    <div v-else class="text-emerald-400 font-bold flex items-center justify-center gap-2">
                        <span>✅</span> Thank you for your feedback!
                    </div>
                </div>

                <!-- Version History (Agent/Admin only) -->
                <div v-if="authStore.user?.role !== 'user' && article.versions?.length" class="glass-card p-8 rounded-xl space-y-4">
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-dim">Version History</h3>
                    <div class="space-y-2">
                        <div v-for="version in article.versions" :key="version.id" class="flex items-center justify-between p-3 rounded-lg bg-surface-light border border-glass-border">
                            <div>
                                <p class="text-sm font-bold text-text-main">v{{ version.version_number }} - {{ version.change_summary }}</p>
                                <p class="text-[10px] text-text-dim">by {{ version.editor?.name }} on {{ formatDate(version.created_at) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="py-24 text-center space-y-6">
        <h2 class="text-4xl font-black text-text-main">404</h2>
        <p class="text-text-dim">The article you are looking for is missing or you don't have permission to view it.</p>
        <router-link to="/kb" class="px-10 py-4 bg-primary text-white rounded-lg inline-block">Back to KB</router-link>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, nextTick } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import axios from '@/plugins/axios';
import { useToast } from 'vue-toastification';

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();
const toast = useToast();

const article = ref(null);
const loading = ref(true);
const feedbackSubmitted = ref(false);
const tableOfContents = ref([]);

onMounted(async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/kb/articles/${route.params.slug}`);
        article.value = response.data.data;
        
        if (authStore.user?.role !== 'user') {
            const versionsResponse = await axios.get(`/kb/articles/${route.params.slug}/versions`);
            article.value.versions = versionsResponse.data.data;
        }

        await nextTick();
        generateTOC();
    } catch (err) {
        console.error('Failed to load article', err);
    } finally {
        loading.value = false;
    }
});

const processedContent = computed(() => {
    if (!article.value?.content) return '';
    // Very basic markdown handling if you stored raw markdown instead of html.
    // Assuming TipTap output is HTML, we just inject IDs to headings.
    let content = article.value.content;
    
    // Add IDs to h2, h3 for TOC anchoring (basic regex approach if it's raw HTML)
    // Note: A real DOM parser is safer, but this works for basic HTML strings.
    let headingCounter = 0;
    content = content.replace(/<(h[2-3])([^>]*)>(.*?)<\/\1>/gi, (match, tag, attrs, text) => {
        headingCounter++;
        const id = `heading-${headingCounter}`;
        return `<${tag} id="${id}"${attrs}>${text}</${tag}>`;
    });
    
    return content;
});

const generateTOC = () => {
    tableOfContents.value = [];
    const elements = document.querySelectorAll('.prose h2, .prose h3');
    elements.forEach(el => {
        tableOfContents.value.push({
            id: el.id,
            text: el.innerText,
            level: parseInt(el.tagName.replace('H', ''))
        });
    });
};

const submitFeedback = async (isHelpful) => {
    try {
        await axios.post(`/kb/articles/${article.value.slug}/feedback`, {
            helpful: isHelpful
        });
        feedbackSubmitted.value = true;
    } catch (err) {
        toast.error('Failed to submit feedback');
    }
};

const editArticle = () => {
    router.push(`/agent/kb/edit/${article.value.slug}`);
};

const formatTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / 1000 / 60);

    if (diffInMinutes < 60) return `${diffInMinutes || 1}m ago`;
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
    return `${Math.floor(diffInMinutes / 1440)}d ago`;
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric'
    });
};
</script>
