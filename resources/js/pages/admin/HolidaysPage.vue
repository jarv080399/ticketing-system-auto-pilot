<template>
    <div class="space-y-6 pb-10">
        <!-- Page Header -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold text-text-main tracking-tight">System Holidays</h1>
                <p class="mt-1 text-sm text-text-dim">
                    Specify non-working dates. SLA timers will automatically pause during these designated dates globally.
                </p>
            </div>
            <button
                @click="showModal = true"
                class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Add Holiday
            </button>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="flex items-center justify-center p-24">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <div v-else class="space-y-6">
            <!-- Empty State -->
            <div v-if="holidays.length === 0" class="bg-surface border border-glass-border rounded-xl p-16 text-center">
                <div class="w-16 h-16 bg-surface-light rounded-xl flex items-center justify-center mx-auto mb-4 border border-glass-border">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <h3 class="text-lg font-bold text-text-main">No Holidays Active</h3>
                <p class="text-text-dim mt-2 max-w-sm mx-auto text-sm">No custom non-working dates have been defined. Standard business hours will apply.</p>
                <button @click="showModal = true" class="mt-6 bg-surface-light hover:bg-surface text-text-dim hover:text-text-main text-sm font-semibold px-4 py-2 rounded-lg transition-colors border border-glass-border">
                    Create First Holiday
                </button>
            </div>

            <!-- Holiday Calendar — Section Panel -->
            <div v-else class="bg-surface border border-glass-border rounded-xl overflow-hidden">
                <div class="px-6 py-4 bg-surface-light border-b border-glass-border flex items-center justify-between">
                    <div>
                        <h2 class="text-[11px] font-semibold tracking-widest text-text-dim uppercase">Holiday Calendar</h2>
                        <p class="text-xs text-text-dim mt-0.5">Listed dates where the system treats as offline globally.</p>
                    </div>
                    <div class="hidden sm:flex w-8 h-8 bg-background border border-glass-border rounded-lg items-center justify-center text-primary cursor-help relative group/tooltip">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <div class="absolute bottom-full right-0 mb-2 hidden group-hover/tooltip:block w-56 p-3 bg-surface border border-glass-border text-xs text-text-dim rounded-lg shadow-lg z-20 leading-relaxed">
                            <div class="font-semibold text-text-main mb-1">Calendar Guide</div>
                            "Recurring" holidays automatically pause SLA timers on the same month/date every year.
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="bg-surface-light border-b border-glass-border">
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Holiday Name</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Scheduled Date</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Behavior</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-glass-border">
                            <tr v-for="holiday in holidays" :key="holiday.id" class="hover:bg-surface-light/40 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-text-main">{{ holiday.name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-background border border-glass-border rounded-lg text-sm font-mono text-primary">
                                        {{ holiday.date }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="holiday.is_recurring" class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">
                                        Annual Recurring
                                    </span>
                                    <span v-else class="bg-slate-500/20 text-slate-400 border border-slate-500/30 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">
                                        One-time Only
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-row justify-end items-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="deleteHoliday(holiday.id)" class="bg-red-500/20 hover:bg-red-500/30 text-red-400 text-sm font-semibold p-2 rounded-lg transition-colors border border-red-500/30">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Holiday Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-background/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-surface border border-glass-border rounded-xl w-full max-w-md p-8 shadow-2xl">
                <div class="flex items-center gap-3 mb-8 pb-4 border-b border-glass-border">
                    <div class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center text-primary border border-primary/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-text-main">Add New Holiday</h2>
                        <p class="text-xs text-text-dim mt-0.5">SLA timers will freeze on this date.</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-text-main">Holiday Name</label>
                        <input v-model="newHoliday.name" type="text" placeholder="e.g. Christmas Day..." class="w-full bg-background border border-glass-border rounded-lg px-4 py-2.5 text-sm text-text-main placeholder-text-dim focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" />
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-text-main">Calendar Date</label>
                        <input v-model="newHoliday.date" type="date" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2.5 text-sm text-text-main focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" />
                    </div>
                    <div class="flex items-center justify-between bg-background border border-glass-border rounded-lg px-4 py-3">
                        <div>
                            <span class="text-sm font-medium text-text-main">Annual Recurring</span>
                            <p class="text-xs text-text-dim mt-0.5">Repeat every year</p>
                        </div>
                        <button
                            @click="newHoliday.is_recurring = !newHoliday.is_recurring"
                            :class="newHoliday.is_recurring ? 'bg-primary' : 'bg-surface-light'"
                            class="w-11 h-6 rounded-full p-1 flex items-center transition-colors focus:outline-none flex-shrink-0"
                        >
                            <span
                                :class="newHoliday.is_recurring ? 'translate-x-5' : 'translate-x-0'"
                                class="w-4 h-4 bg-white rounded-full shadow transition-transform block"
                            ></span>
                        </button>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-glass-border">
                    <button @click="showModal = false" class="bg-surface-light hover:bg-surface text-text-dim text-sm font-semibold px-4 py-2 rounded-lg transition-colors border border-glass-border">Cancel</button>
                    <button @click="addHoliday" :disabled="!newHoliday.name || !newHoliday.date" class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-6 py-2 rounded-lg transition-colors disabled:opacity-50">
                        Create Record
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
const holidays = ref([]);
const showModal = ref(false);
const newHoliday = ref({ name: '', date: '', is_recurring: false });

const fetchHolidays = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/admin/holidays');
        holidays.value = response.data.data;
    } catch (error) {
        toast.error('Failed to retrieve holidays.');
    } finally {
        loading.value = false;
    }
};

const addHoliday = async () => {
    try {
        await axios.post('/admin/holidays', newHoliday.value);
        showModal.value = false;
        newHoliday.value = { name: '', date: '', is_recurring: false };
        fetchHolidays();
        toast.success('Holiday registered successfully.');
    } catch (error) {
        toast.error('Failed to add holiday.');
    }
};

const deleteHoliday = async (id) => {
    if (!confirm('Are you sure you want to remove this holiday?')) return;
    try {
        await axios.delete(`/admin/holidays/${id}`);
        fetchHolidays();
        toast.info('Holiday removed.');
    } catch (error) {
        toast.error('Failed to delete holiday.');
    }
};

onMounted(fetchHolidays);
</script>
