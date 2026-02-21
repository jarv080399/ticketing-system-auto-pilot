<template>
    <div class="w-full space-y-12 pb-12">
        <!-- Header -->
        <div class="space-y-4">
            <h1 class="text-4xl font-black tracking-tight text-text-main">
                Create New <span class="text-gradient">Request</span>
            </h1>
            <p class="text-text-dim font-medium">Choose a category that best describes your issue to get started.</p>
        </div>

        <!-- Step 1: Category Selection -->
        <div v-if="step === 1" class="space-y-8 animate-fade-in">
            <CategoryTiles 
                :categories="ticketStore.categories" 
                :selected-id="form.category_id"
                @select="selectCategory" 
            />
        </div>

        <!-- Step 2: Form Details -->
        <div v-else class="glass-card rounded-xl p-8 md:p-12 space-y-10 animate-slide-up">
            <div class="flex items-center gap-4 pb-6 border-b border-glass-border">
                <button @click="step = 1" class="p-3 rounded-xl bg-surface-light hover:bg-primary/10 text-text-dim hover:text-primary transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div>
                    <h2 class="text-xl font-bold text-text-main">{{ selectedCategory?.name }}</h2>
                    <p class="text-sm text-text-dim">Step 2: Provide details regarding your request</p>
                </div>
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-8">
                <!-- Duplicate Warning -->
                <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-4" enter-to-class="opacity-100 translate-y-0">
                    <div v-if="duplicateWarning" class="p-4 rounded-lg bg-amber-500/10 border border-amber-500/20 flex gap-4 items-start">
                        <span class="text-2xl">⚠️</span>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-amber-500">Possible Duplicate Found</p>
                            <p class="text-xs text-text-dim">You recently submitted a similar request ({{ duplicateWarning.ticket_number }}). Would you like to proceed or view the existing one?</p>
                            <div class="flex gap-4 mt-2">
                                <router-link :to="`/tickets/${duplicateWarning.ticket_number}`" class="text-xs font-bold text-amber-500 underline uppercase tracking-widest">View Existing</router-link>
                                <button type="button" @click="duplicateWarning = null" class="text-xs font-bold text-text-dim uppercase tracking-widest">Ignore</button>
                            </div>
                        </div>
                    </div>
                </transition>

                <div class="space-y-6">
                    <!-- Title -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-text-dim uppercase tracking-[0.2em] ml-1">Summary of request</label>
                        <input 
                            v-model="form.title"
                            @blur="checkDuplicate"
                            type="text" 
                            required
                            placeholder="e.g. Broken laptop screen after travel"
                            class="w-full px-6 py-4 bg-surface-light/50 border border-glass-border rounded-lg text-text-main placeholder:text-text-dim/30 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition-all"
                        />
                    </div>

                    <!-- Article Suggestions -->
                    <ArticleSuggestions :query="form.title" />

                    <!-- Description -->
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-text-dim uppercase tracking-[0.2em] ml-1">Detailed Description</label>
                        <textarea 
                            v-model="form.description"
                            required
                            rows="5"
                            placeholder="Please provide as much detail as possible..."
                            class="w-full px-6 py-4 bg-surface-light/50 border border-glass-border rounded-lg text-text-main placeholder:text-text-dim/30 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition-all resize-none"
                        ></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Priority -->
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-text-dim uppercase tracking-[0.2em] ml-1">Urgency</label>
                            <select 
                                v-model="form.priority"
                                class="w-full px-6 py-4 bg-surface-light/50 border border-glass-border rounded-lg text-text-main focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary/50 transition-all appearance-none"
                            >
                                <option value="low">Low - General query</option>
                                <option value="medium">Medium - Normal operations</option>
                                <option value="high">High - Business impact</option>
                                <option value="critical">Critical - Total stoppage</option>
                            </select>
                        </div>

                        <!-- Attachments with Preview -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-black text-text-dim uppercase tracking-[0.2em] ml-1">
                                    Attachments <span class="text-text-dim/50">(Images &amp; PDF · max 5 · 10 MB each)</span>
                                </label>
                                <span v-if="files.length" class="text-[10px] font-bold text-primary">{{ files.length }}/5</span>
                            </div>

                            <!-- Drop Zone -->
                            <div
                                class="relative rounded-lg transition-all duration-200"
                                :class="isDragging
                                    ? 'border-2 border-primary bg-primary/5 scale-[1.01]'
                                    : 'border-2 border-dashed border-glass-border bg-surface-light/50 hover:border-primary/50 hover:bg-surface-light'"
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="handleDrop"
                            >
                                <input
                                    ref="fileInputRef"
                                    type="file"
                                    multiple
                                    accept="image/jpeg,image/png,image/gif,image/webp,application/pdf"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                    @change="handleFileChange"
                                />
                                <div class="flex flex-col items-center justify-center gap-2 py-6 px-4 pointer-events-none">
                                    <div class="w-10 h-10 rounded-full bg-surface flex items-center justify-center text-xl">
                                        {{ isDragging ? '📂' : '📎' }}
                                    </div>
                                    <p class="text-sm font-bold text-text-dim text-center">
                                        {{ isDragging ? 'Release to attach' : 'Drop files here or click to browse' }}
                                    </p>
                                    <p class="text-[10px] text-text-dim/60 font-medium">JPG · PNG · GIF · WEBP · PDF</p>
                                </div>
                            </div>

                            <!-- Preview Grid -->
                            <div v-if="filePreviews.length" class="grid grid-cols-3 gap-2">
                                <div
                                    v-for="(preview, i) in filePreviews"
                                    :key="i"
                                    class="relative group rounded-lg overflow-hidden bg-surface-light border border-glass-border aspect-square"
                                >
                                    <!-- Image preview -->
                                    <img
                                        v-if="preview.type === 'image'"
                                        :src="preview.url"
                                        :alt="preview.name"
                                        class="w-full h-full object-cover"
                                    />

                                    <!-- PDF preview -->
                                    <div v-else class="w-full h-full flex flex-col items-center justify-center gap-1 p-2">
                                        <span class="text-3xl">📄</span>
                                        <p class="text-[9px] font-bold text-text-dim text-center truncate w-full px-1">{{ preview.name }}</p>
                                        <p class="text-[9px] text-text-dim/60">{{ preview.size }}</p>
                                    </div>

                                    <!-- Overlay on hover -->
                                    <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center gap-1 p-1">
                                        <p class="text-[9px] font-bold text-white text-center truncate w-full px-2">{{ preview.name }}</p>
                                        <p class="text-[9px] text-white/70">{{ preview.size }}</p>
                                        <button
                                            type="button"
                                            @click.stop="removeFile(i)"
                                            class="mt-1 w-7 h-7 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center text-xs font-black transition-colors"
                                            title="Remove"
                                        >✕</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 flex justify-end gap-4">
                    <button 
                        type="button" 
                        @click="step = 1"
                        class="px-8 py-4 rounded-lg text-sm font-bold text-text-dim hover:text-text-main transition-colors"
                    >
                        Back
                    </button>
                    <button 
                        type="submit" 
                        :disabled="loading"
                        class="px-10 py-4 bg-primary hover:bg-primary-dark text-white font-black rounded-lg shadow-xl shadow-primary/20 hover-lift active:scale-95 transition-all disabled:opacity-50"
                    >
                        <span v-if="loading">Processing...</span>
                        <span v-else>Submit Request</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useTicketStore } from '@/stores/tickets';
