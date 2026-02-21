<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-white">General Settings</h1>
            <p class="text-text-dim text-sm mt-1">Manage global system parameters and branding.</p>
        </div>

        <div v-if="loading" class="flex items-center justify-center p-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <div v-else class="space-y-8 max-w-4xl">
            <!-- Settings Groups -->
            <div v-for="(settings, group) in groupedSettings" :key="group" class="bg-surface rounded-xl border border-glass-border overflow-hidden">
                <div class="px-6 py-4 bg-surface-light/50 border-b border-glass-border">
                    <h2 class="text-sm font-black uppercase tracking-widest text-white">{{ group }}</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <div v-for="setting in settings" :key="setting.id" class="flex flex-col gap-2">
                        <label :for="setting.key" class="text-sm font-medium text-gray-300">
                            {{ formatLabel(setting.key) }}
                        </label>
                        
                        <input 
                            v-if="setting.type === 'string' || setting.type === 'integer'"
                            :id="setting.key"
                            v-model="editedSettings[setting.key]"
                            :type="setting.type === 'integer' ? 'number' : 'text'"
                            class="w-full px-4 py-2 bg-background border border-glass-border rounded-lg text-white focus:outline-none focus:border-primary transition-colors"
                        />

                        <div v-else-if="setting.type === 'boolean'" class="flex items-center gap-3">
                            <button 
                                @click="editedSettings[setting.key] = !editedSettings[setting.key]"
                                :class="[
                                    'w-12 h-6 rounded-full p-1 transition-colors duration-200 ease-in-out relative',
                                    editedSettings[setting.key] ? 'bg-primary' : 'bg-gray-600'
                                ]"
                            >
                                <span 
                                    :class="[
                                        'w-4 h-4 bg-white rounded-full transition-transform duration-200 shadow-sm inline-block',
                                        editedSettings[setting.key] ? 'translate-x-6' : 'translate-x-0'
                                    ]"
                                ></span>
                            </button>
                            <span class="text-sm text-text-dim">{{ editedSettings[setting.key] ? 'Enabled' : 'Disabled' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 sticky bottom-0 py-4 bg-background/80 backdrop-blur-md border-t border-glass-border">
                <button 
                    @click="reset"
                    class="px-4 py-2 text-sm text-text-dim hover:text-white transition-colors"
                >
                    Discard Changes
                </button>
                <button 
                    @click="save"
                    :disabled="saving"
                    class="px-6 py-2 bg-primary hover:bg-primary-dark text-surface font-bold rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                >
                    <span v-if="saving" class="animate-spin rounded-full h-4 w-4 border-b-2 border-surface"></span>
                    {{ saving ? 'Saving...' : 'Save Settings' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '@/plugins/axios';

const loading = ref(true);
const saving = ref(false);
const groupedSettings = ref({});
const editedSettings = ref({});

const fetchSettings = async () => {
    try {
        const response = await axios.get('/admin/settings');
        groupedSettings.value = response.data.data;
        
        // Initialize editedSettings
        const flat = {};
        Object.values(groupedSettings.value).flat().forEach(s => {
            flat[s.key] = s.value;
            // Type conversion for booleans if stored as strings
            if (s.type === 'boolean') {
                flat[s.key] = s.value === 'true' || s.value === true || s.value === '1' || s.value === 1;
            }
        });
        editedSettings.value = flat;
    } catch (error) {
        alert('Failed to load settings');
    } finally {
        loading.ref = false;
    }
};

const formatLabel = (key) => {
    return key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
};

const save = async () => {
    saving.value = true;
    try {
        await axios.patch('/admin/settings', {
            settings: editedSettings.value
        });
        alert('Settings updated successfully');
    } catch (error) {
        alert('Failed to save settings');
    } finally {
        saving.value = false;
    }
};

const reset = () => {
    fetchSettings();
};

onMounted(fetchSettings);
</script>
