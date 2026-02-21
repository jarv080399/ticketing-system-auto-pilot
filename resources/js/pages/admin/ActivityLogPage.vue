<template>
    <div class="space-y-6 pb-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Audit Log</h1>
                <p class="text-text-dim text-sm mt-2">Immutable audit trail for all administrative actions, configurations, and system updates.</p>
            </div>
            <button class="px-5 py-2.5 bg-background border border-glass-border hover:bg-surface-light shadow-[inset_0_1px_0_rgba(255,255,255,0.05)] text-text-dim hover:text-text-main font-black text-sm rounded-xl transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Export CSV
            </button>
        </div>

        <!-- Filters Section -->
        <div class="bg-surface/50 border border-glass-border rounded-2xl p-5 flex flex-wrap gap-4 items-end shadow-sm">
            <div class="w-full sm:w-auto">
                <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2">Action Keyword</label>
                <input v-model="filters.action" type="text" placeholder="e.g. created, updated..." class="w-full sm:w-48 bg-background border border-glass-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/50 transition-all shadow-inner" @keyup.enter="applyFilters" />
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2">User ID</label>
                <input v-model="filters.user_id" type="number" placeholder="User ID..." class="w-full sm:w-32 bg-background border border-glass-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/50 transition-all shadow-inner" @keyup.enter="applyFilters" />
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2">Entity Type</label>
                <select v-model="filters.entity_type" class="w-full bg-background border border-glass-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/50 transition-all shadow-inner" @change="applyFilters">
                    <option value="">All Supported Entities</option>
                    <option value="App\Models\Ticket">Ticket</option>
                    <option value="App\Models\User">User</option>
                    <option value="App\Models\Asset">Asset</option>
                    <option value="App\Models\SystemSetting">Setting</option>
                    <option value="App\Models\AutomationRule">Automation</option>
                </select>
            </div>
            <button @click="applyFilters" class="px-6 py-2.5 bg-surface-light border border-glass-border hover:bg-white/10 text-white font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-text-dim" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                Apply
            </button>
            <button @click="resetFilters" class="px-4 py-2.5 text-text-dim hover:text-text-main transition-colors text-sm font-bold">
                Clear
            </button>
        </div>

        <div v-if="loading" class="flex items-center justify-center p-24">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <div v-else class="space-y-4">
            <div class="bg-surface rounded-2xl border border-glass-border shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="bg-surface-light/50 border-b border-glass-border">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Timestamp</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">User</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Action</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Entity Type</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Node Reference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-glass-border text-gray-300">
                            <tr v-for="log in logs" :key="log.id" class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4 text-sm tabular-nums text-text-dim group-hover:text-text-main transition-colors">
                                    {{ formatDate(log.created_at) }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-background border border-glass-border text-primary rounded-full flex items-center justify-center text-xs font-black">
                                            {{ log.user?.name?.charAt(0) || 'S' }}
                                        </div>
                                        <div>
                                            <div class="text-white font-bold tracking-wide">{{ log.user?.name || 'System Auto Pilot' }}</div>
                                            <div class="text-[10px] text-text-dim">UID: {{ log.user_id || 'SYS' }} • {{ log.ip_address }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span 
                                        :class="[
                                            'px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest border',
                                            log.action.includes('deleted') ? 'bg-red-500/10 text-red-400 border-red-500/20' : 
                                            log.action.includes('created') ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 
                                            log.action.includes('updated') ? 'bg-primary/10 text-primary border-primary/20' : 
                                            'bg-surface-light text-text-dim border-glass-border'
                                        ]"
                                    >
                                        {{ log.action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-white tracking-wide">
                                    {{ log.auditable_type?.split('\\').pop() || 'Unknown' }}
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-text-dim">
                                    #{{ log.auditable_id }}
                                </td>
                            </tr>
                            <tr v-if="logs.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-text-dim font-medium text-sm">
                                    No audit entries found matching your criteria.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Datatable Pagination -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-2">
                <span class="text-xs font-bold uppercase tracking-widest text-text-dim">
                    Showing page {{ currentPage }} of {{ lastPage }} <span class="text-gray-500 ml-1" v-if="total > 0">({{ total }} total logs)</span>
                </span>
                <div class="flex items-center gap-1.5 bg-surface border border-glass-border rounded-xl p-1 shadow-sm">
                    <button 
                        @click="changePage(currentPage - 1)" 
                        :disabled="currentPage === 1"
                        class="px-3 py-1.5 text-xs font-bold text-white rounded-lg hover:bg-surface-light transition-colors disabled:opacity-30 disabled:hover:bg-transparent"
                    >
                        Prev
                    </button>
                    
                    <div class="px-2 flex gap-1">
                        <!-- Only show limited page numbers for simplicity -->
                        <button 
                            v-for="page in getPageNumbers" 
                            :key="page"
                            @click="changePage(page)"
                            :class="[
                                'w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold transition-all',
                                page === currentPage ? 'bg-primary text-white shadow-md' : 'text-text-dim hover:text-text-main hover:bg-surface-light'
                            ]"
                        >
                            {{ page }}
                        </button>
                    </div>

                    <button 
                        @click="changePage(currentPage + 1)" 
                        :disabled="currentPage === lastPage"
                        class="px-3 py-1.5 text-xs font-bold text-white rounded-lg hover:bg-surface-light transition-colors disabled:opacity-30 disabled:hover:bg-transparent"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from '@/plugins/axios';

const loading = ref(true);
const logs = ref([]);
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);

const filters = ref({
    action: '',
    user_id: '',
    entity_type: ''
});

const fetchLogs = async (page = 1) => {
    loading.value = true;
    try {
        const response = await axios.get('/admin/activity-log', {
            params: { ...filters.value, page }
        });
        
        if (response.data && response.data.current_page !== undefined) {
             logs.value = response.data.data;
             currentPage.value = response.data.current_page;
             lastPage.value = response.data.last_page;
             total.value = response.data.total;
        } else if (response.data.data && response.data.data.current_page !== undefined) {
             logs.value = response.data.data.data;
             currentPage.value = response.data.data.current_page;
             lastPage.value = response.data.data.last_page;
             total.value = response.data.data.total;
        } else {
             logs.value = Array.isArray(response.data) ? response.data : response.data.data || [];
        }
    } catch (error) {
        console.error('Failed to load logs');
    } finally {
        loading.value = false;
    }
};

const applyFilters = () => {
    fetchLogs(1);
};

const resetFilters = () => {
    filters.value = { action: '', user_id: '', entity_type: '' };
    fetchLogs(1);
};

const changePage = (page) => {
    if (page >= 1 && page <= lastPage.value) {
        fetchLogs(page);
    }
};

const getPageNumbers = computed(() => {
    let pages = [];
    const maxPagesToShow = 5;
    
    if (lastPage.value <= maxPagesToShow) {
        for (let i = 1; i <= lastPage.value; i++) pages.push(i);
    } else {
        pages.push(1);
        
        let start = Math.max(2, currentPage.value - 1);
        let end = Math.min(lastPage.value - 1, currentPage.value + 1);
        
        if (currentPage.value === 1) end = 3;
        if (currentPage.value === lastPage.value) start = lastPage.value - 2;
        
        for (let i = start; i <= end; i++) pages.push(i);
        
        if (end < lastPage.value - 1) { /* push dots visually in actual table component logic */ }
        pages.push(lastPage.value);
    }
    
    // De-duplicate in edge cases
    return [...new Set(pages)].sort((a,b)=>a-b);
});

const formatDate = (dateStr) => {
    if (!dateStr) return 'N/A';
    const d = new Date(dateStr);
    return new Intl.DateTimeFormat('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true
    }).format(d);
};

onMounted(() => fetchLogs(1));
</script>
