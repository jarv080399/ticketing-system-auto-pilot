<template>
    <div class="min-h-screen bg-background text-white font-sans flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-surface border-r border-surface-light flex flex-col">
            <div class="p-4 border-b border-surface-light">
                <router-link to="/agent" class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-accent rounded-lg flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold">Agent Cockpit</span>
                </router-link>
            </div>

            <nav class="flex-1 p-4 space-y-1">
                <router-link to="/agent" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-300 hover:bg-surface-light hover:text-white transition-colors">
                    📥 Queue
                </router-link>
                <router-link to="/" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-300 hover:bg-surface-light hover:text-white transition-colors">
                    🏠 Portal
                </router-link>
            </nav>

            <div class="p-4 border-t border-surface-light">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center text-sm font-bold">
                        {{ initials }}
                    </div>
                    <div>
                        <p class="text-sm font-medium">{{ authStore.fullName }}</p>
                        <p class="text-xs text-gray-400">Agent</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-auto">
            <router-view />
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const initials = computed(() => {
    const name = authStore.fullName;
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
});
</script>
