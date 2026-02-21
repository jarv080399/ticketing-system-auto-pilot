<template>
    <div class="space-y-8 animate-fade-in pb-12">

        <!-- ─── Page Header ─── -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black text-text-main tracking-tight">
                    Notification <span class="text-primary">Preferences</span>
                </h1>
                <p class="text-text-dim font-medium mt-1">Configure how and when you receive alerts across all channels.</p>
            </div>
            <div class="flex gap-4">
                <button
                    @click="savePreferences"
                    :disabled="!isDirty || saving"
                    class="px-8 py-3 bg-primary hover:bg-primary-dark text-white font-black rounded-xl shadow-xl shadow-primary/20 hover-lift active:scale-95 transition-all flex items-center gap-2 disabled:opacity-40 disabled:shadow-none disabled:hover:scale-100 disabled:cursor-not-allowed"
                >
                    <svg v-if="saving" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ saving ? 'Saving...' : 'Save Preferences' }}
                </button>
            </div>
        </div>

        <!-- ─── Loading ─── -->
        <div v-if="loading" class="flex items-center justify-center py-24">
            <div class="flex flex-col items-center gap-4">
                <div class="animate-spin rounded-full h-10 w-10 border-b-4 border-primary"></div>
                <p class="text-sm font-bold text-text-dim animate-pulse uppercase tracking-widest">Loading Preferences...</p>
            </div>
        </div>

        <template v-else>

            <!-- ─── Unsaved Banner ─── -->
            <Transition name="slide-down">
                <div v-if="isDirty" class="glass-card p-4 rounded-2xl border-l-4 border-amber-400 flex items-center gap-3">
                    <span class="text-amber-400 text-lg">⚠️</span>
                    <p class="text-sm font-bold text-text-dim">
                        You have <span class="text-amber-400">unsaved changes</span>. Click "Save Preferences" to apply them.
                    </p>
                </div>
            </Transition>

            <!-- ─── Notification Matrix Table ─── -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="bg-surface-light/50 px-6 py-5 border-b border-glass-border">
                    <h2 class="text-[10px] font-black uppercase tracking-widest text-text-dim">Notification Matrix</h2>
                    <p class="text-sm font-bold text-text-main mt-0.5">Toggle which events trigger alerts on each channel.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-light/50 border-b border-glass-border">
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim min-w-[240px]">Event</th>
                                <th
                                    v-for="channel in uniqueChannels"
                                    :key="channel"
                                    class="px-6 py-5 text-center text-[10px] font-black uppercase tracking-widest text-text-dim"
                                >
                                    <div class="flex flex-col items-center gap-1.5">
                                        <span class="text-lg">{{ channelIcon(channel) }}</span>
                                        <span>{{ channel }}</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-glass-border">
                            <tr
                                v-for="event in uniqueEvents"
                                :key="event"
                                class="hover:bg-white/5 transition-colors group"
                            >
                                <!-- Event Info -->
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-surface-light flex items-center justify-center text-base flex-shrink-0">
                                            {{ eventIcon(event) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-text-main text-sm">{{ formatEvent(event) }}</p>
                                            <p class="text-xs text-text-dim font-mono mt-0.5">{{ event }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Toggles per channel -->
                                <td
                                    v-for="channel in uniqueChannels"
                                    :key="channel"
                                    class="px-6 py-5 text-center"
                                >
                                    <div class="flex justify-center">
                                        <button
                                            @click="togglePreference(event, channel)"
                                            :class="[
                                                'w-12 h-6 rounded-full transition-all duration-300 relative outline-none focus:ring-2 focus:ring-primary/40',
                                                getPref(event, channel)?.is_enabled
                                                    ? 'bg-primary shadow-lg shadow-primary/20'
                                                    : 'bg-surface-light border border-glass-border'
                                            ]"
                                            :title="`Toggle ${channel} for ${formatEvent(event)}`"
                                        >
                                            <div
                                                class="absolute top-1 w-4 h-4 rounded-full transition-all duration-300"
                                                :class="getPref(event, channel)?.is_enabled
                                                    ? 'right-1 bg-white shadow-sm'
                                                    : 'left-1 bg-text-dim'"
                                            ></div>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Empty state -->
                    <div v-if="uniqueEvents.length === 0" class="py-20 text-center">
                        <p class="text-text-dim italic">No notification preferences found.</p>
                    </div>
                </div>
            </div>

            <!-- ─── Info Cards ─── -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="glass-card p-6 rounded-2xl border-l-4 border-primary flex gap-4">
                    <span class="text-2xl flex-shrink-0">🔔</span>
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-primary mb-1">Security Note</p>
                        <p class="text-sm text-text-dim leading-relaxed">
                            In-app notifications are mandatory for critical system events and cannot be fully disabled per internal security policy.
                        </p>
                    </div>
                </div>
                <div class="glass-card p-6 rounded-2xl border-l-4 border-emerald-500 flex gap-4">
                    <span class="text-2xl flex-shrink-0">⚡</span>
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-emerald-400 mb-1">Real-time Updates</p>
                        <p class="text-sm text-text-dim leading-relaxed">
                            Emails are dispatched via async queues. In-app alerts are pushed in real-time via WebSocket. Typical lag: &lt; 2 seconds.
                        </p>
                    </div>
                </div>
            </div>

        </template>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from '@/plugins/axios';
import { useToast } from 'vue-toastification';

const toast = useToast();
const loading = ref(true);
const saving  = ref(false);
const preferences = ref([]);
const isDirty = ref(false);

const fetchData = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/user/notification-preferences');
        preferences.value = response.data.data;
    } catch {
        toast.error('Failed to load notification preferences.');
    } finally {
        loading.value = false;
    }
};

const uniqueChannels = computed(() => [...new Set(preferences.value.map(p => p.channel))]);
const uniqueEvents   = computed(() => [...new Set(preferences.value.map(p => p.event_type))]);

const getPref = (event, channel) =>
    preferences.value.find(p => p.event_type === event && p.channel === channel);

const togglePreference = (event, channel) => {
    const pref = getPref(event, channel);
    if (pref) {
        pref.is_enabled = !pref.is_enabled;
        isDirty.value = true;
    }
};

const savePreferences = async () => {
    saving.value = true;
    try {
        await axios.patch('/user/notification-preferences', {
            preferences: preferences.value.map(p => ({ id: p.id, is_enabled: p.is_enabled }))
        });
        toast.success('Notification preferences saved.');
        isDirty.value = false;
    } catch {
        toast.error('Failed to save preferences.');
    } finally {
        saving.value = false;
    }
};

const formatEvent = (event) =>
    event.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');

const channelIcon = (channel) => {
    const icons = { email: '📧', in_app: '🔔', slack: '💬', teams: '🟦', sms: '📱' };
    return icons[channel] ?? '📡';
};

const eventIcon = (event) => {
    if (event.includes('ticket'))  return '🎫';
    if (event.includes('comment')) return '💬';
    if (event.includes('assign'))  return '👤';
    if (event.includes('sla'))     return '⏱️';
    if (event.includes('close'))   return '✅';
    if (event.includes('survey'))  return '📋';
    return '📣';
};

onMounted(fetchData);
</script>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.25s ease;
}
.slide-down-enter-from,
.slide-down-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>
