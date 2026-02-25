<template>
    <div class="space-y-6 pb-10">
        <!-- Page Header -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold text-text-main">Audit Log</h1>
                <p class="mt-1 text-sm text-text-dim">Immutable audit trail for all administrative actions, configurations, and system updates.</p>
            </div>
            <button class="bg-surface-light hover:bg-surface text-text-dim hover:text-text-main text-sm font-semibold px-4 py-2 rounded-lg transition-colors border border-glass-border flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                Export CSV
            </button>
        </div>

        <!-- Filters — Section Panel -->
        <div class="bg-surface border border-glass-border rounded-xl p-6">
            <h2 class="text-[11px] font-semibold tracking-widest text-text-dim uppercase mb-4">Filters</h2>
            <div class="flex flex-wrap gap-4 items-end">
                <div class="w-full sm:w-auto space-y-1">
                    <label class="text-sm font-medium text-text-main">Action Keyword</label>
                    <input v-model="filters.action" type="text" placeholder="e.g. created, updated..." class="w-full sm:w-48 bg-background border border-glass-border rounded-lg px-4 py-2.5 text-sm text-text-main placeholder-text-dim focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" @keyup.enter="applyFilters" />
                </div>
                <div class="w-full sm:w-auto space-y-1">
                    <label class="text-sm font-medium text-text-main">User ID</label>
                    <input v-model="filters.user_id" type="number" placeholder="User ID..." class="w-full sm:w-32 bg-background border border-glass-border rounded-lg px-4 py-2.5 text-sm text-text-main placeholder-text-dim focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" @keyup.enter="applyFilters" />
                </div>
                <div class="flex-1 min-w-[200px] space-y-1">
                    <label class="text-sm font-medium text-text-main">Entity Type</label>
                    <select v-model="filters.entity_type" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2.5 text-sm text-text-main focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" @change="applyFilters">
                        <option value="">All Entities</option>
                        <option value="App\Models\Ticket">Ticket</option>
                        <option value="App\Models\User">User</option>
                        <option value="App\Models\Asset">Asset</option>
                        <option value="App\Models\SystemSetting">Setting</option>
                        <option value="App\Models\AutomationRule">Automation</option>
                    </select>
                </div>
                <button @click="applyFilters" class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                    Apply
                </button>
                <button @click="resetFilters" class="text-text-dim hover:text-text-main transition-colors text-sm font-semibold px-3 py-2.5">
                    Clear
                </button>
            </div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex items-center justify-center p-24">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <div v-else class="space-y-4">
            <!-- Data Table -->
            <div class="bg-surface rounded-xl border border-glass-border overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="bg-surface-light border-b border-glass-border">
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Timestamp</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">User</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Action</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Entity Type</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-glass-border">
                            <tr v-for="log in logs" :key="log.id" class="hover:bg-surface-light/40 transition-colors group">
                                <td class="px-6 py-4 text-sm tabular-nums text-text-dim group-hover:text-text-main transition-colors">
                                    {{ formatDate(log.created_at) }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-surface-light border border-glass-border rounded-full flex items-center justify-center text-xs font-bold text-text-main">
                                            {{ log.user?.name?.charAt(0) || 'S' }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-text-main">{{ log.user?.name || 'System Autopilot' }}</div>
                                            <div class="text-[10px] text-text-dim">UID: {{ log.user_id || 'SYS' }} · {{ log.ip_address }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <!-- Status badge per skill spec table -->
                                    <span
                                        :class="[
                                            'text-[10px] font-bold tracking-widest px-2 py-0.5 rounded-full uppercase border',
                                            log.action.includes('deleted') ? 'bg-red-500/20 text-red-400 border-red-500/30' :
                                            log.action.includes('created') ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' :
                                            log.action.includes('updated') ? 'bg-amber-500/20 text-amber-400 border-amber-500/30' :
                                            'bg-slate-500/20 text-slate-400 border-slate-500/30'
                                        ]"
                                    >
                                        {{ log.action }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-text-main">
                                    {{ log.auditable_type?.split('\\').pop() || 'Unknown' }}
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-text-dim">
                                    #{{ log.auditable_id }}
                                </td>
                            </tr>
                            <tr v-if="logs.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-text-dim text-sm">
                                    No audit entries found matching your criteria.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-2">
                <span class="text-[10px] font-bold uppercase tracking-widest text-text-dim">
                    Showing page {{ currentPage }} of {{ lastPage }}
                    <span class="text-text-dim ml-1" v-if="total > 0">({{ total }} total)</span>
                </span>
                <div class="flex items-center gap-1.5 bg-surface border border-glass-border rounded-lg p-1">
                    <button
                        @click="changePage(currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="px-3 py-1.5 text-xs font-semibold text-text-dim hover:text-text-main rounded-md hover:bg-surface-light transition-colors disabled:opacity-30"
                    >Prev</button>

                    <div class="px-1 flex gap-1">
                        <button
                            v-for="page in getPageNumbers"
                            :key="page"
                            @click="changePage(page)"
                            :class="[
                                'w-8 h-8 rounded-md flex items-center justify-center text-xs font-bold transition-all',
                                page === currentPage ? 'bg-primary text-white' : 'text-text-dim hover:text-text-main hover:bg-surface-light'
                            ]"
                        >{{ page }}</button>
                    </div>

                    <button
                        @click="changePage(currentPage + 1)"
                        :disabled="currentPage === lastPage"
                        class="px-3 py-1.5 text-xs font-semibold text-text-dim hover:text-text-main rounded-md hover:bg-surface-light transition-colors disabled:opacity-30"
                    >Next</button>
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
        pages.push(lastPage.value);
    }
    return [...new Set(pages)].sort((a, b) => a - b);
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
