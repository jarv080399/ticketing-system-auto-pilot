<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Activity Log</h1>
            <p class="text-text-dim text-sm mt-1">Audit trail for all administrative actions and system updates.</p>
        </div>

        <!-- Filters -->
        <div class="bg-surface border border-glass-border rounded-xl p-4 flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-text-dim mb-2">Action</label>
                <input v-model="filters.action" type="text" placeholder="Filter by action..." class="bg-background border border-glass-border rounded-lg px-4 py-2 text-sm text-white focus:outline-none focus:border-primary w-48" />
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-text-dim mb-2">User</label>
                <input v-model="filters.user_id" type="text" placeholder="User ID..." class="bg-background border border-glass-border rounded-lg px-4 py-2 text-sm text-white focus:outline-none focus:border-primary w-32" />
            </div>
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-text-dim mb-2">Entity Type</label>
                <select v-model="filters.entity_type" class="bg-background border border-glass-border rounded-lg px-4 py-2 text-sm text-white focus:outline-none focus:border-primary">
                    <option value="">All Entities</option>
                    <option value="App\Models\Ticket">Ticket</option>
                    <option value="App\Models\User">User</option>
                    <option value="App\Models\Asset">Asset</option>
                    <option value="App\Models\SystemSetting">Setting</option>
                    <option value="App\Models\AutomationRule">Automation</option>
                </select>
            </div>
            <button @click="fetchLogs(1)" class="px-6 py-2 bg-surface-light hover:bg-white/10 text-white font-bold rounded-lg transition-all text-sm h-[38px]">
                Apply Filters
            </button>
            <button @click="resetFilters" class="px-4 py-2 text-text-dim hover:text-white transition-colors text-sm h-[38px]">
                Reset
            </button>
        </div>

        <div v-if="loading" class="flex items-center justify-center p-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <div v-else class="space-y-4">
            <div class="bg-surface rounded-xl border border-glass-border overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-surface-light border-b border-glass-border">
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">Timestamp</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">User</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">Action</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">Entity</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-glass-border text-gray-300">
                        <tr v-for="log in logs" :key="log.id" class="hover:bg-white/5 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ formatDate(log.created_at) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-primary/20 text-primary rounded-full flex items-center justify-center text-[10px] font-bold">
                                        {{ log.user?.name?.charAt(0) || 'S' }}
                                    </div>
                                    <span class="text-white">{{ log.user?.name || 'System' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-surface-light text-text-dim group-hover:bg-primary/20 group-hover:text-primary transition-colors">
                                    {{ log.action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-text-dim">
                                {{ log.auditable_type?.split('\\').pop() }} #{{ log.auditable_id }}
                            </td>
                            <td class="px-6 py-4 text-sm">{{ log.ip_address }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Simple Pagination -->
            <div class="flex items-center justify-between">
                <span class="text-sm text-text-dim">Showing page {{ currentPage }} of {{ lastPage }}</span>
                <div class="flex gap-2">
                    <button 
                        @click="fetchLogs(currentPage - 1)" 
                        :disabled="currentPage === 1"
                        class="px-4 py-2 bg-surface-light text-white rounded-lg disabled:opacity-30"
                    >
                        Previous
                    </button>
                    <button 
                        @click="fetchLogs(currentPage + 1)" 
                        :disabled="currentPage === lastPage"
                        class="px-4 py-2 bg-surface-light text-white rounded-lg disabled:opacity-30"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '@/plugins/axios';

const loading = ref(true);
const logs = ref([]);
const currentPage = ref(1);
const lastPage = ref(1);
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
        logs.value = response.data.data;
        currentPage.value = response.data.current_page;
        lastPage.value = response.data.last_page;
    } catch (error) {
        console.error('Failed to load logs');
    } finally {
        loading.value = false;
    }
};

const resetFilters = () => {
    filters.value = { action: '', user_id: '', entity_type: '' };
    fetchLogs(1);
};

const formatDate = (dateStr) => {
    const d = new Date(dateStr);
    return d.toLocaleString();
};

onMounted(() => fetchLogs(1));
</script>
