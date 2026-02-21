<template>
    <div class="space-y-8 animate-fade-in pb-12">

        <!-- ─── Page Header ─── -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black text-text-main tracking-tight">
                    Canned <span class="text-primary">Responses</span>
                </h1>
                <p class="text-text-dim font-medium mt-1">Pre-saved reply templates for faster, consistent ticket communication.</p>
            </div>
            <div class="flex gap-4">
                <button
                    @click="openCreateModal"
                    class="px-8 py-3 bg-primary hover:bg-primary-dark text-white font-black rounded-xl shadow-xl shadow-primary/20 hover-lift active:scale-95 transition-all flex items-center gap-2"
                >
                    <span class="text-xl">+</span> New Response
                </button>
            </div>
        </div>

        <!-- ─── Filters ─── -->
        <div class="glass-card p-6 rounded-2xl flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-[300px] relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-text-dim">🔍</span>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search title or body..."
                    class="w-full bg-surface-light border border-glass-border rounded-xl py-3 pl-12 pr-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all"
                />
            </div>
            <select
                v-model="filterCategory"
                class="bg-surface-light border border-glass-border rounded-xl px-4 py-3 text-sm text-text-main focus:outline-none"
            >
                <option value="">All Categories</option>
                <option v-for="cat in uniqueCategories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
            <select
                v-model="filterVisibility"
                class="bg-surface-light border border-glass-border rounded-xl px-4 py-3 text-sm text-text-main focus:outline-none"
            >
                <option value="">All Visibility</option>
                <option value="shared">🟢 Shared</option>
                <option value="private">🔒 Private</option>
            </select>
        </div>

        <!-- ─── Loading ─── -->
        <div v-if="loading" class="flex items-center justify-center py-24">
            <div class="flex flex-col items-center gap-4">
                <div class="animate-spin rounded-full h-10 w-10 border-b-4 border-primary"></div>
                <p class="text-sm font-bold text-text-dim animate-pulse uppercase tracking-widest">Loading Responses...</p>
            </div>
        </div>

        <template v-else>
            <!-- ─── Table ─── -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-light/50 border-b border-glass-border">
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Response</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Category</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Visibility</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim">Created By</th>
                            <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-text-dim text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-glass-border">
                        <tr
                            v-for="response in filteredResponses"
                            :key="response.id"
                            class="hover:bg-white/5 transition-colors group cursor-default"
                        >
                            <!-- Response Info -->
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-surface-light flex items-center justify-center text-xl flex-shrink-0">
                                        💬
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-text-main tracking-tight">{{ response.title }}</p>
                                        <p class="text-xs text-text-dim font-mono mt-0.5 truncate max-w-sm">{{ response.body }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-6 py-5">
                                <span
                                    v-if="response.category"
                                    class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border bg-primary/10 text-primary border-primary/20"
                                >
                                    {{ response.category }}
                                </span>
                                <span v-else class="text-xs text-text-dim italic">—</span>
                            </td>

                            <!-- Visibility -->
                            <td class="px-6 py-5">
                                <span
                                    :class="response.is_shared
                                        ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20'
                                        : 'bg-gray-500/10 text-gray-400 border-gray-500/20'"
                                    class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border"
                                >
                                    {{ response.is_shared ? '🟢 Shared' : '🔒 Private' }}
                                </span>
                            </td>

                            <!-- Creator -->
                            <td class="px-6 py-5">
                                <div v-if="response.creator" class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-[10px] font-bold text-primary">
                                        {{ response.creator.name.charAt(0) }}
                                    </div>
                                    <span class="text-sm font-bold text-text-main">{{ response.creator.name }}</span>
                                </div>
                                <span v-else class="text-xs text-text-dim italic">You</span>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button
                                        @click.stop="copyToClipboard(response.body)"
                                        title="Copy body"
                                        class="p-2 text-text-dim hover:text-primary transition-colors"
                                    >📋</button>
                                    <button
                                        @click.stop="openEditModal(response)"
                                        title="Edit"
                                        class="p-2 text-text-dim hover:text-primary transition-colors"
                                    >✏️</button>
                                    <button
                                        @click.stop="confirmDelete(response)"
                                        title="Delete"
                                        class="p-2 text-text-dim hover:text-red-400 transition-colors"
                                    >🗑️</button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Empty State inside table -->
                <div v-if="filteredResponses.length === 0" class="py-20 text-center">
                    <p class="text-text-dim italic">
                        {{ searchQuery || filterCategory || filterVisibility
                            ? 'No responses match your filters.'
                            : 'No canned responses yet. Create your first one.' }}
                    </p>
                </div>
            </div>

            <!-- ─── Footer Count ─── -->
            <div class="flex justify-between items-center px-2">
                <p class="text-xs text-text-dim font-medium">
                    Showing {{ filteredResponses.length }} of {{ responses.length }} responses
                </p>
            </div>
        </template>

        <!-- ─── Create / Edit Modal ─── -->
        <Transition name="modal">
            <div v-if="showModal" class="fixed inset-0 bg-background/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="glass-card rounded-2xl w-full max-w-lg p-8 shadow-[0_0_60px_rgba(0,0,0,0.4)]">

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-black text-text-main">
                                {{ editingResponse ? 'Edit Response' : 'New Canned Response' }}
                            </h2>
                            <p class="text-xs text-text-dim mt-0.5">
                                {{ editingResponse ? 'Update this template.' : 'Create a reusable reply for your team.' }}
                            </p>
                        </div>
                        <button @click="closeModal" class="p-2 text-text-dim hover:text-text-main transition-colors rounded-lg hover:bg-white/5">
                            ✕
                        </button>
                    </div>

                    <div class="space-y-5">
                        <!-- Title -->
                        <div>
                            <label class="block text-xs font-bold text-text-dim mb-2">
                                Title <span class="text-red-400">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="e.g. Password Reset Instructions"
                                class="w-full bg-surface-light border border-glass-border rounded-xl py-3 px-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all"
                            />
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-xs font-bold text-text-dim mb-2">Category</label>
                            <input
                                v-model="form.category"
                                type="text"
                                list="category-suggestions"
                                placeholder="e.g. Billing, Access, Hardware..."
                                class="w-full bg-surface-light border border-glass-border rounded-xl py-3 px-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all"
                            />
                            <datalist id="category-suggestions">
                                <option v-for="cat in uniqueCategories" :key="cat" :value="cat" />
                            </datalist>
                        </div>

                        <!-- Body -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-bold text-text-dim">
                                    Body <span class="text-red-400">*</span>
                                </label>
                                <span class="text-[10px] text-text-dim font-mono">{{ form.body.length }} chars</span>
                            </div>
                            <textarea
                                v-model="form.body"
                                rows="6"
                                placeholder="Type your template message here..."
                                class="w-full bg-surface-light border border-glass-border rounded-xl py-3 px-4 text-sm text-text-main focus:outline-none focus:border-primary/50 transition-all resize-y font-mono"
                            ></textarea>
                        </div>

                        <!-- Shared Toggle -->
                        <div
                            class="flex items-center gap-4 bg-surface-light border border-glass-border px-4 py-3 rounded-xl cursor-pointer hover:border-primary/30 transition-colors"
                            @click="form.is_shared = !form.is_shared"
                        >
                            <button
                                type="button"
                                :class="[
                                    'w-12 h-6 rounded-full p-1 transition-colors duration-300 flex-shrink-0 flex items-center',
                                    form.is_shared ? 'bg-emerald-500 justify-end' : 'bg-gray-700 justify-start'
                                ]"
                            >
                                <span class="w-4 h-4 bg-white rounded-full shadow-sm block"></span>
                            </button>
                            <div>
                                <span class="text-sm font-black tracking-wide" :class="form.is_shared ? 'text-emerald-400' : 'text-text-dim'">
                                    {{ form.is_shared ? 'Shared with Team' : 'Private to Me' }}
                                </span>
                                <p class="text-[10px] text-text-dim/70 mt-0.5">
                                    {{ form.is_shared ? 'All agents can see and use this.' : 'Only visible to you.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-glass-border">
                        <button
                            @click="closeModal"
                            class="px-5 py-2.5 text-sm text-text-dim font-bold hover:text-text-main hover:bg-white/5 rounded-xl transition-all"
                        >
                            Cancel
                        </button>
                        <button
                            @click="submitForm"
                            :disabled="!form.title.trim() || !form.body.trim() || saving"
                            class="px-8 py-2.5 bg-primary hover:bg-primary-dark text-white font-black rounded-xl shadow-xl shadow-primary/20 hover-lift active:scale-95 transition-all flex items-center gap-2 disabled:opacity-50 disabled:hover:scale-100 disabled:shadow-none"
                        >
                            <svg v-if="saving" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ saving ? 'Saving...' : (editingResponse ? 'Save Changes' : 'Create Response') }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- ─── Delete Confirm Modal ─── -->
        <Transition name="modal">
            <div v-if="showDeleteConfirm" class="fixed inset-0 bg-background/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                <div class="glass-card rounded-2xl w-full max-w-sm p-8 shadow-[0_0_60px_rgba(0,0,0,0.4)] border border-red-500/20">
                    <h2 class="text-xl font-black text-text-main mb-2">Delete Response?</h2>
                    <p class="text-sm text-text-dim mb-6">
                        You are about to permanently delete
                        <span class="text-text-main font-bold">"{{ deletingResponse?.title }}"</span>.
                        This cannot be undone.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button
                            @click="showDeleteConfirm = false; deletingResponse = null"
                            class="px-5 py-2.5 text-sm text-text-dim font-bold hover:text-text-main hover:bg-white/5 rounded-xl transition-all"
                        >
                            Cancel
                        </button>
                        <button
                            @click="executeDelete"
                            :disabled="deleting"
                            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-black rounded-xl transition-all flex items-center gap-2 disabled:opacity-50"
                        >
                            <svg v-if="deleting" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ deleting ? 'Deleting...' : 'Yes, Delete' }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>

    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from '@/plugins/axios';
