<template>
    <div class="space-y-8 pb-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">System Holidays</h1>
                <p class="text-text-dim text-sm mt-2 max-w-2xl">
                    Specify non-working dates. SLA timers will automatically pause during these designated dates globally, bypassing standard business hours.
                </p>
            </div>
            <button 
                @click="showModal = true"
                class="px-5 py-2.5 bg-primary hover:bg-primary-dark shadow-lg shadow-primary/20 text-white font-black text-sm rounded-xl transition-all hover-lift active:scale-95 flex items-center gap-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Add Holiday
            </button>
        </div>

        <div v-if="loading" class="flex items-center justify-center p-24">
            <div class="flex flex-col items-center gap-4">
                <div class="animate-spin rounded-full h-10 w-10 border-b-4 border-primary"></div>
                <p class="text-sm font-bold text-text-dim animate-pulse uppercase tracking-widest">Loading Records...</p>
            </div>
        </div>

        <div v-else class="max-w-7xl space-y-4">
            <div v-if="holidays.length === 0" class="bg-surface rounded-2xl border border-glass-border p-16 text-center shadow-xl">
                <div class="w-16 h-16 bg-surface-light rounded-2xl flex items-center justify-center mx-auto mb-4 border border-glass-border shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <h3 class="text-lg font-black text-white tracking-wide">No Holidays Active</h3>
                <p class="text-text-dim mt-2 max-w-sm mx-auto text-sm leading-relaxed">No custom non-working dates have been defined yet. The standard business hour schedule will strictly apply every week.</p>
                <button @click="showModal = true" class="mt-6 px-6 py-2.5 bg-surface-light hover:bg-white/5 border border-glass-border text-white font-bold rounded-xl transition-all shadow-sm">
                    Create First Holiday
                </button>
            </div>

            <div v-else class="bg-surface rounded-2xl border border-glass-border shadow-xl overflow-hidden relative">
                <div class="px-8 py-5 bg-surface-light border-b border-glass-border flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black uppercase tracking-widest text-white">Holiday Calendar</h2>
                        <p class="text-xs text-text-dim mt-1">Listed dates where the system treats as offline globally.</p>
                    </div>
                    
                    <div class="hidden justify-center sm:flex w-10 h-10 bg-surface rounded-xl border border-glass-border items-center text-primary shadow-sm hover:scale-110 transition-transform cursor-help relative group/tooltip">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        <div class="absolute bottom-full right-0 mb-3 hidden group-hover/tooltip:block w-72 p-3 bg-gray-800 text-xs text-blue-100 rounded-xl border border-blue-500/30 shadow-2xl z-20 normal-case leading-relaxed font-normal">
                            <div class="font-black text-white mb-1">Calendar Guide:</div>
                            "Recurring" holidays automatically pause SLA timers on the same month/date every year without manual renewal.
                        </div>
                    </div>
                </div>
                
                <div class="overflow-x-auto bg-background/30 p-2">
                    <table class="w-full text-left whitespace-nowrap">
                        <thead>
                            <tr class="border-b border-glass-border">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Holiday Name</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Scheduled Date</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Behavior</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-glass-border text-gray-300">
                            <tr v-for="holiday in holidays" :key="holiday.id" class="hover:bg-white/5 transition-colors group">
                                <td class="px-6 py-5">
                                    <div class="font-black text-white tracking-wide text-sm">{{ holiday.name }}</div>
                                </td>
                                <td class="px-6 py-5">
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-surface border border-glass-border rounded-lg text-sm font-mono text-primary-dim shadow-inner">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        {{ holiday.date }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <span v-if="holiday.is_recurring" class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-widest rounded-md">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" /></svg>
                                        Annual Recurring
                                    </span>
                                    <span v-else class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-500/10 border border-gray-500/20 text-text-dim text-[10px] font-black uppercase tracking-widest rounded-md">
                                        One-time Only
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex flex-row justify-end items-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="deleteHoliday(holiday.id)" class="p-2 bg-surface border border-glass-border rounded-lg text-text-dim hover:text-red-400 hover:bg-red-500/10 transition-all font-bold text-xs flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            Remove
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
        <div v-if="showModal" class="fixed inset-0 bg-background/90 backdrop-blur-md flex items-center justify-center z-50 p-4">
            <div class="bg-surface border border-glass-border rounded-2xl w-full max-w-md p-8 shadow-[0_0_50px_rgba(0,0,0,0.5)] transform scale-100 transition-all">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-primary/20 rounded-xl flex items-center justify-center text-primary border border-primary/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-white">Add New Holiday</h2>
                        <p class="text-xs text-text-dim font-medium mt-1">SLA timers will freeze on this date.</p>
                    </div>
                </div>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Holiday Name</label>
                        <input v-model="newHoliday.name" type="text" placeholder="e.g. Christmas Day, New Year..." class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner" />
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Calendar Date</label>
                        <input v-model="newHoliday.date" type="date" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner" />
                    </div>
                    <div class="flex items-center gap-4 bg-background px-4 py-3 p-4 rounded-xl border border-glass-border shadow-inner cursor-pointer hover:border-gray-600 transition-colors" @click="newHoliday.is_recurring = !newHoliday.is_recurring">
                        <button 
                            :class="[
                                'w-12 h-6 rounded-full p-1 transition-colors duration-300 ease-in-out relative flex-shrink-0 flex items-center justify-start',
                                newHoliday.is_recurring ? 'bg-emerald-500' : 'bg-gray-700'
                            ]"
                        >
                            <span 
                                :class="[
                                    'w-4 h-4 bg-white rounded-full transition-transform duration-300 shadow-sm block',
                                    newHoliday.is_recurring ? 'translate-x-6' : 'translate-x-0'
                                ]"
                            ></span>
                        </button>
                        <div>
                            <span class="text-sm font-black tracking-wide" :class="newHoliday.is_recurring ? 'text-emerald-400' : 'text-gray-300'">
                                Annual Recurring
                            </span>
                            <p class="text-[10px] font-medium text-text-dim/80 mt-0.5" v-if="newHoliday.is_recurring">Repeats every year on {{ (newHoliday.date || 'this date').split('-').slice(1).join('-') }}</p>
                            <p class="text-[10px] font-medium text-text-dim/80 mt-0.5" v-else>Will only apply precisely to the selected year.</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-glass-border">
                    <button @click="showModal = false" class="px-5 py-2.5 text-sm text-text-dim font-bold hover:text-white hover:bg-white/5 rounded-xl transition-all">Cancel</button>
                    <button @click="addHoliday" :disabled="!newHoliday.name || !newHoliday.date" class="px-7 py-2.5 bg-primary hover:bg-primary-dark text-white shadow-lg shadow-primary/30 font-black rounded-xl transition-all hover-lift active:scale-95 disabled:opacity-50 disabled:hover:scale-100 disabled:shadow-none flex items-center gap-2">
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
        toast.error('Failed to connect to the database to retrieve holidays.');
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
        toast.success('Successfully registered new system holiday.');
    } catch (error) {
        toast.error('Failed to add holiday to schedule. Check for duplicate dates.');
    }
};

const deleteHoliday = async (id) => {
    if (!confirm('Are you absolutely sure you want to remove this holiday? Active SLA timers on this date will resume instantly.')) return;
    try {
        await axios.delete(`/admin/holidays/${id}`);
        fetchHolidays();
        toast.info('Holiday configuration successfully removed.');
    } catch (error) {
        toast.error('Failed to delete holiday.');
    }
};

onMounted(fetchHolidays);
</script>
