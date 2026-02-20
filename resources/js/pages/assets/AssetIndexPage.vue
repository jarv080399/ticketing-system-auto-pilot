<template>
    <div class="space-y-8 animate-fade-in pb-12">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black text-text-main tracking-tight">Asset <span class="text-primary">Registry</span></h1>
                <p class="text-text-dim font-medium mt-1">Manage corporate hardware and lifecycle events.</p>
            </div>
            <div class="flex gap-4">
                <button @click="showImportModal = true" class="px-6 py-3 bg-surface-light border border-glass-border rounded-xl text-sm font-bold text-text-dim hover:text-primary transition-all flex items-center gap-2">
                    <span>📤</span> Bulk Import
                </button>
                <button @click="openCreateModal" class="px-8 py-3 bg-primary hover:bg-primary-dark text-white font-black rounded-xl shadow-xl shadow-primary/20 hover-lift active:scale-95 transition-all flex items-center gap-2">
                    <span class="text-xl">+</span> Add Asset
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="glass-card p-6 rounded-2xl flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-[300px] relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-text-dim">🔍</span>
                <input v-model="filters.q" @input="debouncedFetch" type="text" placeholder="Search serial, model, or tag..." class="w-full bg-surface-light border border-glass-border rounded-xl py-3 pl-12 pr-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all" />
            </div>
            <select v-model="filters.type" @change="fetchAssets" class="bg-surface-light border border-glass-border rounded-xl px-4 py-3 text-sm text-text-main focus:outline-none">
                <option value="">All Types</option>
                <option value="laptop">Laptops</option>
                <option value="desktop">Desktops</option>
                <option value="monitor">Monitors</option>
                <option value="printer">Printers</option>
                <option value="phone">Phones</option>
            </select>
            <select v-model="filters.status" @change="fetchAssets" class="bg-surface-light border border-glass-border rounded-xl px-4 py-3 text-sm text-text-main focus:outline-none">
                <option value="">All Statuses</option>
                <option value="active">🟢 Active</option>
                <option value="in_repair">🟡 In Repair</option>
                <option value="in_stock">🔵 In Stock</option>
                <option value="retired">🔴 Retired</option>
            </select>
        </div>

        <!-- Asset Table -->
        <div class="glass-card rounded-2xl overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-light/50 border-b border-glass-border">
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Asset Info</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Type</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Status</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Owner</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-glass-border">
                    <tr v-for="asset in assets" :key="asset.id" class="hover:bg-white/5 transition-colors group cursor-pointer" @click="viewAsset(asset)">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-lg bg-surface-light flex items-center justify-center text-xl">
                                    {{ getIcon(asset.type) }}
                                </div>
                                <div>
                                    <p class="font-bold text-text-main tracking-tight">{{ asset.manufacturer }} {{ asset.model }}</p>
                                    <p class="text-xs text-text-dim font-mono">{{ asset.serial_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 capitalize text-sm font-medium text-text-dim">
                            {{ asset.type }}
                        </td>
                        <td class="px-6 py-5">
                            <span :class="getStatusClass(asset.status)" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border">
                                {{ asset.status }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            <div v-if="asset.owner" class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-[10px] font-bold text-primary">
                                    {{ asset.owner.name.charAt(0) }}
                                </div>
                                <span class="text-sm font-bold text-text-main">{{ asset.owner.name }}</span>
                            </div>
                            <span v-else class="text-xs text-text-dim italic">Unassigned</span>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <button @click.stop="openEditModal(asset)" class="p-2 text-text-dim hover:text-primary transition-colors">✏️</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="!loading && assets.length === 0" class="py-20 text-center">
                <p class="text-text-dim italic">No assets found matching your criteria.</p>
            </div>
        </div>

        <!-- Pagination (Simple) -->
        <div class="flex justify-between items-center px-2">
            <p class="text-xs text-text-dim font-medium">Showing {{ assets.length }} of {{ totalAssets }} assets</p>
            <div class="flex gap-2">
                <button :disabled="page === 1" @click="page--; fetchAssets()" class="px-4 py-2 bg-surface-light border border-glass-border rounded-lg text-xs font-bold disabled:opacity-30">Previous</button>
                <button :disabled="!hasMore" @click="page++; fetchAssets()" class="px-4 py-2 bg-surface-light border border-glass-border rounded-lg text-xs font-bold disabled:opacity-30">Next</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue';
import { useRouter } from 'vue-router';
import axios from '@/plugins/axios';
import _ from 'lodash';

const router = useRouter();
const assets = ref([]);
const loading = ref(true);
const totalAssets = ref(0);
const page = ref(1);
const hasMore = ref(false);

const filters = reactive({
    q: '',
    type: '',
    status: ''
});

const fetchAssets = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/assets', {
            params: { ...filters, page: page.value }
        });
        assets.value = response.data.data;
        totalAssets.value = response.data.total;
        hasMore.value = response.data.next_page_url !== null;
    } catch (err) {
        console.error('Failed to fetch assets', err);
    } finally {
        loading.value = false;
    }
};

const debouncedFetch = _.debounce(() => {
    page.value = 1;
    fetchAssets();
}, 500);

onMounted(fetchAssets);

const viewAsset = (asset) => router.push(`/agent/assets/${asset.id}`);

const getIcon = (type) => {
    const icons = {
        laptop: '💻',
        desktop: '🖥️',
        monitor: '📺',
        printer: '🖨️',
        phone: '📱',
        license: '📜',
        peripheral: '⌨️'
    };
    return icons[type] || '📦';
};

const getStatusClass = (status) => {
    const classes = {
        active: 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
        in_repair: 'bg-amber-500/10 text-amber-500 border-amber-500/20',
        in_stock: 'bg-blue-500/10 text-blue-500 border-blue-500/20',
        retired: 'bg-rose-500/10 text-rose-500 border-rose-500/20',
        disposed: 'bg-gray-500/10 text-gray-500 border-gray-500/20'
    };
    return classes[status] || '';
};

const openCreateModal = () => { /* Implement modal later */ };
const openEditModal = (asset) => { /* Implement modal later */ };
</script>
