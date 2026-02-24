<template>
    <div class="space-y-8">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight">Admin Dashboard</h1>
            <p class="text-text-dim mt-2">Welcome back. System is <span class="text-emerald-400 font-bold">Healthy</span> and performing within normal parameters.</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="stat in stats" :key="stat.label" class="bg-surface border border-glass-border p-6 rounded-lg group hover:border-primary/50 transition-all cursor-default">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-surface-light rounded-lg flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                        {{ stat.icon }}
                    </div>
                    <div class="text-[10px] font-black uppercase tracking-widest text-text-dim bg-background px-2 py-1 rounded">Live</div>
                </div>
                <div class="text-2xl font-black text-white mb-1">{{ stat.value }}</div>
                <div class="text-xs text-text-dim font-medium">{{ stat.label }}</div>
            </div>
        </div>

        <!-- Admin Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-surface border border-glass-border rounded-lg overflow-hidden shadow-sm">
                <div class="px-6 py-4 bg-surface-light/50 border-b border-glass-border flex items-center justify-between">
                    <h2 class="text-sm font-black uppercase tracking-widest text-white">System Configuration</h2>
                    <span class="text-[10px] font-bold text-primary">RELIABILITY: 99.9%</span>
                </div>
                <div class="p-6 grid grid-cols-2 gap-4">
                    <router-link to="/admin/settings" class="p-4 bg-background border border-glass-border rounded-lg hover:border-primary transition-all group">
                        <div class="text-2xl mb-2 group-hover:scale-125 transition-transform origin-left">⚙️</div>
                        <div class="font-bold text-white text-sm">General Settings</div>
                        <div class="text-[10px] text-text-dim mt-1">Branding, SLA, and Apps</div>
                    </router-link>
                    <router-link to="/admin/business-hours" class="p-4 bg-background border border-glass-border rounded-lg hover:border-primary transition-all group">
                        <div class="text-2xl mb-2 group-hover:scale-125 transition-transform origin-left">🕒</div>
                        <div class="font-bold text-white text-sm">Business Hours</div>
                        <div class="text-[10px] text-text-dim mt-1">Working schedule & SLA</div>
                    </router-link>
                    <router-link to="/admin/automation" class="p-4 bg-background border border-glass-border rounded-lg hover:border-primary transition-all group">
                        <div class="text-2xl mb-2 group-hover:scale-125 transition-transform origin-left">🤖</div>
                        <div class="font-bold text-white text-sm">Automations</div>
                        <div class="text-[10px] text-text-dim mt-1">Rules & Ticket Routing</div>
                    </router-link>
                    <router-link to="/admin/custom-fields" class="p-4 bg-background border border-glass-border rounded-lg hover:border-primary transition-all group">
                        <div class="text-2xl mb-2 group-hover:scale-125 transition-transform origin-left">🏗️</div>
                        <div class="font-bold text-white text-sm">Custom Fields</div>
                        <div class="text-[10px] text-text-dim mt-1">Data Schema Extensions</div>
                    </router-link>
                </div>
            </div>

            <div class="bg-surface border border-glass-border rounded-lg overflow-hidden shadow-sm">
                <div class="px-6 py-4 bg-surface-light/50 border-b border-glass-border">
                    <h2 class="text-sm font-black uppercase tracking-widest text-white">Maintenance & Security</h2>
                </div>
                <div class="p-6 space-y-4">
                    <router-link to="/admin/health" class="flex items-center gap-4 p-4 bg-background border border-glass-border rounded-lg hover:border-primary transition-all">
                        <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-lg flex items-center justify-center text-2xl">🏥</div>
                        <div class="flex-1">
                            <div class="font-bold text-white">System Health</div>
                            <div class="text-xs text-text-dim">Redis, Database, and Worker status</div>
                        </div>
                        <div class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></div>
                    </router-link>
                    <router-link to="/admin/activity" class="flex items-center gap-4 p-4 bg-background border border-glass-border rounded-lg hover:border-primary transition-all">
                        <div class="w-12 h-12 bg-primary/10 text-primary rounded-lg flex items-center justify-center text-2xl">📋</div>
                        <div class="flex-1">
                            <div class="font-bold text-white">Audit Trail</div>
                            <div class="text-xs text-text-dim">Review all administrative activities</div>
                        </div>
                    </router-link>
                    <router-link to="/admin/users" class="flex items-center gap-4 p-4 bg-background border border-glass-border rounded-lg hover:border-primary transition-all">
                        <div class="w-12 h-12 bg-warning/10 text-warning rounded-lg flex items-center justify-center text-2xl">👥</div>
                        <div class="flex-1">
                            <div class="font-bold text-white">User Controls</div>
                            <div class="text-xs text-text-dim">Permissions, roles, and access</div>
                        </div>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const stats = ref([
    { label: 'Active Sessions', value: '42', icon: '⚡' },
    { label: 'System Uptime', value: '99.98%', icon: '⏱️' },
    { label: 'Queued Jobs', value: '0', icon: '📦' },
    { label: 'Users Online', value: '12', icon: '👤' },
]);
</script>
