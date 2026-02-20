<template>
    <div v-if="loading" class="w-full space-y-12 py-12">
        <div class="h-12 w-64 bg-surface-light animate-pulse rounded-xl"></div>
        <div class="h-64 glass-card animate-pulse rounded-xl"></div>
    </div>

    <div v-else-if="ticket" class="w-full space-y-12 pb-24">
        <!-- Header Actions -->
        <div class="flex items-center justify-between">
            <router-link to="/tickets" class="flex items-center gap-2 text-text-dim hover:text-primary transition-colors group">
                <div class="p-2 rounded-lg group-hover:bg-primary/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </div>
                <span class="font-bold text-sm tracking-tight">Back to my tickets</span>
            </router-link>

            <div class="flex gap-3">
                <button class="px-6 py-2.5 glass-card rounded-xl text-xs font-black uppercase tracking-widest text-text-dim hover:bg-white/5 transition-all">Download PDF</button>
            </div>
        </div>

        <!-- Progress Tracker -->
        <div class="glass-card p-10 rounded-xl">
            <TicketStatusTracker :status="ticket.status" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-12">
                <!-- Ticket Content -->
                <div class="glass-card p-12 rounded-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8">
                        <span class="px-4 py-2 bg-surface-light rounded-xl text-[10px] font-black uppercase tracking-[0.2em] text-text-dim">
                            {{ ticket.source }}
                        </span>
                    </div>

                    <div class="space-y-8">
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-black uppercase tracking-widest text-primary">{{ ticket.ticket_number }}</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-glass-border"></span>
                                <span class="text-xs text-text-dim font-medium">{{ formatDate(ticket.created_at) }}</span>
                            </div>
                            <h1 class="text-4xl font-black text-text-main tracking-tight leading-tight">{{ ticket.title }}</h1>
                        </div>

                        <div class="prose prose-invert max-w-none">
                            <p class="text-lg text-text-dim leading-relaxed whitespace-pre-wrap">{{ ticket.description }}</p>
                        </div>

                        <!-- Attachments -->
                        <div v-if="ticket.attachments?.length" class="pt-8 border-t border-glass-border">
                            <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-4">Attached Evidence</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div v-for="file in ticket.attachments" :key="file.id" class="p-4 rounded-lg bg-surface-light border border-glass-border flex items-center gap-4 group cursor-pointer hover:border-primary/50 transition-all">
                                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                    </div>
                                    <div class="overflow-hidden">
                                        <p class="text-sm font-bold text-text-main truncate">{{ file.file_name }}</p>
                                        <p class="text-[10px] text-text-dim uppercase tracking-widest">{{ (file.file_size / 1024).toFixed(1) }} KB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conversation Section -->
                <div class="space-y-8">
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-dim ml-4">Activity Log</h3>
                    
                    <div v-if="publicComments.length" class="space-y-8 pl-12 border-l-2 border-glass-border relative">
                        <div v-for="comment in publicComments" :key="comment.id" class="relative">
                            <!-- Connector Dot -->
                            <div class="absolute -left-14 top-2 w-4 h-4 rounded-full border-4 border-surface shadow-sm bg-primary"></div>
                            
                            <div class="glass-card p-8 rounded-xl space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="font-black text-text-main text-sm">{{ comment.user?.name || 'User' }}</span>
                                        <span v-if="comment.user?.role !== 'user'" class="px-2 py-0.5 bg-primary/20 text-primary text-[8px] font-black uppercase tracking-[0.2em] rounded-md">Agent</span>
                                    </div>
                                    <span class="text-[10px] text-text-dim font-bold uppercase tracking-widest">{{ formatTimeAgo(comment.created_at) }}</span>
                                </div>
                                <p class="text-sm text-text-dim leading-relaxed font-semibold whitespace-pre-wrap">{{ comment.body }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="glass-card p-12 rounded-xl text-center space-y-4">
                        <div class="w-16 h-16 bg-surface-light rounded-lg flex items-center justify-center mx-auto text-3xl opacity-50">💬</div>
                        <p class="text-text-dim font-medium">No replies yet. Our agents will comment here once they review your request.</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-10">
                <div class="glass-card p-8 rounded-xl space-y-8">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-4">Category</p>
                        <div class="flex items-center gap-4 p-4 rounded-lg bg-surface-light border border-glass-border">
                            <span class="text-2xl">{{ ticket.category?.icon }}</span>
                            <span class="font-bold text-text-main">{{ ticket.category?.name }}</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-4">Ticket Status</p>
                        <div class="flex items-center gap-3">
                            <div :class="getStatusIconClass(ticket.status)" class="w-3 h-3 rounded-full animate-pulse shadow-sm"></div>
                            <span class="font-bold text-text-main capitalize">{{ ticket.status.replace('_', ' ') }}</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-4">Priority</p>
                        <div class="px-4 py-2 rounded-xl bg-linear-to-r inline-block" :class="getPriorityClass(ticket.priority)">
                            <span class="text-[10px] font-black uppercase tracking-widest text-white">{{ ticket.priority }}</span>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-glass-border">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-4">Assigned Expert</p>
                        <div v-if="ticket.agent" class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-surface-light flex items-center justify-center font-black text-primary border border-primary/20">
                                {{ ticket.agent.name.charAt(0) }}
                            </div>
                            <div>
                                <p class="font-bold text-text-main line-clamp-1">{{ ticket.agent.name }}</p>
                                <p class="text-[10px] text-text-dim uppercase tracking-widest">IT Specialist</p>
                            </div>
                        </div>
                        <div v-else class="p-4 bg-white/5 rounded-lg text-center">
                            <p class="text-xs font-bold text-text-dim italic">Waiting for assignment...</p>
                        </div>
                    </div>
                </div>

                <!-- Self Service Card -->
                <div class="glass-card p-8 rounded-xl bg-linear-to-br from-primary/10 to-secondary/10 border-primary/20">
                    <h4 class="font-black text-lg text-text-main mb-2">Need an update?</h4>
                    <p class="text-sm text-text-dim mb-6 leading-relaxed">Most hardware tickets are resolved within 24 hours. Check our Knowledge Base for quick fixes.</p>
                    <router-link to="/kb" class="block w-full py-4 bg-white text-primary font-black text-center rounded-lg text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover-lift">Knowledge Base</router-link>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="py-24 text-center space-y-6">
        <h2 class="text-4xl font-black text-text-main">404</h2>
        <p class="text-text-dim">The ticket you are looking for does not exist or you lack permission to view it.</p>
        <router-link to="/tickets" class="px-10 py-4 bg-primary text-white rounded-lg inline-block">My Tickets</router-link>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useTicketStore } from '@/stores/tickets';
