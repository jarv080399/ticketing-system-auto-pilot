<template>
    <div class="space-y-8 pb-10">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-white tracking-tight">Business Hours</h1>
            <p class="text-text-dim text-sm mt-2 max-w-2xl">
                Configure your global support availability. These hours dictate SLA calculations, determining when time tracking pauses for active tickets during off hours.
            </p>
        </div>

        <div v-if="loading" class="flex items-center justify-center p-24">
            <div class="flex flex-col items-center gap-4">
                <div class="animate-spin rounded-full h-10 w-10 border-b-4 border-primary"></div>
                <p class="text-sm font-bold text-text-dim animate-pulse uppercase tracking-widest">Loading Schedule...</p>
            </div>
        </div>

        <div v-else class="space-y-10 max-w-7xl">
            <div class="bg-surface rounded-2xl border border-glass-border shadow-xl overflow-hidden relative">
                
                <div class="px-8 py-5 bg-surface-light border-b border-glass-border flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black uppercase tracking-widest text-white">Standard Weekly Schedule</h2>
                        <p class="text-xs text-text-dim mt-1">Define active working hours for each day of the week.</p>
                    </div>
                    
                    <div class="hidden justify-center sm:flex w-10 h-10 bg-surface rounded-xl border border-glass-border items-center text-primary shadow-sm hover:scale-110 transition-transform cursor-help relative group/tooltip">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        <div class="absolute bottom-full right-0 mb-3 hidden group-hover/tooltip:block w-64 p-3 bg-gray-800 text-xs text-blue-100 rounded-xl border border-blue-500/30 shadow-2xl z-20 normal-case leading-relaxed font-normal">
                            <div class="font-black text-white mb-1">Schedule Guide:</div>
                            SLA timers only tick during active hours on "Working Days". Time will safely freeze when out of bounds.
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto bg-background/30 p-4">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-glass-border">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim group/item relative">
                                    Day of Week
                                </th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Operational Window</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-glass-border text-gray-300">
                            <tr v-for="(day, index) in hours" :key="index" class="hover:bg-white/5 transition-colors">
                                <td class="px-6 py-6 border-l-2" :class="day.is_working_day ? 'border-emerald-500' : 'border-gray-600'">
                                    <div class="font-black tracking-wide text-white">{{ dayNames[day.day_of_week] }}</div>
                                    <p class="text-[10px] font-medium text-text-dim mt-1 border-l-2 border-primary/30 pl-2">
                                        {{ day.is_working_day ? 'SLA clock actively tracks.' : 'System treats as offline.' }}
                                    </p>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-4 bg-surface px-4 py-2.5 rounded-xl border border-glass-border shadow-inner cursor-pointer hover:border-gray-500 w-max transition-colors" @click="day.is_working_day = !day.is_working_day">
                                        <button 
                                            :class="[
                                                'w-12 h-6 rounded-full p-1 transition-colors duration-300 ease-in-out relative flex-shrink-0 flex items-center justify-start',
                                                day.is_working_day ? 'bg-emerald-500' : 'bg-gray-700'
                                            ]"
                                        >
                                            <span 
                                                :class="[
                                                    'w-4 h-4 bg-white rounded-full transition-transform duration-300 shadow-sm block',
                                                    day.is_working_day ? 'translate-x-6' : 'translate-x-0'
                                                ]"
                                            ></span>
                                        </button>
                                        <span class="text-xs font-black tracking-wide" :class="day.is_working_day ? 'text-emerald-400' : 'text-gray-400'">
                                            {{ day.is_working_day ? 'Working Day' : 'Rest Day' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-4" :class="{'opacity-40 grayscale pointer-events-none': !day.is_working_day}">
                                        <div class="relative">
                                            <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-1 absolute -top-5 left-1">Start Time</label>
                                            <input 
                                                v-model="day.start_time"
                                                type="time"
                                                :disabled="!day.is_working_day"
                                                class="bg-surface border border-glass-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner font-mono"
                                            />
                                        </div>
                                        <span class="text-text-dim font-bold mt-2">—</span>
                                        <div class="relative">
                                            <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-1 absolute -top-5 left-1">End Time</label>
                                            <input 
                                                v-model="day.end_time"
                                                type="time"
                                                :disabled="!day.is_working_day"
                                                class="bg-surface border border-glass-border rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner font-mono"
                                            />
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
                        @click="updateHours"
                        :disabled="saving"
                        class="px-8 py-3 bg-primary hover:bg-primary-dark text-white font-black rounded-xl shadow-lg shadow-primary/30 hover-lift active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 min-w-[180px]"
                    >
                        <span v-if="saving" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                        <svg v-if="!saving" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        <span>{{ saving ? 'Processing...' : 'Apply Schedule' }}</span>
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
const hours = ref([]);

const dayNames = [
    'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
];

const fetchHours = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/admin/business-hours');
        hours.value = response.data.data;
    } catch (error) {
        toast.error('Failed to load business hours');
    } finally {
        loading.value = false;
    }
};

const updateHours = async () => {
    saving.value = true;
    try {
        await axios.put('/admin/business-hours', {
            hours: hours.value
        });
        toast.success('Business hours successfully committed to system schedule.');
    } catch (error) {
        toast.error('Failed to update business hours.');
    } finally {
        saving.value = false;
    }
};

const reset = () => {
    fetchHours();
    toast.info('Discarded local changes.');
};

onMounted(fetchHours);
</script>