import { useToast } from 'vue-toastification';

const toast = useToast();

// ─── State ───
const loading  = ref(true);
const saving   = ref(false);
const deleting = ref(false);
const responses = ref([]);

const showModal         = ref(false);
const showDeleteConfirm = ref(false);
const editingResponse   = ref(null);
const deletingResponse  = ref(null);

const searchQuery      = ref('');
const filterCategory   = ref('');
const filterVisibility = ref('');

const defaultForm = () => ({ title: '', body: '', category: '', is_shared: false });
const form = ref(defaultForm());

// ─── Computed ───
const uniqueCategories = computed(() => {
    const cats = responses.value.map(r => r.category).filter(Boolean);
    return [...new Set(cats)].sort();
});

const filteredResponses = computed(() => {
    return responses.value.filter(r => {
        const q = searchQuery.value.toLowerCase();
        const matchesSearch     = !q || r.title.toLowerCase().includes(q) || r.body.toLowerCase().includes(q);
        const matchesCategory   = !filterCategory.value || r.category === filterCategory.value;
        const matchesVisibility =
            !filterVisibility.value ||
            (filterVisibility.value === 'shared'  && r.is_shared) ||
            (filterVisibility.value === 'private' && !r.is_shared);
        return matchesSearch && matchesCategory && matchesVisibility;
    });
});

