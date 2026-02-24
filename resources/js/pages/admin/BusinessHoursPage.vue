<template>
    <div class="space-y-6 pb-10">
        <!-- Page Header -->
        <div>
            <h1 class="text-3xl font-bold text-text-main">Business Hours</h1>
            <p class="mt-1 text-sm text-text-dim">
                Configure your global support availability. These hours dictate SLA calculations and determine when time tracking pauses.
            </p>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex items-center justify-center p-24">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <div v-else class="space-y-6">
            <!-- Schedule — Section Panel -->
            <div class="bg-surface border border-glass-border rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-surface-light border-b border-glass-border flex items-center justify-between">
                    <div>
                        <h2 class="text-[11px] font-semibold tracking-widest text-text-dim uppercase">Standard Weekly Schedule</h2>
                        <p class="text-xs text-text-dim mt-0.5">Define active working hours for each day of the week.</p>
                    </div>
                    <div class="hidden sm:flex w-8 h-8 bg-background border border-glass-border rounded-lg items-center justify-center text-primary cursor-help relative group/tooltip">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        <div class="absolute bottom-full right-0 mb-2 hidden group-hover/tooltip:block w-56 p-3 bg-surface border border-glass-border text-xs text-text-dim rounded-lg shadow-lg z-20 leading-relaxed">
                            <div class="font-semibold text-text-main mb-1">Schedule Guide</div>
                            SLA timers only tick during active hours on "Working Days". Time freezes when out of bounds.
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="bg-surface-light border-b border-glass-border">
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Day</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Status</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Operational Window</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-glass-border">
                            <tr v-for="(day, index) in hours" :key="index" class="hover:bg-surface-light/40 transition-colors">
                                <!-- Day Name -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-1 h-8 rounded-full" :class="day.is_working_day ? 'bg-emerald-400' : 'bg-slate-600'"></div>
                                        <div>
                                            <div class="font-semibold text-text-main">{{ dayNames[day.day_of_week] }}</div>
                                            <p class="text-[10px] text-text-dim mt-0.5">
                                                {{ day.is_working_day ? 'SLA clock actively tracks' : 'System treats as offline' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Toggle -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3 cursor-pointer" @click="day.is_working_day = !day.is_working_day">
                                        <button
                                            :class="day.is_working_day ? 'bg-primary' : 'bg-surface-light'"
                                            class="relative w-11 h-6 rounded-full transition-colors focus:outline-none"
                                        >
                                            <span
                                                :class="day.is_working_day ? 'translate-x-5' : 'translate-x-1'"
                                                class="absolute top-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform"
                                            ></span>
                                        </button>
                                        <span class="text-xs font-bold" :class="day.is_working_day ? 'text-emerald-400' : 'text-text-dim'">
                                            {{ day.is_working_day ? 'Working Day' : 'Rest Day' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Time Pickers -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3" :class="{'opacity-30 pointer-events-none': !day.is_working_day}">
                                        <div class="flex items-center gap-1 bg-background border border-glass-border rounded-lg px-3 py-2">
                                            <input
                                                v-model="day.start_time"
                                                type="time"
                                                :disabled="!day.is_working_day"
                                                class="bg-transparent text-sm text-text-main font-mono focus:outline-none"
                                            />
                                        </div>
                                        <span class="text-text-dim">—</span>
                                        <div class="flex items-center gap-1 bg-background border border-glass-border rounded-lg px-3 py-2">
                                            <input
                                                v-model="day.end_time"
                                                type="time"
                                                :disabled="!day.is_working_day"
                                                class="bg-transparent text-sm text-text-main font-mono focus:outline-none"
                                            />
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Save Bar — inline, not sticky -->
            <div class="flex items-center justify-between p-6 rounded-xl bg-surface border border-glass-border">
                <div class="hidden sm:block">
                    <p class="text-[11px] font-semibold tracking-widest text-text-dim uppercase">Unsaved Changes</p>
                    <p class="text-xs text-text-dim mt-0.5">Save your configurations before navigating away.</p>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <button
                        @click="reset"
                        class="bg-surface-light hover:bg-surface text-text-dim hover:text-text-main text-sm font-semibold px-4 py-2 rounded-lg transition-colors border border-glass-border"
                        :disabled="saving"
                    >
                        Discard
                    </button>
                    <button
                        @click="updateHours"
                        :disabled="saving"
                        class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-6 py-2 rounded-lg transition-colors disabled:opacity-50 flex items-center gap-2"
                    >
                        <span v-if="saving" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                        {{ saving ? 'Processing...' : 'Apply Schedule' }}
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
        toast.success('Business hours updated.');
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
