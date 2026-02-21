<template>
    <div class="min-h-screen bg-background font-sans selection:bg-primary/30">
        <!-- ─── Modern Glass Header ─── -->
        <header class="sticky top-0 z-50 glass-card border-x-0 border-t-0 bg-background/60">
            <div class="max-w-[1600px] mx-auto px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Logo -->
                    <div class="flex items-center gap-2 group cursor-pointer" @click="$router.push('/')">
                        <div class="w-10 h-10 bg-linear-to-br from-primary to-secondary rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-primary/40 transition-all duration-300">
                            <span class="text-white font-bold text-xl">IT</span>
                        </div>
                        <span class="text-xl font-extrabold tracking-tight text-gradient">Helpdesk</span>
                    </div>

                    <!-- Desktop Nav -->
                    <nav class="hidden md:flex items-center space-x-1">
                        <router-link 
                            v-for="item in navItems" 
                            :key="item.path"
                            :to="item.path"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-smooth hover:bg-white/10"
                            :class="[$route.path === item.path ? 'text-primary bg-primary/10' : 'text-gray-400 hover:text-white']"
                        >
                            {{ item.name }}
                        </router-link>
                    </nav>

                    <!-- Right Side Actions -->
                    <div class="flex items-center gap-4">
                        <NotificationBell />
                        
                        <!-- Theme Toggle -->
                        <button 
                            @click="themeStore.toggleTheme"
                            class="p-2.5 rounded-xl bg-white/5 border border-white/10 text-text-dim hover:text-primary hover:border-primary/50 transition-all duration-300"
                            title="Toggle Theme"
                        >
                            <svg v-if="themeStore.theme === 'dark'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.243 16.243l.707.707M7.757 7.757l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        <router-link 
                            to="/tickets/new"
                            class="hidden sm:flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary/20 hover-lift active:scale-95"
                        >
                            <span>New Request</span>
                        </router-link>

                        <!-- User Profile Dropdown -->
                        <div class="relative">
                            <button 
                                @click="isUserMenuOpen = !isUserMenuOpen"
                                class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-white/5 transition-smooth border border-transparent hover:border-white/10"
                            >
                                <div class="w-8 h-8 rounded-lg bg-surface-light flex items-center justify-center border border-white/10">
                                    <span class="text-xs font-bold text-text-main">{{ auth.user?.name?.charAt(0) }}</span>
                                </div>
                                <span class="hidden sm:inline text-sm font-medium text-text-dim group-hover:text-text-main">{{ auth.user?.name }}</span>
                            </button>

                            <!-- Dropdown Menu -->
                            <transition enter-active-class="transition duration-100 ease-out" enter-from-class="transform scale-95 opacity-0" enter-to-class="transform scale-100 opacity-100" leave-active-class="transition duration-75 ease-in" leave-from-class="transform scale-100 opacity-100" leave-to-class="transform scale-95 opacity-0">
                                <div v-if="isUserMenuOpen" class="absolute right-0 mt-2 w-56 glass-card rounded-lg py-2 overflow-hidden ring-1 ring-white/10">
                                    <div class="px-4 py-3 border-b border-white/10 bg-white/5">
                                        <p class="text-xs text-text-dim font-medium uppercase tracking-wider">Role</p>
                                        <p class="text-sm font-bold text-primary truncate capitalize">{{ auth.user?.role }}</p>
                                    </div>
                                    <div class="px-2 py-2 border-b border-white/10">
                                        <router-link 
                                            v-if="['agent', 'admin'].includes(auth.user?.role)"
                                            to="/agent"
                                            class="w-full text-left px-4 py-2.5 text-xs font-bold text-text-main hover:bg-primary/10 hover:text-primary rounded-xl flex items-center gap-3 transition-all"
                                        >
                                            <span>🎧</span>
                                            Agent Workspace
                                        </router-link>
                                        <router-link 
                                            v-if="auth.user?.role === 'admin'"
                                            to="/admin"
                                            class="w-full text-left px-4 py-2.5 text-xs font-bold text-text-main hover:bg-warning/10 hover:text-warning rounded-xl flex items-center gap-3 transition-all mt-1"
                                        >
                                            <span>👑</span>
                                            Admin Panel
                                        </router-link>
                                    </div>
                                    <button 
                                        @click="handleLogout"
                                        class="w-full text-left px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 flex items-center gap-2 transition-colors"
                                    >
                                        Logout
                                    </button>
                                </div>
                            </transition>
                        </div>

                        <!-- Mobile Menu Button -->
                        <button class="md:hidden p-2 text-text-dim hover:text-text-main">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-[1600px] mx-auto px-8 py-10 text-text-main">
            <router-view v-slot="{ Component }">
                <transition name="fade" mode="out-in">
                    <component :is="Component" />
                </transition>
            </router-view>
        </main>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useThemeStore } from '@/stores/theme';
import { useRouter } from 'vue-router';
import NotificationBell from '@/components/NotificationBell.vue';

const auth = useAuthStore();
const themeStore = useThemeStore();
const router = useRouter();
const isUserMenuOpen = ref(false);

const navItems = [
    { name: 'Dashboard', path: '/' },
    { name: 'My Tickets', path: '/tickets' },
    { name: 'Knowledge Base', path: '/kb' },
];

const handleLogout = async () => {
    await auth.logout();
    router.push('/login');
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
