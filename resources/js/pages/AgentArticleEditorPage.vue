<template>
    <div class="space-y-8 animate-fade-in pb-24">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-text-main tracking-tight">{{ isEditing ? 'Edit Article' : 'New Article' }}</h1>
                <p class="text-text-dim text-sm mt-1">Manage knowledge base documentation</p>
            </div>
            <div class="flex gap-4">
                <router-link to="/kb" class="px-6 py-2 rounded-xl text-sm font-bold text-text-dim hover:text-text-main transition-colors">Discard</router-link>
                <button @click="save" :disabled="loading" class="px-8 py-2 bg-primary hover:bg-primary-dark text-white font-black rounded-xl shadow-lg shadow-primary/20 hover-lift active:scale-95 transition-all disabled:opacity-50">
                    {{ loading ? 'Saving...' : 'Save Article' }}
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Main Content Area -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Title -->
                <div class="glass-card p-6 rounded-xl space-y-2">
                    <label class="text-[10px] font-black text-text-dim uppercase tracking-widest ml-1">Article Title *</label>
                    <input type="text" v-model="form.title" placeholder="e.g. How to connect to the office VPN" class="w-full bg-surface-light/50 border border-glass-border rounded-lg px-4 py-3 text-lg font-bold text-text-main focus:outline-none focus:border-primary/50 transition-colors" />
                </div>
                
                <!-- Editor -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-text-dim uppercase tracking-widest ml-1">Article Content *</label>
                    <ArticleEditor v-model="form.content" />
                </div>

                <!-- Update Summary (Only shown on Edit) -->
                <div v-if="isEditing" class="glass-card p-6 rounded-xl bg-surface-light border border-primary/20 space-y-2">
                    <label class="text-[10px] font-black text-primary uppercase tracking-widest ml-1">Change Summary (Required for Version History)</label>
                    <input type="text" v-model="form.change_summary" placeholder="e.g. Added section on Mac OS support" class="w-full bg-surface-dark border border-glass-border rounded-lg px-4 py-3 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-colors" required />
                </div>
            </div>

            <!-- Sidebar (Metadata) -->
            <div class="lg:col-span-1 space-y-6 lg:sticky lg:top-8 self-start">
                
                <!-- Visibility & Status -->
                <div class="glass-card p-6 rounded-xl space-y-6">
                    <h3 class="text-xs font-black uppercase tracking-widest text-text-dim pb-4 border-b border-glass-border">Publishing</h3>
                    
                    <!-- Status -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-text-dim uppercase tracking-widest">Status</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button @click="form.status = 'draft'" :class="form.status === 'draft' ? 'bg-amber-500/20 text-amber-500 border-amber-500/30' : 'bg-surface-light border-glass-border text-text-dim'" class="px-4 py-2 rounded-lg border text-xs font-bold transition-all">Draft</button>
                            <button @click="form.status = 'published'" :class="form.status === 'published' ? 'bg-emerald-500/20 text-emerald-500 border-emerald-500/30' : 'bg-surface-light border-glass-border text-text-dim'" class="px-4 py-2 rounded-lg border text-xs font-bold transition-all">Published</button>
                        </div>
                    </div>

                    <!-- Visibility -->
                    <div class="space-y-3 pt-2">
                        <label class="text-[10px] font-black text-text-dim uppercase tracking-widest">Visibility</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button @click="form.visibility = 'public'" :class="form.visibility === 'public' ? 'bg-primary/20 text-primary border-primary/30' : 'bg-surface-light border-glass-border text-text-dim'" class="px-4 py-2 rounded-lg border text-xs font-bold transition-all">Public</button>
                            <button @click="form.visibility = 'internal'" :class="form.visibility === 'internal' ? 'bg-rose-500/20 text-rose-500 border-rose-500/30' : 'bg-surface-light border-glass-border text-text-dim'" class="px-4 py-2 rounded-lg border text-xs font-bold transition-all">Internal</button>
                        </div>
                        <p class="text-[10px] text-text-dim leading-relaxed">
                            <span v-if="form.visibility === 'public'">Visible to everyone, including end-users.</span>
                            <span v-else>Visible only to Agents and Admins. For internal procedures.</span>
                        </p>
                    </div>
                </div>

                <!-- Organization -->
                <div class="glass-card p-6 rounded-xl space-y-6">
                    <h3 class="text-xs font-black uppercase tracking-widest text-text-dim pb-4 border-b border-glass-border">Organization</h3>
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-text-dim uppercase tracking-widest">Category</label>
                        <select v-model="form.category_id" class="w-full bg-surface-light border border-glass-border rounded-lg px-4 py-2 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-colors appearance-none">
                            <option :value="null">No Category</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-text-dim uppercase tracking-widest">Excerpt</label>
                        <textarea v-model="form.excerpt" rows="3" placeholder="Brief summary for search results..." class="w-full bg-surface-light border border-glass-border rounded-lg px-4 py-2 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-colors resize-none"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from '@/plugins/axios';
import { useToast } from 'vue-toastification';
import ArticleEditor from '@/components/Kb/ArticleEditor.vue';

const route = useRoute();
const router = useRouter();
const toast = useToast();

const isEditing = computed(() => !!route.params.slug);
const loading = ref(false);
const categories = ref([]);

const form = ref({
    title: '',
    content: '',
    excerpt: '',
    visibility: 'public',
    status: 'draft',
    category_id: null,
    change_summary: '', // required for updates
});

onMounted(async () => {
    await fetchCategories();
    if (isEditing.value) {
        await loadArticle();
    }
});

const fetchCategories = async () => {
    try {
        const response = await axios.get('/kb/categories');
        categories.value = response.data.data;
    } catch (err) {
        console.error('Failed to load categories', err);
    }
};

const loadArticle = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/kb/articles/${route.params.slug}`);
        const article = response.data.data;
        form.value = {
            title: article.title,
            content: article.content,
            excerpt: article.excerpt,
            visibility: article.visibility,
            status: article.status,
            category_id: article.category_id,
            change_summary: '', // Clear it for the new edit
        };
    } catch (err) {
        toast.error('Failed to load article');
        router.push('/kb');
    } finally {
        loading.value = false;
    }
};

const save = async () => {
    if (!form.value.title || !form.value.content) {
        toast.error('Title and content are required.');
        return;
    }
    if (isEditing.value && !form.value.change_summary) {
        toast.error('Change summary is required when updating an article.');
        return;
    }

    loading.value = true;
    try {
        let response;
        if (isEditing.value) {
            response = await axios.put(`/kb/articles/${route.params.slug}`, form.value);
            toast.success('Article updated successfully');
        } else {
            response = await axios.post('/kb/articles', form.value);
            toast.success('Article created successfully');
        }
        
        // Go to article view
        router.push(`/kb/${response.data.data.slug}`);
    } catch (err) {
        console.error(err);
        toast.error(err.response?.data?.message || 'Error saving article');
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.4s ease-out; }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
