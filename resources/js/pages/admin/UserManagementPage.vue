<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">User Management</h1>
                <p class="text-text-dim text-sm mt-1">Manage system users, access roles, and account status.</p>
            </div>
            <button class="px-4 py-2 bg-primary hover:bg-primary-dark text-surface font-black text-sm rounded-lg transition-all">
                + Create User
            </button>
        </div>

        <div v-if="loading" class="flex items-center justify-center p-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <div v-else class="space-y-4">
            <div class="bg-surface rounded-xl border border-glass-border overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-surface-light border-b border-glass-border">
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">User</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">Role</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">Status</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">Applied Assets</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-glass-border text-gray-300">
                        <tr v-for="user in users" :key="user.id" class="hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-surface-light rounded-full border border-glass-border flex items-center justify-center font-bold text-primary">
                                        {{ user.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-white">{{ user.name }}</div>
                                        <div class="text-xs text-text-dim">{{ user.email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span 
                                    :class="[
                                        'px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-widest border',
                                        user.role === 'admin' ? 'bg-warning/20 text-warning border-warning/30' : 
                                        user.role === 'agent' ? 'bg-primary/20 text-primary border-primary/30' : 
                                        'bg-gray-500/20 text-gray-400 border-gray-500/30'
                                    ]"
                                >
                                    {{ user.role }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="flex items-center gap-2 text-xs text-emerald-400">
                                    <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                                    Active
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-text-dim">
                                {{ user.assets_count || 0 }} assets
                            </td>
                            <td class="px-6 py-4 text-right space-x-3">
                                <button @click="editUser(user)" class="text-text-dim hover:text-white transition-colors text-sm font-bold">Edit</button>
                                <button class="text-red-400 hover:text-red-300 transition-colors text-sm font-bold">Deactivate</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '@/plugins/axios';

const loading = ref(true);
const users = ref([]);

const fetchUsers = async () => {
    try {
        const response = await axios.get('/api/v1/search/users');
        // Note: The search/users endpoint might return a different structure, let's assume simple list for now
        users.value = Array.isArray(response.data) ? response.data : response.data.data || [];
    } catch (error) {
        console.error('Failed to load users');
    } finally {
        loading.value = false;
    }
};

onMounted(fetchUsers);
</script>
