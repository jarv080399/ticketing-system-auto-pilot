<template>
    <div class="space-y-8 pb-10">

        <!-- ─── Page Header ─── -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold text-text-main tracking-tight">Admin Dashboard</h1>
                <p class="mt-1 text-sm text-text-dim">
                    Welcome back. System is
                    <span class="text-emerald-500 font-bold">Healthy</span>
                    and performing within normal parameters.
                </p>
            </div>
            <!-- Primary CTA — top-right per skill spec -->
            <div class="flex items-center gap-3">
                <span
                    class="text-[10px] font-bold tracking-widest px-2.5 py-1 rounded-full uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center gap-1.5"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
                    All Systems Running
                </span>
                <router-link
                    to="/admin/health"
                    class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    View Diagnostics
                </router-link>
            </div>
        </div>

        <!-- ─── KPI Metric Cards ─── -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
                v-for="metric in metrics"
                :key="metric.label"
                class="bg-surface border border-glass-border rounded-xl p-6 group hover:border-primary/40 transition-all"
            >
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-surface-light border border-glass-border flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        {{ metric.icon }}
                    </div>
                    <span class="text-[11px] font-semibold tracking-widest text-text-dim uppercase">
                        {{ metric.label }}
                    </span>
                </div>
                <div class="text-4xl font-bold text-text-main">
                    {{ metric.value }}<span class="text-xl text-text-dim ml-1">{{ metric.unit }}</span>
                </div>
                <div class="mt-2 text-xs flex items-center gap-1" :class="metric.trendUp ? 'text-emerald-400' : 'text-red-400'">
                    {{ metric.trendUp ? '↑' : '↓' }} {{ metric.trend }}%
                    <span class="text-text-dim opacity-60 ml-1">vs last period</span>
                </div>
            </div>
        </div>

        <!-- ─── System Service Status Strip ─── -->
        <div class="bg-surface border border-glass-border rounded-xl overflow-hidden">
            <div class="px-6 py-4 bg-surface-light border-b border-glass-border flex items-center justify-between">
                <h2 class="text-[11px] font-semibold tracking-widest text-text-dim uppercase">Service Infrastructure</h2>
                <router-link to="/admin/health" class="text-[10px] font-bold text-primary hover:text-primary-dark transition-colors uppercase tracking-widest">
                    Full Report →
                </router-link>
            </div>
            <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div
                    v-for="svc in services"
                    :key="svc.name"
                    class="bg-background border border-glass-border rounded-lg p-4 flex flex-col gap-3"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-semibold tracking-widest text-text-dim uppercase">
                            {{ svc.name }}
                        </span>
                        <!-- Status Badge — per skill spec -->
                        <span
                            class="text-[10px] font-bold tracking-widest px-2 py-0.5 rounded-full uppercase border"
                            :class="svc.ok
                                ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30'
                                : 'bg-red-500/20 text-red-400 border-red-500/30'"
                        >
                            {{ svc.ok ? 'Running' : 'Issue' }}
                        </span>
                    </div>
                    <div class="text-sm font-bold text-text-main">{{ svc.value }}</div>
                    <div class="h-1 rounded-full bg-surface-light overflow-hidden">
                        <div
                            class="h-full rounded-full transition-all duration-700"
                            :class="svc.ok ? 'bg-emerald-400' : 'bg-red-400'"
                            :style="{ width: svc.ok ? '100%' : '30%' }"
                        ></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Main Content Grid ─── -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Configuration Quick Actions -->
            <div class="bg-surface border border-glass-border rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-surface-light border-b border-glass-border flex items-center justify-between">
                    <h2 class="text-[11px] font-semibold tracking-widest text-text-dim uppercase">Configuration</h2>
                    <span class="text-[10px] font-bold text-text-dim bg-background border border-glass-border px-2 py-0.5 rounded-full">RELIABILITY: 99.9%</span>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <router-link
                        v-for="action in configActions"
                        :key="action.to"
                        :to="action.to"
                        class="p-4 bg-background border border-glass-border rounded-lg hover:border-primary/50 transition-all group"
                    >
                        <div class="text-2xl mb-3 group-hover:scale-110 transition-transform origin-left">{{ action.icon }}</div>
                        <div class="font-semibold text-text-main text-sm">{{ action.label }}</div>
                        <div class="text-[10px] text-text-dim mt-1">{{ action.sub }}</div>
                    </router-link>
                </div>
            </div>

            <!-- Maintenance & Security -->
            <div class="bg-surface border border-glass-border rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-surface-light border-b border-glass-border">
                    <h2 class="text-[11px] font-semibold tracking-widest text-text-dim uppercase">Maintenance & Security</h2>
                </div>
                <div class="p-6 space-y-3">
                    <router-link
                        v-for="item in maintenanceLinks"
                        :key="item.to"
                        :to="item.to"
                        class="flex items-center gap-4 p-4 bg-background border border-glass-border rounded-lg hover:border-primary/50 transition-all group"
                    >
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl flex-shrink-0" :class="item.iconBg">
                            {{ item.icon }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-text-main text-sm">{{ item.label }}</div>
                            <div class="text-xs text-text-dim mt-0.5">{{ item.sub }}</div>
                        </div>
                        <div class="flex-shrink-0" v-if="item.badge">
                            <span
                                class="text-[10px] font-bold tracking-widest px-2 py-0.5 rounded-full uppercase border"
                                :class="item.badge === 'Running'
                                    ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30'
                                    : 'bg-slate-500/20 text-text-dim border-glass-border'"
                            >{{ item.badge }}</span>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-text-dim group-hover:text-primary transition-colors flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </router-link>
                </div>
            </div>
        </div>

        <!-- ─── Recent Activity Feed ─── -->
        <div class="bg-surface border border-glass-border rounded-xl overflow-hidden">
            <div class="px-6 py-4 bg-surface-light border-b border-glass-border flex items-center justify-between">
                <h2 class="text-[11px] font-semibold tracking-widest text-text-dim uppercase">Recent Activity</h2>
                <router-link to="/admin/activity" class="text-[10px] font-bold text-primary hover:text-primary-dark transition-colors uppercase tracking-widest">
                    Full Audit Log →
                </router-link>
            </div>
            <div class="divide-y divide-glass-border">
                <div
                    v-for="event in recentActivity"
                    :key="event.id"
                    class="px-6 py-4 flex items-start gap-4 hover:bg-surface-light/40 transition-colors"
                >
                    <div class="w-8 h-8 rounded-full bg-surface-light border border-glass-border flex items-center justify-center text-sm font-black text-primary flex-shrink-0 mt-0.5">
                        {{ event.actor.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-sm font-semibold text-text-main">{{ event.actor }}</span>
                            <span
                                class="text-[10px] font-bold tracking-widest px-2 py-0.5 rounded-full uppercase border"
                                :class="actionBadgeClass(event.type)"
                            >{{ event.type }}</span>
                        </div>
                        <p class="text-xs text-text-dim mt-0.5 truncate">{{ event.description }}</p>
                    </div>
                    <div class="text-[10px] text-text-dim font-mono flex-shrink-0 mt-1">{{ event.time }}</div>
                </div>
                <div v-if="recentActivity.length === 0" class="px-6 py-12 text-center text-text-dim text-sm">
                    No recent events to display.
                </div>
            </div>
        </div>

    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '@/plugins/axios';
import { useToast } from 'vue-toastification';

const toast = useToast();

// ─── Metric KPI cards ───────────────────────────────────────────────────────
const metrics = ref([
    { label: 'Active Sessions',  value: '-',     unit: '',    icon: '⚡',  trend: '0', trendUp: true  },
    { label: 'System Uptime',    value: '-',     unit: '%',   icon: '⏱️', trend: '0', trendUp: true  },
    { label: 'Queued Jobs',      value: '-',     unit: '',    icon: '📦',  trend: '0', trendUp: true },
    { label: 'Total Users',      value: '-',     unit: '',    icon: '👤',  trend: '0', trendUp: true  },
]);

// ─── Service status strip ────────────────────────────────────────────────────
const services = ref([
    { name: 'Database',   value: 'Checking...',      ok: true  },
    { name: 'Redis',      value: 'Checking...',      ok: true  },
    { name: 'Queue',      value: '...',              ok: true  },
    { name: 'Scheduler',  value: 'Checking...',      ok: true  },
]);

// ─── Config quick actions ────────────────────────────────────────────────────
const configActions = ref([
    { to: '/admin/settings',       icon: '⚙️',  label: 'General Settings',  sub: 'Branding, SLA & Apps'          },
    { to: '/admin/business-hours', icon: '🕒',  label: 'Business Hours',    sub: 'Working schedule & SLA'        },
    { to: '/admin/automation',     icon: '🤖',  label: 'Automations',       sub: 'Rules & ticket routing'        },
    { to: '/admin/custom-fields',  icon: '🏗️', label: 'Custom Fields',     sub: 'Schema data extensions'        },
]);

// ─── Maintenance links ────────────────────────────────────────────────────────
const maintenanceLinks = ref([
    {
        to: '/admin/health',
        icon: '🏥', iconBg: 'bg-emerald-500/10 text-emerald-400',
        label: 'System Health', sub: 'Redis, Database & Worker status', badge: 'Running'
    },
    {
        to: '/admin/activity',
        icon: '📋', iconBg: 'bg-primary/10 text-primary',
        label: 'Audit Trail', sub: 'Review all administrative activities', badge: null
    },
    {
        to: '/admin/users',
        icon: '👥', iconBg: 'bg-amber-500/10 text-amber-500', // Using standard tailwind warning colors if needed
        label: 'User Controls', sub: 'Permissions, roles & access', badge: null
    },
]);

// ─── Recent activity feed ────────────────────────────────────────────────────
const recentActivity = ref([]);

// ─── Data Fetching ───────────────────────────────────────────────────────────
const fetchDashboardData = async () => {
    try {
        const response = await axios.get('/admin/dashboard');
        const data = response.data;
        
        if (data.metrics) metrics.value = data.metrics;
        if (data.services) services.value = data.services;
        if (data.recentActivity) recentActivity.value = data.recentActivity;
        
    } catch (error) {
        toast.error('Failed to load live dashboard data.');
        console.error('Dashboard load error:', error);
    }
};

onMounted(() => {
    fetchDashboardData();
    // In a real sophisticated app, we could poll this or use Echo, but for now fetching on mount is fine
});

// ─── Helpers ─────────────────────────────────────────────────────────────────
function actionBadgeClass(type) {
    if (!type) return 'bg-slate-500/20 text-text-dim border-glass-border';
    
    const lowerType = type.toLowerCase();
    if (lowerType.includes('created')) return 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30';
    if (lowerType.includes('deleted')) return 'bg-red-500/20 text-red-400 border-red-500/30';
    if (lowerType.includes('updated')) return 'bg-amber-500/20 text-amber-400 border-amber-500/30';
    
    return 'bg-slate-500/20 text-slate-400 border-slate-500/30'; // UNKNOWN badge
}
</script>
