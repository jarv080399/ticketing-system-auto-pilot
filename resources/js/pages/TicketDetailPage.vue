<template>
    <div v-if="loading" class="w-full space-y-12 py-12">
        <div class="h-12 w-64 bg-surface-light animate-pulse rounded-xl"></div>
        <div class="h-64 glass-card animate-pulse rounded-xl"></div>
    </div>

    <div v-else-if="ticket" class="w-full space-y-12 pb-24">
        <!-- Header Actions -->
        <div class="flex items-center justify-between">
            <router-link to="/tickets" class="flex items-center gap-2 text-text-dim hover:text-primary transition-colors group">
                <div class="p-2 rounded-lg group-hover:bg-primary/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </div>
                <span class="font-bold text-sm tracking-tight">Back to my tickets</span>
            </router-link>

            <div class="flex gap-3">
                <button class="px-6 py-2.5 glass-card rounded-xl text-xs font-black uppercase tracking-widest text-text-dim hover:bg-white/5 transition-all">Download PDF</button>
            </div>
        </div>

        <!-- Progress Tracker -->
        <div class="glass-card p-10 rounded-xl">
            <TicketStatusTracker :status="ticket.status" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-12">
                <!-- Ticket Content -->
                <div class="glass-card p-12 rounded-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8">
                        <span class="px-4 py-2 bg-surface-light rounded-xl text-[10px] font-black uppercase tracking-[0.2em] text-text-dim">
                            {{ ticket.source }}
                        </span>
                    </div>

                    <div class="space-y-8">
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-black uppercase tracking-widest text-primary">{{ ticket.ticket_number }}</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-glass-border"></span>
                                <span class="text-xs text-text-dim font-medium">{{ formatDate(ticket.created_at) }}</span>
                            </div>
                            <h1 class="text-4xl font-black text-text-main tracking-tight leading-tight">{{ ticket.title }}</h1>
                        </div>

                        <div class="prose prose-invert max-w-none">
                            <p class="text-lg text-text-dim leading-relaxed whitespace-pre-wrap">{{ ticket.description }}</p>
                        </div>

                        <!-- Attachments -->
                        <div v-if="ticket.attachments?.length" class="pt-8 border-t border-glass-border">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim">Attachments <span class="text-text-dim/50">({{ ticket.attachments.length }})</span></h4>
                            </div>

                            <!-- Image thumbnails row -->
                            <div v-if="imageAttachments.length" class="mb-4">
                                <p class="text-[9px] font-black uppercase tracking-widest text-text-dim/50 mb-2">Images</p>
                                <div class="flex flex-wrap gap-3">
                                    <div
                                        v-for="(file, i) in imageAttachments"
                                        :key="file.id"
                                        class="relative w-20 h-20 rounded-lg overflow-hidden border border-glass-border cursor-zoom-in hover:border-primary/50 hover:scale-105 transition-all group"
                                        @click="openLightbox(i)"
                                    >
                                        <img :src="file.url" :alt="file.file_name" class="w-full h-full object-cover" loading="lazy" />
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <span class="text-white text-lg">🔍</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PDF / doc list -->
                            <div v-if="pdfAttachments.length">
                                <p class="text-[9px] font-black uppercase tracking-widest text-text-dim/50 mb-2">Documents</p>
                                <div class="flex flex-col gap-2">
                                    <a
                                        v-for="file in pdfAttachments"
                                        :key="file.id"
                                        :href="file.url"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="flex items-center gap-4 p-3 rounded-lg bg-surface-light border border-glass-border hover:border-primary/40 hover:bg-white/5 transition-all group"
                                    >
                                        <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center text-xl flex-shrink-0">
                                            📄
                                        </div>
                                        <div class="flex-1 overflow-hidden">
                                            <p class="text-sm font-bold text-text-main truncate group-hover:text-primary transition-colors">{{ file.file_name }}</p>
                                            <p class="text-[10px] text-text-dim uppercase tracking-widest">{{ formatBytes(file.file_size) }} · PDF</p>
                                        </div>
                                        <span class="text-text-dim group-hover:text-primary transition-colors text-sm flex-shrink-0">⬇</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conversation Section -->
                <div class="space-y-8">
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] text-text-dim ml-4">Activity Log</h3>
                    
                    <div v-if="publicComments.length" class="space-y-8 pl-12 border-l-2 border-glass-border relative">
                        <div v-for="comment in publicComments" :key="comment.id" class="relative">
                            <!-- Connector Dot -->
                            <div class="absolute -left-14 top-2 w-4 h-4 rounded-full border-4 border-surface shadow-sm bg-primary"></div>
                            
                            <div class="glass-card p-8 rounded-xl space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="font-black text-text-main text-sm">{{ comment.user?.name || 'User' }}</span>
                                        <span v-if="comment.user?.role !== 'user'" class="px-2 py-0.5 bg-primary/20 text-primary text-[8px] font-black uppercase tracking-[0.2em] rounded-md">Agent</span>
                                    </div>
                                    <span class="text-[10px] text-text-dim font-bold uppercase tracking-widest">{{ formatTimeAgo(comment.created_at) }}</span>
                                </div>
                                <p class="text-sm text-text-dim leading-relaxed font-semibold whitespace-pre-wrap">{{ comment.body }}</p>
                            </div>
                        </div>
                    </div>

                    <div v-else class="glass-card p-12 rounded-xl text-center space-y-4">
                        <div class="w-16 h-16 bg-surface-light rounded-lg flex items-center justify-center mx-auto text-3xl opacity-50">💬</div>
                        <p class="text-text-dim font-medium">No replies yet. Our agents will comment here once they review your request.</p>
                    </div>

                    <!-- Reply Area -->
                    <div class="glass-card p-6 rounded-xl shadow-2xl mt-8">
                        <div class="space-y-4">
                            <textarea 
                                v-model="commentBody"
                                placeholder="Write a reply..."
                                class="w-full bg-surface-light border border-glass-border rounded-lg p-4 text-sm text-text-main focus:ring-2 focus:ring-primary/40 min-h-[120px] resize-none"
                            ></textarea>
                            <div class="text-right">
                                <button 
                                    @click="submitComment"
                                    :disabled="!commentBody.trim() || sending"
                                    class="px-8 py-3 bg-primary text-white rounded-lg text-xs font-black uppercase tracking-widest hover-lift disabled:opacity-50"
                                >
                                    {{ sending ? 'Sending...' : 'Reply' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-10">
                <div class="glass-card p-8 rounded-xl space-y-8">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-4">Category</p>
                        <div class="flex items-center gap-4 p-4 rounded-lg bg-surface-light border border-glass-border">
                            <span class="text-2xl">{{ ticket.category?.icon }}</span>
                            <span class="font-bold text-text-main">{{ ticket.category?.name }}</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-4">Ticket Status</p>
                        <div class="flex items-center gap-3">
                            <div :class="getStatusIconClass(ticket.status)" class="w-3 h-3 rounded-full animate-pulse shadow-sm"></div>
                            <span class="font-bold text-text-main capitalize">{{ ticket.status.replace('_', ' ') }}</span>
                        </div>
                    </div>

                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-4">Priority</p>
                        <div class="px-4 py-2 rounded-xl bg-linear-to-r inline-block" :class="getPriorityClass(ticket.priority)">
                            <span class="text-[10px] font-black uppercase tracking-widest text-white">{{ ticket.priority }}</span>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-glass-border">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-text-dim mb-4">Assigned Expert</p>
                        <div v-if="ticket.agent" class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-surface-light flex items-center justify-center font-black text-primary border border-primary/20">
                                {{ ticket.agent.name.charAt(0) }}
                            </div>
                            <div>
                                <p class="font-bold text-text-main line-clamp-1">{{ ticket.agent.name }}</p>
                                <p class="text-[10px] text-text-dim uppercase tracking-widest">IT Specialist</p>
                            </div>
                        </div>
                        <div v-else class="p-4 bg-white/5 rounded-lg text-center">
                            <p class="text-xs font-bold text-text-dim italic">Waiting for assignment...</p>
                        </div>
                    </div>
                </div>

                <!-- Self Service Card -->
                <div class="glass-card p-8 rounded-xl bg-linear-to-br from-primary/10 to-secondary/10 border-primary/20">
                    <h4 class="font-black text-lg text-text-main mb-2">Need an update?</h4>
                    <p class="text-sm text-text-dim mb-6 leading-relaxed">Most hardware tickets are resolved within 24 hours. Check our Knowledge Base for quick fixes.</p>
                    <router-link to="/kb" class="block w-full py-4 bg-white text-primary font-black text-center rounded-lg text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover-lift">Knowledge Base</router-link>
                </div>
            </div>
        </div>
    </div>

    <div v-else class="py-24 text-center space-y-6">
        <h2 class="text-4xl font-black text-text-main">404</h2>
        <p class="text-text-dim">The ticket you are looking for does not exist or you lack permission to view it.</p>
        <router-link to="/tickets" class="px-10 py-4 bg-primary text-white rounded-lg inline-block">My Tickets</router-link>
    </div>

    <!-- ─── Lightbox ─── -->
    <Transition name="lightbox">
        <div
            v-if="lightbox.open"
            class="fixed inset-0 z-50 bg-black/95 backdrop-blur-sm flex items-center justify-center"
            @click.self="closeLightbox"
            @keydown.esc="closeLightbox"
            tabindex="0"
        >
            <!-- Close -->
            <button
                @click="closeLightbox"
                class="absolute top-5 right-5 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-lg transition-all z-10"
            >✕</button>

            <!-- Prev -->
            <button
                v-if="imageAttachments.length > 1"
                @click="lightbox.index = (lightbox.index - 1 + imageAttachments.length) % imageAttachments.length"
                class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white text-xl flex items-center justify-center transition-all z-10"
            >‹</button>

            <!-- Image -->
            <div class="max-w-5xl max-h-[85vh] w-full px-16 flex flex-col items-center gap-4">
                <img
                    :src="imageAttachments[lightbox.index]?.url"
                    :alt="imageAttachments[lightbox.index]?.file_name"
                    class="max-w-full max-h-[75vh] object-contain rounded-xl shadow-2xl"
                />
                <div class="text-center">
                    <p class="text-white font-bold text-sm">{{ imageAttachments[lightbox.index]?.file_name }}</p>
                    <p class="text-white/50 text-xs mt-0.5">{{ formatBytes(imageAttachments[lightbox.index]?.file_size) }} · {{ lightbox.index + 1 }} / {{ imageAttachments.length }}</p>
                </div>
                <a
                    :href="imageAttachments[lightbox.index]?.url"
                    :download="imageAttachments[lightbox.index]?.file_name"
                    class="px-5 py-2 text-xs font-black uppercase tracking-widest bg-white/10 hover:bg-white/20 text-white rounded-lg transition-all"
                >⬇ Download</a>
            </div>

            <!-- Next -->
            <button
                v-if="imageAttachments.length > 1"
                @click="lightbox.index = (lightbox.index + 1) % imageAttachments.length"
                class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/10 hover:bg-white/20 text-white text-xl flex items-center justify-center transition-all z-10"
            >›</button>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted, computed, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import { useTicketStore } from '@/stores/tickets';
import { useAuthStore } from '@/stores/auth';
import axios from '@/plugins/axios';
import { useToast } from 'vue-toastification';
import TicketStatusTracker from '@/components/TicketStatusTracker.vue';

const route = useRoute();
const ticketStore = useTicketStore();
const authStore = useAuthStore();
const toast = useToast();

const ticket = ref(null);
const loading = ref(true);
const sending = ref(false);
const commentBody = ref('');
const lightbox = ref({ open: false, index: 0 });

const imageAttachments = computed(() => ticket.value?.attachments?.filter(a => a.is_image) ?? []);
const pdfAttachments   = computed(() => ticket.value?.attachments?.filter(a => !a.is_image) ?? []);

const formatBytes = (bytes) => {
    if (!bytes) return '';
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
};

const openLightbox = (index) => {
    lightbox.value = { open: true, index };
    document.addEventListener('keydown', handleLightboxKey);
};
const closeLightbox = () => {
    lightbox.value.open = false;
    document.removeEventListener('keydown', handleLightboxKey);
};
const handleLightboxKey = (e) => {
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowRight') lightbox.value.index = (lightbox.value.index + 1) % imageAttachments.value.length;
    if (e.key === 'ArrowLeft')  lightbox.value.index = (lightbox.value.index - 1 + imageAttachments.value.length) % imageAttachments.value.length;
};

const setupEcho = () => {
    if (window.Echo && ticket.value) {
        window.Echo.private(`ticket.${ticket.value.id}`)
            .listen('TicketCommentCreated', (e) => {
                if (!ticket.value.comments.find(c => c.id === e.comment.id)) {
                    ticket.value.comments.push(e.comment);
                    if (e.comment.user_id !== authStore.user?.id) {
                        toast.info('New reply received');
                    }
                }
            });
    }
};

const submitComment = async () => {
    if (!commentBody.value.trim()) return;
    
    sending.value = true;
    try {
        const response = await axios.post(`/tickets/${ticket.value.id}/comments`, {
            body: commentBody.value,
        });
        
        ticket.value.comments.push(response.data.data);
        commentBody.value = '';
    } catch (err) {
        toast.error('Failed to send reply');
    } finally {
        sending.value = false;
    }
};

onMounted(async () => {
    loading.value = true;
    try {
        await ticketStore.fetchTicketByNumber(route.params.ticketNumber);
        ticket.value = ticketStore.currentTicket;
        setupEcho();
    } finally {
        loading.value = false;
    }
});

onUnmounted(() => {
    if (window.Echo && ticket.value) {
        window.Echo.leave(`ticket.${ticket.value.id}`);
    }
});

const publicComments = computed(() => {
    return ticket.value?.comments?.filter(c => !c.is_internal) || [];
});

const formatTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now - date) / 1000 / 60);

    if (diffInMinutes < 1) return 'Just now';
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
    return date.toLocaleString();
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString('en-US', { 
        month: 'long', 
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });
};

const getStatusIconClass = (status) => {
    switch (status) {
        case 'new': return 'bg-blue-400 shadow-blue-400';
        case 'in_progress': return 'bg-primary shadow-primary';
        case 'waiting_on_customer': return 'bg-amber-500 shadow-amber-500';
        case 'resolved': return 'bg-emerald-500 shadow-emerald-500';
        default: return 'bg-text-dim shadow-text-dim';
    }
};

const getPriorityClass = (priority) => {
    switch (priority) {
        case 'low': return 'from-blue-500 to-indigo-500 shadow-lg shadow-blue-500/20';
        case 'medium': return 'from-primary to-primary-dark shadow-lg shadow-primary/20';
        case 'high': return 'from-amber-500 to-orange-500 shadow-lg shadow-amber-500/20';
        case 'critical': return 'from-red-600 to-rose-600 shadow-lg shadow-red-500/30';
        default: return 'from-gray-500 to-slate-500';
    }
};
</script>

<style scoped>
.lightbox-enter-active,
.lightbox-leave-active {
    transition: opacity 0.2s ease;
}
.lightbox-enter-from,
.lightbox-leave-to {
    opacity: 0;
}
.cursor-zoom-in { cursor: zoom-in; }
</style>
