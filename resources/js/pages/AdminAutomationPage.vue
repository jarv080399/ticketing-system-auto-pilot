<template>
    <div class="space-y-8 pb-10">
        <!-- ─── Page Header ─── -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-black text-text-main tracking-tight">Automation Engine</h1>
                <p class="mt-1 text-sm text-text-dim">
                    Configure structural SLA parameters, autopilot triggers, and system escalation routing protocols.
                </p>
            </div>
            <button @click="openModal()" class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Deploy New Rule
            </button>
        </div>

        <!-- ─── How It Works — Section Panel ─── -->
        <div class="bg-surface border border-glass-border rounded-xl p-6">
            <h2 class="text-[11px] font-semibold tracking-widest text-text-dim uppercase mb-4 flex items-center gap-2">
                <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
                How the Automation Engine Works
            </h2>
            <div class="flex flex-col xl:flex-row gap-6 items-start">
                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2" v-for="step in engineSteps" :key="step.num">
                        <div class="text-[10px] font-bold text-text-main uppercase tracking-widest flex items-center gap-2">
                            <span class="bg-primary/20 text-primary w-5 h-5 rounded flex items-center justify-center border border-primary/30 text-[9px] font-bold">{{ step.num }}</span>
                            {{ step.title }}
                        </div>
                        <p class="text-[11px] text-text-dim leading-relaxed">{{ step.desc }}</p>
                    </div>
                </div>
                <div class="xl:w-1/3 bg-background border border-glass-border p-4 rounded-lg">
                    <h3 class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest mb-3">Live Scenario: Auto-Escalate</h3>
                    <div class="space-y-2.5 font-mono text-[10px]">
                        <div class="flex gap-2">
                            <span class="text-text-dim">IF:</span>
                            <span class="text-text-main">title contains "CRITICAL FAIL"</span>
                        </div>
                        <div class="flex gap-2">
                            <span class="text-text-dim">THEN:</span>
                            <span class="text-primary">set priority "High" + notify Slack</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── Tabs ─── -->
        <div class="flex bg-surface-light rounded-lg p-1 gap-1 border border-glass-border">
            <button
                v-for="tab in tabs" :key="tab.id"
                @click="activeTab = tab.id"
                :class="activeTab === tab.id
                    ? 'bg-primary text-white'
                    : 'text-text-dim hover:text-text-main'"
                class="text-xs font-bold px-4 py-1.5 rounded-md transition-colors"
            >
                {{ tab.label }}
            </button>
        </div>

        <!-- ─── Loading ─── -->
        <div v-if="loading" class="flex flex-col items-center justify-center py-24">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mb-4"></div>
            <p class="text-[10px] font-bold uppercase tracking-widest text-text-dim animate-pulse">Initializing Matrices...</p>
        </div>

        <div v-else class="min-h-[400px] space-y-6">

            <!-- ═══════════════════ TAB 1: Rules ═══════════════════ -->
            <div v-if="activeTab === 'rules'" class="space-y-4">
                <!-- Automation Rule Card (per skill spec) -->
                <div
                    v-for="rule in rules" :key="rule.id"
                    class="bg-surface border border-glass-border rounded-xl p-6 group hover:border-primary/40 transition-all"
                >
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-text-main font-semibold">{{ rule.name }}</h3>
                                <!-- Status Badge — per skill spec table -->
                                <span
                                    class="text-[10px] font-bold tracking-widest px-2 py-0.5 rounded-full uppercase border"
                                    :class="rule.is_active
                                        ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30'
                                        : 'bg-red-500/20 text-red-400 border-red-500/30'"
                                >
                                    {{ rule.is_active ? 'Running' : 'Stopped' }}
                                </span>
                            </div>
                            <p class="text-sm text-text-dim mt-0.5">{{ rule.description }}</p>
                        </div>
                        <!-- Action buttons — visible on hover -->
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="openModal(rule)" class="bg-surface-light hover:bg-surface text-text-dim hover:text-text-main text-sm font-semibold px-4 py-2 rounded-lg transition-colors border border-glass-border flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-primary" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                Configure
                            </button>
                            <button @click="deleteRule(rule.id)" class="bg-red-500/20 hover:bg-red-500/30 text-red-400 text-sm font-semibold p-2 rounded-lg transition-colors border border-red-500/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            </button>
                        </div>
                    </div>
                    <!-- Pipeline visualization — matching skill spec colors -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="bg-purple-500/20 text-purple-400 border border-purple-500/30 text-[10px] font-bold px-2 py-0.5 rounded uppercase">TRIGGER</span>
                        <span class="text-xs text-text-dim font-mono">{{ rule.trigger_event.replace(/_/g, ' ') }}</span>
                        <span class="text-text-dim">▶▶</span>
                        <span class="bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-[10px] font-bold px-2 py-0.5 rounded uppercase">✔ Conditions</span>
                        <span class="text-text-dim">▶▶</span>
                        <span class="bg-primary/20 text-primary border border-primary/30 text-[10px] font-bold px-2 py-0.5 rounded uppercase">THEN Exec Actions</span>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="rules.length === 0" class="py-24 border-2 border-dashed border-glass-border rounded-xl flex flex-col items-center justify-center bg-surface text-text-dim">
                    <div class="w-16 h-16 bg-background rounded-lg flex items-center justify-center border border-glass-border mb-6">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                    <h4 class="text-text-main font-semibold text-lg">No Automation Rules Found</h4>
                    <p class="text-xs mt-2 text-text-dim">Deploy your first rule to automate workflows.</p>
                </div>
            </div>

            <!-- ═══════════════════ TAB 2: SLA Policies ═══════════════════ -->
            <div v-if="activeTab === 'slas'" class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-text-main">SLA Contracts</h3>
                        <p class="text-xs text-text-dim mt-0.5">Operational thresholds defined by ticket priority levels.</p>
                    </div>
                    <button @click="openSlaModal()" class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                        Create New Standard
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="policy in slaPolicies" :key="policy.id" class="bg-surface border border-glass-border rounded-xl p-6 relative group hover:border-primary/40 transition-all overflow-hidden">
                        <!-- Priority indicator top bar -->
                        <div class="absolute top-0 inset-x-0 h-1" :class="getPriorityColorClass(policy.priority).split(' ')[0]"></div>

                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-[10px] font-bold text-text-dim uppercase tracking-widest">Policy Node</span>
                                <h3 class="text-lg font-bold text-text-main">{{ policy.name }}</h3>
                            </div>
                            <div class="px-2.5 py-1 bg-background border border-glass-border rounded-lg">
                                <div class="w-2.5 h-2.5 rounded-full" :class="getPriorityColorClass(policy.priority).split(' ')[0]"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-background p-4 rounded-lg border border-glass-border">
                                <p class="text-[10px] font-bold text-text-dim uppercase tracking-widest mb-2">Response</p>
                                <p class="text-2xl font-bold text-text-main">{{ formatMinutes(policy.response_time_minutes) }}</p>
                            </div>
                            <div class="bg-background p-4 rounded-lg border border-glass-border">
                                <p class="text-[10px] font-bold text-text-dim uppercase tracking-widest mb-2">Resolution</p>
                                <p class="text-2xl font-bold text-text-main">{{ formatMinutes(policy.resolution_time_minutes) }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t border-glass-border">
                            <span class="text-[10px] font-bold text-text-dim uppercase tracking-widest">{{ policy.business_hours_only ? 'Business Hours' : '24/7' }}</span>
                            <div class="flex gap-2">
                                <button @click="openSlaModal(policy)" class="bg-surface-light hover:bg-surface text-text-dim hover:text-text-main p-2 rounded-lg transition-colors border border-glass-border">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                </button>
                                <button @click="deleteSla(policy.id)" class="bg-red-500/20 hover:bg-red-500/30 text-red-400 p-2 rounded-lg transition-colors border border-red-500/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════ TAB 3: Audit Log ═══════════════════ -->
            <div v-if="activeTab === 'audit'" class="space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="relative flex-1 max-w-md">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-text-dim">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </span>
                        <input
                            v-model="auditSearch"
                            @input="fetchAuditLogs(1)"
                            type="text"
                            placeholder="Search action, actor, or resource..."
                            class="w-full bg-background border border-glass-border rounded-lg pl-10 pr-4 py-2.5 text-sm text-text-main placeholder-text-dim focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
                        />
                    </div>
                    <div class="text-[10px] font-bold uppercase tracking-widest text-text-dim">
                        Total Records: <span class="text-text-main">{{ auditMeta.total }}</span>
                    </div>
                </div>

                <div class="bg-surface border border-glass-border rounded-xl overflow-hidden relative">
                    <div v-if="auditLoading" class="absolute inset-0 bg-background/50 backdrop-blur-sm z-10 flex items-center justify-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-light border-b border-glass-border">
                                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Timestamp</th>
                                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Action</th>
                                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Resource</th>
                                    <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Actor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-glass-border">
                                <tr v-for="log in auditLogs" :key="log.id" class="hover:bg-surface-light/40 transition-colors">
                                    <td class="px-6 py-4 text-xs font-mono text-text-dim">{{ new Date(log.created_at).toLocaleString() }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 bg-surface-light border border-glass-border text-text-main rounded text-[10px] font-bold uppercase tracking-widest">{{ log.action.replace(/_/g, ' ') }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-semibold text-text-main">
                                        {{ log.auditable_type.split('\\').pop() }} <span class="text-primary">#{{ log.auditable_id }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-xs text-text-dim">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-surface-light border border-glass-border flex items-center justify-center flex-shrink-0">
                                                <span v-if="log.user" class="text-[9px] font-bold text-text-main">{{ log.user.name.charAt(0) }}</span>
                                                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-text-dim" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" /></svg>
                                            </div>
                                            {{ log.user ? log.user.name : 'System (Autopilot)' }}
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="auditLogs.length === 0">
                                    <td colspan="4" class="px-6 py-12 text-center text-sm text-text-dim">
                                        No transaction logs found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="auditMeta.lastPage > 1" class="px-6 py-4 border-t border-glass-border flex items-center justify-between bg-surface-light/30">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-text-dim">
                            Page {{ auditMeta.currentPage }} of {{ auditMeta.lastPage }}
                        </span>
                        <div class="flex gap-2">
                            <button
                                @click="fetchAuditLogs(auditMeta.currentPage - 1)"
                                :disabled="auditMeta.currentPage === 1 || auditLoading"
                                class="px-3 py-1.5 bg-surface-light border border-glass-border rounded-lg text-xs font-semibold text-text-dim hover:text-text-main disabled:opacity-30 transition-all"
                            >Previous</button>
                            <button
                                @click="fetchAuditLogs(auditMeta.currentPage + 1)"
                                :disabled="auditMeta.currentPage === auditMeta.lastPage || auditLoading"
                                class="px-3 py-1.5 bg-surface-light border border-glass-border rounded-lg text-xs font-semibold text-text-dim hover:text-text-main disabled:opacity-30 transition-all"
                            >Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══════════════════ Rule Modal ═══════════════════ -->
        <div v-if="showModal" class="fixed inset-0 bg-background/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-surface border border-glass-border rounded-xl w-full max-w-4xl p-8 shadow-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex items-center gap-3 mb-8 pb-4 border-b border-glass-border">
                    <div class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center text-primary border border-primary/30">🤖</div>
                    <div>
                        <h2 class="text-xl font-bold text-text-main">{{ editingId ? 'Update Automation Rule' : 'Deploy Automation Logic' }}</h2>
                        <p class="text-xs text-text-dim mt-0.5">Define computational parameters for machine-triggered actions.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Workflow Label -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-text-main">Workflow Label</label>
                        <input v-model="form.name" type="text" placeholder="e.g. Route Hardware to IT" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2.5 text-sm text-text-main placeholder-text-dim focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" />
                    </div>

                    <!-- Trigger Event -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-text-main">Trigger Event</label>
                        <select v-model="form.trigger_event" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2.5 text-sm text-text-main focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                            <option value="ticket_created">When a ticket is Created</option>
                            <option value="ticket_updated">When a ticket is Updated</option>
                            <option value="sla_approaching">When SLA warning is Approaching</option>
                            <option value="schedule">CRON Schedule Base</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="col-span-full space-y-1">
                        <label class="text-sm font-medium text-text-main">Description</label>
                        <input v-model="form.description" type="text" placeholder="Internal notes describing when this operates." class="w-full bg-background border border-glass-border rounded-lg px-4 py-2.5 text-sm text-text-main placeholder-text-dim focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" />
                    </div>

                    <!-- Conditions JSON -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-text-main">Condition Parameters (JSON)</label>
                        <textarea v-model="form.condition_json" rows="8" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2.5 text-xs font-mono text-emerald-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"></textarea>
                        <p class="text-xs text-text-dim">Filter exactly what conditions satisfy this rule.</p>
                    </div>

                    <!-- Actions JSON -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-text-main">Action Payloads (JSON Array)</label>
                        <textarea v-model="form.action_json" rows="8" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2.5 text-xs font-mono text-rose-400 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"></textarea>
                        <p class="text-xs text-text-dim">Array block dictating exact machine tasks to execute.</p>
                    </div>

                    <!-- Priority -->
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-text-main">Execution Priority</label>
                        <div class="relative">
                            <input v-model="form.priority" type="number" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2.5 text-sm text-text-main pr-16 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" />
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-text-dim tracking-widest">NUM</span>
                        </div>
                    </div>

                    <!-- Toggle -->
                    <div class="flex items-center justify-between bg-background border border-glass-border rounded-lg px-4 py-3">
                        <div>
                            <span class="text-sm font-medium text-text-main">Live Toggle</span>
                            <p class="text-xs text-text-dim mt-0.5">Machine state on/off.</p>
                        </div>
                        <button
                            @click="form.is_active = !form.is_active"
                            :class="form.is_active ? 'bg-primary' : 'bg-surface-light'"
                            class="w-11 h-6 rounded-full p-1 flex items-center transition-colors focus:outline-none flex-shrink-0"
                        >
                            <span
                                :class="form.is_active ? 'translate-x-5' : 'translate-x-0'"
                                class="w-4 h-4 bg-white rounded-full shadow transition-transform block"
                            ></span>
                        </button>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-glass-border">
                    <button @click="showModal = false" class="bg-surface-light hover:bg-surface text-text-dim text-sm font-semibold px-4 py-2 rounded-lg transition-colors border border-glass-border">Discard</button>
                    <button @click="saveRule" :disabled="saving" class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-6 py-2 rounded-lg transition-colors disabled:opacity-50 flex items-center gap-2">
                        <span v-if="saving" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                        {{ editingId ? 'Update Rule' : 'Deploy Rule' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- ═══════════════════ SLA Modal ═══════════════════ -->
        <div v-if="showSlaModal" class="fixed inset-0 bg-background/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-surface border border-glass-border rounded-xl w-full max-w-2xl p-8 shadow-2xl">
                <div class="flex items-center gap-3 mb-8 pb-4 border-b border-glass-border">
                    <div class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center text-primary border border-primary/30">⏱️</div>
                    <div>
                        <h2 class="text-xl font-bold text-text-main">{{ editingSlaId ? 'Update SLA Contract' : 'Deploy New SLA Contract' }}</h2>
                        <p class="text-xs text-text-dim mt-0.5">Define operational thresholds for service level agreements.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-full space-y-1">
                        <label class="text-sm font-medium text-text-main">Contract Name</label>
                        <input v-model="slaForm.name" type="text" placeholder="e.g. Critical Support Response" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2.5 text-sm text-text-main placeholder-text-dim focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" />
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-text-main">Priority Assignment</label>
                        <select v-model="slaForm.priority" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2.5 text-sm text-text-main focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-between bg-background border border-glass-border rounded-lg px-4 py-3">
                        <span class="text-sm font-medium text-text-main">{{ slaForm.business_hours_only ? 'Business Hours' : '24/7' }}</span>
                        <button
                            @click="slaForm.business_hours_only = !slaForm.business_hours_only"
                            :class="slaForm.business_hours_only ? 'bg-primary' : 'bg-surface-light'"
                            class="w-11 h-6 rounded-full p-1 flex items-center transition-colors focus:outline-none flex-shrink-0"
                        >
                            <span
                                :class="slaForm.business_hours_only ? 'translate-x-5' : 'translate-x-0'"
                                class="w-4 h-4 bg-white rounded-full shadow transition-transform block"
                            ></span>
                        </button>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-text-main">First Response Goal (Minutes)</label>
                        <input v-model="slaForm.response_time_minutes" type="number" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2.5 text-sm text-text-main focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" />
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-text-main">Full Resolution Goal (Minutes)</label>
                        <input v-model="slaForm.resolution_time_minutes" type="number" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2.5 text-sm text-text-main focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all" />
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-glass-border">
                    <button @click="showSlaModal = false" class="bg-surface-light hover:bg-surface text-text-dim text-sm font-semibold px-4 py-2 rounded-lg transition-colors border border-glass-border">Discard</button>
                    <button @click="saveSla" :disabled="saving" class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-6 py-2 rounded-lg transition-colors disabled:opacity-50 flex items-center gap-2">
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

// ─── Static data ─────────────────────────────────────────────────────────────
const engineSteps = [
    { num: '1', title: 'Trigger Event', desc: 'The catalyst (e.g., ticket created) that wakes the engine to evaluate this rule.' },
    { num: '2', title: 'Condition Map', desc: 'The "if" statement. The engine checks if certain fields match your specified logic.' },
    { num: '3', title: 'Action Payload', desc: 'The machine tasks performed if conditions met (e.g., reassign, tag, or email).' },
];

const tabs = [
    { id: 'rules', label: 'Triggers & Rules' },
    { id: 'slas', label: 'SLA Contracts' },
    { id: 'audit', label: 'Audit Trail' },
];

// ─── Reactive state ──────────────────────────────────────────────────────────
const activeTab = ref('rules');
const loading = ref(true);

const rules = ref([]);
const slaPolicies = ref([]);
const auditLogs = ref([]);
const auditMeta = ref({ currentPage: 1, lastPage: 1, total: 0, perPage: 15 });
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

// SLA Modal
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
            toast.success('SLA Contract updated.');
        } else {
            await api.post('/admin/sla-policies', slaForm.value);
            toast.success('New SLA Contract deployed.');
        }
        showSlaModal.value = false;
        fetchData();
    } catch (error) {
        toast.error('Failed to save SLA parameters.');
    } finally {
        saving.value = false;
    }
};

const deleteSla = async (id) => {
    if (confirm('Permanently decommission this SLA protocol?')) {
        try {
            await api.delete(`/admin/sla-policies/${id}`);
            toast.info('SLA removed.');
            fetchData();
        } catch (error) {
            toast.error('Deletion failed.');
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
            toast.error('Invalid JSON structure. Check condition or action fields.');
            saving.value = false;
            return;
        }

        if (editingId.value) {
            await api.put(`/admin/automation-rules/${editingId.value}`, payload);
            toast.success('Rule updated.');
        } else {
            await api.post('/admin/automation-rules', payload);
            toast.success('New rule deployed.');
        }
        showModal.value = false;
        fetchData();
    } catch (error) {
        toast.error('Failed to save rule.');
    } finally {
        saving.value = false;
    }
};

const fetchAuditLogsInternal = async (page = 1) => {
    auditLoading.value = true;
    try {
        const response = await api.get('/admin/audit-logs', {
            params: { page, search: auditSearch.value, per_page: auditMeta.value.perPage }
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
        toast.error('Failed to fetch automation data.');
    } finally {
        loading.value = false;
    }
};

onMounted(fetchData);

const formatMinutes = (min) => {
    if (min < 60) return `${min}m`;
    const hours = Math.floor(min / 60);
    const rem = min % 60;
    return rem > 0 ? `${hours}H ${rem}M` : `${hours}H`;
};

const getPriorityColorClass = (priority) => {
    switch (priority) {
        case 'critical': return 'bg-red-500 text-red-500';
        case 'high': return 'bg-amber-500 text-amber-500';
        case 'medium': return 'bg-blue-500 text-blue-500';
        case 'low': return 'bg-emerald-500 text-emerald-500';
        default: return 'bg-gray-500 text-gray-500';
    }
};

const deleteRule = async (id) => {
    if (confirm('Permanently delete this automation rule?')) {
        try {
            await api.delete(`/admin/automation-rules/${id}`);
            toast.info('Rule deleted.');
            fetchData();
        } catch (error) {
            toast.error('Deletion failed.');
        }
    }
};
</script>
