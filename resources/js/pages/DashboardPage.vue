<template>
    <div>
        <h1 class="text-2xl font-bold mb-6">Welcome back, {{ authStore.fullName }}!</h1>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-surface rounded-xl p-6 border border-surface-light">
                <p class="text-sm text-gray-400 mb-1">Open Tickets</p>
                <p class="text-3xl font-bold text-primary">—</p>
            </div>
            <div class="bg-surface rounded-xl p-6 border border-surface-light">
                <p class="text-sm text-gray-400 mb-1">Resolved This Week</p>
                <p class="text-3xl font-bold text-accent">—</p>
            </div>
            <div class="bg-surface rounded-xl p-6 border border-surface-light">
                <p class="text-sm text-gray-400 mb-1">My Devices</p>
                <p class="text-3xl font-bold text-secondary">—</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <router-link to="/tickets/new" class="bg-surface hover:bg-surface-light border border-surface-light rounded-xl p-5 transition-colors group">
                <div class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center mb-3 group-hover:bg-primary/30 transition-colors">
                    <span class="text-xl">🎫</span>
                </div>
                <p class="font-medium">New Request</p>
                <p class="text-sm text-gray-400 mt-1">Submit a new IT ticket</p>
            </router-link>
            <router-link to="/tickets" class="bg-surface hover:bg-surface-light border border-surface-light rounded-xl p-5 transition-colors group">
                <div class="w-10 h-10 bg-accent/20 rounded-lg flex items-center justify-center mb-3 group-hover:bg-accent/30 transition-colors">
                    <span class="text-xl">📋</span>
                </div>
                <p class="font-medium">My Tickets</p>
                <p class="text-sm text-gray-400 mt-1">View your open requests</p>
            </router-link>
            <router-link to="/kb" class="bg-surface hover:bg-surface-light border border-surface-light rounded-xl p-5 transition-colors group">
                <div class="w-10 h-10 bg-secondary/20 rounded-lg flex items-center justify-center mb-3 group-hover:bg-secondary/30 transition-colors">
                    <span class="text-xl">📚</span>
                </div>
                <p class="font-medium">Knowledge Base</p>
                <p class="text-sm text-gray-400 mt-1">Find answers to common questions</p>
            </router-link>
            <div class="bg-surface hover:bg-surface-light border border-surface-light rounded-xl p-5 transition-colors group cursor-pointer">
                <div class="w-10 h-10 bg-warning/20 rounded-lg flex items-center justify-center mb-3 group-hover:bg-warning/30 transition-colors">
                    <span class="text-xl">💻</span>
                </div>
                <p class="font-medium">My Devices</p>
                <p class="text-sm text-gray-400 mt-1">View assigned hardware</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
</script>
