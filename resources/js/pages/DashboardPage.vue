<template>
    <div class="space-y-12 pb-12">
        <!-- ─── Header Section ─── -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
            <div class="space-y-2">
                <h1 class="text-4xl font-black tracking-tight text-text-main transition-smooth">
                    Hello, <span class="text-gradient">{{ authStore.user?.name?.split(' ')[0] }}</span> 
                    <span class="inline-block animate-wave ml-2">👋</span>
                </h1>
                <p class="text-text-dim font-medium tracking-wide">Here's what's happening in your IT workspace today.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-5 py-2.5 glass-card rounded-2xl text-xs font-black uppercase tracking-widest text-text-dim flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                    System Online
                </span>
            </div>
        </div>

        <!-- ─── Search Bar ─── -->
        <div class="flex justify-center">
            <UniversalSearch />
        </div>

        <!-- ─── Premium Stats Grid ─── -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div v-for="stat in stats" :key="stat.label" 
                class="glass-card p-8 rounded-[2.5rem] hover-lift group relative overflow-hidden"
            >
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700"></div>
                
                <div class="relative z-10 space-y-5">
                    <div :class="`w-14 h-14 rounded-2xl ${stat.bgColor} flex items-center justify-center text-3xl shadow-lg shadow-black/20`">
                        {{ stat.icon }}
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-1.5">{{ stat.label }}</p>
                        <div class="flex items-baseline gap-2">
                            <h2 class="text-4xl font-black text-text-main">{{ stat.value }}</h2>
                            <span v-if="stat.trend" :class="`text-xs font-bold ${stat.trendColor}`">{{ stat.trend }}</span>
                        </div>
                    </div>
                    <!-- Micro Chart Stub -->
                    <div class="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
                        <div :class="`h-full rounded-full ${stat.barColor} transition-all duration-1000`" :style="`width: ${stat.progress}%`"></div>
                    </div>
                </div>
            </div>
            
            <div class="glass-card p-8 rounded-[2.5rem] hover-lift group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-secondary/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700"></div>
                <div class="relative z-10 space-y-5">
                    <div class="w-14 h-14 rounded-2xl bg-secondary/20 flex items-center justify-center text-3xl shadow-lg shadow-black/20">🚀</div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-1.5">SLA Compliance</p>
                        <div class="flex items-baseline gap-2">
                            <h2 class="text-4xl font-black text-text-main">100%</h2>
                        </div>
                    </div>
                    <div class="h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-secondary w-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Grid: Quick Actions & Recent Activity ─── -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
            <!-- Quick Actions -->
            <div class="lg:col-span-3 space-y-8">
                <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-dim ml-1">Priority Operations</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                    <router-link v-for="action in actions" :key="action.name" :to="action.path"
                        class="glass-card p-7 rounded-[2rem] hover-lift bg-white/5 hover:bg-white/10 group flex flex-col gap-4 border-white/5"
                    >
                        <div :class="`w-14 h-14 shrink-0 rounded-2xl ${action.iconBg} flex items-center justify-center text-2xl group-hover:scale-110 transition-smooth shadow-xl shadow-black/20`">
                            {{ action.icon }}
                        </div>
                        <div>
                            <h4 class="font-bold text-text-main text-lg tracking-tight">{{ action.name }}</h4>
                            <p class="text-sm text-text-dim mt-1.5 leading-relaxed">{{ action.desc }}</p>
                        </div>
                    </router-link>
                </div>

                <!-- Active Maintenance Feed -->
                <div class="space-y-8 pt-4">
                    <div class="flex justify-between items-center px-1">
                        <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-dim">My Recent Tickets</h3>
                        <router-link to="/tickets" class="text-[10px] font-black text-primary uppercase tracking-widest hover:text-text-main transition-colors">View All →</router-link>
                    </div>
                    
                    <div class="glass-card rounded-[2.5rem] overflow-hidden border-white/5">
                        <div v-if="recentTickets.length === 0" class="p-20 text-center space-y-6">
                            <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto text-4xl">📭</div>
                            <div class="space-y-2">
                                <p class="text-text-main font-bold text-lg">System Clearing</p>
                                <p class="text-text-dim max-w-xs mx-auto">No high-priority incidents reported. All infrastructure monitors are green.</p>
                            </div>
                            <router-link to="/tickets/new" class="inline-block px-8 py-3 bg-primary/10 hover:bg-primary/20 text-primary font-black rounded-2xl text-xs uppercase tracking-[0.2em] transition-smooth border border-primary/20">Sync Intel</router-link>
                        </div>
                        
                        <div v-else class="divide-y divide-white/5">
                            <router-link 
                                v-for="ticket in recentTickets" :key="ticket.id" 
                                :to="`/tickets/${ticket.ticket_number}`"
                                class="flex items-center justify-between p-6 hover:bg-white/5 transition-colors group"
                            >
                                <div class="flex items-center gap-5">
                                    <div class="w-12 h-12 rounded-xl bg-surface-light flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                        {{ ticket.category?.icon || '🎫' }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-3 mb-1">
                                            <span class="text-[9px] font-black uppercase tracking-widest text-primary">{{ ticket.ticket_number }}</span>
                                            <span :class="getStatusClass(ticket.status)" class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-widest border">
                                                {{ ticket.status.replace('_', ' ') }}
                                            </span>
                                        </div>
                                        <h4 class="font-bold text-text-main group-hover:text-primary transition-colors">{{ ticket.title }}</h4>
                                    </div>
                                </div>
                                <div class="text-right hidden sm:block">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-text-dim mb-1">{{ formatDate(ticket.created_at) }}</p>
                                    <p class="text-xs font-bold text-text-main capitalize">{{ ticket.priority }}</p>
                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Cards -->
            <div class="space-y-10">
                <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-dim ml-1">Intelligence</h3>
                
                <!-- Announcement Card -->
                <div class="glass-card p-7 rounded-[2rem] bg-linear-to-br from-indigo-500/10 to-purple-500/10 border-indigo-500/20 relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 text-8xl opacity-5 group-hover:opacity-10 transition-opacity rotate-12">📢</div>
                    <div class="relative z-10 space-y-5">
                        <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 text-[10px] font-black uppercase tracking-widest rounded-xl">Global Alert</span>
                        <h4 class="font-bold text-lg text-text-main leading-tight">Azure Maintenance Window</h4>
                        <p class="text-sm text-text-dim leading-relaxed">Intermittent connectivity expected on West Europe region this Sunday.</p>
                        <button class="text-xs font-black text-indigo-400 hover:text-white transition-colors flex items-center gap-2 uppercase tracking-widest">
                            View Details <span class="text-xl">→</span>
                        </button>
                    </div>
                </div>

                <!-- My Hardware Card -->
                <div class="glass-card p-7 rounded-[2rem] space-y-6">
                    <h4 class="text-sm font-black uppercase tracking-widest text-text-main flex items-center gap-3">
                        <span class="text-2xl">💻</span> Asset Inventory
                    </h4>
                    <div class="space-y-3">
                        <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-smooth cursor-pointer group">
                            <div class="w-12 h-12 rounded-xl bg-surface flex items-center justify-center font-bold text-xs text-text-dim group-hover:text-primary transition-colors">MBP</div>
                            <div>
                                <p class="text-sm font-bold text-text-main">MacBook Pro M3</p>
                                <p class="text-[10px] text-text-dim uppercase tracking-widest">Active • Deployment</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useTicketStore } from '@/stores/tickets';
