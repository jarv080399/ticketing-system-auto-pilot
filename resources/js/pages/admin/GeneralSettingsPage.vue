<template>
    <div class="space-y-10 pb-20">
        <!-- Header -->
        <div class="mb-10">
            <h1 class="text-3xl font-black text-white tracking-tight">System Configuration</h1>
            <p class="text-text-dim text-sm mt-2">
                Manage global parameters, operational boundaries, and platform presentation. 
            </p>
        </div>

        <div v-if="loading" class="flex items-center justify-center p-24">
            <div class="flex flex-col items-center gap-4">
                <div class="animate-spin rounded-full h-10 w-10 border-b-4 border-primary"></div>
                <p class="text-sm font-bold text-text-dim animate-pulse uppercase tracking-widest">Loading Preferences...</p>
            </div>
        </div>

        <div v-else class="space-y-8"> 
            <!-- Settings Groups Vertical Stack -->
            <div v-for="(settings, group) in groupedSettings" :key="group" class="bg-surface rounded-2xl border border-glass-border shadow-2xl overflow-hidden">
                
                <!-- Group Header -->
                <div class="px-8 py-5 bg-surface-light border-b border-glass-border">
                    <h2 class="text-sm font-black uppercase tracking-[0.2em] text-white">{{ group }} Settings</h2>
                </div>
                
                <!-- Group Fields: Contents Side by Side -->
                <div class="divide-y divide-glass-border bg-background/20">
                    <div v-for="setting in settings" :key="setting.id" class="px-8 py-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start group/item hover:bg-white/[0.02] transition-colors">
                        
                        <!-- Left Side: Label & Description -->
                        <div class="lg:col-span-4 space-y-1">
                            <label :for="setting.key" class="text-sm font-bold text-gray-200 group-hover/item:text-primary transition-colors">
                                {{ formatLabel(setting.key) }}
                            </label>
                            <p class="text-[11px] text-text-dim leading-relaxed max-w-sm">
                                {{ getHelperText(setting.key) }}
                            </p>
                        </div>
                        
                        <!-- Right Side: Input Control -->
                        <div class="lg:col-span-8 flex items-center">
                            <!-- Text/Number Input -->
                            <div v-if="setting.type === 'string' || setting.type === 'integer'" class="w-full max-w-xl relative">
                                <input 
                                    :id="setting.key"
                                    v-model="editedSettings[setting.key]"
                                    :type="setting.type === 'integer' ? 'number' : 'text'"
                                    class="w-full px-4 py-2.5 bg-background border border-glass-border rounded-xl text-white text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary/20 transition-all font-mono"
                                />
                                <div v-if="setting.type === 'integer'" class="absolute right-3 top-2.5 text-[9px] font-black text-text-dim opacity-50 uppercase tracking-widest pointer-events-none">NUM</div>
                            </div>

                            <!-- Boolean Toggle -->
                            <div v-else-if="setting.type === 'boolean'" class="flex items-center gap-4 cursor-pointer" @click="editedSettings[setting.key] = !editedSettings[setting.key]">
                                <button 
                                    :class="[
                                        'w-11 h-5.5 rounded-full p-1 transition-colors duration-300 relative flex items-center',
                                        editedSettings[setting.key] ? 'bg-primary' : 'bg-surface-light'
                                    ]"
                                >
                                    <span 
                                        :class="[
                                            'w-3.5 h-3.5 bg-white rounded-full transition-transform duration-300 block',
                                            editedSettings[setting.key] ? 'translate-x-[22px]' : 'translate-x-0'
                                        ]"
                                    ></span>
                                </button>
                                <span class="text-xs font-bold uppercase tracking-widest" :class="editedSettings[setting.key] ? 'text-primary' : 'text-text-dim'">
                                    {{ editedSettings[setting.key] ? 'Active' : 'Disabled' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Save Bar -->
            <div class="flex items-center justify-between sticky bottom-6 p-5 rounded-2xl bg-surface/90 backdrop-blur-xl border border-glass-border shadow-2xl z-40 mt-10">
                <div class="hidden sm:block">
                    <p class="text-[10px] font-black text-text-dim uppercase tracking-widest">Global Persistence</p>
                    <p class="text-[11px] text-primary mt-0.5 font-bold">Unsaved changes will be discarded on navigation.</p>
                </div>
                
                <div class="flex items-center gap-4 w-full sm:w-auto justify-end">
                    <button 
                        @click="reset"
                        class="px-5 py-2.5 text-xs font-bold text-text-dim hover:text-white transition-all uppercase tracking-widest"
                        :disabled="saving"
                    >
                        Discard
                    </button>
                    <button 
                        @click="save"
                        :disabled="saving"
                        class="px-8 py-3 bg-primary hover:bg-primary-dark text-black font-black text-xs uppercase tracking-widest rounded-xl shadow-lg shadow-primary/20 transition-all disabled:opacity-50 flex items-center gap-2"
                    >
                        <span v-if="saving" class="animate-spin rounded-full h-3 w-3 border-b-2 border-black"></span>
                        <span v-else>Apply Config</span>
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
