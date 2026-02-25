<template>
    <div class="space-y-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div v-for="stat in stats" :key="stat.label" class="glass-card p-8 rounded-xl hover-lift group">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-surface-light flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        {{ stat.icon }}
                    </div>
                    <span class="text-xs font-black" :class="stat.trendColor">{{ stat.trend }}</span>
                </div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-1">{{ stat.label }}</p>
                <h3 class="text-3xl font-black text-text-main">{{ stat.value }}</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Critical Updates -->
            <div class="lg:col-span-2 space-y-8">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-dim">Critical Attention Required</h3>
                    <router-link to="/agent/queue" class="text-[10px] font-black text-primary uppercase tracking-widest hover:underline">View Full Queue</router-link>
                </div>

                <div class="glass-card rounded-xl overflow-hidden border-glass-border">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-light/50 border-b border-glass-border">
                                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Ticket</th>
                                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Priority</th>
                                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Time Left</th>
                                <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-glass-border">
                            <tr v-for="ticket in criticalTickets" :key="ticket.id" class="hover:bg-white/5 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-surface-light flex items-center justify-center text-xl">
                                            {{ ticket.category?.icon || '🎫' }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-text-main text-sm">{{ ticket.title }}</p>
                                            <p class="text-[10px] text-text-dim uppercase tracking-widest">{{ ticket.ticket_number }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 bg-red-500/10 text-red-500 border border-red-500/20 rounded-lg text-[9px] font-bold uppercase tracking-widest">
                                        {{ ticket.priority }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></div>
                                        <span class="text-xs font-bold text-text-main">42m</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <router-link :to="`/agent/tickets/${ticket.ticket_number}`" class="px-6 py-2 bg-primary text-white text-[9px] font-black uppercase tracking-widest rounded-lg hover:shadow-lg hover:shadow-primary/20 transition-all">Command</router-link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Agent Performance Sidebar -->
            <div class="space-y-8">
                <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-dim">Live SLA Health</h3>
                <div class="glass-card p-10 rounded-xl space-y-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-widest text-text-dim mb-1">Weekly Target</p>
                            <h4 class="text-2xl font-black text-text-main">92.4%</h4>
                        </div>
                        <div class="w-16 h-16 rounded-lg border-4 border-emerald-500/20 border-t-emerald-500 flex items-center justify-center font-black text-emerald-500 text-xs shadow-lg shadow-emerald-500/20">
                            +4%
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div v-for="sla in slaMetrics" :key="sla.label" class="space-y-2">
                            <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                                <span class="text-text-dim">{{ sla.label }}</span>
                                <span class="text-text-main">{{ sla.value }}%</span>
                            </div>
                            <div class="h-1.5 bg-surface-light rounded-full overflow-hidden">
                                <div class="h-full bg-primary rounded-full" :style="{ width: sla.value + '%' }"></div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-glass-border">
                        <button class="w-full py-4 glass-card border-glass-border rounded-lg text-[10px] font-black uppercase tracking-widest text-text-dim hover:bg-white/5 transition-all">Detailed Analytics</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useTicketStore } from '@/stores/tickets';

const ticketStore = useTicketStore();
const criticalTickets = ref([]);

const stats = ref([
    { label: 'Assigned To Me', value: '-', icon: '👤', trend: '...', trendColor: 'text-emerald-500' },
    { label: 'In Queue', value: '-', icon: '📥', trend: '...', trendColor: 'text-emerald-500' },
    { label: 'Avg Resolution', value: '-', icon: '⚡', trend: '...', trendColor: 'text-red-500' },
    { label: 'Customer CSAT', value: '-', icon: '⭐️', trend: '...', trendColor: 'text-primary' },
]);

const slaMetrics = ref([
    { label: 'Initial Response', value: 0 },
    { label: 'First Contact Resolve', value: 0 },
    { label: 'Resolution Time', value: 0 },
]);

onMounted(async () => {
    fetchDashboardData();
    const response = await ticketStore.fetchMyTickets({ priority: 'high', per_page: 5 });
    criticalTickets.value = response.data;
});

const fetchDashboardData = async () => {
    try {
        const { data } = await axios.get('/agent/dashboard');
        stats.value = data.stats;
        slaMetrics.value = data.slaMetrics;
    } catch (err) {
        console.error('Failed to load agent dashboard data', err);
    }
};
</script>
