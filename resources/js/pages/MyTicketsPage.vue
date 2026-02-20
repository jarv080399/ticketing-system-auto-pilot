<template>
    <div class="space-y-12 pb-12">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="space-y-4">
                <h1 class="text-4xl font-black tracking-tight text-text-main">
                    My <span class="text-gradient">Requests</span>
                </h1>
                <p class="text-text-dim font-medium">Track and manage your IT support history.</p>
            </div>
            
            <!-- Filters -->
            <div class="flex items-center gap-3">
                <div class="glass-card p-1.5 rounded-lg flex gap-1">
                    <button 
                        v-for="f in filterOptions" :key="f.label"
                        @click="activeStatus = f.value"
                        class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-widest transition-all duration-300"
                        :class="activeStatus === f.value ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-text-dim hover:bg-white/5'"
                    >
                        {{ f.label }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Tickets List -->
        <div v-if="loading" class="grid grid-cols-1 gap-6">
            <div v-for="n in 3" :key="n" class="glass-card h-40 animate-pulse rounded-xl"></div>
        </div>

        <div v-else-if="tickets.length === 0" class="glass-card p-20 text-center space-y-6 rounded-xl">
            <div class="w-24 h-24 bg-surface-light rounded-full flex items-center justify-center mx-auto text-5xl">📦</div>
            <div class="space-y-2">
                <h3 class="text-2xl font-black text-text-main">Clear as Day</h3>
                <p class="text-text-dim max-w-sm mx-auto">You don't have any active tickets in this category. Need help with something?</p>
            </div>
            <router-link to="/tickets/new" class="inline-flex px-10 py-4 bg-primary text-white font-black rounded-lg shadow-xl shadow-primary/20 hover-lift">Submit First Request</router-link>
        </div>

        <div v-else class="grid grid-cols-1 gap-6">
            <router-link 
                v-for="ticket in tickets" :key="ticket.id" 
                :to="`/tickets/${ticket.ticket_number}`"
                class="glass-card p-8 rounded-xl hover-lift group flex flex-col md:flex-row md:items-center justify-between gap-8 border-transparent hover:border-primary/30 transition-all duration-500"
            >
                <div class="flex items-center gap-6">
                    <div class="w-16 h-16 rounded-lg bg-surface-light flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                        {{ ticket.category?.icon || '🎫' }}
                    </div>
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-primary">{{ ticket.ticket_number }}</span>
                            <span :class="getStatusClass(ticket.status)" class="px-2.5 py-1 rounded-lg text-[9px] font-bold uppercase tracking-widest border">
                                {{ ticket.status.replace('_', ' ') }}
                            </span>
                        </div>
                        <h3 class="text-xl font-bold text-text-main group-hover:text-primary transition-colors">{{ ticket.title }}</h3>
                        <p class="text-sm text-text-dim mt-1 flex items-center gap-2">
                            <span>Created {{ formatDate(ticket.created_at) }}</span>
                            <span class="w-1 h-1 rounded-full bg-glass-border"></span>
                            <span class="capitalize">{{ ticket.priority }} Priority</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-6 md:border-l border-glass-border md:pl-8">
                    <div class="text-right hidden sm:block">
                        <p class="text-[10px] font-black uppercase tracking-widest text-text-dim mb-1">Assigned Agent</p>
                        <p class="font-bold text-text-main">{{ ticket.agent?.name || 'Unassigned' }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-surface-light flex items-center justify-center text-text-dim group-hover:bg-primary group-hover:text-white transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                </div>
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useTicketStore } from '@/stores/tickets';

const ticketStore = useTicketStore();
const activeStatus = ref('all');
const tickets = ref([]);
const loading = ref(true);

const filterOptions = [
    { label: 'All', value: 'all' },
    { label: 'Active', value: 'in_progress' },
    { label: 'Waiting', value: 'waiting_on_customer' },
    { label: 'Resolved', value: 'resolved' },
];

const loadTickets = async () => {
    loading.value = true;
    const params = activeStatus.value !== 'all' ? { status: activeStatus.value } : {};
    const response = await ticketStore.fetchMyTickets(params);
    tickets.value = response.data;
    loading.value = false;
};

onMounted(loadTickets);

watch(activeStatus, loadTickets);

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric',
        year: 'numeric'
    });
};

const getStatusClass = (status) => {
    switch (status) {
        case 'new': return 'bg-blue-500/10 text-blue-400 border-blue-500/20';
        case 'in_progress': return 'bg-primary/10 text-primary border-primary/20';
        case 'waiting_on_customer': return 'bg-amber-500/10 text-amber-500 border-amber-500/20';
        case 'resolved': return 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20';
        case 'closed': return 'bg-gray-500/10 text-text-dim border-glass-border';
        default: return 'bg-white/5 text-text-dim border-glass-border';
    }
};
</script>
