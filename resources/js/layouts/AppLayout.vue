<template>
    <div class="min-h-screen bg-background text-white font-sans">
        <!-- Header -->
        <header class="bg-surface border-b border-surface-light sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <router-link to="/" class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-white">IT Helpdesk</span>
                    </router-link>

                    <!-- Navigation -->
                    <nav class="hidden md:flex items-center gap-6">
                        <router-link to="/" class="text-sm text-gray-300 hover:text-white transition-colors" active-class="text-primary font-semibold">Dashboard</router-link>
                        <router-link to="/tickets" class="text-sm text-gray-300 hover:text-white transition-colors" active-class="text-primary font-semibold">My Tickets</router-link>
                        <router-link to="/kb" class="text-sm text-gray-300 hover:text-white transition-colors" active-class="text-primary font-semibold">Knowledge Base</router-link>
                        <router-link v-if="authStore.isAgent" to="/agent" class="text-sm text-gray-300 hover:text-white transition-colors" active-class="text-primary font-semibold">Agent Panel</router-link>
                        <router-link v-if="authStore.isAdmin" to="/admin" class="text-sm text-gray-300 hover:text-white transition-colors" active-class="text-primary font-semibold">Admin</router-link>
                    </nav>

                    <!-- Right side -->
                    <div class="flex items-center gap-4">
                        <!-- New Ticket -->
                        <router-link to="/tickets/new" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            + New Request
                        </router-link>

                        <!-- User menu -->
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-secondary rounded-full flex items-center justify-center text-sm font-bold">
                                {{ initials }}
                            </div>
                            <button @click="handleLogout" class="text-sm text-gray-400 hover:text-white transition-colors">
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <router-view />
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const router = useRouter();

const initials = computed(() => {
    const name = authStore.fullName;
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
});

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>
