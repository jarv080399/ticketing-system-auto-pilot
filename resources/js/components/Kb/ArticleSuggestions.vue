<template>
    <div v-if="query.length > 2 && !loading && suggestions.length > 0" class="glass-card p-6 rounded-xl space-y-4 bg-linear-to-r from-primary/5 to-secondary/5 border-primary/20">
        <div class="flex items-center gap-3">
            <span class="text-xl">💡</span>
            <div>
                <h3 class="text-sm font-black text-text-main">Before you submit...</h3>
                <p class="text-xs text-text-dim">These articles might solve your issue immediately.</p>
            </div>
        </div>
        
        <div class="space-y-2">
            <router-link 
                v-for="article in suggestions" 
                :key="article.id"
                :to="'/kb/' + article.slug" 
                target="_blank"
                class="block p-4 rounded-lg bg-surface-light border border-glass-border hover:border-primary/50 hover:bg-primary/5 transition-all group"
            >
                <div class="flex items-center gap-3 w-full">
                    <div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary group-hover:scale-110 transition-transform shrink-0">
                        📚
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-text-main group-hover:text-primary transition-colors truncate">{{ article.title }}</p>
                        <p v-if="article.excerpt" class="text-xs text-text-dim truncate mt-0.5">{{ article.excerpt }}</p>
                    </div>
                    <div class="ml-auto flex items-center text-primary opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </div>
                </div>
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from '@/plugins/axios';
import _ from 'lodash';

const props = defineProps({
    query: {
        type: String,
        default: ''
    }
});

const suggestions = ref([]);
const loading = ref(false);

const fetchSuggestions = _.debounce(async (val) => {
    if (val.length < 3) {
        suggestions.value = [];
        return;
    }
    
    loading.value = true;
    try {
        const response = await axios.get('/kb/suggest', {
            params: { q: val }
        });
        suggestions.value = response.data.data;
    } catch (err) {
        console.error('Failed to fetch KB suggestions', err);
    } finally {
        loading.value = false;
    }
}, 500);

watch(() => props.query, (newVal) => {
    fetchSuggestions(newVal);
});
</script>
