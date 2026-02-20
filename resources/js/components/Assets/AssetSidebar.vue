<template>
    <div class="glass-card p-6 rounded-2xl space-y-6">
        <div class="flex items-center justify-between border-b border-glass-border pb-4">
            <h3 class="text-xs font-black uppercase tracking-widest text-text-dim">User Hardware</h3>
            <span class="px-2 py-0.5 rounded bg-primary/10 text-[10px] font-bold text-primary">{{ assets.length }} Assets</span>
        </div>

        <div v-if="loading" class="space-y-4 animate-pulse">
            <div v-for="i in 2" :key="i" class="h-20 bg-surface-light rounded-xl"></div>
        </div>

        <div v-else-if="assets.length > 0" class="space-y-4">
            <div v-for="asset in assets" :key="asset.id" class="p-4 rounded-xl bg-surface-light border border-glass-border hover:border-primary/30 transition-all cursor-pointer group" @click="$router.push(`/agent/assets/${asset.id}`)">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-surface flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        {{ getIcon(asset.type) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-black text-text-main truncate uppercase tracking-tight">{{ asset.manufacturer }} {{ asset.model }}</p>
                        <p class="text-[10px] text-text-dim font-mono truncate">{{ asset.serial_number }}</p>
                    </div>
                </div>
                
                <div class="mt-4 flex items-center justify-between">
                    <span :class="getStatusClass(asset.status)" class="text-[9px] font-black uppercase tracking-widest">{{ asset.status }}</span>
                    <span v-if="daysRemaining(asset.warranty_expires_at) < 30" class="text-[9px] font-black uppercase text-rose-500 animate-pulse">⚠️ Warranty</span>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-8">
            <span class="text-3xl filter grayscale mb-2 block">🚫</span>
            <p class="text-xs text-text-dim font-medium uppercase tracking-tight">No assets assigned</p>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from '@/plugins/axios';

const props = defineProps({
    userId: {
        type: [Number, String],
        required: true
    }
});

const assets = ref([]);
const loading = ref(false);

const fetchUserAssets = async () => {
    if (!props.userId) return;
    loading.value = true;
    try {
        const response = await axios.get(`/users/${props.userId}/assets`);
        assets.value = response.data.data;
    } catch (err) {
        console.error('Failed to fetch user assets', err);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchUserAssets);
watch(() => props.userId, fetchUserAssets);

const getIcon = (type) => {
    const icons = { laptop: '💻', desktop: '🖥️', monitor: '📺', printer: '🖨️', phone: '📱' };
    return icons[type] || '📦';
};

const getStatusClass = (status) => {
    const classes = { active: 'text-emerald-500', in_repair: 'text-amber-500', in_stock: 'text-blue-500' };
    return classes[status] || 'text-text-dim';
};

const daysRemaining = (date) => {
    if (!date) return 999;
    const expiry = new Date(date);
    const diffTime = expiry - new Date();
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};
</script>
