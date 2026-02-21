<template>
    <div class="max-w-5xl mx-auto space-y-12 pb-24">
        <!-- Header -->
        <div class="space-y-2">
            <h1 class="text-4xl font-black text-text-main tracking-tight italic">Intelligence <span class="text-gradient">Optimization</span></h1>
            <p class="text-text-dim font-medium uppercase tracking-[0.2em] text-[10px]">Configure your multi-channel alert infrastructure.</p>
        </div>

        <div v-if="loading" class="flex items-center justify-center py-24">
            <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
        </div>

        <div v-else class="grid grid-cols-1 gap-12">
            <!-- Channel Matrix -->
            <div class="glass-card overflow-hidden rounded-2xl border border-glass-border">
                <div class="bg-surface-light/50 p-8 border-b border-glass-border flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-black text-text-main uppercase tracking-widest">Notification Matrix</h2>
                        <p class="text-[10px] text-text-dim font-bold uppercase tracking-widest mt-1">Status: {{ isDirty ? 'Unsaved Changes Detected' : 'All channels synced' }}</p>
                    </div>
                    <button 
                        @click="savePreferences"
                        :disabled="!isDirty"
                        class="px-8 py-3 bg-primary text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl shadow-xl shadow-primary/20 hover-lift disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    >
                        Synchronize Preferences
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-light/30 border-b border-glass-border">
                                <th class="px-8 py-6 text-[10px] font-black uppercase tracking-[0.3em] text-text-dim">Event Vector</th>
                                <th v-for="channel in uniqueChannels" :key="channel" class="px-8 py-6 text-center text-[10px] font-black uppercase tracking-[0.3em] text-text-dim">
                                    {{ channel }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-glass-border">
                            <tr v-for="event in uniqueEvents" :key="event" class="hover:bg-white/2 cursor-default transition-colors">
                                <td class="px-8 py-6">
                                    <p class="text-sm font-bold text-text-main uppercase tracking-widest">{{ formatEvent(event) }}</p>
                                    <p class="text-[10px] text-text-dim font-medium mt-1 italic">Alert vector triggered on {{ event.replace('_', ' ') }}</p>
                                </td>
                                <td v-for="channel in uniqueChannels" :key="channel" class="px-8 py-6 text-center">
                                    <div class="flex justify-center">
                                        <button 
                                            @click="togglePreference(event, channel)"
                                            class="w-12 h-6 rounded-full transition-all relative outline-none ring-primary/20 focus:ring-4"
                                            :class="getPref(event, channel)?.is_enabled ? 'bg-primary' : 'bg-surface-light border border-glass-border'"
                                        >
                                            <div 
                                                class="absolute top-1 w-4 h-4 rounded-full transition-all"
                                                :class="getPref(event, channel)?.is_enabled ? 'right-1 bg-white' : 'left-1 bg-text-dim'"
                                            ></div>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Global Killswitch Style Mockup -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-white">
                <div class="glass-card p-8 rounded-2xl space-y-4 border-l-4 border-primary">
                    <p class="text-xs font-black uppercase tracking-widest text-primary">Security Note</p>
                    <p class="text-sm leading-relaxed text-text-dim">
                        In-app notifications (the bell icon) are mandatory for critical system audits and cannot be fully disabled per internal security policy.
                    </p>
                </div>
                <div class="glass-card p-8 rounded-2xl space-y-4 border-l-4 border-secondary">
                    <p class="text-xs font-black uppercase tracking-widest text-secondary">Latency Optimization</p>
                    <p class="text-sm leading-relaxed text-text-dim">
                        Emails are dispatched via secondary queues to prevent frontend blocking. Typical lag: &lt; 2 seconds.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from '@/plugins/axios';
import { useToast } from 'vue-toastification';

const toast = useToast();
const loading = ref(true);
const preferences = ref([]);
const isDirty = ref(false);

const fetchData = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/user/notification-preferences');
        preferences.value = response.data.data;
    } catch (error) {
        toast.error('Failed to load neural preferences');
    } finally {
        loading.value = false;
    }
};

const uniqueChannels = computed(() => {
    return [...new Set(preferences.value.map(p => p.channel))];
});

const uniqueEvents = computed(() => {
    return [...new Set(preferences.value.map(p => p.event_type))];
});

const getPref = (event, channel) => {
    return preferences.value.find(p => p.event_type === event && p.channel === channel);
};

const togglePreference = (event, channel) => {
    const pref = getPref(event, channel);
    if (pref) {
        pref.is_enabled = !pref.is_enabled;
        isDirty.value = true;
    }
};

const formatEvent = (event) => {
    return event.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
};

const savePreferences = async () => {
    try {
        await axios.patch('/user/notification-preferences', {
            preferences: preferences.value.map(p => ({ id: p.id, is_enabled: p.is_enabled }))
        });
        toast.success('Alert matrix synchronized successfully');
        isDirty.value = false;
    } catch (error) {
        toast.error('Failed to sync preferences');
    }
};

onMounted(fetchData);
</script>

<style scoped>
.text-gradient {
    background: linear-gradient(to right, var(--color-primary, #6366f1), var(--color-secondary, #a855f7));
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}
</style>
