<template>
    <div v-if="loading" class="w-full py-12">
        <div class="h-12 w-64 bg-surface-light animate-pulse rounded-xl mb-12"></div>
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
            <div class="lg:col-span-3 h-96 glass-card animate-pulse rounded-xl"></div>
            <div class="h-96 glass-card animate-pulse rounded-xl"></div>
        </div>
    </div>

    <div v-else-if="ticket" class="w-full space-y-12 pb-24">
        <!-- Breadcrumbs & Quick Actions -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <router-link to="/agent/queue" class="p-3 bg-surface-light rounded-xl text-text-dim hover:text-primary transition-all shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </router-link>
                <div>
                    <div class="flex items-center gap-3 mb-0.5">
                        <span class="text-[10px] font-black text-primary uppercase tracking-[0.2em]">{{ ticket.ticket_number }}</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-glass-border"></span>
                        <span class="text-[10px] font-bold text-text-dim uppercase tracking-widest">{{ ticket.category?.name }}</span>
                    </div>
                    <h1 class="text-2xl font-black text-text-main line-clamp-1 truncate max-w-xl">{{ ticket.title }}</h1>
                </div>
            </div>

            <div class="flex gap-3">
                <button v-if="!isAssignedToMe" @click="handleAssignToMe" class="px-6 py-3 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:shadow-xl hover:shadow-primary/20 hover-lift transition-all">Claim Ticket</button>
                <div class="h-12 w-[1px] bg-glass-border mx-2"></div>
                <button class="px-6 py-3 glass-card rounded-xl text-[10px] font-black uppercase tracking-widest text-text-dim hover:bg-white/5 transition-all">Relink</button>
                <button class="px-6 py-3 bg-red-500/10 text-red-500 border border-red-500/20 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all">Spam</button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
            <!-- Main Content: Thread and Reply -->
            <div class="lg:col-span-3 space-y-12">
                <!-- Request Info Card -->
                <div class="glass-card p-10 rounded-xl space-y-8 bg-linear-to-br from-white/[0.02] to-transparent">
                    <div class="flex items-start justify-between">
                        <div class="flex gap-6">
                            <div class="w-16 h-16 rounded-lg bg-surface-light flex items-center justify-center text-3xl shadow-sm border border-glass-border">
                                {{ ticket.requester?.name.charAt(0) }}
                            </div>
                            <div>
                                <p class="text-xl font-black text-text-main">{{ ticket.requester?.name }}</p>
                                <p class="text-sm text-text-dim font-medium">{{ ticket.requester?.email }}</p>
                                <p class="mt-2 text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-primary"></span>
                                    Verified Requester
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black uppercase tracking-widest text-text-dim mb-1">Source</p>
                            <span class="px-3 py-1 bg-surface-light rounded-lg text-[10px] font-black uppercase tracking-widest text-text-main border border-glass-border">{{ ticket.source }}</span>
                        </div>
                    </div>

                    <div class="p-8 bg-surface-light/30 rounded-xl border border-glass-border">
                        <p class="text-text-main leading-relaxed whitespace-pre-wrap font-medium">{{ ticket.description }}</p>
                    </div>

                    <div v-if="ticket.attachments?.length" class="flex flex-wrap gap-4">
                        <div v-for="file in ticket.attachments" :key="file.id" class="px-4 py-3 bg-surface-light rounded-xl border border-glass-border flex items-center gap-3 group cursor-pointer hover:border-primary/40 transition-all">
                            <span class="text-lg opacity-50 group-hover:opacity-100 transition-opacity">📄</span>
                            <span class="text-[10px] font-bold text-text-dim group-hover:text-primary transition-colors">{{ file.file_name }}</span>
                        </div>
                    </div>
                </div>

                <!-- Discussion Thread -->
                <div class="space-y-8 pl-12 border-l-2 border-glass-border relative">
                    <div v-for="comment in ticket.comments" :key="comment.id" :class="comment.is_internal ? 'internal-note' : ''" class="relative">
                        <!-- Connector Dot -->
                        <div class="absolute -left-14 top-2 w-4 h-4 rounded-full border-4 border-surface shadow-sm" :class="comment.is_internal ? 'bg-amber-500' : 'bg-primary'"></div>
                        
                        <div class="glass-card p-8 rounded-xl space-y-4" :class="comment.is_internal ? 'bg-amber-500/5 border-amber-500/20' : ''">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="font-black text-text-main text-sm">{{ comment.user.name }}</span>
                                    <span v-if="comment.is_internal" class="px-2 py-0.5 bg-amber-500/20 text-amber-500 text-[8px] font-black uppercase tracking-[0.2em] rounded-md">Internal Note</span>
                                    <span v-else-if="comment.user.role !== 'user'" class="px-2 py-0.5 bg-primary/20 text-primary text-[8px] font-black uppercase tracking-[0.2em] rounded-md">Agent</span>
                                </div>
                                <span class="text-[10px] text-text-dim font-bold uppercase tracking-widest">{{ formatTimeAgo(comment.created_at) }}</span>
                            </div>
                            <p class="text-sm text-text-dim leading-relaxed font-semibold">{{ comment.body }}</p>
                        </div>
                    </div>
                </div>

                <!-- Reply Area -->
                <div class="glass-card p-2 rounded-xl shadow-2xl overflow-hidden">
                    <div class="flex border-b border-glass-border">
                        <button 
                            @click="isInternal = false"
                            class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest transition-all"
                            :class="!isInternal ? 'bg-primary text-white' : 'text-text-dim hover:bg-white/5'"
                        >
                            Public Reply
                        </button>
                        <button 
                            @click="isInternal = true"
                            class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest transition-all"
                            :class="isInternal ? 'bg-amber-500 text-white' : 'text-text-dim hover:bg-white/5'"
                        >
                            Internal Note
                        </button>
                    </div>

                    <div class="p-8 space-y-6">
                        <textarea 
                            v-model="commentBody"
                            :placeholder="isInternal ? 'Add a private note only agents can see...' : 'Compose your public reply to the customer...'"
                            class="w-full bg-surface-light border-none rounded-lg p-6 text-text-main placeholder:text-text-dim/50 focus:ring-2 focus:ring-primary/40 min-h-[160px] resize-none font-medium leading-relaxed"
                        ></textarea>

                        <div class="flex items-center justify-between">
                            <div class="flex gap-4">
                                <button class="p-3 bg-surface-light rounded-xl text-text-dim hover:text-primary transition-all border border-glass-border">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                </button>
                                <button class="px-6 py-3 bg-surface-light rounded-xl text-[10px] font-black uppercase tracking-widest text-text-dim hover:text-primary transition-all border border-glass-border">Use Canned Response</button>
                            </div>
                            <button 
                                @click="handleSendComment"
                                :disabled="!commentBody.trim() || sending"
                                class="px-10 py-4 font-black transition-all rounded-lg text-[10px] font-black uppercase tracking-[0.2em] shadow-xl hover-lift"
                                :class="isInternal ? 'bg-amber-600 text-white shadow-amber-600/20' : 'bg-primary text-white shadow-primary/20'"
                            >
                                {{ sending ? 'Syncing...' : 'Dispatch Message' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Attributes & Controls -->
            <div class="space-y-10">
                <div class="glass-card p-10 rounded-xl space-y-10">
                    <!-- Status -->
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-4">Ticket Status</p>
                        <select 
                            v-model="localStatus" 
                            @change="handleAttributeUpdate('status')"
                            class="w-full bg-surface-light border border-glass-border rounded-xl p-4 text-sm font-bold text-text-main focus:ring-2 focus:ring-primary/40 outline-none"
                        >
                            <option value="new">New / Unprocessed</option>
                            <option value="in_progress">Investigation</option>
                            <option value="waiting_on_customer">Awaiting Response</option>
                            <option value="resolved">Resolved / Fixed</option>
                            <option value="closed">Closed / Archived</option>
                        </select>
                    </div>

                    <!-- Priority -->
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-4">Urgency Level</p>
                        <div class="grid grid-cols-2 gap-3">
                            <button 
                                v-for="p in ['low', 'medium', 'high', 'critical']" :key="p"
                                @click="localPriority = p; handleAttributeUpdate('priority')"
                                class="py-2.5 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all border"
                                :class="localPriority === p ? 'bg-primary text-white border-primary shadow-lg shadow-primary/20' : 'bg-surface-light text-text-dim border-glass-border hover:bg-white/5'"
                            >
                                {{ p }}
                            </button>
                        </div>
                    </div>

                    <!-- Assignee -->
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-4">Assignment</p>
                        <div class="p-4 bg-surface-light rounded-lg border border-glass-border">
                            <div v-if="ticket.agent" class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center font-black text-primary border border-primary/20">
                                    {{ ticket.agent.name.charAt(0) }}
                                </div>
                                <div class="overflow-hidden">
                                    <p class="font-bold text-text-main truncate text-sm">{{ ticket.agent.name }}</p>
                                    <button @click="handleAssignToMe" v-if="!isAssignedToMe" class="text-[9px] font-black uppercase text-primary tracking-widest hover:underline">Reassign to Me</button>
                                </div>
                            </div>
                            <div v-else class="text-center py-2">
                                <button @click="handleAssignToMe" class="text-[9px] font-black uppercase text-primary tracking-widest hover:underline">Claim this request</button>
                            </div>
                        </div>
                    </div>

                    <!-- Meta Data -->
                    <div class="pt-8 border-t border-glass-border space-y-6">
                        <div>
                            <p class="text-[9px] font-black uppercase tracking-widest text-text-dim mb-2 text-center">Efficiency KPI</p>
                            <div class="p-4 rounded-xl bg-emerald-500/5 border border-emerald-500/10 text-center">
                                <p class="text-xs font-bold text-emerald-500">SLA Healthy</p>
                                <p class="text-[9px] text-text-dim uppercase tracking-widest mt-1">Due in 4h 20m</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Asset Context -->
                <div v-if="ticket.requester_id">
                    <AssetSidebar :user-id="ticket.requester_id" />
                </div>

                <!-- Side Actions -->
                <div class="glass-card p-10 rounded-xl bg-linear-to-br from-primary/10 to-secondary/10 border-primary/20">
                    <h4 class="font-black text-lg text-text-main mb-2">Internal Tools</h4>
                    <p class="text-[11px] text-text-dim mb-6 leading-relaxed">Need help from Level 2 support or want to link this to a master incident?</p>
                    <button class="w-full py-4 bg-white text-primary font-black text-center rounded-lg text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-primary/20 hover-lift">Escalate to Dev</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import { useTicketStore } from '@/stores/tickets';
import { useAuthStore } from '@/stores/auth';
import axios from '@/plugins/axios';
import { useToast } from 'vue-toastification';
import AssetSidebar from '@/components/Assets/AssetSidebar.vue';

const route = useRoute();
const ticketStore = useTicketStore();
const authStore = useAuthStore();
const toast = useToast();

const ticket = ref(null);
const loading = ref(true);
const sending = ref(false);
const commentBody = ref('');
const isInternal = ref(false);

const localStatus = ref('');
const localPriority = ref('');

const isAssignedToMe = computed(() => {
    return ticket.value?.agent_id === authStore.user?.id;
});

const setupEcho = () => {
    if (window.Echo && ticket.value) {
        window.Echo.private(`ticket.${ticket.value.id}`)
            .listen('TicketCommentCreated', (e) => {
                if (!ticket.value.comments.find(c => c.id === e.comment.id)) {
                    ticket.value.comments.push(e.comment);
                    if (e.comment.user_id !== authStore.user?.id) {
                        toast.info('New comment received');
                    }
                }
            });
    }
};

const loadTicket = async () => {
    loading.value = true;
    try {
        await ticketStore.fetchTicketByNumber(route.params.ticketNumber);
        ticket.value = ticketStore.currentTicket;
        localStatus.value = ticket.value.status;
        localPriority.value = ticket.value.priority;
        setupEcho();
    } finally {
        loading.value = false;
    }
};

onMounted(loadTicket);

onUnmounted(() => {
    if (window.Echo && ticket.value) {
        window.Echo.leave(`ticket.${ticket.value.id}`);
    }
});

const handleSendComment = async () => {
    if (!commentBody.value.trim()) return;
    
    sending.value = true;
    try {
        const response = await axios.post(`/agent/tickets/${ticket.value.id}/comments`, {
            body: commentBody.value,
            is_internal: isInternal.value
        });
        
        ticket.value.comments.push(response.data.data);
        commentBody.value = '';
        toast.success(isInternal.value ? 'Note saved locally' : 'Reply dispatched to customer');
    } catch (err) {
        toast.error('Failed to sync message');
    } finally {
        sending.value = false;
    }
};

const handleAttributeUpdate = async (type) => {
    try {
        const payload = {};
        if (type === 'status') payload.status = localStatus.value;
        if (type === 'priority') payload.priority = localPriority.value;

        await axios.patch(`/agent/tickets/${ticket.value.id}`, payload);
        toast.success(`Ticket ${type} updated`);
    } catch (err) {
        toast.error('Sync failed');
        // Revert local state
        if (type === 'status') localStatus.value = ticket.value.status;
        if (type === 'priority') localPriority.value = ticket.value.priority;
    }
};

const handleAssignToMe = async () => {
    try {
        await axios.patch(`/agent/tickets/${ticket.value.id}`, {
            agent_id: authStore.user.id
        });
        ticket.value.agent = authStore.user;
        ticket.value.agent_id = authStore.user.id;
        toast.success('Ticket claimed successfully');
    } catch (err) {
        toast.error('Failed to claim ticket');
    }
};

const formatTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / 1000 / 60);

    if (diffInMinutes < 1) return 'Just now';
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
    return date.toLocaleString();
};
</script>

<style scoped>
</style>
