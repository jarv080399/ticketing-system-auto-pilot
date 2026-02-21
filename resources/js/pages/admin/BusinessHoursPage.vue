<template>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Business Hours</h1>
            <p class="text-text-dim text-sm mt-1">Configure your support availability for SLA calculations.</p>
        </div>

        <div v-if="loading" class="flex items-center justify-center p-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <div v-else class="space-y-6 max-w-4xl">
            <div class="bg-surface rounded-xl border border-glass-border overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-surface-light border-b border-glass-border">
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">Day</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">Working Day</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">Start Time</th>
                            <th class="px-6 py-4 text-xs font-black uppercase tracking-widest text-text-dim">End Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-glass-border text-gray-300">
                        <tr v-for="(day, index) in hours" :key="index">
                            <td class="px-6 py-4 font-medium text-white">{{ dayNames[day.day_of_week] }}</td>
                            <td class="px-6 py-4">
                                <button 
                                    @click="day.is_working_day = !day.is_working_day"
                                    :class="[
                                        'w-12 h-6 rounded-full p-1 transition-colors duration-200 relative',
                                        day.is_working_day ? 'bg-primary' : 'bg-gray-600'
                                    ]"
                                >
                                    <span 
                                        :class="[
                                            'w-4 h-4 bg-white rounded-full transition-transform duration-200 shadow-sm inline-block',
                                            day.is_working_day ? 'translate-x-6' : 'translate-x-0'
                                        ]"
                                    ></span>
                                </button>
                            </td>
                            <td class="px-6 py-4">
                                <input 
                                    v-model="day.start_time"
                                    type="time"
                                    :disabled="!day.is_working_day"
                                    class="bg-background border border-glass-border rounded-lg px-3 py-1 text-sm focus:outline-none focus:border-primary disabled:opacity-50"
                                />
                            </td>
                            <td class="px-6 py-4">
                                <input 
                                    v-model="day.end_time"
                                    type="time"
                                    :disabled="!day.is_working_day"
                                    class="bg-background border border-glass-border rounded-lg px-3 py-1 text-sm focus:outline-none focus:border-primary disabled:opacity-50"
                                />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end">
                <button 
                    @click="updateHours"
                    :disabled="saving"
                    class="px-6 py-2 bg-primary hover:bg-primary-dark text-surface font-bold rounded-lg transition-all flex items-center gap-2"
                >
                    <span v-if="saving" class="animate-spin rounded-full h-4 w-4 border-b-2 border-surface"></span>
                    Save Changes
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
const hours = ref([]);

const dayNames = [
    'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
];

const fetchHours = async () => {
    try {
        const response = await axios.get('/admin/business-hours');
        hours.value = response.data.data;
    } catch (error) {
        alert('Failed to load business hours');
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
        alert('Business hours updated successfully');
    } catch (error) {
        alert('Failed to update business hours');
    } finally {
        saving.value = false;
    }
};

onMounted(fetchHours);
</script>
