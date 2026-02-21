<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Custom Fields</h1>
                <p class="text-text-dim text-sm mt-1">Extend system entities with additional metadata fields.</p>
            </div>
            <button 
                @click="openModal()"
                class="px-4 py-2 bg-primary hover:bg-primary-dark text-surface font-black text-sm rounded-lg transition-all"
            >
                + Add Custom Field
            </button>
        </div>

        <div v-if="loading" class="flex items-center justify-center p-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
        </div>

        <div v-else class="max-w-5xl space-y-8">
            <div v-for="entity in ['ticket', 'asset', 'user']" :key="entity" class="space-y-4">
                <div class="flex items-center gap-4">
                    <h2 class="text-sm font-black uppercase tracking-widest text-primary">{{ entity }} Fields</h2>
                    <div class="flex-1 h-[1px] bg-glass-border"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div 
                        v-for="field in getFieldsByEntity(entity)" 
                        :key="field.id"
                        class="bg-surface border border-glass-border rounded-xl p-4 flex items-center justify-between group"
                    >
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-surface-light rounded-lg flex items-center justify-center text-xl">
                                {{ getIconForType(field.field_type) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-white">{{ field.field_label }}</h3>
                                <p class="text-xs text-text-dim">Key: <span class="text-primary-dim">{{ field.field_name }}</span> • {{ field.field_type }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="openModal(field)" class="p-2 text-text-dim hover:text-white transition-colors">Edit</button>
                            <button @click="deleteField(field.id)" class="p-2 text-red-400 hover:text-red-300 transition-colors">Delete</button>
                        </div>
                    </div>

                    <div v-if="getFieldsByEntity(entity).length === 0" class="col-span-full py-8 border-2 border-dashed border-glass-border rounded-xl flex items-center justify-center text-text-dim text-sm">
                        No custom fields for {{ entity }}s.
                    </div>
                </div>
            </div>
        </div>

        <!-- Add/Edit Field Modal -->
        <div v-if="showModal" class="fixed inset-0 bg-background/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-surface border border-glass-border rounded-2xl w-full max-w-lg p-6 shadow-2xl">
                <h2 class="text-xl font-bold text-white mb-6">{{ editingId ? 'Edit' : 'Add' }} Custom Field</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Entity Type</label>
                        <select v-model="form.entity_type" :disabled="editingId" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
                            <option value="ticket">Ticket</option>
                            <option value="asset">Asset</option>
                            <option value="user">User</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Field Name (ID)</label>
                        <input v-model="form.field_name" :disabled="editingId" type="text" placeholder="e.g. department_id" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Display Label</label>
                        <input v-model="form.field_label" type="text" placeholder="e.g. Department" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Field Type</label>
                        <select v-model="form.field_type" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
                            <option value="text">Text Input</option>
                            <option value="number">Number</option>
                            <option value="select">Dropdown (Select)</option>
                            <option value="date">Date Picker</option>
                            <option value="checkbox">Checkbox</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Sort Order</label>
                        <input v-model="form.sort_order" type="number" class="w-full bg-background border border-glass-border rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary" />
                    </div>

                    <div v-if="form.field_type === 'select'" class="col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Options (comma separated)</label>
                        <input 
                            @input="e => form.options = e.target.value.split(',').map(s => s.trim())"
                            :value="form.options.join(', ')"
                            type="text" 
                            placeholder="Option 1, Option 2, Option 3" 
                            class="w-full bg-background border border-glass-border rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary" 
                        />
                    </div>

                    <div class="col-span-2 flex items-center gap-3">
                        <input v-model="form.is_required" type="checkbox" id="required_field" class="w-4 h-4 rounded border-glass-border bg-background text-primary" />
                        <label for="required_field" class="text-sm text-gray-300">This field is mandatory</label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8">
                    <button @click="showModal = false" class="px-4 py-2 text-sm text-text-dim hover:text-white transition-colors">Cancel</button>
                    <button @click="saveField" :disabled="saving" class="px-6 py-2 bg-primary hover:bg-primary-dark text-surface font-bold rounded-lg transition-all disabled:opacity-50">
                        {{ editingId ? 'Update Field' : 'Create Field' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from '@/plugins/axios';

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
    try {
        const response = await axios.get('/admin/custom-fields');
        fields.value = response.data.data;
    } catch (error) {
        alert('Failed to load custom fields');
    } finally {
        loading.value = false;
    }
};

const getFieldsByEntity = (entity) => {
    return fields.value.filter(f => f.entity_type === entity);
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
        } else {
            await axios.post('/admin/custom-fields', form.value);
        }
        showModal.value = false;
        fetchFields();
    } catch (error) {
        alert(error.response?.data?.message || 'Failed to save field');
    } finally {
        saving.value = false;
    }
};

const deleteField = async (id) => {
    if (!confirm('All data stored in this field across existing records will be lost. Continue?')) return;
    try {
        await axios.delete(`/admin/custom-fields/${id}`);
        fetchFields();
    } catch (error) {
        alert('Failed to delete field');
    }
};

onMounted(fetchFields);
</script>
