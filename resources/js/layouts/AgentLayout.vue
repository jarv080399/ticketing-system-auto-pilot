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
                        {{ authStore.user?.name.charAt(0) }}
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
            <header class="h-24 px-12 border-b border-glass-border flex items-center justify-between sticky top-0 bg-surface/80 backdrop-blur-xl z-40">
                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-text-dim">Console /</span>
                    <h2 class="text-lg font-bold text-text-main">{{ pageTitle }}</h2>
                </div>

                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-4 px-4 py-2 bg-surface-light rounded-xl border border-glass-border">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-text-main">System Online</span>
                    </div>
                    <div class="h-10 w-[1px] bg-glass-border"></div>
                    <button class="relative p-2 text-text-dim hover:text-primary transition-colors">
                        <span class="absolute top-1 right-1 w-2 h-2 bg-primary rounded-full border-2 border-surface"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v1m6 0H9" />
                        </svg>
                    </button>
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

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const navItems = [
    { label: 'Overview', path: '/agent', icon: '📊' },
    { label: 'Issue Queue', path: '/agent/queue', icon: '📥' },
    { label: 'Macros', path: '/agent/canned-responses', icon: '⚡' },
    { label: 'Performance', path: '/agent/stats', icon: '📈' },
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
