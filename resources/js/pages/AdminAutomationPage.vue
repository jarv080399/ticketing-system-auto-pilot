<template>
    <div class="space-y-8 pb-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center text-primary border border-primary/30 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                    </span>
                    Automation Engine
                </h1>
                <p class="text-text-dim text-sm mt-2 max-w-2xl">
                    Configure structural SLA parameters, autopilot triggers, and system escalation matrix routing protocols. 
                </p>
            </div>
            
            <button @click="openModal()" class="px-5 py-2.5 bg-primary hover:bg-primary-dark shadow-lg shadow-primary/20 text-white font-black text-sm rounded-xl transition-all hover-lift flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Deploy New Rule
            </button>
        </div>

        <div class="max-w-7xl space-y-10">
            <!-- Glassmorphic Tabs -->
            <div class="flex items-center flex-wrap gap-2 p-1.5 bg-surface/50 border border-glass-border rounded-xl w-max shadow-inner backdrop-blur-md">
                <button 
                    v-for="tab in tabs" :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                        'px-5 py-2.5 text-xs font-black uppercase tracking-widest rounded-lg transition-all duration-300',
                        activeTab === tab.id 
                            ? 'bg-primary text-white shadow-lg shadow-primary/20 transform scale-100' 
                            : 'text-text-dim hover:text-white hover:bg-white/5 transparent'
                    ]"
                >
                    {{ tab.label }}
                </button>
            </div>

            <div v-if="loading" class="flex flex-col items-center justify-center py-24">
                <div class="animate-spin rounded-full h-10 w-10 border-b-4 border-primary mb-4"></div>
                <p class="text-[10px] font-black uppercase tracking-widest text-text-dim animate-pulse">Initializing Matrices...</p>
            </div>
            
            <div v-else class="min-h-[400px]">
                <!-- 1. Triggers & Rules -->
                <div v-if="activeTab === 'rules'" class="grid grid-cols-1 lg:grid-cols-2 gap-5 animate-in fade-in duration-300">
                    <div 
                        v-for="rule in rules" :key="rule.id" 
                        class="bg-surface border border-glass-border p-6 rounded-2xl group flex flex-col justify-between hover:border-gray-500 transition-all shadow-xl relative overflow-hidden"
                    >
                        <div v-if="rule.is_active" class="absolute top-0 left-0 w-1 h-full bg-emerald-500 shadow-[0_0_10px_#10b981]"></div>
                        <div v-else class="absolute top-0 left-0 w-1 h-full bg-gray-600"></div>

                        <div class="pl-2 space-y-2">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="text-base font-black text-white tracking-wide">{{ rule.name }}</h3>
                                    <p class="text-xs text-text-dim mt-1 leading-relaxed pr-8">{{ rule.description }}</p>
                                </div>
                                <span 
                                    :class="[
                                        'px-2.5 py-1 rounded bg-background shadow-inner border text-[9px] font-black uppercase tracking-widest flex-shrink-0',
                                        rule.is_active ? 'text-emerald-400 border-emerald-500/20' : 'text-gray-400 border-gray-600/30'
                                    ]"
                                >
                                    {{ rule.is_active ? 'Online' : 'Offline' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="pl-2 flex items-center justify-between mt-6 pt-4 border-t border-glass-border">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2 text-[10px] font-medium text-text-dim">
                                    <span class="px-2 py-0.5 bg-background border border-glass-border rounded text-white shadow-inner">TRIGGER</span>
                                    <span class="text-primary-dim lowercase tracking-normal font-mono border-l border-primary/20 pl-2">{{ rule.trigger_event.replace('_', ' ') }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button @click="openModal(rule)" class="p-2 bg-background border border-glass-border shadow-inner rounded-xl text-text-dim hover:text-white hover:bg-surface-light hover-lift transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                </button>
                                <button @click="deleteRule(rule.id)" class="p-2 bg-background border border-glass-border shadow-inner rounded-xl text-red-500 hover:text-white hover:bg-red-500/20 hover:border-red-500/30 hover-lift transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="rules.length === 0" class="col-span-full py-16 border-2 border-dashed border-glass-border rounded-2xl flex flex-col items-center justify-center bg-background/30 text-text-dim">
                        <div class="w-12 h-12 bg-surface rounded-full flex items-center justify-center border border-glass-border mb-3 shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        </div>
                        <span class="text-sm font-bold tracking-wide">No active automation matrices compiled.</span>
                    </div>
                </div>

                <!-- 2. SLA Policies -->
                <div v-if="activeTab === 'slas'" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 animate-in fade-in duration-300">
                    <div v-for="policy in slaPolicies" :key="policy.id" class="bg-surface rounded-2xl border border-glass-border shadow-xl overflow-hidden relative group">
                        <div class="h-2 w-full" :class="getPriorityGlow(policy.priority)"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-8">
                                <h3 class="text-lg font-black text-white tracking-wide">{{ policy.name }}</h3>
                                <span class="text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded bg-background shadow-inner border border-glass-border text-white">{{ policy.priority }} PRIORITY</span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-background/50 p-4 rounded-xl border border-glass-border shadow-inner">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-text-dim mb-1">First Response</p>
                                    <p class="text-xl font-black text-white">{{ formatMinutes(policy.response_time_minutes) }}</p>
                                </div>
                                <div class="bg-background/50 p-4 rounded-xl border border-glass-border shadow-inner">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-text-dim mb-1">Resolution SLA</p>
                                    <p class="text-xl font-black text-white">{{ formatMinutes(policy.resolution_time_minutes) }}</p>
                                </div>
                            </div>
                            
                            <div class="pt-4 border-t border-glass-border flex items-center justify-between">
                                <div class="flex items-center gap-2 text-[10px] font-bold text-text-dim uppercase tracking-widest">
                                    <span class="w-2.5 h-2.5 rounded-full inline-block shadow-sm" :class="policy.business_hours_only ? 'bg-indigo-500 shadow-indigo-500/50' : 'bg-rose-500 shadow-rose-500/50'"></span>
                                    {{ policy.business_hours_only ? 'Business Hours Timers' : '24/7 Clock Enabled' }}
                                </div>
                                <button class="w-8 h-8 rounded-lg bg-surface-light border border-glass-border flex items-center justify-center text-text-dim hover:text-white transition-colors opacity-0 group-hover:opacity-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Audit Trails -->
                <div v-if="activeTab === 'audit'" class="animate-in fade-in duration-300">
                    <div class="bg-surface border border-glass-border rounded-2xl shadow-xl overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-surface-light border-b border-glass-border text-[10px] font-black py-4 uppercase tracking-widest text-text-dim">
                                        <th class="px-6 py-4">Event Timestamp</th>
                                        <th class="px-6 py-4">Transaction Action</th>
                                        <th class="px-6 py-4">Resource Target</th>
                                        <th class="px-6 py-4">Initiating Actor</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-glass-border bg-background/30">
                                    <tr v-for="log in auditLogs" :key="log.id" class="hover:bg-white/5 transition-colors">
                                        <td class="px-6 py-4 text-xs font-mono text-text-dim bg-background/20">{{ new Date(log.created_at).toLocaleString() }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2.5 py-1 bg-surface shadow-inner border border-glass-border text-white rounded text-[9px] font-black uppercase tracking-widest">{{ log.action.replace(/_/g, ' ') }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-bold text-white tracking-wide">
                                            {{ log.auditable_type.split('\\').pop() }} <span class="text-primary-dim">#{{ log.auditable_id }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-text-dim">
                                            <div class="flex items-center gap-2">
                                                <div class="w-5 h-5 rounded-full bg-surface-light overflow-hidden flex items-center justify-center border border-glass-border flex-shrink-0">
                                                    <svg v-if="!log.user" xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-primary-dim" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" /></svg>
                                                    <span v-else class="text-[9px] font-bold text-white">{{ log.user.name.charAt(0) }}</span>
                                                </div>
                                                {{ log.user ? log.user.name : 'System (Autopilot)' }}
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="auditLogs.length === 0">
                                        <td colspan="4" class="px-6 py-12 text-center text-sm font-bold text-text-dim border-none">
                                            <div class="w-10 h-10 mx-auto bg-surface border border-glass-border shadow-inner rounded-xl flex items-center justify-center mb-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                            </div>
                                            No system transaction logs found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Rule Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-background/90 backdrop-blur-md flex items-center justify-center z-50 p-4">
            <div class="bg-surface border border-glass-border rounded-2xl w-full max-w-4xl p-8 shadow-[0_0_50px_rgba(0,0,0,0.5)] transform scale-100 transition-all max-h-[90vh] overflow-y-auto">
                <div class="flex items-center gap-3 mb-8 pb-4 border-b border-glass-border">
                    <div class="w-10 h-10 bg-primary/20 rounded-xl flex items-center justify-center text-primary border border-primary/30">
                        🤖
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-white">{{ editingId ? 'Rewrite Matrices Protocol' : 'Deploy Automation Logic' }}</h2>
                        <p class="text-xs text-text-dim font-medium mt-1">Write the computational parameters triggering machine actions.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                    <div class="col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Workflow Label</label>
                        <input v-model="form.name" type="text" placeholder="e.g. Route Hardware to IT" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner" />
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Trigger Event</label>
                        <select v-model="form.trigger_event" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner">
                            <option value="ticket_created">When a ticket is Created</option>
                            <option value="ticket_updated">When a ticket is Updated (Reply, Change, etc)</option>
                            <option value="sla_approaching">When an SLA warning is Approaching</option>
                            <option value="schedule">CRON Schedule Base</option>
                        </select>
                    </div>

                    <div class="col-span-full">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Description Brief</label>
                        <input v-model="form.description" type="text" placeholder="Internal notes describing when this operates." class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner" />
                    </div>

                    <div class="col-span-1 border-r border-glass-border pr-6">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Condition Parameters (JSON Map)</label>
                        <textarea v-model="form.condition_json" rows="8" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-xs font-mono text-emerald-400 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner"></textarea>
                        <p class="text-[9px] font-medium text-text-dim border-l-2 border-emerald-500/30 pl-2 mt-2">Filter exactly what conditions satisfy this rule running.</p>
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Action Payloads (JSON Array)</label>
                        <textarea v-model="form.action_json" rows="8" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-xs font-mono text-rose-400 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner"></textarea>
                        <p class="text-[9px] font-medium text-text-dim border-l-2 border-rose-500/30 pl-2 mt-2">Array block dictating exact machine tasks to execute.</p>
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Execution Priority (Stack Value)</label>
                        <input v-model="form.priority" type="number" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner" />
                    </div>

                    <div class="col-span-1 flex items-center justify-between gap-4 bg-background px-5 py-3 rounded-xl border border-glass-border shadow-inner mt-[1.35rem]">
                        <div>
                            <span class="text-sm font-black text-white tracking-wide">Live Toggle</span>
                            <p class="text-[10px] font-medium text-text-dim mt-1">Machine state on/off.</p>
                        </div>
                        <button 
                            @click="form.is_active = !form.is_active"
                            :class="[
                                'w-12 h-6 rounded-full p-1 transition-colors duration-300 ease-in-out relative flex-shrink-0 cursor-pointer flex items-center justify-start',
                                form.is_active ? 'bg-primary' : 'bg-gray-700'
                            ]"
                        >
                            <span 
                                :class="[
                                    'w-4 h-4 bg-white rounded-full transition-transform duration-300 shadow-sm block',
                                    form.is_active ? 'translate-x-6' : 'translate-x-0'
                                ]"
                            ></span>
                        </button>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-10 pt-6 border-t border-glass-border">
                    <button @click="showModal = false" class="px-5 py-2.5 text-sm font-bold text-text-dim hover:text-white hover:bg-white/5 disabled:opacity-50 transition-colors rounded-xl">Discard Work</button>
                    <button @click="saveRule" :disabled="saving" class="px-7 py-2.5 bg-primary hover:bg-primary-dark shadow-lg shadow-primary/30 text-white font-black rounded-xl transition-all hover-lift active:scale-95 disabled:opacity-50 flex items-center gap-2">
                        <span v-if="saving" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                        {{ editingId ? 'Update System Rule' : 'Commit To Logic Matrix' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/plugins/axios';
import { useToast } from 'vue-toastification';

const toast = useToast();
const tabs = [
    { id: 'rules', label: 'Triggers & Matrix' },
    { id: 'slas', label: 'SLA Contracts' },
    { id: 'audit', label: 'Transaction Hash Log' },
];

const activeTab = ref('rules');
const loading = ref(true);

const rules = ref([]);
const slaPolicies = ref([]);
const auditLogs = ref([]);

// Modal State
const showModal = ref(false);
const editingId = ref(null);
const saving = ref(false);
const form = ref({
    name: '',
    description: '',
    trigger_event: 'ticket_created',
    priority: 10,
    is_active: true,
    condition_json: '{\n  "field": "title",\n  "op": "contains",\n  "value": "example"\n}',
    action_json: '[\n  {\n    "type": "add_tag",\n    "payload": {"tag": "example"}\n  }\n]',
});

const openModal = (rule = null) => {
    if (rule) {
        editingId.value = rule.id;
        form.value = { 
            name: rule.name,
            description: rule.description || '',
            trigger_event: rule.trigger_event,
            priority: rule.priority,
            is_active: rule.is_active,
            condition_json: JSON.stringify(rule.condition_json, null, 2),
            action_json: JSON.stringify(rule.action_json, null, 2)
        };
    } else {
        editingId.value = null;
        form.value = {
            name: '',
            description: '',
            trigger_event: 'ticket_created',
            priority: 10,
            is_active: true,
            condition_json: '{\n  "field": "title",\n  "op": "contains",\n  "value": "example"\n}',
            action_json: '[\n  {\n    "type": "add_tag",\n    "payload": {"tag": "example"}\n  }\n]',
        };
    }
    showModal.value = true;
};

const saveRule = async () => {
    saving.value = true;
    try {
        const payload = { ...form.value };
        
        try {
            payload.condition_json = JSON.parse(payload.condition_json);
            payload.action_json = JSON.parse(payload.action_json);
        } catch (e) {
            toast.error('Invalid JSON structure. Please check the condition or action fields.');
            saving.value = false;
            return;
        }

        if (editingId.value) {
            await api.put(`/admin/automation-rules/${editingId.value}`, payload);
            toast.success('Matrix structurally modified.');
        } else {
            await api.post('/admin/automation-rules', payload);
            toast.success('New engine logic deployed.');
        }
        showModal.value = false;
        fetchData();
    } catch (error) {
        toast.error('Failed to save logic rule. Check parameters.');
    } finally {
        saving.value = false;
    }
};

const fetchData = async () => {
    loading.value = true;
    try {
        const [rRes, sRes, aRes] = await Promise.all([
            api.get('/admin/automation-rules'),
            api.get('/admin/sla-policies'),
            api.get('/admin/audit-logs'),
        ]);
        
        rules.value = rRes.data.data || [];
        slaPolicies.value = sRes.data.data || [];
        auditLogs.value = aRes.data.data || aRes.data || [];
    } catch (e) {
        toast.error('Failed to securely fetch engine architecture schema from database.');
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
    return rem > 0 ? `${hours}H ${rem}M` : `${hours}H 00M`;
};

const getPriorityGlow = (priority) => {
    switch(priority) {
        case 'critical': return 'bg-red-500 shadow-[0_0_15px_#ef4444]';
        case 'high': return 'bg-amber-500 shadow-[0_0_15px_#f59e0b]';
        case 'medium': return 'bg-blue-500 shadow-[0_0_15px_#3b82f6]';
        case 'low': return 'bg-emerald-500 shadow-[0_0_15px_#10b981]';
        default: return 'bg-gray-600';
    }
};

const deleteRule = async (id) => {
    if(confirm('WARNING: Destroying this structural rule will permanently detach logic from the automation matrix. Continue deletion?')) {
        try {
            await api.delete(`/admin/automation-rules/${id}`);
            toast.info('Rule successfully scraped from schema.');
            fetchData();
        } catch (error) {
            toast.error('Engine error during rule deletion context.');
        }
    }
}
</script>