import TicketStatusTracker from '@/components/TicketStatusTracker.vue';

const route = useRoute();
const ticketStore = useTicketStore();

const ticket = ref(null);
const loading = ref(true);

onMounted(async () => {
    loading.value = true;
    try {
        await ticketStore.fetchTicketByNumber(route.params.ticketNumber);
        ticket.value = ticketStore.currentTicket;
    } finally {
        loading.value = false;
    }
});

const publicComments = computed(() => {
    return ticket.value?.comments?.filter(c => !c.is_internal) || [];
});

const formatTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / 1000 / 60);

    if (diffInMinutes < 1) return 'Just now';
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
    return date.toLocaleString();
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('en-US', { 
        month: 'long', 
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });
};

const getStatusIconClass = (status) => {
    switch (status) {
        case 'new': return 'bg-blue-400 shadow-blue-400';
        case 'in_progress': return 'bg-primary shadow-primary';
        case 'waiting_on_customer': return 'bg-amber-500 shadow-amber-500';
        case 'resolved': return 'bg-emerald-500 shadow-emerald-500';
        default: return 'bg-text-dim shadow-text-dim';
    }
};

const getPriorityClass = (priority) => {
    switch (priority) {
        case 'low': return 'from-blue-500 to-indigo-500 shadow-lg shadow-blue-500/20';
        case 'medium': return 'from-primary to-primary-dark shadow-lg shadow-primary/20';
        case 'high': return 'from-amber-500 to-orange-500 shadow-lg shadow-amber-500/20';
        case 'critical': return 'from-red-600 to-rose-600 shadow-lg shadow-red-500/30';
        default: return 'from-gray-500 to-slate-500';
    }
};
</script>
