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
                    <button @click="performAction('clear-cache')" :disabled="isProcessing['clear-cache']" class="px-4 py-3 bg-background border border-glass-border rounded-lg text-sm text-white hover:border-primary transition-all text-left disabled:opacity-50 flex items-center justify-between">
                        <span>🧹 Clear Cache</span>
                        <div v-if="isProcessing['clear-cache']" class="w-4 h-4 rounded-full border-2 border-primary border-t-transparent animate-spin"></div>
                    </button>
                    <button @click="performAction('restart-workers')" :disabled="isProcessing['restart-workers']" class="px-4 py-3 bg-background border border-glass-border rounded-lg text-sm text-white hover:border-primary transition-all text-left disabled:opacity-50 flex items-center justify-between">
                        <span>🔄 Restart Workers</span>
                        <div v-if="isProcessing['restart-workers']" class="w-4 h-4 rounded-full border-2 border-primary border-t-transparent animate-spin"></div>
                    </button>
                    <button @click="performAction('run-migrations')" :disabled="isProcessing['run-migrations']" class="px-4 py-3 bg-background border border-glass-border rounded-lg text-sm text-white hover:border-primary transition-all text-left disabled:opacity-50 flex items-center justify-between">
                        <span>📦 Run Migrations</span>
                        <div v-if="isProcessing['run-migrations']" class="w-4 h-4 rounded-full border-2 border-primary border-t-transparent animate-spin"></div>
                    </button>
                    <button @click="performAction('run-tests')" :disabled="isProcessing['run-tests']" class="px-4 py-3 bg-background border border-glass-border rounded-lg text-sm text-white hover:border-primary transition-all text-left disabled:opacity-50 flex items-center justify-between">
                        <span>🧪 Run Self-Tests</span>
                        <div v-if="isProcessing['run-tests']" class="w-4 h-4 rounded-full border-2 border-primary border-t-transparent animate-spin"></div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '@/plugins/axios';
import { useToast } from 'vue-toastification';

const toast = useToast();
const loading = ref(true);
const healthData = ref({});
const isProcessing = ref({
    'clear-cache': false,
    'restart-workers': false,
    'run-migrations': false,
    'run-tests': false,
});

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

const performAction = async (action) => {
    if(isProcessing.value[action]) return;
    
    isProcessing.value[action] = true;
    try {
        const response = await axios.post(`/admin/system-health/${action}`);
        toast.success(response.data.message || 'Action executed successfully.');
        fetchHealth(); // Refresh health after action
    } catch (error) {
        const msg = error.response?.data?.message || 'Failed to execute maintenance action.';
        toast.error(msg);
    } finally {
        isProcessing.value[action] = false;
    }
};

onMounted(() => {
    fetchHealth();
    // Auto refresh every 30 seconds
    const interval = setInterval(fetchHealth, 30000);
    return () => clearInterval(interval);
});
</script>
