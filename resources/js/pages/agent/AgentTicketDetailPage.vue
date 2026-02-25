<template>
    <div class="w-full">

        <!-- ── Loading skeleton ── -->
        <div v-if="loading" class="space-y-8 py-8 animate-pulse">
            <div class="h-10 w-72 bg-surface-light rounded-xl"></div>
            <div class="h-64 glass-card rounded-xl"></div>
            <div class="h-96 glass-card rounded-xl"></div>
        </div>

        <!-- ── 404 ── -->
        <div v-else-if="!ticket" class="py-24 text-center space-y-6">
            <h2 class="text-4xl font-black text-text-main">Ticket Not Found</h2>
            <p class="text-text-dim">This ticket does not exist or you lack permission to view it.</p>
            <router-link to="/agent/queue" class="px-10 py-4 bg-primary text-white rounded-lg inline-block font-bold">Back to Queue</router-link>
        </div>

        <!-- ── Main ── -->
        <div v-else class="space-y-8 pb-24 animate-fade-in">

            <!-- Archived Banner -->
            <div v-if="ticket.is_archived" class="bg-amber-500/10 border border-amber-500/30 text-amber-500 px-6 py-4 rounded-xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="text-xl">🗄️</span>
                    <div>
                        <h3 class="font-bold text-sm">Archived Record</h3>
                        <p class="text-xs opacity-80 mt-1">This ticket is archived and removed from active queues. Interactions are disabled.</p>
                    </div>
                </div>
                <!-- Admin Unarchive Button -->
                <button v-if="auth.user?.role === 'admin'" 
                        @click="toggleArchive" 
                        class="px-4 py-2 bg-amber-500 text-white rounded-lg text-xs font-bold uppercase tracking-widest hover-lift">
                    Unarchive
                </button>
            </div>

            <!-- Header -->
            <div class="flex items-center justify-between flex-wrap gap-4">
                <router-link to="/agent/queue" class="flex items-center gap-2 text-text-dim hover:text-primary transition-colors group">
                    <div class="p-2 rounded-lg group-hover:bg-primary/10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </div>
                    <span class="font-bold text-sm tracking-tight">Back to Queue</span>
                </router-link>

                <!-- Quick status pill -->
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest"
                          :class="statusPillClass(ticket.status)">
                        {{ ticket.status.replace(/_/g, ' ') }}
                    </span>
                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest"
                          :class="priorityPillClass(ticket.priority)">
                        {{ ticket.priority }}
                    </span>

                    <button @click="markSpam"
                            :disabled="actionLoading || ticket.is_archived"
                            title="Mark as Spam"
                            class="p-2 rounded-lg bg-surface-light border border-glass-border hover:bg-red-500/10 hover:border-red-500/40 hover:text-red-400 text-text-dim transition-all text-sm font-bold flex items-center gap-1.5 disabled:opacity-50">
                        🚫 <span class="text-xs">Spam</span>
                    </button>

                    <!-- Relink button -->
                    <button @click="showRelinkModal = true"
                            :disabled="actionLoading || ticket.is_archived"
                            title="Relink / Reassign"
                            class="p-2 rounded-lg bg-surface-light border border-glass-border hover:bg-primary/10 hover:border-primary/40 hover:text-primary text-text-dim transition-all text-sm font-bold flex items-center gap-1.5 disabled:opacity-50">
                        🔗 <span class="text-xs">Relink</span>
                    </button>

                    <!-- Archive button (Admin Only) -->
                    <button v-if="auth.user?.role === 'admin' && !ticket.is_archived" 
                            @click="toggleArchive"
                            :disabled="actionLoading"
                            title="Archive Ticket"
                            class="p-2 rounded-lg bg-surface-light border border-glass-border hover:bg-amber-500/10 hover:border-amber-500/40 hover:text-amber-500 text-text-dim transition-all text-sm font-bold flex items-center gap-1.5 disabled:opacity-50">
                        🗄️ <span class="text-xs">Archive</span>
                    </button>
                </div>
            </div>

            <!-- Two-column layout -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

                <!-- ── Left: Ticket body + conversation ── -->
                <div class="xl:col-span-2 space-y-8">

                    <!-- Ticket card -->
                    <div class="glass-card p-10 rounded-2xl relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-6">
                            <span class="px-3 py-1 bg-surface-light rounded-lg text-[9px] font-black uppercase tracking-[0.2em] text-text-dim">
                                {{ ticket.source }}
                            </span>
                        </div>

                        <div class="space-y-6">
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <span class="text-xs font-black uppercase tracking-widest text-primary">{{ ticket.ticket_number }}</span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-glass-border"></span>
                                    <span class="text-xs text-text-dim font-medium">{{ formatDate(ticket.created_at) }}</span>
                                    <span class="w-1.5 h-1.5 rounded-full bg-glass-border"></span>
                                    <span class="text-xs text-text-dim font-medium">
                                        by <strong class="text-text-main">{{ ticket.requester?.name }}</strong>
                                    </span>
                                </div>
                                <h1 class="text-3xl font-black text-text-main tracking-tight leading-tight">{{ ticket.title }}</h1>
                            </div>

                            <div class="prose prose-invert max-w-none">
                                <p class="text-base text-text-dim leading-relaxed whitespace-pre-wrap">{{ ticket.description }}</p>
                            </div>

                            <!-- Ticket Attachments -->
                            <div v-if="ticket.attachments?.length" class="pt-6 border-t border-glass-border">
                                <p class="text-[9px] font-black uppercase tracking-widest text-text-dim/50 mb-3">
                                    Attachments ({{ ticket.attachments.length }})
                                </p>
                                <div class="flex flex-wrap gap-3">
                                    <template v-for="file in ticket.attachments" :key="file.id">
                                        <a v-if="file.is_image"
                                           :href="file.url" target="_blank"
                                           class="relative w-20 h-20 rounded-lg overflow-hidden border border-glass-border hover:border-primary/50 hover:scale-105 transition-all block">
                                            <img :src="file.url" :alt="file.file_name" class="w-full h-full object-cover"/>
                                        </a>
                                        <a v-else :href="file.url" target="_blank"
                                           class="flex items-center gap-3 px-4 py-2 rounded-lg bg-surface-light border border-glass-border hover:border-primary/40 transition-all group">
                                            <span class="text-xl">📄</span>
                                            <div>
                                                <p class="text-xs font-bold text-text-main truncate max-w-[120px]">{{ file.file_name }}</p>
                                                <p class="text-[9px] text-text-dim">{{ formatBytes(file.file_size) }}</p>
                                            </div>
                                        </a>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Log -->
                    <div class="space-y-6">
                        <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-dim ml-2">Activity Log</h3>

                        <div v-if="ticket.comments?.length" class="space-y-4 pl-10 border-l-2 border-glass-border relative">
                            <div v-for="comment in ticket.comments" :key="comment.id" class="relative">
                                <!-- Dot -->
                                <div class="absolute -left-12 top-3 w-4 h-4 rounded-full border-4 border-surface shadow-sm"
                                     :class="comment.is_internal ? 'bg-amber-500' : 'bg-primary'">
                                </div>

                                <div class="glass-card p-6 rounded-xl space-y-2"
                                     :class="comment.is_internal ? 'border border-amber-500/20 bg-amber-500/5' : ''">
                                    <div class="flex items-center justify-between flex-wrap gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="font-black text-text-main text-sm">{{ comment.user?.name || 'Unknown' }}</span>
                                            <span v-if="comment.user?.role !== 'user'"
                                                  class="px-2 py-0.5 bg-primary/20 text-primary text-[8px] font-black uppercase tracking-[0.2em] rounded-md">
                                                Agent
                                            </span>
                                            <span v-if="comment.is_internal"
                                                  class="px-2 py-0.5 bg-amber-500/20 text-amber-400 text-[8px] font-black uppercase tracking-[0.2em] rounded-md">
                                                Internal Note
                                            </span>
                                        </div>
                                        <span class="text-[10px] text-text-dim font-bold uppercase tracking-widest">
                                            {{ formatTimeAgo(comment.created_at) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-text-dim leading-relaxed whitespace-pre-wrap">{{ comment.body }}</p>

                                    <!-- Comment attachments -->
                                    <div v-if="comment.attachments?.length" class="flex flex-wrap gap-2 pt-2 border-t border-glass-border/50">
                                        <a v-for="att in comment.attachments" :key="att.id"
                                           :href="att.url" target="_blank"
                                           class="text-xs text-primary underline hover:no-underline">
                                            📎 {{ att.file_name }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="glass-card p-10 rounded-xl text-center space-y-4">
                            <div class="w-14 h-14 bg-surface-light rounded-lg flex items-center justify-center mx-auto text-2xl opacity-40">💬</div>
                            <p class="text-text-dim font-medium text-sm">No activity yet on this ticket.</p>
                        </div>
                    </div>

                    <!-- ── Reply / Internal Note Area ── -->
                    <div class="glass-card p-6 rounded-2xl shadow-2xl space-y-4">
                        <!-- Toggle: Reply vs Internal Note -->
                        <div class="flex gap-2">
                            <button @click="replyMode = 'reply'"
                                    :class="replyMode === 'reply'
                                        ? 'bg-primary text-white'
                                        : 'bg-surface-light text-text-dim hover:bg-white/5'"
                                    class="px-5 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all">
                                💬 Reply
                            </button>
                            <button @click="replyMode = 'internal'"
                                    :class="replyMode === 'internal'
                                        ? 'bg-amber-500 text-white'
                                        : 'bg-surface-light text-text-dim hover:bg-white/5'"
                                    class="px-5 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all">
                                🔒 Internal Note
                            </button>
                        </div>

                        <!-- Canned response picker -->
                        <div class="relative" ref="cannedDropdownRef">
                            <button @click="toggleCannedDropdown"
                                    class="flex items-center gap-2 px-4 py-2 bg-surface-light border border-glass-border rounded-lg text-xs font-bold text-text-dim hover:border-primary/40 hover:text-primary transition-all">
                                ⚡ Canned Response
                                <svg class="w-3 h-3 transition-transform" :class="showCannedDropdown ? 'rotate-180' : ''"
                                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <div v-if="showCannedDropdown"
                                 class="absolute left-0 top-10 z-30 w-80 glass-card rounded-xl shadow-2xl border border-glass-border overflow-hidden">
                                <div class="p-3 border-b border-glass-border">
                                    <input v-model="cannedSearch"
                                           placeholder="Search canned responses..."
                                           class="w-full bg-surface-light border border-glass-border rounded-lg px-3 py-2 text-xs text-text-main placeholder:text-text-dim focus:outline-none focus:ring-2 focus:ring-primary/40"/>
                                </div>
                                <div class="max-h-60 overflow-y-auto divide-y divide-glass-border">
                                    <div v-if="loadingCanned" class="p-4 text-center text-xs text-text-dim animate-pulse">
                                        Loading...
                                    </div>
                                    <div v-else-if="filteredCanned.length === 0" class="p-4 text-center text-xs text-text-dim italic">
                                        No canned responses found.
                                    </div>
                                    <button v-for="cr in filteredCanned" :key="cr.id"
                                            @click="applyCanned(cr)"
                                            class="w-full text-left px-4 py-3 hover:bg-white/5 transition-colors space-y-0.5">
                                        <p class="text-sm font-bold text-text-main">{{ cr.title }}</p>
                                        <p class="text-xs text-text-dim line-clamp-2">{{ cr.body }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span v-if="cr.category" class="text-[9px] px-2 py-0.5 bg-primary/10 text-primary rounded font-bold uppercase tracking-widest">{{ cr.category }}</span>
                                            <span class="text-[9px] px-2 py-0.5 rounded font-bold uppercase tracking-widest"
                                                  :class="cr.is_shared ? 'bg-emerald-500/10 text-emerald-400' : 'bg-surface-light text-text-dim'">
                                                {{ cr.is_shared ? 'Shared' : 'Private' }}
                                            </span>
                                        </div>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Textarea -->
                        <textarea
                            v-model="commentBody"
                            :placeholder="replyMode === 'internal' ? 'Write an internal note (only visible to agents)...' : 'Write a reply to the customer...'"
                            class="w-full bg-surface-light border border-glass-border rounded-xl p-4 text-sm text-text-main focus:ring-2 focus:ring-primary/40 focus:outline-none min-h-[140px] resize-none transition-all"
                            :class="replyMode === 'internal' ? 'border-amber-500/30 focus:ring-amber-500/40' : ''"
                        ></textarea>

                        <!-- Attachment area -->
                        <div>
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-text-dim hover:text-primary transition-colors w-fit">
                                <input type="file" multiple ref="fileInput" @change="onFilesSelected" class="hidden" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip"/>
                                📎 <span>Attach Files</span>
                            </label>

                            <div v-if="attachedFiles.length" class="mt-3 flex flex-wrap gap-2">
                                <div v-for="(f, i) in attachedFiles" :key="i"
                                     class="flex items-center gap-2 px-3 py-1.5 bg-surface-light border border-glass-border rounded-lg">
                                    <span class="text-sm">{{ f.type.startsWith('image/') ? '🖼️' : '📄' }}</span>
                                    <span class="text-xs text-text-main max-w-[120px] truncate">{{ f.name }}</span>
                                    <button @click="removeFile(i)" class="text-text-dim hover:text-red-400 transition-colors text-sm leading-none">✕</button>
                                </div>
                            </div>
                        </div>

                        <!-- Send Row -->
                        <div class="flex items-center justify-between flex-wrap gap-3 pt-2 border-t border-glass-border">
                            <div class="text-xs text-text-dim/60 font-medium">
                                <span v-if="replyMode === 'internal'" class="text-amber-400">⚠ Internal Note — not visible to customer</span>
                                <span v-else>✉ This reply will be emailed to the customer</span>
                            </div>
                            <button
                                @click="submitComment"
                                :disabled="!commentBody.trim() || sending || ticket.is_archived"
                                class="px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest transition-all disabled:opacity-40 disabled:cursor-not-allowed hover-lift"
                                :class="replyMode === 'internal'
                                    ? 'bg-amber-500 text-white'
                                    : 'bg-primary text-white'">
                                {{ sending ? 'Sending...' : (replyMode === 'internal' ? 'Add Note' : 'Send Reply') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ── Right: Sidebar ── -->
                <div class="space-y-6">

                    <!-- Status & Priority -->
                    <div class="glass-card p-6 rounded-2xl space-y-5">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-text-dim">Ticket Controls</h4>

                        <!-- Status -->
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-text-dim/60 mb-2 block">Status</label>
                            <select v-model="editStatus" @change="updateTicket({ status: editStatus })" :disabled="ticket.is_archived"
                                    class="w-full bg-surface-light border border-glass-border rounded-lg px-3 py-2 text-sm font-bold text-text-main focus:ring-2 focus:ring-primary/40 focus:outline-none cursor-pointer disabled:opacity-50">
                                <option value="new">🔵 New</option>
                                <option value="in_progress">🟣 In Progress</option>
                                <option value="waiting_on_customer">🟡 Waiting on Customer</option>
                                <option value="resolved">🟢 Resolved</option>
                                <option value="closed">⚫ Closed</option>
                            </select>
                        </div>

                        <!-- Priority -->
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-text-dim/60 mb-2 block">Priority</label>
                            <select v-model="editPriority" @change="updateTicket({ priority: editPriority })" :disabled="ticket.is_archived"
                                    class="w-full bg-surface-light border border-glass-border rounded-lg px-3 py-2 text-sm font-bold text-text-main focus:ring-2 focus:ring-primary/40 focus:outline-none cursor-pointer disabled:opacity-50">
                                <option value="low">🔵 Low</option>
                                <option value="medium">🟣 Medium</option>
                                <option value="high">🟠 High</option>
                                <option value="critical">🔴 Critical</option>
                            </select>
                        </div>

                        <!-- Assigned Agent -->
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-text-dim/60 mb-2 block">Assigned Agent</label>
                            <div v-if="ticket.agent" class="flex items-center gap-3 p-3 bg-surface-light rounded-lg border border-glass-border">
                                <div class="w-9 h-9 rounded-lg bg-primary/10 flex items-center justify-center font-black text-primary text-sm border border-primary/20">
                                    {{ ticket.agent.name.charAt(0) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-text-main text-sm truncate">{{ ticket.agent.name }}</p>
                                    <p class="text-[9px] text-text-dim uppercase tracking-widest truncate">{{ ticket.agent.email }}</p>
                                </div>
                                <button @click="updateTicket({ agent_id: null })" class="text-text-dim/40 hover:text-red-400 transition-colors text-xs">✕</button>
                            </div>
                            <div v-else class="p-3 bg-white/5 rounded-lg text-center">
                                <p class="text-xs font-bold text-text-dim italic">Unassigned</p>
                            </div>
                            <button @click="showRelinkModal = true"
                                    class="mt-2 w-full text-center text-xs font-bold text-primary hover:underline">
                                Reassign →
                            </button>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="glass-card p-6 rounded-2xl space-y-3">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-text-dim">Category</h4>
                        <div class="flex items-center gap-3 p-3 bg-surface-light rounded-lg border border-glass-border">
                            <span class="text-2xl">{{ ticket.category?.icon }}</span>
                            <span class="font-bold text-text-main text-sm">{{ ticket.category?.name }}</span>
                        </div>
                    </div>

                    <!-- Requester -->
                    <div class="glass-card p-6 rounded-2xl space-y-3">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-text-dim">Requester</h4>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-surface-light flex items-center justify-center font-black text-text-dim text-sm border border-glass-border">
                                {{ ticket.requester?.name?.charAt(0) }}
                            </div>
                            <div>
                                <p class="font-bold text-text-main text-sm">{{ ticket.requester?.name }}</p>
                                <p class="text-[10px] text-text-dim uppercase tracking-widest">Customer</p>
                            </div>
                        </div>
                    </div>

                    <!-- SLA -->
                    <div v-if="ticket.sla_due_at" class="glass-card p-6 rounded-2xl space-y-3">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-text-dim">SLA Due</h4>
                        <p class="text-sm font-bold" :class="isSlaBreached ? 'text-red-400' : 'text-text-main'">
                            {{ isSlaBreached ? '🔴' : '🟢' }} {{ formatDate(ticket.sla_due_at) }}
                        </p>
                    </div>

                    <!-- Tags -->
                    <div v-if="ticket.tags?.length" class="glass-card p-6 rounded-2xl space-y-3">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-text-dim">Tags</h4>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="tag in ticket.tags" :key="tag"
                                  class="px-3 py-1 bg-surface-light border border-glass-border rounded-full text-[10px] font-bold text-text-dim uppercase tracking-widest">
                                {{ tag }}
                            </span>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="glass-card p-6 rounded-2xl space-y-3 text-xs">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-text-dim">Timeline</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between gap-2">
                                <span class="text-text-dim">Created</span>
                                <span class="font-bold text-text-main text-right">{{ formatDate(ticket.created_at) }}</span>
                            </div>
                            <div v-if="ticket.first_response_at" class="flex justify-between gap-2">
                                <span class="text-text-dim">First Response</span>
                                <span class="font-bold text-emerald-400 text-right">{{ formatDate(ticket.first_response_at) }}</span>
                            </div>
                            <div v-if="ticket.resolved_at" class="flex justify-between gap-2">
                                <span class="text-text-dim">Resolved</span>
                                <span class="font-bold text-emerald-400 text-right">{{ formatDate(ticket.resolved_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Relink / Reassign Modal ── -->
        <Transition name="modal">
            <div v-if="showRelinkModal"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
                 @click.self="showRelinkModal = false">
                <div class="glass-card rounded-2xl w-full max-w-md p-8 space-y-6 shadow-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-black text-text-main">🔗 Relink Ticket</h3>
                        <button @click="showRelinkModal = false" class="text-text-dim hover:text-text-main p-1 rounded-lg hover:bg-white/5 transition-all">✕</button>
                    </div>

                    <!-- Agent search -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-text-dim block">Assign to Agent</label>
                        <div class="relative">
                            <input v-model="agentSearch"
                                   @input="searchAgents"
                                   placeholder="Search agents by name..."
                                   class="w-full bg-surface-light border border-glass-border rounded-lg px-4 py-2.5 text-sm text-text-main placeholder:text-text-dim focus:ring-2 focus:ring-primary/40 focus:outline-none"/>
                        </div>

                        <div v-if="agentResults.length" class="bg-surface-light border border-glass-border rounded-xl overflow-hidden divide-y divide-glass-border max-h-48 overflow-y-auto">
                            <button v-for="u in agentResults" :key="u.id"
                                    @click="selectAgent(u)"
                                    class="w-full text-left px-4 py-3 hover:bg-white/5 transition-colors flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-sm font-black text-primary">
                                    {{ u.name.charAt(0) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-text-main">{{ u.name }}</p>
                                    <p class="text-[10px] text-text-dim">{{ u.email }}</p>
                                </div>
                            </button>
                        </div>

                        <button v-if="relinkAgentId"
                                @click="relinkAgentId = null; relinkAgentName = ''; agentSearch = ''"
                                class="text-xs text-red-400 hover:underline">
                            ✕ Remove agent assignment
                        </button>

                        <div v-if="relinkAgentName" class="px-4 py-2 bg-primary/10 border border-primary/20 rounded-lg text-xs font-bold text-primary">
                            ✓ Will assign to: {{ relinkAgentName }}
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button @click="showRelinkModal = false"
                                class="px-6 py-2 bg-surface-light border border-glass-border rounded-lg text-xs font-black uppercase tracking-widest text-text-dim hover:bg-white/5 transition-all">
                            Cancel
                        </button>
                        <button @click="applyRelink"
                                :disabled="actionLoading"
                                class="px-6 py-2 bg-primary text-white rounded-lg text-xs font-black uppercase tracking-widest hover-lift disabled:opacity-50 transition-all">
                            {{ actionLoading ? 'Saving...' : 'Confirm Relink' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useToast } from 'vue-toastification';
import axios from '@/plugins/axios';

// ─── Router / Auth ───
const route  = useRoute();
const router = useRouter();
const auth   = useAuthStore();
const toast  = useToast();

// ─── State ───
const ticket        = ref(null);
const loading       = ref(true);
const sending       = ref(false);
const actionLoading = ref(false);

// Reply area
const replyMode   = ref('reply');   // 'reply' | 'internal'
const commentBody = ref('');
const attachedFiles = ref([]);
const fileInput   = ref(null);

// Canned responses
const cannedResponses    = ref([]);
const loadingCanned      = ref(false);
const showCannedDropdown = ref(false);
const cannedSearch       = ref('');
const cannedDropdownRef  = ref(null);

// Sidebar edits
const editStatus   = ref('');
const editPriority = ref('');

// Relink modal
const showRelinkModal = ref(false);
const agentSearch     = ref('');
const agentResults    = ref([]);
const relinkAgentId   = ref(null);
const relinkAgentName = ref('');

let agentSearchTimer = null;

// ─── Computed ───
const filteredCanned = computed(() => {
    const q = cannedSearch.value.toLowerCase();
    if (!q) return cannedResponses.value;
    return cannedResponses.value.filter(cr =>
        cr.title.toLowerCase().includes(q) || cr.body.toLowerCase().includes(q)
    );
});

const isSlaBreached = computed(() => {
    if (!ticket.value?.sla_due_at) return false;
    return new Date(ticket.value.sla_due_at) < new Date();
});

// ─── Lifecycle ───
onMounted(async () => {
    await loadTicket();
    await loadCannedResponses();
    document.addEventListener('click', handleOutsideClick);
});

onUnmounted(() => {
    document.removeEventListener('click', handleOutsideClick);
    if (window.Echo && ticket.value) {
        window.Echo.leave(`ticket.${ticket.value.id}`);
    }
});

// ─── Data Loaders ───
async function loadTicket() {
    loading.value = true;
    try {
        const { data } = await axios.get(`/tickets/${route.params.ticketNumber}`);
        ticket.value     = data.data;
        editStatus.value   = ticket.value.status;
        editPriority.value = ticket.value.priority;
        setupEcho();
    } catch (err) {
        toast.error('Failed to load ticket.');
    } finally {
        loading.value = false;
    }
}

async function loadCannedResponses() {
    loadingCanned.value = true;
    try {
        const { data } = await axios.get('/agent/canned-responses');
        cannedResponses.value = data.data ?? [];
    } catch {
        // silently fail – canned picker just stays empty
    } finally {
        loadingCanned.value = false;
    }
}

// ─── Echo (real-time) ───
function setupEcho() {
    if (window.Echo && ticket.value) {
        window.Echo.private(`ticket.${ticket.value.id}`)
            .listen('TicketCommentCreated', (e) => {
                const existing = ticket.value.comments?.find(c => c.id === e.comment.id);
                if (!existing) {
                    ticket.value.comments = ticket.value.comments ?? [];
                    ticket.value.comments.push(e.comment);
                    if (e.comment.user_id !== auth.user?.id) {
                        toast.info('New reply received');
                    }
                }
            })
            .listen('TicketUpdated', (e) => {
                // Smoothly sync the entire ticket object
                Object.assign(ticket.value, e.ticket);
                editStatus.value = ticket.value.status;
                editPriority.value = ticket.value.priority;
                toast.info(`Ticket updated to ${ticket.value.status.replace(/_/g, ' ')}`);
            });
    }
}

// ─── Reply / Comment ───
async function submitComment() {
    if (!commentBody.value.trim()) return;
    sending.value = true;
    try {
        const formData = new FormData();
        formData.append('body', commentBody.value);
        formData.append('is_internal', replyMode.value === 'internal' ? '1' : '0');
        attachedFiles.value.forEach(f => formData.append('attachments[]', f));

        const { data } = await axios.post(
            `/agent/tickets/${ticket.value.id}/comments`,
            formData,
            { headers: { 'Content-Type': 'multipart/form-data' } }
        );

        ticket.value.comments = ticket.value.comments ?? [];
        ticket.value.comments.push(data.data);
        commentBody.value = '';
        attachedFiles.value = [];
        toast.success(replyMode.value === 'internal' ? 'Internal note added.' : 'Reply sent!');
    } catch (err) {
        toast.error(err.response?.data?.message || 'Failed to send reply.');
    } finally {
        sending.value = false;
    }
}

// ─── Canned responses ───
function toggleCannedDropdown() {
    showCannedDropdown.value = !showCannedDropdown.value;
}

function applyCanned(cr) {
    commentBody.value = cr.body;
    showCannedDropdown.value = false;
    cannedSearch.value = '';
}

function handleOutsideClick(e) {
    if (cannedDropdownRef.value && !cannedDropdownRef.value.contains(e.target)) {
        showCannedDropdown.value = false;
    }
}

// ─── File attachments ───
function onFilesSelected(e) {
    const files = Array.from(e.target.files || []);
    attachedFiles.value.push(...files);
    if (fileInput.value) fileInput.value.value = '';
}

function removeFile(index) {
    attachedFiles.value.splice(index, 1);
}

// ─── Ticket update (status / priority / agent) ───
async function updateTicket(payload) {
    actionLoading.value = true;
    try {
        const { data } = await axios.patch(`/agent/tickets/${ticket.value.id}`, payload);
        Object.assign(ticket.value, data.data);
        editStatus.value   = ticket.value.status;
        editPriority.value = ticket.value.priority;
        toast.success('Ticket updated.');
    } catch (err) {
        toast.error(err.response?.data?.message || 'Update failed.');
        // Revert select
        editStatus.value   = ticket.value.status;
        editPriority.value = ticket.value.priority;
    } finally {
        actionLoading.value = false;
    }
}

// ─── Archive / Unarchive ───
async function toggleArchive() {
    if (!confirm(ticket.value.is_archived ? 'Unarchive this ticket?' : 'Archive this ticket? It will be hidden from queues.')) return;
    
    actionLoading.value = true;
    try {
        const action = ticket.value.is_archived ? 'unarchive' : 'archive';
        await axios.post(`/admin/tickets/${ticket.value.ticket_number}/${action}`);
        
        ticket.value.is_archived = !ticket.value.is_archived;
        toast.success(`Ticket ${ticket.value.is_archived ? 'archived' : 'unarchived'}.`);
    } catch (err) {
        toast.error(err.response?.data?.message || 'Failed to toggle archive status.');
    } finally {
        actionLoading.value = false;
    }
}

// ─── Spam ───
async function markSpam() {
    if (!confirm('Mark this ticket as spam and close it?')) return;
    await updateTicket({ status: 'closed', tags: [...(ticket.value.tags ?? []), 'spam'] });
}

// ─── Relink / Reassign ───
function searchAgents() {
    clearTimeout(agentSearchTimer);
    if (!agentSearch.value.trim()) {
        agentResults.value = [];
        return;
    }
    agentSearchTimer = setTimeout(async () => {
        try {
            const { data } = await axios.get('/search/users', { params: { q: agentSearch.value, role: 'agent' } });
            agentResults.value = data.data ?? [];
        } catch {
            agentResults.value = [];
        }
    }, 300);
}

function selectAgent(u) {
    relinkAgentId.value   = u.id;
    relinkAgentName.value = u.name;
    agentResults.value    = [];
    agentSearch.value     = u.name;
}

async function applyRelink() {
    const payload = {};
    if (relinkAgentId.value !== null) payload.agent_id = relinkAgentId.value;

    if (!Object.keys(payload).length) {
        showRelinkModal.value = false;
        return;
    }

    await updateTicket(payload);
    showRelinkModal.value = false;
    relinkAgentId.value   = null;
    relinkAgentName.value = '';
    agentSearch.value     = '';
    agentResults.value    = [];
}

// ─── Formatters ───
function formatDate(d) {
    if (!d) return '—';
    return new Date(d).toLocaleString('en-US', {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: 'numeric', minute: '2-digit', hour12: true,
    });
}

function formatTimeAgo(d) {
    const diff = Math.floor((Date.now() - new Date(d)) / 1000 / 60);
    if (diff < 1)    return 'Just now';
    if (diff < 60)   return `${diff}m ago`;
    if (diff < 1440) return `${Math.floor(diff / 60)}h ago`;
    return formatDate(d);
}

function formatBytes(b) {
    if (!b) return '';
    if (b < 1024)          return b + ' B';
    if (b < 1024 * 1024)   return (b / 1024).toFixed(1) + ' KB';
    return (b / (1024 * 1024)).toFixed(1) + ' MB';
}

function statusPillClass(s) {
    return {
        new:                  'bg-blue-500/20 text-blue-300',
        in_progress:          'bg-primary/20 text-primary',
        waiting_on_customer:  'bg-amber-500/20 text-amber-300',
        resolved:             'bg-emerald-500/20 text-emerald-300',
        closed:               'bg-gray-500/20 text-gray-400',
    }[s] ?? 'bg-surface-light text-text-dim';
}

function priorityPillClass(p) {
    return {
        low:      'bg-blue-500/20 text-blue-300',
        medium:   'bg-primary/20 text-primary',
        high:     'bg-amber-500/20 text-amber-300',
        critical: 'bg-red-500/20 text-red-300',
    }[p] ?? 'bg-surface-light text-text-dim';
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active { transition: opacity 0.2s ease; }
.modal-enter-from,
.modal-leave-to { opacity: 0; }
</style>
