<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Holidays</h1>
                <p class="text-text-dim text-sm mt-1">Specify non-working dates for the system.</p>
            </div>
            <button 
                @click="showModal = true"
                class="px-4 py-2 bg-primary hover:bg-primary-dark text-surface font-black text-sm rounded-lg transition-all"
            >
                + Add Holiday
            </button>
        </div>

        <div v-if="loading" class="flex items-center justify-center p-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <div v-else class="max-w-4xl space-y-4">
            <div v-if="holidays.length === 0" class="bg-surface rounded-xl border border-glass-border p-12 text-center">
                <p class="text-text-dim">No holidays defined yet.</p>
            </div>

            <div v-else class="bg-surface rounded-xl border border-glass-border overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-surface-light border-b border-glass-border">
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">Name</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">Date</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">Recurring</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-glass-border text-gray-300">
                        <tr v-for="holiday in holidays" :key="holiday.id">
                            <td class="px-6 py-4 font-medium text-white">{{ holiday.name }}</td>
                            <td class="px-6 py-4 text-sm">{{ holiday.date }}</td>
                            <td class="px-6 py-4">
                                <span v-if="holiday.is_recurring" class="text-xs bg-primary/20 text-primary px-2 py-1 rounded">Yes</span>
                                <span v-else class="text-xs bg-gray-500/20 text-gray-500 px-2 py-1 rounded">No</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button @click="deleteHoliday(holiday.id)" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Holiday Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-background/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-surface border border-glass-border rounded-2xl w-full max-w-md p-6 shadow-2xl">
                <h2 class="text-xl font-bold text-white mb-4">Add New Holiday</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Holiday Name</label>
                        <input v-model="newHoliday.name" type="text" placeholder="e.g. Christmas Day" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Date</label>
                        <input v-model="newHoliday.date" type="date" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary" />
                    </div>
                    <div class="flex items-center gap-3">
                        <input v-model="newHoliday.is_recurring" type="checkbox" id="recurring" class="w-4 h-4 rounded border-glass-border bg-background text-primary focus:ring-primary" />
                        <label for="recurring" class="text-sm text-gray-300">Annual recurring holiday</label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-8">
                    <button @click="showModal = false" class="px-4 py-2 text-sm text-text-dim hover:text-white transition-colors">Cancel</button>
                    <button @click="addHoliday" :disabled="!newHoliday.name || !newHoliday.date" class="px-6 py-2 bg-primary hover:bg-primary-dark text-surface font-bold rounded-lg transition-all disabled:opacity-50">Create Holiday</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '@/plugins/axios';

const loading = ref(true);
const holidays = ref([]);
const showModal = ref(false);
const newHoliday = ref({ name: '', date: '', is_recurring: false });

const fetchHolidays = async () => {
    try {
        const response = await axios.get('/admin/holidays');
        holidays.value = response.data.data;
    } catch (error) {
        alert('Failed to load holidays');
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
    } catch (error) {
        alert('Failed to add holiday');
    }
};

const deleteHoliday = async (id) => {
    if (!confirm('Are you sure you want to delete this holiday?')) return;
    try {
        await axios.delete(`/admin/holidays/${id}`);
        fetchHolidays();
    } catch (error) {
        alert('Failed to delete holiday');
    }
};

onMounted(fetchHolidays);
</script>
