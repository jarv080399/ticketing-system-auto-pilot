<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-white">System Health</h1>
            <p class="text-text-dim text-sm mt-1">Real-time status of critical infrastructure services.</p>
        </div>

        <div v-if="loading" class="flex items-center justify-center p-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <div v-else class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div 
                    v-for="(service, name) in healthData" 
                    :key="name"
                    class="bg-surface border border-glass-border rounded-xl p-6 space-y-4"
                >
                    <div class="flex items-center justify-between">
                        <h3 class="font-black uppercase tracking-widest text-xs text-text-dim">{{ name }}</h3>
                        <div 
                            :class="[
                                'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                                service.status === 'ok' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'
                            ]"
                        >
                            {{ service.status === 'ok' ? 'Running' : 'Issue' }}
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-lg font-bold text-white">{{ service.message }}</div>
                        <div v-if="service.details" class="mt-2 text-xs text-text-dim grid grid-cols-2 gap-2">
                            <div v-for="(val, label) in service.details" :key="label">
                                <span class="capitalize">{{ label }}:</span> {{ val }}
                            </div>
                        </div>
                    </div>

                    <div v-if="service.status === 'ok'" class="h-1 w-full bg-surface-light rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 w-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></div>
                    </div>
                    <div v-else class="h-1 w-full bg-surface-light rounded-full overflow-hidden">
                        <div class="h-full bg-red-500 w-full animate-pulse shadow-[0_0_10px_rgba(239,68,68,0.5)]"></div>
                    </div>
                </div>
            </div>

            <!-- Maintenance Actions -->
            <div class="bg-surface border border-glass-border rounded-xl overflow-hidden mt-8">
                <div class="px-6 py-4 bg-surface-light border-b border-glass-border">
                    <h2 class="text-sm font-black uppercase tracking-widest text-white">Maintenance Actions</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <button class="px-4 py-3 bg-background border border-glass-border rounded-lg text-sm text-white hover:border-primary transition-all text-left">
                        🧹 Clear Cache
                    </button>
                    <button class="px-4 py-3 bg-background border border-glass-border rounded-lg text-sm text-white hover:border-primary transition-all text-left">
                        🔄 Restart Workers
                    </button>
                    <button class="px-4 py-3 bg-background border border-glass-border rounded-lg text-sm text-white hover:border-primary transition-all text-left">
                        📦 Run Migrations
                    </button>
                    <button class="px-4 py-3 bg-background border border-glass-border rounded-lg text-sm text-white hover:border-primary transition-all text-left">
                        🧪 Run Self-Tests
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '@/plugins/axios';

const loading = ref(true);
const healthData = ref({});

const fetchHealth = async () => {
    try {
        const response = await axios.get('/admin/system-health');
        healthData.value = response.data.data;
    } catch (error) {
        console.error('Failed to load system health');
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchHealth();
    // Auto refresh every 30 seconds
    const interval = setInterval(fetchHealth, 30000);
    return () => clearInterval(interval);
});
</script>
