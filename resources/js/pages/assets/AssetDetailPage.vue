<template>
    <div v-if="loading" class="animate-pulse space-y-8">
        <div class="h-12 w-64 bg-surface-light rounded-xl"></div>
        <div class="grid grid-cols-3 gap-8">
            <div class="col-span-2 h-96 bg-surface-light rounded-2xl"></div>
            <div class="h-96 bg-surface-light rounded-2xl"></div>
        </div>
    </div>

    <div v-else-if="asset" class="space-y-8 animate-fade-in pb-12">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-6">
                <button @click="$router.back()" class="p-3 rounded-xl bg-surface-light border border-glass-border hover:text-primary transition-all">←</button>
                <div>
                    <h1 class="text-3xl font-black text-text-main tracking-tight">{{ asset.manufacturer }} {{ asset.model }}</h1>
                    <p class="text-text-dim text-sm font-mono mt-1">Serial: {{ asset.serial_number }} | Tag: {{ asset.asset_tag || 'N/A' }}</p>
                </div>
            </div>
            <div class="flex gap-4">
                <button v-if="asset.owner" @click="unassign" class="px-6 py-2.5 bg-rose-500/10 text-rose-500 border border-rose-500/20 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-rose-500/20 transition-all">Unassign User</button>
                <button @click="openAssignModal" class="px-6 py-2.5 bg-primary/20 text-primary border border-primary/30 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-primary/30 transition-all">Assign User</button>
                <button @click="openEditModal" class="px-8 py-2.5 bg-primary text-white font-black rounded-xl shadow-lg shadow-primary/20 hover-lift active:scale-95 transition-all">Edit Details</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Asset Details -->
            <div class="lg:col-span-2 space-y-8">
                <div class="glass-card p-10 rounded-2xl">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-text-dim mb-8">Technical Specifications</h3>
                    <div class="grid grid-cols-2 lg:grid-cols-3 gap-y-10">
                        <div>
                            <p class="text-[10px] font-black uppercase text-text-dim mb-1">Equipment Type</p>
                            <p class="text-sm font-bold text-text-main capitalize">{{ asset.type }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-text-dim mb-1">Status</p>
                            <span :class="getStatusClass(asset.status)" class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border inline-block">
                                {{ asset.status }}
                            </span>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-text-dim mb-1">Location</p>
                            <p class="text-sm font-bold text-text-main">{{ asset.location || 'Unknown' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-text-dim mb-1">Purchase Date</p>
                            <p class="text-sm font-bold text-text-main">{{ formatDate(asset.purchase_date) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-text-dim mb-1">Warranty Expiry</p>
                            <p :class="getWarrantyColor(asset.warranty_expires_at)" class="text-sm font-black">{{ formatDate(asset.warranty_expires_at) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase text-text-dim mb-1">Cost</p>
                            <p class="text-sm font-bold text-text-main">${{ asset.purchase_cost || '0.00' }}</p>
                        </div>
                    </div>

                    <div class="mt-12 pt-10 border-t border-glass-border">
                        <p class="text-[10px] font-black uppercase text-text-dim mb-4">Internal Notes</p>
                        <p class="text-sm text-text-dim leading-relaxed whitespace-pre-wrap">{{ asset.notes || 'No notes provided.' }}</p>
                    </div>
                </div>

                <!-- History Timeline -->
                <div class="glass-card p-10 rounded-2xl">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-text-dim mb-8">Asset History & Audit Trail</h3>
                    <AssetHistoryTimeline :history="asset.history" />
                </div>
            </div>

            <!-- Profile / Context -->
            <div class="space-y-8">
                <!-- Current Owner -->
                <div class="glass-card p-8 rounded-2xl text-center">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-6">Current Custodian</h3>
                    <div v-if="asset.owner" class="space-y-4">
                        <div class="w-20 h-20 mx-auto rounded-2xl bg-primary/10 border border-primary/20 flex items-center justify-center text-3xl font-black text-primary">
                            {{ asset.owner.name.charAt(0) }}
                        </div>
                        <div>
                            <p class="text-lg font-black text-text-main tracking-tight">{{ asset.owner.name }}</p>
                            <p class="text-xs text-text-dim font-medium">{{ asset.owner.department || 'General Operations' }}</p>
                        </div>
                        <router-link :to="`/agent/users/${asset.owner.id}`" class="block w-full py-3 bg-surface-light border border-glass-border rounded-xl text-[10px] font-black uppercase tracking-widest text-text-dim hover:text-primary transition-all">View User Profile</router-link>
                    </div>
                    <div v-else class="py-10">
                        <span class="text-4xl filter grayscale mb-4 block">🏢</span>
                        <p class="text-sm font-medium text-text-dim">Currently in Inventory</p>
                    </div>
                </div>
                
                <!-- Warranty Stats -->
                <div v-if="daysRemaining !== null" class="glass-card p-8 rounded-2xl overflow-hidden relative">
                    <div class="relative z-10">
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-2">Warranty Life</h3>
                        <p class="text-4xl font-black tracking-tighter" :class="daysRemaining < 30 ? 'text-rose-500' : 'text-text-main'">
                            {{ daysRemaining }} <span class="text-xs uppercase tracking-normal">Days Left</span>
                        </p>
                        <div class="w-full h-1 bg-surface-light rounded-full mt-4 overflow-hidden">
                            <div class="h-full bg-primary transition-all" :style="{ width: Math.min(100, (daysRemaining / 365) * 100) + '%' }"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import axios from '@/plugins/axios';
import AssetHistoryTimeline from '@/components/Assets/AssetHistoryTimeline.vue';
import { useToast } from 'vue-toastification';

const route = useRoute();
const toast = useToast();
const asset = ref(null);
const loading = ref(true);

const fetchAsset = async () => {
    loading.value = true;
    try {
        const response = await axios.get(`/assets/${route.params.id}`);
        asset.value = response.data;
    } catch (err) {
        console.error('Failed to fetch asset', err);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchAsset);

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', { 
        year: 'numeric', month: 'short', day: 'numeric' 
    });
};

const getStatusClass = (status) => {
    const classes = {
        active: 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
        in_repair: 'bg-amber-500/10 text-amber-500 border-amber-500/20',
        in_stock: 'bg-blue-500/10 text-blue-500 border-blue-500/20',
        retired: 'bg-rose-500/10 text-rose-500 border-rose-500/20'
    };
    return classes[status] || 'bg-gray-500/10 text-gray-500 border-gray-500/20';
};

const daysRemaining = computed(() => {
    if (!asset.value?.warranty_expires_at) return null;
    const expiry = new Date(asset.value.warranty_expires_at);
    const diffTime = expiry - new Date();
    return Math.max(0, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));
});

const getWarrantyColor = (date) => {
    if (!date) return 'text-text-dim';
    const expiry = new Date(date);
    if (expiry < new Date()) return 'text-rose-500';
    if (expiry < new Date(new Date().setDate(new Date().getDate() + 30))) return 'text-amber-500';
    return 'text-emerald-500';
};

const unassign = async () => {
    if (!confirm('Unassign this asset from current owner?')) return;
    try {
        await axios.post(`/assets/${asset.value.id}/unassign`);
        toast.success('Asset unassigned successfully');
        fetchAsset();
    } catch (err) {
        toast.error('Failed to unassign asset');
    }
};

const openAssignModal = () => { /* Logic for assign modal */ };
const openEditModal = () => { /* Logic for edit modal */ };
</script>
