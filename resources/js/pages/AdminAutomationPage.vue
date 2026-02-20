<template>
    <div class="space-y-8 animate-in mt-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-text-main flex items-center gap-4">
                    <span class="w-12 h-12 rounded-2xl bg-primary/20 flex items-center justify-center text-primary text-2xl">🤖</span>
                    Automation Engine
                </h1>
                <p class="text-text-dim font-medium mt-2">Manage Autopilot rules, SLAs, and escalation paths.</p>
            </div>
            
            <div class="flex items-center gap-4">
                <button @click="openNewRuleModal = true" class="px-6 py-3 bg-primary hover:bg-primary-dark text-white text-sm font-bold rounded-xl flex items-center gap-2 hover-lift">
                    <span class="text-xl">+</span> New Rule
                </button>
            </div>
        </div>

        <!-- Horizontal Tabs -->
        <div class="flex items-center gap-2 border-b border-glass-border">
            <button 
                v-for="tab in tabs" :key="tab.id"
                @click="activeTab = tab.id"
                class="px-6 py-4 text-sm font-bold border-b-2 transition-colors relative"
                :class="activeTab === tab.id ? 'text-primary border-primary' : 'text-text-dim border-transparent hover:text-text-main hover:bg-white/5'"
            >
                {{ tab.label }}
                <!-- Active Indicator Glow -->
                <div v-if="activeTab === tab.id" class="absolute bottom-0 left-0 w-full h-[1px] bg-primary shadow-[0_0_10px_#4f46e5]"></div>
            </button>
        </div>

        <!-- Tab Content -->
        <div class="mt-8">
            <!-- 1. Triggers & Rules -->
            <div v-if="activeTab === 'rules'" class="space-y-6">
                <div v-if="loading" class="text-center py-20 text-text-dim animate-pulse font-bold tracking-widest uppercase">Initializing Matrices...</div>
                
                <div v-else class="grid grid-cols-1 gap-4">
                    <div v-for="rule in rules" :key="rule.id" class="glass-card p-6 rounded-2xl flex items-center justify-between group hover:bg-white/5 transition-colors border-l-4" :class="rule.is_active ? 'border-primary' : 'border-gray-500'">
                        <div class="space-y-2 flex-1">
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-bold text-text-main">{{ rule.name }}</h3>
                                <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest" :class="rule.is_active ? 'bg-primary/20 text-primary' : 'bg-gray-500/20 text-gray-400'">
                                    {{ rule.is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </div>
                            <p class="text-sm text-text-dim max-w-2xl">{{ rule.description }}</p>
                            
                            <div class="flex items-center gap-6 mt-4">
                                <div class="flex items-center gap-2 text-xs font-medium text-text-dim">
                                    <span class="px-2 py-1 bg-surface rounded-md border border-glass-border uppercase tracking-widest text-[9px]">Trigger</span>
                                    <span class="text-primary">{{ rule.trigger_event.replace('_', ' ') }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-xs font-medium text-text-dim">
                                    <span class="px-2 py-1 bg-surface rounded-md border border-glass-border uppercase tracking-widest text-[9px]">Priority</span>
                                    {{ rule.priority }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button class="w-10 h-10 rounded-xl bg-surface flex items-center justify-center text-text-dim hover:text-primary hover:bg-primary/10 transition-colors">✏️</button>
                            <button @click="deleteRule(rule.id)" class="w-10 h-10 rounded-xl bg-surface flex items-center justify-center text-text-dim hover:text-red-400 hover:bg-red-500/10 transition-colors">🗑️</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. SLA Policies -->
            <div v-if="activeTab === 'slas'" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="policy in slaPolicies" :key="policy.id" class="glass-card p-8 rounded-[2rem] border-t-4" :class="getPriorityBorder(policy.priority)">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-text-main">{{ policy.name }}</h3>
                            <span class="text-xs font-black uppercase tracking-widest px-3 py-1 rounded-full border border-glass-border">{{ policy.priority }}</span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-6 mb-8">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-text-dim mb-2">First Response</p>
                                <p class="text-2xl font-black text-text-main">{{ formatMinutes(policy.response_time_minutes) }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-text-dim mb-2">Resolution</p>
                                <p class="text-2xl font-black text-text-main">{{ formatMinutes(policy.resolution_time_minutes) }}</p>
                            </div>
                        </div>
                        
                        <div class="pt-6 border-t border-glass-border flex items-center justify-between">
                            <div class="flex items-center gap-2 text-xs font-medium text-text-dim">
                                <span class="w-2 h-2 rounded-full" :class="policy.business_hours_only ? 'bg-primary' : 'bg-red-500'"></span>
                                {{ policy.business_hours_only ? 'Business Hours Only' : '24/7 Support' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Audit Trails -->
            <div v-if="activeTab === 'audit'" class="space-y-6">
                 <div class="glass-card rounded-[2rem] overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-surface-light/50 border-b border-glass-border">
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Timestamp</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Action</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Target</th>
                                <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-text-dim">Actor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-glass-border">
                            <tr v-for="log in auditLogs" :key="log.id" class="hover:bg-white/5 transition-colors cursor-pointer">
                                <td class="px-6 py-4 text-xs font-medium text-text-dim">{{ new Date(log.created_at).toLocaleString() }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-primary/10 text-primary rounded border border-primary/20 text-[10px] font-bold uppercase tracking-widest">{{ log.action.replace(/_/g, ' ') }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-text-main">{{ log.auditable_type.split('\\').pop() }} #{{ log.auditable_id }}</td>
                                <td class="px-6 py-4 text-sm text-text-dim">{{ log.user ? log.user.name : 'System (Autopilot)' }}</td>
                            </tr>
                        </tbody>
                    </table>
                 </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/plugins/axios';

const tabs = [
    { id: 'rules', label: 'Triggers & Rules' },
    { id: 'slas', label: 'SLA Policies' },
    { id: 'escalations', label: 'Escalation Paths' },
    { id: 'audit', label: 'Audit Trail' },
];

const activeTab = ref('rules');
const loading = ref(true);

const rules = ref([]);
const slaPolicies = ref([]);
const auditLogs = ref([]);

const fetchData = async () => {
    loading.value = true;
    try {
        const [rRes, sRes, aRes] = await Promise.all([
            api.get('/admin/automation-rules'),
            api.get('/admin/sla-policies'),
            api.get('/admin/audit-logs'),
        ]);
        
        rules.value = rRes.data.data;
        slaPolicies.value = sRes.data.data;
        auditLogs.value = aRes.data.data || aRes.data; // Depending on pagination structure
    } catch (e) {
        console.error('Failed to load automation config', e);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchData();
});

const formatMinutes = (min) => {
    if (min < 60) return `${min}m`;
    const hours = Math.floor(min / 60);
    const rem = min % 60;
    return rem > 0 ? `${hours}h ${rem}m` : `${hours}h`;
};

const getPriorityBorder = (priority) => {
    switch(priority) {
        case 'critical': return 'border-red-500';
        case 'high': return 'border-amber-500';
        case 'medium': return 'border-blue-500';
        case 'low': return 'border-gray-500';
        default: return 'border-glass-border';
    }
};

// Simplified stub for deleting a rule
const deleteRule = async (id) => {
    if(confirm('Delete rule?')) {
        await api.delete(`/admin/automation-rules/${id}`);
        fetchData();
    }
}
</script>

<style scoped>
</style>