import { useToast } from 'vue-toastification';
import CategoryTiles from '@/components/CategoryTiles.vue';
import ArticleSuggestions from '@/components/Kb/ArticleSuggestions.vue';

const router = useRouter();
const ticketStore = useTicketStore();
const toast = useToast();

const step = ref(1);
const loading = ref(false);
const isDragging = ref(false);
const duplicateWarning = ref(null);
const files = ref([]);
const filePreviews = ref([]);
const fileInputRef = ref(null);

const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
const MAX_SIZE_BYTES = 10 * 1024 * 1024; // 10 MB
const MAX_FILES = 5;

const form = ref({
    title: '',
    description: '',
    category_id: null,
    priority: 'medium',
});

const selectedCategory = computed(() => {
    return ticketStore.categories.find(c => c.id === form.value.category_id);
});

onMounted(async () => {
    await ticketStore.fetchCategories();
});

const selectCategory = (category) => {
    form.value.category_id = category.id;
    step.value = 2;
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const formatBytes = (bytes) => {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
};

const buildPreview = (file) => new Promise((resolve) => {
    const type = file.type.startsWith('image/') ? 'image' : 'pdf';
    if (type === 'pdf') {
        resolve({ type, name: file.name, size: formatBytes(file.size), url: null });
        return;
    }
    const reader = new FileReader();
    reader.onload = (e) => resolve({ type, name: file.name, size: formatBytes(file.size), url: e.target.result });
    reader.readAsDataURL(file);
});

const addFiles = async (incoming) => {
    const valid = [];
    for (const file of incoming) {
        if (!ALLOWED_TYPES.includes(file.type)) {
            toast.error(`"${file.name}" is not allowed. Only images and PDFs.`);
            continue;
        }
        if (file.size > MAX_SIZE_BYTES) {
            toast.error(`"${file.name}" exceeds the 10 MB limit.`);
            continue;
        }
        if (files.value.length + valid.length >= MAX_FILES) {
            toast.warning(`Maximum ${MAX_FILES} attachments allowed.`);
            break;
        }
        valid.push(file);
    }
    const previews = await Promise.all(valid.map(buildPreview));
    files.value = [...files.value, ...valid];
    filePreviews.value = [...filePreviews.value, ...previews];
    // Reset the input so re-selecting the same file triggers change again
    if (fileInputRef.value) fileInputRef.value.value = '';
};

const handleFileChange = (e) => addFiles(Array.from(e.target.files));
const handleDrop = (e) => { isDragging.value = false; addFiles(Array.from(e.dataTransfer.files)); };

const removeFile = (index) => {
    files.value.splice(index, 1);
    filePreviews.value.splice(index, 1);
};

const checkDuplicate = async () => {
    if (form.value.title.length < 5) return;
    const result = await ticketStore.checkDuplicate(form.value.title);
    if (result.is_duplicate) {
        duplicateWarning.value = result.duplicate_ticket;
    }
};

const handleSubmit = async () => {
    loading.value = true;
    try {
        const formData = new FormData();
        formData.append('title', form.value.title);
        formData.append('description', form.value.description);
        formData.append('category_id', form.value.category_id);
        formData.append('priority', form.value.priority);
        
        files.value.forEach((file, index) => {
            formData.append(`attachments[${index}]`, file);
        });

        const response = await ticketStore.createTicket(formData);
        toast.success('Ticket submitted successfully!');
        // TicketResource wraps in { data: {...} } — store returns that wrapper object
        const ticketNumber = response?.data?.ticket_number ?? response?.ticket_number;
        router.push(`/tickets/${ticketNumber}`);
    } catch (err) {
        console.error(err);
        // Show first Laravel validation error if available
        const firstError = err?.errors ? Object.values(err.errors)[0]?.[0] : null;
        toast.error(firstError || err?.message || 'Failed to submit ticket. Please try again.');
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.animate-fade-in { animation: fadeIn 0.5s ease-out; }
.animate-slide-up { animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1); }

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
