<template>
    <div class="space-y-8 pb-10">
        <!-- ─── Page Header ─── -->
        <div class="flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold text-text-main tracking-tight">Archived Tickets</h1>
                <p class="mt-1 text-sm text-text-dim">
                    View and restore tickets that have been hidden from active queues.
                </p>
            </div>
        </div>

        <!-- ─── Archive List ─── -->
        <div class="bg-surface border border-glass-border rounded-xl overflow-hidden">
            <div class="p-6 border-b border-glass-border">
                <h2 class="text-[11px] font-semibold tracking-widest text-text-dim uppercase">Archival Vault</h2>
            </div>
            
            <div v-if="loading" class="p-12 text-center text-text-dim">
                <span class="animate-pulse">Loading archives...</span>
            </div>
            
            <table v-else-if="tickets.length > 0" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-light border-b border-glass-border">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Ticket ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Subject</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Requester</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim">Archived On</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-text-dim text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-glass-border text-sm">
                    <tr v-for="ticket in tickets" :key="ticket.id" class="hover:bg-surface-light transition-colors">
                        <td class="px-6 py-4 font-mono text-xs text-text-dim">
                            <router-link :to="`/agent/tickets/${ticket.ticket_number}`" class="hover:text-primary transition-colors">
                                {{ ticket.ticket_number }}
                            </router-link>
                        </td>
                        <td class="px-6 py-4 font-medium text-text-main">{{ ticket.title }}</td>
                        <td class="px-6 py-4 text-text-dim">{{ ticket.requester?.name || 'Unknown' }}</td>
                        <td class="px-6 py-4 text-text-dim">{{ new Date(ticket.updated_at).toLocaleDateString() }}</td>
                        <td class="px-6 py-4 text-right">
                            <button
                                @click="unarchiveTicket(ticket.ticket_number)"
                                class="bg-primary/10 hover:bg-primary/20 text-primary text-xs font-bold px-3 py-1.5 rounded-lg transition-colors border border-primary/20"
                            >
                                Unarchive
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-else class="p-16 text-center">
                <div class="inline-flex w-16 h-16 rounded-full bg-surface-light items-center justify-center mb-4 border border-glass-border">
                    <span class="text-2xl">🗄️</span>
                </div>
                <h3 class="text-lg font-bold text-text-main mb-1">Vault is Empty</h3>
                <p class="text-sm text-text-dim">No tickets have been archived.</p>
            </div>
            
            <!-- Pagination Controls -->
            <div v-if="meta && meta.last_page > 1" class="px-6 py-4 border-t border-glass-border bg-surface flex items-center justify-between">
                <span class="text-sm text-text-dim">
                    Showing page {{ meta.current_page }} of {{ meta.last_page }}
                </span>
                <div class="flex gap-2">
                    <button 
                        @click="fetchTickets(meta.current_page - 1)" 
                        :disabled="meta.current_page <= 1"
                        class="px-3 py-1.5 rounded-lg bg-surface-light hover:bg-surface border border-glass-border text-text-main text-sm transition-colors disabled:opacity-50"
                    >
                        Prev
                    </button>
                    <button 
                        @click="fetchTickets(meta.current_page + 1)" 
                        :disabled="meta.current_page >= meta.last_page"
                        class="px-3 py-1.5 rounded-lg bg-surface-light hover:bg-surface border border-glass-border text-text-main text-sm transition-colors disabled:opacity-50"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '@/plugins/axios';
import { useToast } from 'vue-toastification';

const toast = useToast();
const tickets = ref([]);
const meta = ref(null);
const loading = ref(true);

const fetchTickets = async (page = 1) => {
    loading.value = true;
    try {
        const response = await axios.get(`/admin/tickets/archived?page=${page}`);
        tickets.value = response.data.data;
        meta.value = response.data.meta;
    } catch (error) {
        toast.error('Failed to load archived tickets.');
    } finally {
        loading.value = false;
    }
};

const unarchiveTicket = async (ticketNumber) => {
    try {
        await axios.post(`/admin/tickets/${ticketNumber}/unarchive`);
        toast.success(`Ticket ${ticketNumber} restored.`);
        fetchTickets(meta.value?.current_page || 1);
    } catch (error) {
        toast.error('Failed to unarchive ticket.');
    }
};

onMounted(() => {
    fetchTickets();
});
</script>
