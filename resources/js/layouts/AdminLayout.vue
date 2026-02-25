<template>
    <div class="min-h-screen bg-background text-text-main font-sans flex">
        <!-- Admin Sidebar -->
        <aside class="w-64 bg-surface border-r border-surface-light flex flex-col">
            <div class="p-4 border-b border-surface-light">
                <router-link to="/admin" class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-warning rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold">Admin Panel</span>
                </router-link>
            </div>

            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <div class="text-[10px] font-black uppercase tracking-widest text-text-dim px-3 mb-2 mt-4">Platform</div>
                <router-link to="/admin" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-text-dim hover:bg-surface-light hover:text-text-main transition-colors" active-class="bg-surface-light text-text-main">
                    📊 Dashboard
                </router-link>
                <router-link to="/admin/analytics" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-text-dim hover:bg-surface-light hover:text-text-main transition-colors" active-class="bg-surface-light text-text-main">
                    📈 Intelligence
                </router-link>

                <div class="text-[10px] font-black uppercase tracking-widest text-text-dim px-3 mb-2 mt-6">Configuration</div>
                <router-link to="/admin/settings" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-text-dim hover:bg-surface-light hover:text-text-main transition-colors" active-class="bg-surface-light text-text-main">
                    ⚙️ General Settings
                </router-link>
                <router-link to="/admin/automation" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-text-dim hover:bg-surface-light hover:text-text-main transition-colors" active-class="bg-surface-light text-text-main">
                    🤖 Automation Rules
                </router-link>
                <router-link to="/admin/business-hours" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-text-dim hover:bg-surface-light hover:text-text-main transition-colors" active-class="bg-surface-light text-text-main">
                    🕒 Business Hours
                </router-link>
                <router-link to="/admin/holidays" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-text-dim hover:bg-surface-light hover:text-text-main transition-colors" active-class="bg-surface-light text-text-main">
                    🗓️ Holidays
                </router-link>
                <router-link to="/admin/custom-fields" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-text-dim hover:bg-surface-light hover:text-text-main transition-colors" active-class="bg-surface-light text-text-main">
                    🏗️ Custom Fields
                </router-link>
                <router-link to="/admin/archived-tickets" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-text-dim hover:bg-surface-light hover:text-text-main transition-colors" active-class="bg-surface-light text-text-main">
                    🗄️ Archived Tickets
                </router-link>

                <div class="text-[10px] font-black uppercase tracking-widest text-text-dim px-3 mb-2 mt-6">System</div>
                <router-link to="/admin/health" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-text-dim hover:bg-surface-light hover:text-text-main transition-colors" active-class="bg-surface-light text-text-main">
                    🏥 System Health
                </router-link>
                <router-link to="/admin/activity" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-text-dim hover:bg-surface-light hover:text-text-main transition-colors" active-class="bg-surface-light text-text-main">
                    📋 Activity Log
                </router-link>
                
                <div class="pt-6 mt-6 border-t border-surface-light">
                    <router-link to="/agent" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-text-dim hover:bg-surface-light hover:text-text-main transition-colors">
                        🛠️ Agent View
                    </router-link>
                    <router-link to="/" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-text-dim hover:bg-surface-light hover:text-text-main transition-colors">
                        🏠 User Portal
                    </router-link>
                </div>
            </nav>
        </aside>

        <!-- Content Area -->
        <div class="flex-1 flex flex-col min-w-0 h-screen">
            <!-- Top Header -->
            <header class="h-16 bg-surface border-b border-surface-light px-8 flex items-center justify-between z-40">
                <div class="flex items-center gap-4">
                    <h2 class="text-xs font-black uppercase tracking-[0.2em] text-text-dim">
                        {{ $route.meta.breadcrumb || 'System Oversight' }}
                    </h2>
                </div>

                <div class="flex items-center gap-6">
                    <!-- Theme Toggle -->
                    <button 
                        @click="themeStore.toggleTheme" 
                        class="p-2.5 rounded-xl bg-background border border-glass-border text-text-dim hover:text-text-main transition-all shadow-inner group"
                        title="Toggle System Luminescence"
                    >
                        <svg v-if="themeStore.theme === 'dark'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <!-- User Account -->
                    <div class="flex items-center gap-3 pl-6 border-l border-glass-border">
                        <div class="text-right">
                            <p class="text-[11px] font-black text-text-main leading-none uppercase tracking-wider">{{ authStore.user?.name }}</p>
                            <p class="text-[9px] font-bold text-primary-dim uppercase tracking-widest mt-1">{{ authStore.user?.role }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-background border border-glass-border flex items-center justify-center text-text-main font-black text-xs shadow-inner overflow-hidden relative group">
                            <div class="absolute inset-0 bg-primary/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            {{ authStore.user?.name?.charAt(0) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Content -->
            <main class="flex-1 p-8 overflow-y-auto bg-background/50">
                <router-view />
            </main>
        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';
import { useThemeStore } from '@/stores/theme';

const authStore = useAuthStore();
const themeStore = useThemeStore();
</script>
