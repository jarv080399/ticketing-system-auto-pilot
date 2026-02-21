<template>
    <div class="space-y-8 pb-10">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-white tracking-tight">Custom Fields</h1>
                <p class="text-text-dim text-sm mt-2 max-w-2xl">
                    Extend core system entities algebraically with additional metadata fields. Dynamically shape your asset management, tickets, and user databases.
                </p>
            </div>
            <button 
                @click="openModal()"
                class="px-5 py-2.5 bg-primary hover:bg-primary-dark shadow-lg shadow-primary/20 text-white font-black text-sm rounded-xl transition-all hover-lift active:scale-95 flex items-center gap-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Add Field
            </button>
        </div>

        <div v-if="loading" class="flex items-center justify-center p-24">
            <div class="flex flex-col items-center gap-4">
                <div class="animate-spin rounded-full h-10 w-10 border-b-4 border-primary"></div>
                <p class="text-sm font-bold text-text-dim animate-pulse uppercase tracking-widest">Loading Fields...</p>
            </div>
        </div>

        <div v-else class="max-w-7xl space-y-10">
            <div v-for="entity in ['ticket', 'asset', 'user']" :key="entity" class="bg-surface rounded-2xl border border-glass-border shadow-xl overflow-hidden relative">
                
                <div class="px-8 py-5 bg-surface-light border-b border-glass-border flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-black uppercase tracking-widest text-white">{{ entity }} Metadata Schemas</h2>
                        <p class="text-xs text-text-dim mt-1">Extra structured data bound to {{ entity }}s globally.</p>
                    </div>
                    
                    <div class="hidden justify-center sm:flex w-10 h-10 bg-surface rounded-xl border border-glass-border items-center text-primary shadow-sm hover:scale-110 transition-transform cursor-help relative group/tooltip">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        <div class="absolute bottom-full right-0 mb-3 hidden group-hover/tooltip:block w-72 p-3 bg-gray-800 text-xs text-blue-100 rounded-xl border border-blue-500/30 shadow-2xl z-20 normal-case leading-relaxed font-normal">
                            <div class="font-black text-white mb-1">Entity Guide:</div>
                            Changes instantly construct new properties globally. Deleting field keys deletes data linked to old {{ entity }}s.
                        </div>
                    </div>
                </div>

                <div class="p-8 bg-background/30">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div 
                            v-for="field in getFieldsByEntity(entity)" 
                            :key="field.id"
                            class="bg-surface border border-glass-border rounded-xl p-5 flex items-center justify-between group/card hover:border-gray-500 transition-colors shadow-sm"
                        >
                            <div class="flex items-center gap-5">
                                <div class="w-12 h-12 bg-background border border-glass-border shadow-inner rounded-xl flex items-center justify-center text-xl text-primary font-bold">
                                    {{ getIconForType(field.field_type) }}
                                </div>
                                <div>
                                    <h3 class="font-black text-white text-base">{{ field.field_label }}</h3>
                                    <p class="text-[10px] text-text-dim mt-1 uppercase tracking-widest">
                                        {{ field.field_type }} TYPE <span class="mx-1">•</span> KEY: <span class="font-mono text-primary-dim lowercase tracking-normal bg-primary/10 px-1 rounded">{{ field.field_name }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 opacity-0 group-hover/card:opacity-100 transition-opacity">
                                <button @click="openModal(field)" class="p-2.5 bg-background shadow-inner border border-glass-border rounded-lg text-text-dim hover:text-white hover:bg-surface-light transition-all">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                </button>
                                <button @click="deleteField(field.id)" class="p-2.5 bg-background shadow-inner border border-glass-border rounded-lg text-red-400 hover:text-white hover:bg-red-500/20 transition-all border-red-500/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                </button>
                            </div>
                        </div>

                        <div v-if="getFieldsByEntity(entity).length === 0" class="col-span-full py-12 border-2 border-dashed border-glass-border rounded-2xl flex flex-col items-center justify-center text-text-dim bg-background/20">
                            <div class="w-12 h-12 bg-surface rounded-full flex items-center justify-center border border-glass-border mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                            </div>
                            <span class="text-sm font-bold">No custom fields deployed for {{ entity }}s.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Field Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-background/90 backdrop-blur-md flex items-center justify-center z-50 p-4">
            <div class="bg-surface border border-glass-border rounded-2xl w-full max-w-2xl p-8 shadow-[0_0_50px_rgba(0,0,0,0.5)] transform scale-100 transition-all max-h-[90vh] overflow-y-auto">
                <div class="flex items-center gap-3 mb-8 pb-4 border-b border-glass-border">
                    <div class="w-10 h-10 bg-primary/20 rounded-xl flex items-center justify-center text-primary border border-primary/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" /></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-white">{{ editingId ? 'Edit Mapping Schema' : 'Draft New Schema Key' }}</h2>
                        <p class="text-xs text-text-dim font-medium mt-1">Configure field rendering and metadata rules.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                    <div class="col-span-full">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Target Entity Model</label>
                        <select v-model="form.entity_type" :disabled="editingId" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner disabled:opacity-50">
                            <option value="ticket">Ticket - Binds to Service Requests</option>
                            <option value="asset">Asset - Binds to Inventory Records</option>
                            <option value="user">User - Binds to Identity Profiles</option>
                        </select>
                        <p class="text-[10px] font-medium text-text-dim border-l-2 border-primary/30 pl-2 mt-2">Cannot be changed after deployment mapping.</p>
                    </div>

                    <div class="col-span-full md:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Metadata Name / Raw Key (ID)</label>
                        <input v-model="form.field_name" :disabled="editingId" type="text" placeholder="e.g. department_id" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm font-mono text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner disabled:opacity-50" />
                        <p class="text-[10px] font-medium text-text-dim border-l-2 border-primary/30 pl-2 mt-2">Database key format (snake_case strongly advised).</p>
                    </div>

                    <div class="col-span-full md:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Human Readable Label</label>
                        <input v-model="form.field_label" type="text" placeholder="e.g. Department Name" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner" />
                        <p class="text-[10px] font-medium text-text-dim border-l-2 border-primary/30 pl-2 mt-2">Will be shown across UI modules & lists.</p>
                    </div>

                    <div class="col-span-full md:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Data Type / UI Renderer</label>
                        <select v-model="form.field_type" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner">
                            <option value="text">String / Text Input</option>
                            <option value="number">Integer / Float</option>
                            <option value="select">Dropdown Choice (Select)</option>
                            <option value="date">Date Picker Timestamp</option>
                            <option value="checkbox">Boolean Toggle (Checkbox)</option>
                        </select>
                    </div>

                    <div class="col-span-full md:col-span-1">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Visual Sort Index</label>
                        <input v-model="form.sort_order" type="number" class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner" />
                    </div>

                    <div v-if="form.field_type === 'select'" class="col-span-full bg-background/50 border border-glass-border p-4 rounded-xl border-dashed">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-text-dim mb-2 ml-1">Enum Options List (CSV)</label>
                        <input 
                            @input="e => form.options = e.target.value.split(',').map(s => s.trim())"
                            :value="form.options.join(', ')"
                            type="text" 
                            placeholder="Option A, Option B, Option C..." 
                            class="w-full bg-background border border-glass-border rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all shadow-inner" 
                        />
                        <p class="text-[10px] font-medium text-text-dim border-l-2 border-primary/30 pl-2 mt-2">Split valid choices using standard commas.</p>
                    </div>

                    <div class="col-span-full flex items-center justify-between gap-4 bg-background px-5 py-4 rounded-xl border border-glass-border shadow-inner mt-2">
                        <div>
                            <span class="text-sm font-black text-white tracking-wide">Mandatory Field Enforcement</span>
                            <p class="text-[10px] font-medium text-text-dim mt-1">Stops forms submitting without evaluating this data key.</p>
                        </div>
                        <button 
                            @click="form.is_required = !form.is_required"
                            :class="[
                                'w-12 h-6 rounded-full p-1 transition-colors duration-300 ease-in-out relative flex-shrink-0 cursor-pointer flex items-center justify-start',
                                form.is_required ? 'bg-primary' : 'bg-gray-700'
                            ]"
                        >
                            <span 
                                :class="[
                                    'w-4 h-4 bg-white rounded-full transition-transform duration-300 shadow-sm block',
                                    form.is_required ? 'translate-x-6' : 'translate-x-0'
                                ]"
                            ></span>
                        </button>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-10 pt-6 border-t border-glass-border">
                    <button @click="showModal = false" class="px-5 py-2.5 text-sm font-bold text-text-dim hover:text-white hover:bg-white/5 disabled:opacity-50 transition-colors rounded-xl">Cancel</button>
                    <button @click="saveField" :disabled="saving" class="px-7 py-2.5 bg-primary hover:bg-primary-dark shadow-lg shadow-primary/30 text-white font-black rounded-xl transition-all hover-lift active:scale-95 disabled:opacity-50 flex items-center gap-2">
                        <span v-if="saving" class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></span>
                        {{ editingId ? 'Update Field Blueprint' : 'Commit New Field' }}
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
const loading = ref(true);
const saving = ref(false);
const fields = ref([]);
const showModal = ref(false);
const editingId = ref(null);