// ─── API ───
const fetchResponses = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/agent/canned-responses');
        responses.value = data.data;
    } catch {
        toast.error('Failed to load canned responses.');
    } finally {
        loading.value = false;
    }
};

const submitForm = async () => {
    saving.value = true;
    try {
        if (editingResponse.value) {
            const { data } = await axios.patch(`/agent/canned-responses/${editingResponse.value.id}`, form.value);
            const idx = responses.value.findIndex(r => r.id === editingResponse.value.id);
            if (idx !== -1) responses.value[idx] = data.data;
            toast.success('Response updated.');
        } else {
            const { data } = await axios.post('/agent/canned-responses', form.value);
            responses.value.unshift(data.data);
            toast.success('Canned response created.');
        }
        closeModal();
    } catch (err) {
        toast.error(err?.response?.data?.message ?? 'Failed to save response.');
    } finally {
        saving.value = false;
    }
};

const executeDelete = async () => {
    deleting.value = true;
    try {
        await axios.delete(`/agent/canned-responses/${deletingResponse.value.id}`);
        responses.value = responses.value.filter(r => r.id !== deletingResponse.value.id);
        toast.info('Response deleted.');
        showDeleteConfirm.value = false;
        deletingResponse.value  = null;
    } catch {
        toast.error('Failed to delete response.');
    } finally {
        deleting.value = false;
    }
};

// ─── Modal Helpers ───
const openCreateModal = () => {
    editingResponse.value = null;
    form.value = defaultForm();
    showModal.value = true;
};

const openEditModal = (response) => {
    editingResponse.value = response;
    form.value = {
        title:     response.title,
        body:      response.body,
        category:  response.category ?? '',
        is_shared: response.is_shared,
    };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value       = false;
    editingResponse.value = null;
    form.value = defaultForm();
};

const confirmDelete = (response) => {
    deletingResponse.value  = response;
    showDeleteConfirm.value = true;
};

// ─── Utils ───
const copyToClipboard = async (text) => {
    try {
        await navigator.clipboard.writeText(text);
        toast.success('Copied to clipboard!');
    } catch {
        toast.error('Copy failed — please copy manually.');
    }
};

// ─── Init ───
onMounted(fetchResponses);
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}
</style>
