<template>
    <div class="space-y-8 pb-10 animate-in fade-in slide-in-from-bottom-4 duration-500">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-text-main tracking-tight flex items-center gap-3">
                    <span class="w-10 h-10 rounded-lg bg-primary/20 flex items-center justify-center text-primary border border-primary/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                    </span>
                    Automation Engine
                </h1>
                <p class="text-text-dim text-sm mt-2 max-w-2xl font-medium opacity-80">
                    Configure structural SLA parameters, autopilot triggers, and system escalation matrix routing protocols. 
                </p>
            </div>
            
            <button @click="openModal()" class="px-5 py-2.5 bg-primary hover:bg-primary-dark shadow-md shadow-primary/20 text-white font-black text-sm rounded-lg transition-all hover-lift flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Deploy New Rule
            </button>
        </div>

        <!-- Logic Protocol Guide -->
        <div class="bg-surface border border-glass-border p-6 rounded-xl relative overflow-hidden shadow-sm">
            <div class="absolute top-0 right-0 p-8 opacity-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-32 h-32 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.364-6.364l-.707-.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M12 18v3m3 0H9m5.5-12.5a3.5 3.5 0 11-7 0 3.5 3.5 0 017 0z" /></svg>
            </div>
            
            <div class="relative z-10 flex flex-col xl:flex-row gap-8 items-center">
                <div class="flex-1">
                    <h2 class="text-xs font-black uppercase tracking-[0.3em] text-primary mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
                        How the Automation Engine Works
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <div class="text-[10px] font-black text-text-main uppercase tracking-widest flex items-center gap-2">
                                <span class="bg-primary/20 text-primary w-5 h-5 rounded flex items-center justify-center border border-primary/30">1</span>
                                Trigger Event
                            </div>
                            <p class="text-[11px] text-text-dim leading-relaxed">
                                The catalyst (e.g., ticket created) that wakes the engine to evaluate this rule.
                            </p>
                        </div>
                        <div class="space-y-2">
                            <div class="text-[10px] font-black text-text-main uppercase tracking-widest flex items-center gap-2">
                                <span class="bg-primary/20 text-primary w-5 h-5 rounded flex items-center justify-center border border-primary/30">2</span>
                                Condition Map
                            </div>
                            <p class="text-[11px] text-text-dim leading-relaxed">
                                The "if" statement. The engine checks if certain fields match your specified logic.
                            </p>
                        </div>
                        <div class="space-y-2">
                            <div class="text-[10px] font-black text-text-main uppercase tracking-widest flex items-center gap-2">
                                <span class="bg-primary/20 text-primary w-5 h-5 rounded flex items-center justify-center border border-primary/30">3</span>
                                Action Payload
                            </div>
                            <p class="text-[11px] text-text-dim leading-relaxed">
                                The machine tasks performed if conditions met (e.g., reassign, tag, or email).
                            </p>
                        </div>
                    </div>
                </div>

                <div class="xl:w-1/3 bg-background/50 border border-glass-border p-4 rounded-lg">
                    <h3 class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-3">Live Scenario: Auto-Escalate</h3>
                    <div class="space-y-3 font-mono text-[9px]">
                        <div class="flex gap-2">
                            <span class="text-text-dim">IF:</span>
                            <span class="text-text-main">title contains "CRITICAL FAIL"</span>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-text-dim">THEN:</span>
                            <span class="text-primary-dim">set priority "High" + notify Slack</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl space-y-10">
            <!-- Glassmorphic Tabs -->
            <div class="flex items-center flex-wrap gap-2 p-1 bg-surface border border-glass-border rounded-lg shadow-sm">
                <button 
                    v-for="tab in tabs" :key="tab.id"
                    @click="activeTab = tab.id"
                    :class="[
                        'px-5 py-2 text-xs font-black uppercase tracking-widest rounded-md transition-all duration-200',
                        activeTab === tab.id 
                            ? 'bg-primary text-white shadow-md' 
                            : 'text-text-dim hover:text-white hover:bg-white/5'
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
                <div v-if="activeTab === 'rules'" class="space-y-4 animate-in fade-in duration-300">
                    <div 
                        v-for="rule in rules" :key="rule.id" 
                        class="bg-surface border border-glass-border rounded-lg group hover:border-primary/50 transition-all duration-300 relative overflow-hidden"
                    >
                        <!-- Status Glow Line -->
                        <div class="absolute top-0 left-0 w-1 h-full" :class="rule.is_active ? 'bg-primary' : 'bg-gray-600'"></div>
                        
                        <div class="p-8">
                            <div class="flex flex-col lg:flex-row gap-8 items-start lg:items-center justify-between">
                                <!-- Info Section -->
                                <div class="flex-1 space-y-4">
                                    <div class="flex items-center gap-3">
                                        <h3 class="text-xl font-black text-white tracking-tight">{{ rule.name }}</h3>
                                        <span 
                                            :class="[
                                                'px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest border transition-colors',
                                                rule.is_active ? 'text-primary border-primary/30 bg-primary/5' : 'text-gray-500 border-gray-700 bg-gray-800/50'
                                            ]"
                                        >
                                            {{ rule.is_active ? 'Running' : 'Paused' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-text-dim max-w-2xl leading-relaxed">{{ rule.description }}</p>
                                    
                                    <!-- Logic Flow Visualization -->
                                    <div class="flex flex-wrap items-center gap-3 pt-2">
                                        <div class="flex items-center gap-2 px-3 py-1 bg-background/30 rounded border border-glass-border">
                                            <span class="text-[9px] font-black text-primary uppercase tracking-tighter">TRIGGER</span>
                                            <span class="text-xs text-white font-mono uppercase">{{ rule.trigger_event.replace('_', ' ') }}</span>
                                        </div>
                                        <div class="text-primary-dim">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
                                        </div>
                                        <div class="flex items-center gap-2 px-3 py-1 bg-background/30 rounded border border-glass-border">
                                            <span class="text-[9px] font-black text-emerald-400 uppercase tracking-tighter">IF</span>
                                            <span class="text-xs text-white font-mono opacity-80">Satisfied Conditions</span>
                                        </div>
                                        <div class="text-primary-dim">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>
                                        </div>
                                        <div class="flex items-center gap-2 px-3 py-1 bg-background/30 rounded border border-glass-border">
                                            <span class="text-[9px] font-black text-rose-400 uppercase tracking-tighter">THEN</span>
                                            <span class="text-xs text-white font-mono opacity-80">Exec Action Batch</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions Section -->
                                <div class="flex items-center gap-3 opacity-0 group-hover:opacity-100 transition-all duration-200">
                                    <button @click="openModal(rule)" class="group/btn flex items-center gap-2 px-4 py-2 bg-surface-light border border-glass-border rounded-lg text-text-dim hover:text-white hover:border-primary transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                        <span class="text-xs font-bold uppercase tracking-wider">Configure</span>
                                    </button>
                                    <button @click="deleteRule(rule.id)" class="p-2.5 bg-red-500/10 border border-red-500/20 rounded-lg text-red-500 hover:bg-red-500 hover:text-white transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="rules.length === 0" class="py-20 border-2 border-dashed border-glass-border rounded-lg flex flex-col items-center justify-center bg-surface text-text-dim">
                        <div class="w-16 h-16 bg-background rounded-lg flex items-center justify-center border border-glass-border mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        </div>
                        <h4 class="text-white font-black text-lg">No Logic Matrices Found</h4>
                        <p class="text-xs mt-2 uppercase tracking-widest opacity-50 font-bold">Deploy your first rule to automate workflows.</p>
                    </div>
                </div>

                <!-- 2. SLA Policies -->
                <div v-if="activeTab === 'slas'" class="animate-in fade-in duration-300 space-y-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-black text-white">SLA Contracts</h3>
                            <p class="text-xs text-text-dim mt-1">Operational thresholds defined by ticket priority levels.</p>
                        </div>
                        <button @click="openSlaModal()" class="group px-6 py-2.5 bg-primary hover:bg-primary-dark shadow-md shadow-primary/20 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-all hover-lift flex items-center gap-3">
                             Create New Standard
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="policy in slaPolicies" :key="policy.id" class="bg-surface border border-glass-border rounded-lg shadow-sm relative group overflow-hidden transition-all duration-300">
                            <!-- Priority Indicator -->
                            <div class="absolute top-0 inset-x-0 h-1" :class="getPriorityColorClass(policy.priority).split(' ')[0]"></div>
                            
                            <div class="p-6 relative z-10">
                                <div class="flex justify-between items-start mb-6">
                                    <div class="space-y-1">
                                        <span class="text-[9px] font-black uppercase tracking-widest opacity-50 text-white">Policy Node</span>
                                        <h3 class="text-xl font-black text-white tracking-tight">{{ policy.name }}</h3>
                                    </div>
                                    <div class="px-3 py-1.5 bg-background shadow-inner border border-glass-border rounded-lg">
                                        <div class="w-2.5 h-2.5 rounded-full" :class="getPriorityColorClass(policy.priority).split(' ')[0]"></div>
                                    </div>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-background p-4 rounded-lg border border-glass-border shadow-inner group/stat">
                                            <p class="text-[9px] font-black uppercase tracking-widest text-text-dim mb-2 flex items-center gap-2">
                                                Response
                                            </p>
                                            <p class="text-2xl font-black text-white group-hover/stat:text-primary transition-colors">{{ formatMinutes(policy.response_time_minutes) }}</p>
                                        </div>
                                        <div class="bg-background p-4 rounded-lg border border-glass-border shadow-inner group/stat">
                                            <p class="text-[9px] font-black uppercase tracking-widest text-text-dim mb-2 flex items-center gap-2">
                                                Resolution
                                            </p>
                                            <p class="text-2xl font-black text-white group-hover/stat:text-red-400 transition-colors">{{ formatMinutes(policy.resolution_time_minutes) }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between pt-4 border-t border-glass-border">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black text-text-dim uppercase tracking-widest">{{ policy.business_hours_only ? 'Business Hours' : '24/7' }}</span>
                                        </div>
                                        <div class="flex gap-2">
                                            <button @click="openSlaModal(policy)" class="w-9 h-9 rounded-lg bg-surface-light border border-glass-border flex items-center justify-center text-text-dim hover:text-white hover:border-primary transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                            </button>
                                            <button @click="deleteSla(policy.id)" class="w-9 h-9 rounded-lg bg-red-500/10 border border-red-500/20 flex items-center justify-center text-red-500 hover:bg-red-500 hover:text-white transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Audit Trails -->
                <div v-if="activeTab === 'audit'" class="animate-in fade-in duration-300">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div class="relative flex-1 max-w-md">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-text-dim">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </span>
                            <input 
                                v-model="auditSearch" 
                                @input="fetchAuditLogs(1)"
                                type="text" 
                                placeholder="Search action, actor, or resource..." 
                                class="w-full bg-surface border border-glass-border rounded-lg pl-10 pr-4 py-2 text-xs text-white focus:outline-none focus:border-primary transition-all shadow-sm"
                            />
                        </div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-text-dim">
                            Total Records: <span class="text-white">{{ auditMeta.total }}</span>
                        </div>
                    </div>

                    <div class="bg-surface border border-glass-border rounded-lg shadow-sm overflow-hidden relative">
                        <div v-if="auditLoading" class="absolute inset-0 bg-background/50 backdrop-blur-sm z-10 flex items-center justify-center">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                        </div>
                        
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

                        <!-- Pagination Controls -->
                        <div v-if="auditMeta.lastPage > 1" class="px-6 py-4 border-t border-glass-border flex items-center justify-between bg-surface-light/30">
                            <div class="text-[10px] font-black uppercase tracking-widest text-text-dim">
                                Page {{ auditMeta.currentPage }} of {{ auditMeta.lastPage }}
                            </div>
                            <div class="flex gap-2">
                                <button 
                                    @click="fetchAuditLogs(auditMeta.currentPage - 1)" 
                                    :disabled="auditMeta.currentPage === 1 || auditLoading"
                                    class="px-3 py-1.5 bg-surface border border-glass-border rounded-md text-[10px] font-black uppercase tracking-widest text-text-dim hover:text-white disabled:opacity-30 transition-all"
                                >
                                    Previous
                                </button>
                                <button 
                                    @click="fetchAuditLogs(auditMeta.currentPage + 1)" 
                                    :disabled="auditMeta.currentPage === auditMeta.lastPage || auditLoading"
                                    class="px-3 py-1.5 bg-surface border border-glass-border rounded-md text-[10px] font-black uppercase tracking-widest text-text-dim hover:text-white disabled:opacity-30 transition-all"
                                >
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Rule Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-background/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-surface border border-glass-border rounded-lg w-full max-w-4xl p-8 shadow-2xl transform scale-100 transition-all max-h-[90vh] overflow-y-auto">
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

        <!-- SLA Policy Modal -->
        <div v-if="showSlaModal" class="fixed inset-0 bg-background/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-surface border border-glass-border rounded-lg w-full max-w-2xl p-8 shadow-2xl transform scale-100 transition-all">
                <div class="flex items-center gap-3 mb-8 pb-4 border-b border-glass-border">
                    <div class="w-10 h-10 bg-primary/20 rounded-xl flex items-center justify-center text-primary border border-primary/30">
                        ⏱️
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-white">{{ editingSlaId ? 'Update SLA Contract' : 'Deploy New SLA Contract' }}</h2>
                        <p class="text-xs text-text-dim font-medium mt-1">Define operational thresholds for service level agreements.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-full">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Contract Name</label>
                        <input v-model="slaForm.name" type="text" placeholder="e.g. Critical Support Response" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary transition-all shadow-inner" />
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Priority Assignment</label>
                        <select v-model="slaForm.priority" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary transition-all shadow-inner">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Business Hours Only</label>
                        <div class="flex items-center justify-between bg-background px-5 py-2.5 rounded-xl border border-glass-border shadow-inner">
                            <span class="text-xs font-bold text-white tracking-wide">{{ slaForm.business_hours_only ? 'Enabled' : '24/7' }}</span>
                            <button 
                                @click="slaForm.business_hours_only = !slaForm.business_hours_only"
                                :class="[
                                    'w-10 h-5 rounded-full p-0.5 transition-colors duration-300 ease-in-out relative flex items-center',
                                    slaForm.business_hours_only ? 'bg-primary' : 'bg-gray-700'
                                ]"
                            >
                                <span 
                                    :class="[
                                        'w-4 h-4 bg-white rounded-full transition-transform duration-300 block',
                                        slaForm.business_hours_only ? 'translate-x-5' : 'translate-x-0'
                                    ]"
                                ></span>
                            </button>
                        </div>
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">First Response Goal (Minutes)</label>
                        <input v-model="slaForm.response_time_minutes" type="number" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary transition-all shadow-inner" />
                    </div>

                    <div class="col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Full Resolution Goal (Minutes)</label>
                        <input v-model="slaForm.resolution_time_minutes" type="number" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary transition-all shadow-inner" />
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-10 pt-6 border-t border-glass-border">
                    <button @click="showSlaModal = false" class="px-5 py-2.5 text-sm font-bold text-text-dim hover:text-white transition-colors rounded-xl">Discard</button>
                    <button @click="saveSla" :disabled="saving" class="px-7 py-2.5 bg-primary hover:bg-primary-dark shadow-lg shadow-primary/20 text-white font-black rounded-xl transition-all hover-lift disabled:opacity-50 flex items-center gap-2">
                        <span v-if="saving" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                        Commit Changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { debounce } from 'lodash';
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
const auditMeta = ref({
    currentPage: 1,
    lastPage: 1,
    total: 0,
    perPage: 15
});
const auditSearch = ref('');
const auditLoading = ref(false);

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

// SLA Modal Logic
const showSlaModal = ref(false);
const editingSlaId = ref(null);
const slaForm = ref({
    name: '',
    priority: 'low',
    response_time_minutes: 60,
    resolution_time_minutes: 240,
    business_hours_only: true,
});

const openSlaModal = (policy = null) => {
    if (policy) {
        editingSlaId.value = policy.id;
        slaForm.value = { ...policy };
    } else {
        editingSlaId.value = null;
        slaForm.value = {
            name: '',
            priority: 'low',
            response_time_minutes: 60,
            resolution_time_minutes: 240,
            business_hours_only: true,
        };
    }
    showSlaModal.value = true;
};

const saveSla = async () => {
    saving.value = true;
    try {
        if (editingSlaId.value) {
            await api.put(`/admin/sla-policies/${editingSlaId.value}`, slaForm.value);
            toast.success('SLA Contract structurally modified.');
        } else {
            await api.post('/admin/sla-policies', slaForm.value);
            toast.success('New SLA Contract deployed.');
        }
        showSlaModal.value = false;
        fetchData();
    } catch (error) {
        toast.error('Failed to save SLA parameters. System refusal.');
    } finally {
        saving.value = false;
    }
};

const deleteSla = async (id) => {
    if(confirm('Permanently decommission this SLA protocol? Systems using this priority will revert to default timers.')) {
        try {
            await api.delete(`/admin/sla-policies/${id}`);
            toast.info('SLA successfully scraped.');
            fetchData();
        } catch (error) {
            toast.error('Deletion context error.');
        }
    }
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

const fetchAuditLogsInternal = async (page = 1) => {
    auditLoading.value = true;
    try {
        const response = await api.get('/admin/audit-logs', {
            params: {
                page,
                search: auditSearch.value,
                per_page: auditMeta.value.perPage
            }
        });
        auditLogs.value = response.data.data;
        auditMeta.value = {
            currentPage: response.data.current_page,
            lastPage: response.data.last_page,
            total: response.data.total,
            perPage: response.data.per_page
        };
    } catch (e) {
        toast.error('Failed to fetch transaction logs.');
    } finally {
        auditLoading.value = false;
    }
};

const debouncedFetchAuditLogs = debounce(() => fetchAuditLogsInternal(1), 500);

const fetchAuditLogs = (page = 1) => {
    if (page === 1 && auditSearch.value) {
        debouncedFetchAuditLogs();
    } else {
        fetchAuditLogsInternal(page);
    }
};

const fetchData = async () => {
    loading.value = true;
    try {
        const [rRes, sRes] = await Promise.all([
            api.get('/admin/automation-rules'),
            api.get('/admin/sla-policies'),
        ]);
        
        rules.value = rRes.data.data || [];
        slaPolicies.value = sRes.data.data || [];
        await fetchAuditLogs();
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

const getPriorityColorClass = (priority) => {
    switch(priority) {
        case 'critical': return 'bg-red-500 text-red-500';
        case 'high': return 'bg-amber-500 text-amber-500';
        case 'medium': return 'bg-blue-500 text-blue-500';
        case 'low': return 'bg-emerald-500 text-emerald-500';
        default: return 'bg-gray-500 text-gray-500';
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
