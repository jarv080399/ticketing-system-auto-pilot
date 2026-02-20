<template>
    <div class="relative w-full max-w-2xl group">
        <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-text-dim group-focus-within:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <input 
            v-model="query"
            @input="handleInput"
            type="text" 
            placeholder="Search tickets, knowledge base, or assets..."
            class="w-full bg-surface-light/50 border border-glass-border rounded-lg py-5 pl-16 pr-6 text-text-main placeholder:text-text-dim/50 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition-all shadow-xl shadow-black/5"
        />
        
        <!-- Results Dropdown -->
        <transition enter-active-class="transition duration-200 ease-out" enter-from-class="opacity-0 translate-y-2" enter-to-class="opacity-100 translate-y-0">
            <div v-if="results.length > 0 && query.length > 0" class="absolute top-full left-0 w-full mt-4 glass-card rounded-xl shadow-2xl overflow-hidden z-[60] py-4 border-glass-border">
                <div class="px-6 py-2">
                    <p class="text-[10px] font-black uppercase tracking-[0.3em] text-text-dim">Search Results</p>
                </div>
                
                <div class="space-y-1">
                    <router-link 
                        v-for="ticket in results" :key="ticket.id"
                        :to="`/tickets/${ticket.ticket_number}`"
                        class="flex items-center gap-4 px-6 py-4 hover:bg-primary/10 transition-colors group"
                        @click="query = ''"
                    >
                        <div class="w-10 h-10 rounded-xl bg-surface-light flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                            {{ ticket.category?.icon || '🎫' }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-text-main group-hover:text-primary transition-colors">{{ ticket.title }}</p>
                            <p class="text-[10px] text-text-dim uppercase tracking-widest">{{ ticket.ticket_number }} • {{ ticket.status.replace('_', ' ') }}</p>
                        </div>
                    </router-link>
                </div>
                
                <div class="px-6 py-4 border-t border-glass-border mt-2">
                    <p class="text-xs text-center text-text-dim">Press <span class="px-2 py-0.5 bg-surface-light rounded-lg border border-glass-border text-text-main">Enter</span> for advanced search</p>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from '@/plugins/axios';
import _ from 'lodash';

const query = ref('');
const results = ref([]);
const loading = ref(false);

const handleInput = _.debounce(async () => {
    if (query.value.length < 2) {
        results.value = [];
        return;
    }

    loading.value = true;
    try {
        const response = await axios.get('/search', { params: { q: query.value } });
        results.value = response.data.data;
    } catch (err) {
        console.error('Search failed', err);
    } finally {
        loading.value = false;
    }
}, 300);
</script>
