<template>
    <div class="max-w-4xl mx-auto space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="flex items-center gap-6">
                <div class="relative group">
                    <div class="w-24 h-24 rounded-2xl bg-linear-to-br from-primary to-secondary flex items-center justify-center text-white text-3xl font-black shadow-2xl shadow-primary/20">
                        {{ auth.user?.name?.charAt(0) }}
                    </div>
                    <button class="absolute -bottom-2 -right-2 p-2 bg-surface border border-glass-border rounded-lg text-primary hover:text-white hover:bg-primary transition-all shadow-lg active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
                <div class="space-y-1">
                    <h1 class="text-3xl font-black tracking-tight text-text-main">{{ auth.user?.name }}</h1>
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest rounded-full border border-primary/20">
                            {{ auth.user?.role }}
                        </span>
                        <span class="text-text-dim text-sm font-medium">{{ auth.user?.department }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Security & Sessions -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Profile Settings -->
                <div class="glass-card rounded-2xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-glass-border bg-white/5">
                        <h2 class="text-sm font-black uppercase tracking-[0.2em] text-text-main">Account Information</h2>
                    </div>
                    <div class="p-8 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-text-dim uppercase tracking-widest ml-1">Full Name</label>
                                <input v-model="form.name" type="text" class="w-full px-4 py-3 bg-surface-light border border-glass-border rounded-xl text-text-main focus:ring-2 focus:ring-primary/40 focus:border-primary/50 outline-none transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-text-dim uppercase tracking-widest ml-1">Email Address</label>
                                <input v-model="form.email" type="email" class="w-full px-4 py-3 bg-surface-light border border-glass-border rounded-xl text-text-main focus:ring-2 focus:ring-primary/40 focus:border-primary/50 outline-none transition-all">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button class="px-6 py-2.5 bg-primary hover:bg-primary-dark text-white text-xs font-black uppercase tracking-widest rounded-xl shadow-lg shadow-primary/20 hover-lift transition-all">
                                Update Profile
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Active Sessions -->
                <div class="glass-card rounded-2xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-glass-border bg-white/5 flex justify-between items-center">
                        <h2 class="text-sm font-black uppercase tracking-[0.2em] text-text-main">Connected Devices</h2>
                        <button @click="handleRevokeAll" class="text-[10px] font-black text-red-400 hover:text-red-300 transition-colors uppercase tracking-widest">
                            Log out all devices
                        </button>
                    </div>
                    <div class="p-0">
                        <div class="divide-y divide-glass-border">
                            <div class="px-8 py-5 flex items-center justify-between hover:bg-white/5 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-surface-light flex items-center justify-center text-xl">💻</div>
                                    <div>
                                        <p class="text-sm font-bold text-text-main">Chrome on Windows 11</p>
                                        <p class="text-[10px] text-text-dim font-medium">192.168.1.15 • Current Session</p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest rounded-full border border-emerald-500/20">Active</span>
                            </div>
                            <div class="px-8 py-5 flex items-center justify-between hover:bg-white/5 transition-colors opacity-50">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-surface-light flex items-center justify-center text-xl">📱</div>
                                    <div>
                                        <p class="text-sm font-bold text-text-main">Mobile App • iPhone 14</p>
                                        <p class="text-[10px] text-text-dim font-medium">London, UK • Yesterday at 14:20</p>
                                    </div>
                                </div>
                                <button class="text-[10px] font-black text-text-dim hover:text-red-400 transition-colors uppercase tracking-widest">Revoke</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar -->
            <div class="space-y-8">
                <!-- Security Card -->
                <div class="glass-card rounded-2xl p-6 bg-linear-to-br from-indigo-500/10 to-purple-500/10 border-indigo-500/20">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-text-main">Password & Security</h3>
                    </div>
                    <p class="text-xs text-text-dim mb-6 leading-relaxed">
                        Keep your account secure by maintaining a strong password and monitoring your active sessions.
                    </p>
                    <button class="w-full py-3 bg-surface-light border border-glass-border hover:border-indigo-500/50 text-text-main text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all hover-lift">
                        Modify Password
                    </button>
                </div>

                <!-- Stats/Activity Mini -->
                <div class="glass-card rounded-2xl p-6">
                    <h3 class="font-bold text-text-main mb-6">Your Activity</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-text-dim">Tickets Resolved</span>
                            <span class="font-bold text-primary">124</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-text-dim">Average Rating</span>
                            <span class="font-bold text-emerald-400">4.9 ★</span>
                        </div>
                        <div class="flex justify-between items-center text-xs text-text-dim/50">
                            <span class="">Member Since</span>
                            <span class="">Oct 2025</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue';
import { useAuthStore } from '@/stores/auth';
import { useToast } from 'vue-toastification';

const auth = useAuthStore();
const toast = useToast();

const form = reactive({
    name: auth.user?.name || '',
    email: auth.user?.email || '',
});

const handleRevokeAll = async () => {
    if (confirm('Verify: This will expire ALL active sessions across all your devices. Continue?')) {
        try {
            await auth.revokeAll();
            toast.success('All sessions revoked successfully');
            window.location.href = '/login';
        } catch (err) {
            toast.error('Operation failed. Please try again.');
        }
    }
};
</script>