import UniversalSearch from '@/components/UniversalSearch.vue';

const authStore = useAuthStore();
const ticketStore = useTicketStore();

onMounted(async () => {
    await ticketStore.fetchMyTickets();
});

const stats = computed(() => [
    { 
        label: 'Active Requests', 
        value: ticketStore.myTickets.filter(t => ['new', 'in_progress', 'waiting_on_customer'].includes(t.status)).length, 
        icon: '⚡', 
        bgColor: 'bg-primary/20', 
        trend: '+2', 
        trendColor: 'text-primary', 
        barColor: 'bg-primary', 
        progress: 65 
    },
    { 
        label: 'Resolved Tasks', 
        value: ticketStore.myTickets.filter(t => t.status === 'resolved').length, 
        icon: '✅', 
        bgColor: 'bg-accent/20', 
        trend: '↑ 14%', 
        trendColor: 'text-accent', 
        barColor: 'bg-accent', 
        progress: 82 
    },
    { 
        label: 'All-Time Support', 
        value: ticketStore.myTickets.length, 
        icon: '💎', 
        bgColor: 'bg-indigo-500/20', 
        trend: 'Stable', 
        trendColor: 'text-gray-500', 
        barColor: 'bg-indigo-500', 
        progress: 100 
    },
]);

const actions = [
    { name: 'New Request', desc: 'Submit a high-priority IT incident or request.', path: '/tickets/new', icon: '🎟️', iconBg: 'bg-primary/20' },
    { name: 'My Tickets', desc: 'Track your pending applications & status.', path: '/tickets', icon: '📁', iconBg: 'bg-accent/20' },
    { name: 'Knowledge', desc: 'Search documentation for instant fixes.', path: '/kb', icon: '📖', iconBg: 'bg-indigo-500/20' },
    { name: 'Self-Service', desc: 'Password reset & account management.', path: '/settings', icon: '⚙️', iconBg: 'bg-red-500/20' },
];

const recentTickets = computed(() => ticketStore.myTickets.slice(0, 5));

const getStatusClass = (status) => {
    switch (status) {
        case 'new': return 'bg-blue-500/10 text-blue-400 border-blue-500/20';
        case 'in_progress': return 'bg-primary/10 text-primary border-primary/20';
        case 'waiting_on_customer': return 'bg-amber-500/10 text-amber-500 border-amber-500/20';
        case 'resolved': return 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20';
        default: return 'bg-white/5 text-text-dim border-glass-border';
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString();
};
</script>

<style scoped>
@keyframes wave {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(15deg); }
    75% { transform: rotate(-15deg); }
}
.animate-wave {
    display: inline-block;
    animation: wave 2.5s ease-in-out infinite;
    transform-origin: 70% 70%;
}
</style>
