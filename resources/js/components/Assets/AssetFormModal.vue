<template>
    <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-6">
        <div class="absolute inset-0 bg-surface/80 backdrop-blur-md" @click="$emit('close')"></div>
        
        <div class="glass-card w-full max-w-2xl rounded-3xl overflow-hidden relative z-10 animate-scale-in">
            <div class="p-8 border-b border-glass-border flex items-center justify-between bg-surface-light/50">
                <div>
                    <h2 class="text-2xl font-black text-text-main tracking-tight">
                        {{ asset ? 'Edit' : 'Register New' }} <span class="text-primary">Asset</span>
                    </h2>
                    <p class="text-xs text-text-dim uppercase tracking-widest font-bold mt-1">Lifecycle Management System</p>
                </div>
                <button @click="$emit('close')" class="w-10 h-10 rounded-xl bg-surface-light flex items-center justify-center text-text-dim hover:text-primary transition-all">✕</button>
            </div>

            <form @submit.prevent="handleSubmit" class="p-8 space-y-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Serial Number -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-text-dim ml-1">Serial Number *</label>
                        <input v-model="form.serial_number" required type="text" placeholder="e.g. C02XG123..." class="w-full bg-surface-light border border-glass-border rounded-xl py-3 px-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all font-mono" />
                    </div>
                    <!-- Asset Tag -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-text-dim ml-1">Asset Tag</label>
                        <input v-model="form.asset_tag" type="text" placeholder="e.g. AST-9921" class="w-full bg-surface-light border border-glass-border rounded-xl py-3 px-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all" />
                    </div>
                    <!-- Type -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-text-dim ml-1">Equipment Type *</label>
                        <select v-model="form.type" required class="w-full bg-surface-light border border-glass-border rounded-xl py-3 px-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all">
                            <option value="laptop">Laptop</option>
                            <option value="desktop">Desktop</option>
                            <option value="monitor">Monitor</option>
                            <option value="printer">Printer</option>
                            <option value="phone">Phone</option>
                            <option value="license">Software License</option>
                            <option value="peripheral">Peripheral</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <!-- Status -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-text-dim ml-1">Current Status *</label>
                        <select v-model="form.status" required class="w-full bg-surface-light border border-glass-border rounded-xl py-3 px-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all">
                            <option value="in_stock">In Stock</option>
                            <option value="active">Active</option>
                            <option value="in_repair">In Repair</option>
                            <option value="retired">Retired</option>
                            <option value="disposed">Disposed</option>
                        </select>
                    </div>
                    <!-- Manufacturer -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-text-dim ml-1">Manufacturer</label>
                        <input v-model="form.manufacturer" type="text" placeholder="e.g. Apple, Dell, HP" class="w-full bg-surface-light border border-glass-border rounded-xl py-3 px-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all" />
                    </div>
                    <!-- Model -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-text-dim ml-1">Model Name</label>
                        <input v-model="form.model" type="text" placeholder="e.g. MacBook Pro 14'" class="w-full bg-surface-light border border-glass-border rounded-xl py-3 px-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all" />
                    </div>
                    <!-- Warranty Expiry -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-text-dim ml-1">Warranty Expiry</label>
                        <input v-model="form.warranty_expires_at" type="date" class="w-full bg-surface-light border border-glass-border rounded-xl py-3 px-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all" />
                    </div>
                    <!-- Owner -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-text-dim ml-1">Custodian (Owner)</label>
                        <select v-model="form.owner_user_id" class="w-full bg-surface-light border border-glass-border rounded-xl py-3 px-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all">
                            <option :value="null">None (In Stock)</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-text-dim ml-1">Detailed Description</label>
                    <textarea v-model="form.description" rows="3" class="w-full bg-surface-light border border-glass-border rounded-xl py-3 px-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all resize-none"></textarea>
                </div>
            </form>

            <div class="p-8 border-t border-glass-border bg-surface-light/30 flex justify-end gap-4">
                <button @click="$emit('close')" class="px-6 py-3 text-sm font-bold text-text-dim hover:text-primary transition-all italic">Cancel</button>
                <button @click="handleSubmit" :disabled="loading" class="px-10 py-3 bg-primary hover:bg-primary-dark text-white font-black rounded-xl shadow-xl shadow-primary/20 hover-lift active:scale-95 transition-all disabled:opacity-50">
                    {{ loading ? 'Saving...' : (asset ? 'Update Asset' : 'Register Asset') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import axios from '@/plugins/axios';
import { useToast } from 'vue-toastification';

const props = defineProps({
    show: Boolean,
    asset: Object
});

const emit = defineEmits(['close', 'saved']);
const toast = useToast();
const loading = ref(false);
const users = ref([]);

const form = reactive({
    serial_number: '',
    asset_tag: '',
    type: 'laptop',
    status: 'in_stock',
    manufacturer: '',
    model: '',
    description: '',
    owner_user_id: null,
    warranty_expires_at: ''
});

const fetchUsers = async () => {
    try {
        const response = await axios.get('/search/users'); // Assuming search controller has users
        users.value = response.data;
    } catch (err) {
        // Fallback or retry
    }
};

onMounted(fetchUsers);

watch(() => props.asset, (newAsset) => {
    if (newAsset) {
        Object.keys(form).forEach(key => {
            form[key] = newAsset[key] || (key === 'owner_user_id' ? null : '');
        });
        // Handle date format for input[type=date]
        if (newAsset.warranty_expires_at) {
            form.warranty_expires_at = newAsset.warranty_expires_at.split('T')[0];
        }
    } else {
        Object.keys(form).forEach(key => {
            form[key] = key === 'type' ? 'laptop' : (key === 'status' ? 'in_stock' : (key === 'owner_user_id' ? null : ''));
        });
    }
}, { immediate: true });

const handleSubmit = async () => {
    loading.value = true;
    try {
        if (props.asset) {
            await axios.put(`/assets/${props.asset.id}`, form);
            toast.success('Asset updated successfully');
        } else {
            await axios.post('/assets', form);
            toast.success('Asset registered successfully');
        }
        emit('saved');
        emit('close');
    } catch (err) {
        const message = err.response?.data?.message || 'Failed to save asset';
        toast.error(message);
        if (err.response?.data?.errors) {
            console.error(err.response.data.errors);
        }
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.animate-scale-in {
    animation: scaleIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes scaleIn {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(var(--color-primary), 0.1);
    border-radius: 10px;
}
</style>
