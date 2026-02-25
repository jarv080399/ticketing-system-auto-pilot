<template>
    <div class="min-h-screen bg-surface flex">
        <!-- Sidebar -->
        <aside class="w-72 bg-surface-dark border-r border-glass-border flex flex-col fixed inset-y-0 z-50">
            <div class="p-8">
                <router-link to="/agent" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-lg shadow-primary/20 group-hover:rotate-12 transition-transform">
                        <span class="text-white font-black">A</span>
                    </div>
                    <span class="text-xl font-black text-text-main tracking-tight uppercase">Agent<span class="text-primary">Ops</span></span>
                </router-link>
            </div>

            <nav class="flex-1 px-4 space-y-2">
                <router-link 
                    v-for="item in navItems" :key="item.path"
                    :to="item.path"
                    class="flex items-center gap-4 px-6 py-4 rounded-lg transition-all duration-300 group"
                    :class="$route.path === item.path ? 'bg-primary text-white shadow-xl shadow-primary/20' : 'text-text-dim hover:bg-white/5'"
                >
                    <span class="text-xl">{{ item.icon }}</span>
                    <span class="font-bold tracking-tight">{{ item.label }}</span>
                </router-link>
            </nav>

            <div class="p-8 border-t border-glass-border space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-surface-light flex items-center justify-center font-black text-primary border border-glass-border">
                        {{ authStore.user?.name?.charAt(0) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="font-bold text-text-main truncate text-sm">{{ authStore.user?.name }}</p>
                        <p class="text-[10px] text-text-dim uppercase tracking-[0.2em]">{{ authStore.user?.role }}</p>
                    </div>
                </div>
                <button @click="handleLogout" class="w-full py-4 rounded-lg border border-glass-border text-[10px] font-black uppercase tracking-widest text-text-dim hover:bg-red-500/10 hover:text-red-400 hover:border-red-500/20 transition-all">
                    Terminate Session
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-72">
            <!-- Header -->
            <header class="h-16 px-8 border-b border-glass-border flex items-center justify-between sticky top-0 bg-surface/80 backdrop-blur-xl z-40">
                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-text-dim">Console /</span>
                    <h2 class="text-sm font-black text-text-main uppercase tracking-widest">{{ pageTitle }}</h2>
                </div>

                <div class="flex items-center gap-6">
                    <div class="hidden md:flex items-center gap-3 px-3 py-1.5 bg-background rounded-lg border border-glass-border">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-text-dim">Sync Active</span>
                    </div>

                    <!-- Theme Toggle -->
                    <button 
                        @click="themeStore.toggleTheme" 
                        class="p-2 rounded-lg bg-background border border-glass-border text-text-dim hover:text-text-main transition-all shadow-inner"
                    >
                        <svg v-if="themeStore.theme === 'dark'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707m12.728 12.728l-.707-.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>

                    <NotificationBell />

                    <div class="w-px h-6 bg-glass-border"></div>

                    <div class="flex items-center gap-3 pl-2">
                        <div class="text-right hidden sm:block">
                            <p class="text-[10px] font-black text-text-main leading-none uppercase tracking-wider">{{ authStore.user?.name }}</p>
                            <p class="text-[8px] font-bold text-primary-dim uppercase tracking-widest mt-1">{{ authStore.user?.role }}</p>
                        </div>
                        <div class="w-9 h-9 rounded-lg bg-background border border-glass-border flex items-center justify-center text-text-main font-black text-xs shadow-inner">
                            {{ authStore.user?.name?.charAt(0) }}
                        </div>
                    </div>
                </div>
            </header>

            <div class="p-12">
                <router-view v-slot="{ Component }">
                    <transition name="page" mode="out-in">
                        <component :is="Component" />
                    </transition>
                </router-view>
            </div>
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useThemeStore } from '@/stores/theme';
import NotificationBell from '@/components/NotificationBell.vue';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const themeStore = useThemeStore();

const navItems = [
    { label: 'Overview', path: '/agent', icon: '📊' },
    { label: 'Issue Queue', path: '/agent/queue', icon: '📥' },
    { label: 'Knowledge Base', path: '/agent/kb/new', icon: '📚' },
    { label: 'Macros', path: '/agent/canned-responses', icon: '⚡' },
    { label: 'Asset Registry', path: '/agent/assets', icon: '📦' },
    { label: 'Performance', path: '/agent/stats', icon: '📈' },
    { label: 'Notifications', path: '/settings/notifications', icon: '🔔' },
    { label: 'User Portal', path: '/', icon: '🏠' },
];

const pageTitle = computed(() => {
    const active = navItems.find(item => item.path === route.path);
    return active ? active.label : 'Agent Workspace';
});

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>

<style scoped>
.page-enter-active, .page-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.page-enter-from {
    opacity: 0;
    transform: translateY(20px);
}
.page-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}
</style>
