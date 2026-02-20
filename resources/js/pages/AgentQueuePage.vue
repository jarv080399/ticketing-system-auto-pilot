<template>
    <div class="space-y-12 pb-12">
        <!-- Queue Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="space-y-4">
                <h1 class="text-4xl font-black tracking-tight text-text-main">
                    Priority <span class="text-gradient">Queue</span>
                </h1>
                <p class="text-text-dim font-medium">Manage incoming requests and service efficiency.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="glass-card p-1.5 rounded-2xl flex gap-1">
                    <button 
                        v-for="f in queueFilters" :key="f.label"
                        @click="activeQueue = f.value"
                        class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-[0.1em] transition-all duration-300"
                        :class="activeQueue === f.value ? 'bg-primary text-white shadow-xl shadow-primary/20' : 'text-text-dim hover:bg-white/5'"
                    >
                        {{ f.label }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div class="glass-card rounded-[3rem] overflow-hidden border-glass-border">
            <div v-if="loading" class="p-20 flex flex-col items-center justify-center space-y-4">
                <div class="w-12 h-12 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                <p class="text-[10px] font-black uppercase tracking-widest text-text-dim">Synchronizing Queue...</p>
            </div>

            <div v-else-if="tickets.length === 0" class="p-40 text-center space-y-6">
                <div class="w-24 h-24 bg-surface-light rounded-full flex items-center justify-center mx-auto text-5xl opacity-40">🍃</div>
                <div class="space-y-2">
                    <h3 class="text-2xl font-black text-text-main">Queue Depleted</h3>
                    <p class="text-text-dim max-w-sm mx-auto">No tickets match your current filters. Great job!</p>
                </div>
            </div>

            <table v-else class="w-full text-left">
                <thead>
                    <tr class="bg-surface-light/50 border-b border-glass-border">
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-text-dim">Request Details</th>
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-text-dim">Category</th>
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-text-dim">Priority</th>
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-text-dim">Assignee</th>
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-text-dim">Time</th>
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-[0.2em] text-text-dim text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-glass-border">
                    <tr v-for="ticket in tickets" :key="ticket.id" class="hover:bg-primary/[0.02] transition-colors group">
                        <td class="px-10 py-8">
                            <div class="flex items-center gap-5">
                                <div class="w-12 h-12 rounded-2xl bg-surface-light flex items-center justify-center text-2xl group-hover:scale-110 transition-transform shadow-sm">
                                    {{ ticket.category?.icon || '🎫' }}
                                </div>
                                <div>
                                    <p class="font-bold text-text-main text-lg line-clamp-1 mb-0.5">{{ ticket.title }}</p>
                                    <div class="flex items-center gap-3">
                                        <span class="text-[10px] font-black text-primary uppercase tracking-widest">{{ ticket.ticket_number }}</span>
                                        <span class="w-1 h-1 rounded-full bg-glass-border"></span>
                                        <span class="text-[10px] text-text-dim font-bold uppercase tracking-widest">{{ ticket.requester?.name }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <span class="text-xs font-bold text-text-main">{{ ticket.category?.name }}</span>
                        </td>
                        <td class="px-10 py-8">
                            <div :class="getPriorityClass(ticket.priority)" class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border border-current inline-block">
                                {{ ticket.priority }}
                            </div>
                        </td>
                        <td class="px-10 py-8">
                            <div v-if="ticket.agent" class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-surface-light flex items-center justify-center font-black text-primary text-[10px] border border-glass-border">
                                    {{ ticket.agent.name.charAt(0) }}
                                </div>
                                <span class="text-xs font-bold text-text-main">{{ ticket.agent.name }}</span>
                            </div>
                            <span v-else class="text-[10px] font-black uppercase tracking-widest text-text-dim italic">Waiting...</span>
                        </td>
                        <td class="px-10 py-8">
                            <div class="space-y-1">
                                <p class="text-xs font-bold text-text-main">{{ formatTimeAgo(ticket.created_at) }}</p>
                                <p class="text-[9px] text-text-dim uppercase tracking-widest">Received</p>
                            </div>
                        </td>
                        <td class="px-10 py-8 text-right">
                            <router-link 
                                :to="`/agent/tickets/${ticket.ticket_number}`" 
                                class="inline-flex h-12 w-12 items-center justify-center bg-surface-light text-text-dim rounded-xl hover:bg-primary hover:text-white hover:shadow-xl hover:shadow-primary/30 transition-all group-hover:bg-primary group-hover:text-white"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </router-link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useTicketStore } from '@/stores/tickets';

const ticketStore = useTicketStore();
const activeQueue = ref('all');
const tickets = ref([]);
const loading = ref(true);

const queueFilters = [
    { label: 'All Open', value: 'all' },
    { label: 'My Desk', value: 'mine' },
    { label: 'Unassigned', value: 'unassigned' },
    { label: 'Critical', value: 'critical' },
];

const loadQueue = async () => {
    loading.value = true;
    const params = {
        per_page: 50,
    };

    if (activeQueue.value === 'mine') {
        params.agent_id = ticketStore.user?.id; // Assuming user info is available
    } else if (activeQueue.value === 'unassigned') {
        params.agent_id = 'unassigned';
    } else if (activeQueue.value === 'critical') {
        params.priority = 'critical';
    }

    try {
        const response = await ticketStore.fetchMyTickets(params);
        tickets.value = response.data;
    } finally {
        loading.value = false;
    }
};

onMounted(loadQueue);
watch(activeQueue, loadQueue);

const formatTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / 1000 / 60);

    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
    return date.toLocaleDateString();
};

const getPriorityClass = (priority) => {
    switch (priority) {
        case 'low': return 'text-blue-400 border-blue-500/20 bg-blue-500/5';
        case 'medium': return 'text-primary border-primary/20 bg-primary/5';
        case 'high': return 'text-amber-500 border-amber-500/20 bg-amber-500/5';
        case 'critical': return 'text-red-500 border-red-500/20 bg-red-500/5';
        default: return 'text-text-dim border-glass-border';
    }
};
</script>
