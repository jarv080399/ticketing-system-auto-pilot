<template>
    <div class="space-y-6 pb-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">User Management</h1>
                <p class="text-text-dim text-sm mt-2">Manage system users, access roles, and account status.</p>
            </div>
            <button class="px-5 py-2.5 bg-primary hover:bg-primary-dark shadow-lg shadow-primary/20 text-white font-black text-sm rounded-xl transition-all hover-lift active:scale-95 flex items-center gap-2 block">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Create User
            </button>
        </div>

        <!-- Filters Section -->
        <div class="bg-surface/50 border border-glass-border rounded-2xl p-5 flex flex-wrap gap-4 items-end shadow-sm">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2">Search Users</label>
                <div class="relative">
                    <input v-model="filters.q" type="text" placeholder="Name or email address..." class="w-full bg-background border border-glass-border rounded-xl pl-10 pr-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/50 transition-all shadow-inner" @keyup.enter="applyFilters" />
                    <svg class="w-4 h-4 text-text-dim absolute left-4 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2">Role</label>
                <select v-model="filters.role" class="w-full sm:w-40 bg-background border border-glass-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/50 transition-all shadow-inner">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="agent">Agent</option>
                    <option value="user">User</option>
                </select>
            </div>
            <button @click="applyFilters" class="px-6 py-2.5 bg-surface-light border border-glass-border hover:bg-white/10 text-white font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-text-dim" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                Apply
            </button>
            <button @click="resetFilters" class="px-4 py-2.5 text-text-dim hover:text-text-main transition-colors text-sm font-bold">
                Clear
            </button>
        </div>

        <div v-if="loading" class="flex items-center justify-center p-24">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <div v-else class="space-y-4">
            <div class="bg-surface rounded-2xl border border-glass-border shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="bg-surface-light/50 border-b border-glass-border">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">User Details</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Role</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Total Assets</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-glass-border text-gray-300">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-background rounded-full border border-glass-border shadow-sm flex items-center justify-center font-black text-primary group-hover:scale-110 transition-transform">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-white tracking-wide">{{ user.name }}</div>
                                            <div class="text-xs text-text-dim mt-0.5">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span 
                                        :class="[
                                            'px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest border',
                                            user.role === 'admin' ? 'bg-warning/10 text-warning border-warning/20' : 
                                            user.role === 'agent' ? 'bg-primary/10 text-primary border-primary/20' : 
                                            'bg-gray-500/10 text-gray-400 border-gray-500/20'
                                        ]"
                                    >
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full" :class="user.id % 2 === 0 ? 'bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.5)]' : 'bg-red-400 shadow-[0_0_8px_rgba(248,113,113,0.5)]'"></div>
                                        <span class="text-xs font-bold" :class="user.id % 2 === 0 ? 'text-emerald-400' : 'text-red-400'">
                                            {{ user.id % 2 === 0 ? 'Active' : 'Suspended' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-text-dim">
                                    <span class="px-2 py-1 bg-background rounded-lg border border-glass-border">
                                        {{ user.assets_count !== undefined ? user.assets_count : (user.assets?.length || 0) }} items
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="editUser(user)" class="p-2 bg-surface-light border border-glass-border rounded-lg text-white hover:text-primary hover:border-primary/50 transition-all font-bold text-xs">
                                            Edit
                                        </button>
                                        <button class="p-2 bg-surface-light border border-glass-border rounded-lg text-text-dim hover:text-red-400 hover:bg-red-500/10 transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 6.524l8.367 8.368zm1.414-1.414L6.524 5.11a6 6 0 018.367 8.367zM18 10a8 8 0 11-16 0 8 8 0 0116 0z" clip-rule="evenodd" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-text-dim font-medium text-sm">
                                    No users found matching your criteria.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Datatable Pagination -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-2">
                <span class="text-xs font-bold uppercase tracking-widest text-text-dim">
                    Showing page {{ currentPage }} of {{ lastPage }} <span class="text-gray-500 ml-1">({{ total }} total users)</span>
                </span>
                <div class="flex items-center gap-1.5 bg-surface border border-glass-border rounded-xl p-1 shadow-sm">
                    <button 
                        @click="changePage(currentPage - 1)" 
                        :disabled="currentPage === 1"
                        class="px-3 py-1.5 text-xs font-bold text-white rounded-lg hover:bg-surface-light transition-colors disabled:opacity-30 disabled:hover:bg-transparent"
                    >
                        Prev
                    </button>
                    
                    <div class="px-2 flex gap-1">
                        <!-- Only show limited page numbers for simplicity -->
                        <button 
                            v-for="page in getPageNumbers" 
                            :key="page"
                            @click="changePage(page)"
                            :class="[
                                'w-8 h-8 rounded-lg flex items-center justify-center',
                                page === currentPage ? 'bg-primary text-white shadow-md' : 'text-text-dim hover:text-text-main hover:bg-surface-light'
                            ]"
                        >
                            {{ page }}
                        </button>
                    </div>

                    <button 
                        @click="changePage(currentPage + 1)" 
                        :disabled="currentPage === lastPage"
                        class="px-3 py-1.5 text-xs font-bold text-white rounded-lg hover:bg-surface-light transition-colors disabled:opacity-30 disabled:hover:bg-transparent"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from '@/plugins/axios';
import { useToast } from 'vue-toastification';

const toast = useToast();
const loading = ref(true);
const users = ref([]);
const currentPage = ref(1);
const lastPage = ref(1);
const total = ref(0);

const filters = ref({
    q: '',
    role: '',
    status: ''
});

const fetchUsers = async (page = 1) => {
    loading.value = true;
    try {
        const response = await axios.get('/search/users', {
            params: { ...filters.value, page }
        });
        
        if (response.data && response.data.current_page !== undefined) {
            // Paginated response
            users.value = response.data.data;
            currentPage.value = response.data.current_page;
            lastPage.value = response.data.last_page;
            total.value = response.data.total;
        } else {
            // Default flat array 
            users.value = Array.isArray(response.data) ? response.data : response.data.data || [];
            currentPage.value = 1;
            lastPage.value = 1;
            total.value = users.value.length;
        }
    } catch (error) {
        toast.error('Failed to load user directory');
    } finally {
        loading.value = false;
    }
};

const applyFilters = () => {
    fetchUsers(1);
};

const resetFilters = () => {
    filters.value = { q: '', role: '', status: '' };
    fetchUsers(1);
};

const changePage = (page) => {
    if (page >= 1 && page <= lastPage.value) {
        fetchUsers(page);
    }
};

const getPageNumbers = computed(() => {
    let pages = [];
    const maxPagesToShow = 5;
    
    if (lastPage.value <= maxPagesToShow) {
        for (let i = 1; i <= lastPage.value; i++) pages.push(i);
    } else {
        pages.push(1);
        
        let start = Math.max(2, currentPage.value - 1);
        let end = Math.min(lastPage.value - 1, currentPage.value + 1);
        
        if (currentPage.value === 1) end = 3;
        if (currentPage.value === lastPage.value) start = lastPage.value - 2;
        
        for (let i = start; i <= end; i++) pages.push(i);
        
        if (end < lastPage.value - 1) { /* push dots visually in actual table component logic */ }
        pages.push(lastPage.value);
    }
    
    // De-duplicate in edge cases
    return [...new Set(pages)].sort((a,b)=>a-b);
});

const editUser = (user) => {
    toast.info(`Editing User Module coming soon for ${user.name}`);
};

onMounted(() => fetchUsers(1));
</script>
