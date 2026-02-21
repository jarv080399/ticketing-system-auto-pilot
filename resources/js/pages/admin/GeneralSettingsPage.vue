<template>
    <div class="space-y-8 pb-10">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-white tracking-tight">System Configuration</h1>
            <p class="text-text-dim text-sm mt-2 max-w-2xl">
                Manage global parameters, operational boundaries, and platform presentation. Changes made here may affect all active sessions and immediate workflows.
            </p>
        </div>

        <div v-if="loading" class="flex items-center justify-center p-24">
            <div class="flex flex-col items-center gap-4">
                <div class="animate-spin rounded-full h-10 w-10 border-b-4 border-primary"></div>
                <p class="text-sm font-bold text-text-dim animate-pulse uppercase tracking-widest">Loading Preferences...</p>
            </div>
        </div>

        <div v-else class="space-y-10 max-w-7xl"> <!-- Maximized View -->
            
            <!-- Settings Groups -->
            <div v-for="(settings, group) in groupedSettings" :key="group" class="bg-surface rounded-2xl border border-glass-border shadow-xl overflow-hidden relative">
                
                <!-- Group Header Elements -->
                <div class="px-8 py-5 bg-surface-light border-b border-glass-border flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black uppercase tracking-widest text-white">{{ group }} Settings</h2>
                        <p class="text-xs text-text-dim mt-1">Configure parameters specific to the {{ group.toLowerCase() }} module.</p>
                    </div>
                    <div class="hidden sm:flex w-10 h-10 bg-surface rounded-xl border border-glass-border items-center justify-center text-primary shadow-sm hover:scale-110 transition-transform cursor-help" title="Module settings">
                        ⚙️
                    </div>
                </div>
                
                <!-- Inputs Flex Grid -->
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-x-12 gap-y-10 bg-background/30">
                    <div v-for="setting in settings" :key="setting.id" class="flex flex-col gap-3 group/item">
                        
                        <div class="flex items-start justify-between">
                            <label :for="setting.key" class="text-sm font-bold text-gray-200 group-hover/item:text-primary transition-colors">
                                {{ formatLabel(setting.key) }}
                            </label>
                            
                            <!-- Help Tooltip / Icon -->
                            <div class="group/tooltip relative flex items-center justify-center cursor-help">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-text-dim hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <!-- Tooltip visual -->
                                <div class="absolute bottom-full right-0 mb-3 hidden group-hover/tooltip:block w-56 p-3 bg-gray-800 text-xs text-blue-100 rounded-xl border border-blue-500/30 shadow-2xl z-20 normal-case leading-relaxed">
                                    <div class="font-black text-white mb-1">Configuration Guide:</div>
                                    This property defines the systemic rules for "{{ formatLabel(setting.key) }}". Ensure accuracy to prevent operational errors.
                                </div>
                            </div>
                        </div>
                        
                        <!-- Text/Number Input -->
                        <div v-if="setting.type === 'string' || setting.type === 'integer'" class="relative">
                            <input 
                                :id="setting.key"
                                v-model="editedSettings[setting.key]"
                                :type="setting.type === 'integer' ? 'number' : 'text'"
                                placeholder="Enter desired value..."
                                class="w-full px-4 py-3 bg-surface border border-glass-border rounded-xl text-white text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner"
                            />
                            <div v-if="setting.type === 'integer'" class="absolute right-3 top-3 text-[10px] uppercase font-black tracking-widest text-text-dim pointer-events-none">NUM</div>
                        </div>

                        <!-- Boolean Toggle Input -->
                        <div v-else-if="setting.type === 'boolean'" class="flex items-center gap-4 bg-surface px-5 py-3 rounded-xl border border-glass-border shadow-inner cursor-pointer hover:border-gray-600 transition-colors" @click="editedSettings[setting.key] = !editedSettings[setting.key]">
                            <button 
                                :class="[
                                    'w-14 h-7 rounded-full p-1 transition-colors duration-300 ease-in-out relative flex-shrink-0 flex items-center justify-start',
                                    editedSettings[setting.key] ? 'bg-emerald-500' : 'bg-gray-700'
                                ]"
                            >
                                <span 
                                    :class="[
                                        'w-5 h-5 bg-white rounded-full transition-transform duration-300 shadow-sm block',
                                        editedSettings[setting.key] ? 'translate-x-[24px]' : 'translate-x-0'
                                    ]"
                                ></span>
                            </button>
                            <div class="flex flex-col">
                                <span class="text-sm font-black tracking-wide" :class="editedSettings[setting.key] ? 'text-emerald-400' : 'text-gray-400'">
                                    {{ editedSettings[setting.key] ? 'System Active' : 'System Offline' }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Mini Guide Text -->
                        <p class="text-[11px] font-medium text-text-dim/80 ml-1 leading-relaxed border-l-2 border-primary/30 pl-2">
                            {{ getHelperText(setting.key) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Sticky Save Bar -->
            <div class="flex items-center justify-between sticky bottom-6 p-5 rounded-2xl bg-surface/80 backdrop-blur-2xl border border-glass-border shadow-2xl z-40 mt-10">
                <div class="hidden sm:block">
                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest">Unsaved Changes Notice</p>
                    <p class="text-xs text-text-dim mt-0.5">Please save your configurations before navigating away.</p>
                </div>
                
                <div class="flex items-center gap-4 w-full sm:w-auto justify-end">
                    <button 
                        @click="reset"
                        class="px-5 py-2.5 text-sm font-bold text-gray-400 hover:text-white hover:bg-white/5 rounded-xl transition-all"
                        :disabled="saving"
                    >
                        Discard
                    </button>
                    <button 
                        @click="save"
                        :disabled="saving"
                        class="px-8 py-3 bg-primary hover:bg-primary-dark text-white font-black rounded-xl shadow-lg shadow-primary/30 hover-lift active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 min-w-[180px]"
                    >
                        <span v-if="saving" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                        <svg v-if="!saving" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>{{ saving ? 'Processing Request...' : 'Apply Configurations' }}</span>
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
        toast.error('Data retrieval failed. The server refused the connection.');
    } finally {
        loading.value = false;
    }
};

const formatLabel = (key) => {
    return key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
};

const getHelperText = (key) => {
    // Specific hardcoded heuristics for friendly text
    const textMap = {
        company_name: "The official name of your organization. This will appear across the user portal, agent interface, and automated email footers.",
        support_email: "The primary email address where end-users can safely direct their questions if they cannot log into the portal.",
        app_name: "The designated name of this helpdesk platform instance. Displayed predominantly in the header tabs and branding metadata.",
        timezone: "Determine the default system timezone. This heavily influences global reporting metrics, SLA timer tracking, and business hours application.",
        session_timeout: "Specify the max duration of inactivity (in minutes) before an agent or absolute admin is logged out automatically for security concerns.",
        enable_registration: "Determine if external guests or standard personnel can organically create their own accounts via the login portal without requiring an IT invite.",
        default_locale: "Set standard system language for clients lacking a specific personal preference.",
    };
    
    if (textMap[key]) return textMap[key];
    
    // Dynamic intelligent fallbacks
    if (key.includes('email')) return "Verify this inbox is actively monitored and capable of accepting incoming messages without bounce backs.";
    if (key.includes('enable') || key.includes('allow') || key.includes('is_')) return "Changing this toggle instantly activates or deactivates the corresponding feature globally.";
    if (key.includes('time') || key.includes('duration') || key.includes('limit')) return "A numerical threshold representing boundary limits in corresponding metric units.";
    if (key.includes('color') || key.includes('theme')) return "A valid hexadecimal representation or known color constraint (e.g. #FF5733 / dark).";
    
    return `Enter the proper ${formatLabel(key).toLowerCase()} to establish correct behaviors for the operational standard.`;
};

const save = async () => {
    saving.value = true;
    try {
        await axios.patch('/admin/settings', {
            settings: editedSettings.value
        });
        toast.success('System configuration updated successfully.');
    } catch (error) {
        toast.error('The system was unable to commit your changes to the database.');
    } finally {
        saving.value = false;
    }
};

const reset = () => {
    fetchSettings();
    toast.info('Discarded local changes. The view has been restored from database defaults.');
};

onMounted(fetchSettings);
</script>