const form = ref({
    entity_type: 'ticket',
    field_name: '',
    field_label: '',
    field_type: 'text',
    options: [],
    is_required: false,
    sort_order: 0
});

const fetchFields = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/admin/custom-fields');
        fields.value = response.data.data;
    } catch (error) {
        toast.error('Failed to load custom fields from the database.');
    } finally {
        loading.value = false;
    }
};

const getFieldsByEntity = (entity) => {
    return fields.value.filter(f => f.entity_type === entity)
                       .sort((a,b) => (a.sort_order || 0) - (b.sort_order || 0));
};

const getIconForType = (type) => {
    switch (type) {
        case 'text': return '✎';
        case 'number': return '#';
        case 'select': return '▼';
        case 'date': return '📅';
        case 'checkbox': return '✓';
        default: return '●';
    }
};

const openModal = (field = null) => {
    if (field) {
        editingId.value = field.id;
        form.value = { ...field, options: field.options || [] };
    } else {
        editingId.value = null;
        form.value = {
            entity_type: 'ticket',
            field_name: '',
            field_label: '',
            field_type: 'text',
            options: [],
            is_required: false,
            sort_order: 0
        };
    }
    showModal.value = true;
};

const saveField = async () => {
    saving.value = true;
    try {
        if (editingId.value) {
            await axios.put(`/admin/custom-fields/${editingId.value}`, form.value);
            toast.success('Field configuration instantly deployed.');
        } else {
            await axios.post('/admin/custom-fields', form.value);
            toast.success('Successfully deployed new metadata key.');
        }
        showModal.value = false;
        fetchFields();
    } catch (error) {
        toast.error(error.response?.data?.message || 'Failed to submit schema update to database.');
    } finally {
        saving.value = false;
    }
};

const deleteField = async (id) => {
    if (!confirm('EXTREME WARNING: All structured data bound to this key across existing records globally will be irrevocably scrubbed. Are you absolutely certain?')) return;
    try {
        await axios.delete(`/admin/custom-fields/${id}`);
        fetchFields();
        toast.info('Custom field wiped across system scope.');
    } catch (error) {
        toast.error('Failed to eliminate field instance.');
    }
};

onMounted(fetchFields);
</script>
